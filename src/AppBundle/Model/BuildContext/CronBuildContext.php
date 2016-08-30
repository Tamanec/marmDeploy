<?php

namespace AppBundle\Model\BuildContext;


use AppBundle\Model\AppConf\AppConfig;
use AppBundle\Model\BuildConf\CronBuildConf;
use AppBundle\Service\AppConfigManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CronBuildContext extends BuildContext {

    /**
     * @var CronBuildConf
     */
    protected $buildConf;

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
     * @param CronBuildConf $buildConf Настройки сборки
     * @param Filesystem $fs
     * @param AppConfigManager $configManager
     */
    public function __construct(
        $basePath,
        CronBuildConf $buildConf,
        Filesystem $fs,
        AppConfigManager $configManager
    ) {
        parent::__construct($basePath, $buildConf);
        $this->fs = $fs;
        $this->configManager = $configManager;
    }

    public function prepare() {
        $this->copyConfig();
    }

    protected function getRelativePath() {
        return '/cron';
    }

    /**
     * Копирует настройки cron в контекст сборки
     */
    protected function copyConfig() {
        $сonfig = new AppConfig();
        $сonfig
            ->setProject($this->buildConf->getProject())
            ->setEnv($this->buildConf->getEnv())
            ->setType('cron')
            ->setName($this->buildConf->getConfig())
        ;
        $configPath = $this->configManager->getConfigPath($сonfig);

        // Конфиг переименовывается по имени проекта
        $targetPath =
            $this->getPath()
            . '/conf'
            . DIRECTORY_SEPARATOR
            . $this->buildConf->getProject()
        ;
        $this->fs->copy($configPath, $targetPath, true);
    }

}