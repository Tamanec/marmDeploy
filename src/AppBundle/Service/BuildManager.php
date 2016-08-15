<?php

namespace AppBundle\Service;


use AppBundle\Entity\AppConfig;
use AppBundle\Entity\AppDataBuildConf;
use AppBundle\Model\ProjectRelatedBuildConf;
use Docker\API\Model\BuildInfo;
use Docker\Context\Context;
use Docker\Docker;
use Docker\Stream\BuildStream;
use GitElephant\Repository;
use Http\Client\Plugin\Exception\ClientErrorException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BuildManager {

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
     * BuildManager constructor.
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
     * Собирает образы данных для box и integrator
     *
     * @param AppDataBuildConf $buildConf Параметры сборки
     * @return BuildInfo[]|BuildStream|ResponseInterface
     */
    public function buildAppDataImage(AppDataBuildConf $buildConf) {
        // Git - клонируем реп (если нужно), переключаемся на указанную ветку
        $projectPath = $this->prepareRepo($buildConf);

        // Копируем конфиги
        $this->copyConfigs($buildConf);

        // Копируем код в контекст сборки
        $appBuildContextPath = $this->buildContextPath . $buildConf->getRelativeBuildContextPath();
        $appSource = $appBuildContextPath . '/source';
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
     * Собирает дополнительные образы связанные привязанные к проекту:
     *      log (data-контейнер),
     *      cron,
     *      logrotate
     *
     * @param ProjectRelatedBuildConf $buildConf
     * @return \Docker\API\Model\BuildInfo[]|BuildStream|ResponseInterface
     */
    public function buildProjectRelatedImage(ProjectRelatedBuildConf $buildConf) {
        // подложить конфиг

        $appBuildContextPath = $this->buildContextPath . $buildConf->getRelativeBuildContextPath();
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
     * Проверяет существует ли образ
     *
     * @param string $name Имя образа
     * @return bool
     */
    public function isExists($name) {
        try {
            $this->docker->getImageManager()->find($name);
            return true;
        } catch (ClientErrorException $e) {
            return false;
        }
    }

    /**
     * Удаляет образ
     *
     * @param $name Название образа
     */
    public function deleteImage($name) {
        $this->docker->getImageManager()->remove($name);
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
                ? BuildManager::REPO_INTEGRATOR
                : BuildManager::REPO_BOX
            ;
            $git->cloneFrom($repoUrl);
            $this->fs->chmod($this->repoPath, 0775, 0, true);
        }

        $git = new Repository($projectPath);
        $git->fetch();
        $git->checkout($imageConf->getBranch());

        $process = new Process('composer install');
        $process->setWorkingDirectory($projectPath . '/protected');
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

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
            ->setType($imageConf->getType())
            ->setName($imageConf->getMainConfig())
        ;
        $mainConfigPath = $this->configManager->getConfigPath($mainConfig);

        $consoleConfig = new AppConfig();
        $consoleConfig
            ->setProject($imageConf->getProject())
            ->setEnv($imageConf->getEnv())
            ->setType($imageConf->getType())
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
     * Копирует код проекта в контекст сборки докера
     * Перед копированием очищает папку контекста
     *
     * @param string $projectPath Путь до кода проекта
     * @param string $appSource Путь до папки содержащей код в контексте сборки
     */
    protected function prepareBuildContext($projectPath, $appSource) {
        $dirContent = (new Finder())->in($appSource);
        $this->fs->remove($dirContent);

        $process = new Process("cp -r {$projectPath} {$appSource}");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

}