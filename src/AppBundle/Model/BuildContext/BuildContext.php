<?php

namespace AppBundle\Model\BuildContext;


use AppBundle\Model\BuildConf\BuildConf;

abstract class BuildContext {

    /**
     * @var string Путь до папки содержащей контексты сборок
     */
    protected $basePath;

    /**
     * @var BuildConf
     */
    protected $buildConf;

    /**
     * BuildContext constructor.
     * @param string $basePath
     * @param BuildConf $buildConf
     */
    public function __construct($basePath, $buildConf) {
        $this->basePath = $basePath;
        $this->buildConf = $buildConf;
    }

    /**
     * Подготавливает контекст сборки:
     *  конфиги
     *  код
     *  и т.д.
     */
    abstract public function prepare();

    /**
     * @return string Путь до папки контекста сборок относительно папки содержащей контексты
     */
    abstract protected function getRelativePath();

    /**
     * @return string Путь до папки контекста сборки
     */
    public function getPath() {
        return $this->basePath . $this->getRelativePath();
    }

    /**
     * @return string
     */
    public function getBasePath() {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     * @return BuildContext
     */
    public function setBasePath($basePath) {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * @return BuildConf
     */
    public function getBuildConf() {
        return $this->buildConf;
    }

    /**
     * @param BuildConf $buildConf
     * @return BuildContext
     */
    public function setBuildConf($buildConf) {
        $this->buildConf = $buildConf;
        return $this;
    }

}