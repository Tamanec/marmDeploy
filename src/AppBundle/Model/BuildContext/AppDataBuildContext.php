<?php

namespace AppBundle\Model\BuildContext;


use AppBundle\Model\AppConf\AppConfig;
use AppBundle\Model\BuildConf\AppDataBuildConf;
use AppBundle\Service\ProjectRepository;
use AppBundle\Service\AppConfigManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class AppDataBuildContext extends BuildContext {

    /**
     * @var AppDataBuildConf
     */
    protected $buildConf;

    /**
     * @var \AppBundle\Service\ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var AppConfigManager
     */
    protected $configManager;

    /**
     * @param string $basePath Директория содержащая контексты сборок
     * @param AppDataBuildConf $buildConf Настройки сборки
     * @param ProjectRepository $projectRepository
     * @param Filesystem $fs
     * @param AppConfigManager $configManager
     */
    public function __construct(
        $basePath,
        AppDataBuildConf $buildConf,
        ProjectRepository $projectRepository,
        Filesystem $fs,
        AppConfigManager $configManager
    ) {
        parent::__construct($basePath, $buildConf);
        $this->projectRepository = $projectRepository;
        $this->fs = $fs;
        $this->configManager = $configManager;
    }

    public function prepare() {
        $this->initRepo();
        $this->copyConfigs();
        $this->copyProject();
    }

    protected function getRelativePath() {
        return '/data/www';
    }

    /**
     * Подготавливает код проекта:
     *  скачивает (если нужно)
     *  переключается на выбранную ветку
     *  устанавливает зависимости с помощью composer
     *
     * @return string Путь до папки с кодом проекта
     */
    protected function initRepo() {
        $sourcePath =
            $this->projectRepository->getBasePath()
            . DIRECTORY_SEPARATOR
            . $this->buildConf->getType()
        ;

        if (!$this->fs->exists($sourcePath)) {
            $repoUrl = $this->buildConf->getType() === 'integrator'
                ? ProjectRepository::INTEGRATOR
                : ProjectRepository::BOX
            ;
            $this->projectRepository->cloneProject($repoUrl);
        }

        $this->projectRepository->checkout(
            $sourcePath,
            $this->buildConf->getBranch()
        );

        $process = new Process('composer install');
        $process->setWorkingDirectory($sourcePath . '/protected');
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $sourcePath;
    }

    /**
     * Копирует настройки проекта в репозиторий
     */
    protected function copyConfigs() {
        $mainConfig = new AppConfig();
        $mainConfig
            ->setProject($this->buildConf->getProject())
            ->setEnv($this->buildConf->getEnv())
            ->setType($this->buildConf->getType())
            ->setName($this->buildConf->getMainConfig())
        ;
        $mainConfigPath = $this->configManager->getConfigPath($mainConfig);

        $consoleConfig = new AppConfig();
        $consoleConfig
            ->setProject($this->buildConf->getProject())
            ->setEnv($this->buildConf->getEnv())
            ->setType($this->buildConf->getType())
            ->setName($this->buildConf->getConsoleConfig())
        ;
        $consoleConfigPath = $this->configManager->getConfigPath($consoleConfig);

        $repoConfigPath =
            $this->projectRepository->getBasePath()
            . DIRECTORY_SEPARATOR
            . $this->buildConf->getType()
            . '/protected/config'
        ;
        $this->fs->copy($mainConfigPath, $repoConfigPath . '/main.php', true);
        $this->fs->copy($consoleConfigPath, $repoConfigPath . '/console.php', true);
    }

    /**
     * Копирует код проекта в контекст сборки
     */
    protected function copyProject() {
        $destinationPath = $this->getPath() . '/source';
        $dirContent = (new Finder())->in($destinationPath);
        $this->fs->remove($dirContent);

        $sourcePath =
            $this->projectRepository->getBasePath()
            . DIRECTORY_SEPARATOR
            . $this->buildConf->getType()
        ;

        $process = new Process("cp -r {$sourcePath} {$destinationPath}");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

}