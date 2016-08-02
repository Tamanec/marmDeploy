<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppConfig;
use AppBundle\Model\ConfigManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AppConfigManager extends ConfigManager {

    /**
     * Номера частей при разбиении относительного пути конфига по папкам
     */
    const PROJECT = 0;
    const ENV = 1;
    const TYPE = 2;
    const NAME = 3;

    /**
     * @var string
     */
    protected $confPath;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * AppConfigManager constructor.
     * @param string $confPath
     * @param Filesystem $fs
     */
    public function __construct($confPath, Filesystem $fs) {
        $this->confPath = $confPath;
        $this->fs = $fs;
    }

    /**
     * @return array Дерево файлов - project->env->files
     */
    public function getConfigTree() {
        $finder = new Finder();
        $finder->files()->in($this->confPath)->sortByName();

        $filesTree = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $meta = $file->getRealPath();
            $meta = str_replace(
                $this->confPath,
                '',
                $meta
            );
            $meta = trim($meta, '/');
            $meta = explode('/', $meta);

            $filesTree[$meta[self::PROJECT]][$meta[self::ENV]][$meta[self::TYPE]][] = $meta[self::NAME];
        }

        return $filesTree;
    }

    public function findAllProjects() {
        return $this->ls($this->confPath, 'default');
    }

    public function findEnvironmentsByProject($project) {
        return $this->ls($this->confPath . DIRECTORY_SEPARATOR . $project);
    }

    /**
     * @return array Список дефолтных конфигов
     */
    public function getDefaultConfigs() {
        $config = new AppConfig();
        $config
            ->setProject('default')
            ->setEnv('default')
        ;

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->getConfigPath($config))
            ->sortByName()
        ;

        $files = [];
        foreach ($finder as $fullFileName) {
            $type = pathinfo(
                pathinfo(
                    $fullFileName,
                    PATHINFO_DIRNAME
                ),
                PATHINFO_BASENAME
            );
            $name = pathinfo($fullFileName, PATHINFO_BASENAME);

            $files[$type][] = $name;
        }

        return $files;
    }

}