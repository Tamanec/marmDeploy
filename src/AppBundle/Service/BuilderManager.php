<?php

namespace AppBundle\Service;


use AppBundle\Entity\AppConfig;
use AppBundle\Entity\AppDataBuildConf;

use AppBundle\Entity\LogDataBuildConf;
use Docker\API\Model\BuildInfo;
use Docker\Context\Context;
use Docker\Docker;
use Docker\Stream\BuildStream;
use GitElephant\Repository;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BuilderManager {

    const REPO_BOX = 'ssh://git@party.altarix.ru:2222/box.git';
    const REPO_INTEGRATOR = 'ssh://git@party.altarix.ru:2222/integrator.git';

    /**
     * Путь до папки контекста сборок
     * относительно папки содержащей контексты
     */

    /**
     * data-контейнера приложения (box|integrator)
     */
    const BUILD_CONTEXT_PATH_DATA_APP = '/data/www';

    /**
     * data-контейнер логов
     */
    const BUILD_CONTEXT_PATH_DATA_LOG = '/data/log';

    /**
     * @var Docker
     */
    protected $docker;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $repoPath;

    /**
     * @var AppConfigManager
     */
    protected $configManager;

    /**
     * @var string
     */
    protected $buildContextPath;

    /**
     * @var string
     */
    protected $registryUrl;

    /**
     * BuilderManager constructor.
     * @param Docker $docker
     * @param Filesystem $fs
     * @param string $repoPath
     * @param AppConfigManager $configManager
     * @param $buildContextPath
     * @param $registryUrl
     */
    public function __construct(
        Docker $docker,
        Filesystem $fs,
        $repoPath,
        AppConfigManager $configManager,
        $buildContextPath,
        $registryUrl
    ) {
        $this->docker = $docker;
        $this->fs = $fs;
        $this->repoPath = realpath($repoPath);
        $this->configManager = $configManager;
        $this->buildContextPath = $buildContextPath;
        $this->registryUrl = $registryUrl;
    }

    /**
     * @param AppDataBuildConf $buildConf Параметры сборки
     * @return BuildInfo[]|BuildStream|ResponseInterface
     */
    public function buildAppData(AppDataBuildConf $buildConf) {
        // Git - клонируем реп (если нужно), переключаемся на указанную ветку
        $projectPath = $this->prepareRepo($buildConf);

        // Копируем конфиги
        $this->copyConfigs($buildConf);

        // Копируем код в контекст Dockerfile
        $appBuildContextPath = $this->buildContextPath
            . self::BUILD_CONTEXT_PATH_DATA_APP
        ;
        $appSource = $appBuildContextPath
            . '/source/'
            . $buildConf->getType()
        ;
        $this->prepareBuildContext($projectPath, $appSource);

        // Запускаем build
        $context = new Context($appBuildContextPath);
        $buildInfo = $this->docker->getImageManager()->build(
            $context->toStream(),
            [
                't' => $buildConf->getFullName(),
                'buildargs' => ['SOURCE_NAME' => $buildConf->getType()]
            ]
        );

        return $buildInfo;
    }

    /**
     * @param LogDataBuildConf $buildConf
     * @return \Docker\API\Model\BuildInfo[]|BuildStream|ResponseInterface
     */
    public function buildLogData(LogDataBuildConf $buildConf) {
        $appBuildContextPath = $this->buildContextPath
            . self::BUILD_CONTEXT_PATH_DATA_APP
        ;
        $context = new Context($appBuildContextPath);
        $buildInfo = $this->docker->getImageManager()->build(
            $context->toStream(),
            [
                't' => $buildConf->getFullName(),
                'buildargs' => ['PROJECT_NAME' => $buildConf->getProject()]
            ]
        );

        return $buildInfo;
    }

    /**
     * @return array Список образов
     */
    public function getImagesList() {
        $images = $this->docker->getImageManager()->findAll();
        $repoList = [];
        foreach ($images as $image) {
            $tags = $image->getRepoTags();

            // Получаем имя репозитория
            $name = $this->getShortImageName($tags[0]);

            $repoList[] = [
                'name' => $name,
                'tags' => $tags
            ];
        }

        return $repoList;
    }

    /**
     * Создает тэг для заданного репозитория (services.yml: build.registry.url)
     * и выполняет пуш
     *
     * @param string $name Имя образа с тегом
     * @return \Docker\API\Model\PushImageInfo[]|\Docker\Stream\CreateImageStream|ResponseInterface
     */
    public function pushImage($name) {
        $imageManager = $this->docker->getImageManager();
        $imageManager->tag(
            $name,
            [
                'repo' => $this->registryUrl . '/' . $this->getShortImageName($name),
                'force' => true,
                'tag' => $this->getImageTag($name)
            ]
        );

        return $imageManager->push(
            $this->registryUrl . '/' . $name,
            [
                'X-Registry-Auth' => [
                    'username' => '',
                    'password' => '',
                    'email' => ''
                ]
            ]
        );
    }

    /**
     * Удаляет образ и тэг на приватный репозиторий
     *
     * @param $name Название образа
     */
    public function deleteImage($name) {
        $this->docker->getImageManager()->remove($name);
        $this->docker->getImageManager()->remove(
            $this->registryUrl . '/' . $name
        );
    }

    /**
     * @return string Ссылка на приватный докер-репозиторий
     */
    public function getRegistryUrl() {
        return $this->registryUrl;
    }

    /**
     * Пример:
     *      from: 172.29.134.38:5000/marm-os:latest
     *      to: marm-os
     *
     * @param string $fullImageName Имя образа
     * @return string Имя образа без репозитория и тэга
     */
    protected function getShortImageName($fullImageName) {
        $privateRepoPattern = '/^.*\//';
        $tagPattern = '/:[^\/]+$/';
        return preg_replace(
            [$privateRepoPattern, $tagPattern],
            '',
            $fullImageName
        );
    }

    /**
     * Пример:
     *      from: 172.29.134.38:5000/marm-os:latest
     *      to: latest
     * @param string $fullImageName Имя образа
     * @return string Тэг образа
     */
    protected function getImageTag($fullImageName) {
        preg_match('/:([^\/]+$)/', $fullImageName, $matches);
        return $matches[1];
    }

    /**
     * @param AppDataBuildConf $imageConf
     * @return string Путь до папки с кодом проекта
     */
    protected function prepareRepo(AppDataBuildConf $imageConf) {
        $projectPath = $this->repoPath . DIRECTORY_SEPARATOR . $imageConf->getType();

        if (!$this->fs->exists($projectPath)) {
            $git = new Repository($this->repoPath);
            $repoUrl = $imageConf->getType() === 'integrator'
                ? BuilderManager::REPO_INTEGRATOR
                : BuilderManager::REPO_BOX
            ;
            $git->cloneFrom($repoUrl);
            $this->fs->chmod($this->repoPath, 0775, 0, true);
        }

        $git = new Repository($projectPath);
        $git->fetch();
        $git->checkout($imageConf->getBranch());

        return $projectPath;
    }

    /**
     * @param AppDataBuildConf $imageConf
     */
    protected function copyConfigs(AppDataBuildConf $imageConf) {
        $mainConfig = new AppConfig();
        $mainConfig
            ->setProject($imageConf->getProject())
            ->setEnv($imageConf->getEnv())
            ->setName($imageConf->getMainConfig())
        ;
        $mainConfigPath = $this->configManager->getConfigPath($mainConfig);

        $consoleConfig = new AppConfig();
        $consoleConfig
            ->setProject($imageConf->getProject())
            ->setEnv($imageConf->getEnv())
            ->setName($imageConf->getConsoleConfig())
        ;
        $consoleConfigPath = $this->configManager->getConfigPath($consoleConfig);

        $repoConfigPath = $this->repoPath
            . DIRECTORY_SEPARATOR
            . $imageConf->getType()
            . '/protected/config'
        ;
        $this->fs->copy($mainConfigPath, $repoConfigPath . '/main.php', true);
        $this->fs->copy($consoleConfigPath, $repoConfigPath . '/console.php', true);
    }

    /**
     * @param $projectPath
     * @param $appSource
     */
    protected function prepareBuildContext($projectPath, $appSource) {
        if ($this->fs->exists($appSource)) {
            $this->fs->chmod($appSource, 0775, 0, true);
        }

        $process = new Process("cp -r {$projectPath} {$appSource}");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

}