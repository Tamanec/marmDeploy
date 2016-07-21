<?php

namespace AppBundle\Model;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;

abstract class ConfigManager {

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
     * @param $project
     * @param $env
     * @param string $name
     * @return string
     */
    public function getConfigContent($project, $env, $name) {
        $fullName = $this->getConfigPath($project, $env)
            . DIRECTORY_SEPARATOR
            . $name
        ;
        $file = (new File($fullName))->openFile();
        return $file->fread($file->getSize());
    }

    /**
     * @param Config $config
     */
    public function saveConfig(Config $config) {
        $path = $this->getConfigPath($config);

        if (!$this->fs->exists($path)) {
            $this->fs->mkdir($path);
            $this->fs->chmod($path, 0775);
        }

        $fullName = $path
            . DIRECTORY_SEPARATOR
            . $config->getName()
        ;

        $this->fs->dumpFile($fullName, $config->getContent());
    }

    /**
     * @param Config $config
     * @return array
     */
    public function findConfigs(Config $config) {
        return $this->ls($this->getConfigPath($config));
    }

    /**
     * @return array Список дефолтных конфигов
     */
    abstract public function getDefaultConfigs();

    /**
     * @param Config $config
     * @return string Путь до конфига без имени файла
     */
    abstract public function getConfigPath(Config $config);

    /**
     * Выполняет поиск файлов и папок заданной директории
     *
     * @param string $path Путь до папки
     * @param array|string $exclude Названия элементов которые должны быть исключены из поиска
     * @return array Элементы директории
     */
    protected function ls($path, $exclude = []) {
        $finder = new Finder();
        $finder
            ->in($path)
            ->sortByName()
            ->depth(0)
            ->exclude($exclude)
        ;

        $list = array_map(function(SplFileInfo $item) {
            return basename($item->getRealPath());
        }, iterator_to_array($finder));

        return array_values($list);
    }

}