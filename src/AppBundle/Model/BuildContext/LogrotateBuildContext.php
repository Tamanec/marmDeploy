<?php

namespace AppBundle\Model\BuildContext;


use AppBundle\Model\AppConf\AppConfig;
use AppBundle\Model\BuildConf\LogrotateBuildConf;
use AppBundle\Service\AppConfigManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class LogrotateBuildContext extends BuildContext {

    /**
     * @var LogrotateBuildConf
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
     * @param LogrotateBuildConf $buildConf Настройки сборки
     * @param Filesystem $fs
     * @param AppConfigManager $configManager
     */
    public function __construct(
        $basePath,
        LogrotateBuildConf $buildConf,
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
        return '/logrotate';
    }

    /**
     * Копирует настройки logrotate в контекст сборки
     */
    protected function copyConfig() {
        $сonfig = new AppConfig();
        $сonfig
            ->setProject($this->buildConf->getProject())
            ->setEnv($this->buildConf->getEnv())
            ->setType('logrotate')
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