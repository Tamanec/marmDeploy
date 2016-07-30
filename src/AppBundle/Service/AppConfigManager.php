<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppConfig;
use AppBundle\Model\Config;
use AppBundle\Model\ConfigManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;

class AppConfigManager extends ConfigManager {

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
            . $name;
        $file = (new File($fullName))->openFile();
        return $file->fread($file->getSize());
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
            $filesTree[$meta[0]][$meta[1]][] = $meta[2];
        }

        return $filesTree;
    }

    public function findAllProjects() {
        return $this->ls($this->confPath, 'default');
    }

    public function findEnvironmentsByProject($project) {
        return $this->ls($this->confPath . DIRECTORY_SEPARATOR . $project);
    }

    public function findConfigs($project, $env) {
        return $this->ls(
            $this->confPath
            . DIRECTORY_SEPARATOR
            . $project
            . DIRECTORY_SEPARATOR
            . $env
        );
    }

    /**
     * @return array Список дефолтных конфигов
     */
    public function getDefaultConfigs() {
        $finder = new Finder();
        $finder
            ->files()
            ->in($this->getConfigPath('default', 'default'))
            ->sortByName()
        ;

        $files = [];
        foreach ($finder as $fullFileName) {
            $files[] = pathinfo($fullFileName, PATHINFO_BASENAME);
        }

        return $files;
    }

    /**
     * @param Config $config
     * @return string
     */
    public function getConfigPath(Config $config) {
        $dirs = array_filter([
            $this->confPath,
            $config->getProject(),
            $config->getEnv(),
            $config->getName()
        ]);

        return implode(DIRECTORY_SEPARATOR, $dirs);
    }

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