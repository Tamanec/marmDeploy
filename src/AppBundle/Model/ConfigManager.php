<?php

namespace AppBundle\Model;

use AppBundle\Entity\AppConfig;
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
     * @param Config $config
     * @return string
     */
    public function getConfigContent(Config $config) {
        $file = (new File($this->getConfigPath($config)))->openFile();
        return $file->fread($file->getSize());
    }

    /**
     * @param Config $config
     */
    public function saveConfig(Config $config) {
        $path = $this->getConfigPath($config);

        $dir = dirname($path);
        if (!$this->fs->exists($dir)) {
            $this->fs->mkdir($dir);
            $this->fs->chmod($dir, 0775);
        }

        $this->fs->dumpFile($path, $config->getContent());
    }

    /**
     * @param Config $config
     * @return array
     */
    public function findSiblingConfigs(Config $config) {
        return $this->ls($this->getConfigPath($config));
    }

    /**
     * @return array Список дефолтных конфигов
     */
    abstract public function getDefaultConfigs();

    /**
     * @param Config $config
     * @return string Путь до конфига
     * @throws \InvalidArgumentException
     */
    public function getConfigPath(Config $config) {
        return $this->confPath . $config->getRelativePath();
    }

    /**
     * Выполняет поиск файлов и папок заданной директории
     *
     * @param string $path Путь до папки
     * @param array|string $exclude Названия элементов которые должны быть исключены из поиска
     * @return array Элементы директории
     */
    protected function ls($path, $exclude = []) {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException('Некорретный путь до файла: ' . $path);
        }

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