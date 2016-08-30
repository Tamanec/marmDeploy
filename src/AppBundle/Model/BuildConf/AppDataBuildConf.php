<?php

namespace AppBundle\Model\BuildConf;

class AppDataBuildConf extends ProjectRelatedBuildConf {

    /**
     * @var string Тип образа (box, integrator)
     */
    protected $type;

    /**
     * @var string Название ветки гита содержащую нужную версию кода
     */
    protected $branch;

    /**
     * @var string Название окружения
     */
    protected $env;

    /**
     * @var string Название конфига для http-запросов
     */
    protected $mainConfig;

    /**
     * @var string Название конфига для консольных команд
     */
    protected $consoleConfig;

    protected function getImagePrefix() {
        return 'marm-data-';
    }

    public function getRelativeBuildContextPath() {
        return '/data/www';
    }

    public function getBuildArgs() {
        return json_encode(['SOURCE_NAME' => $this->getType()]);
    }

    /**
     * @return string Название образа включая тэг
     */
    public function getFullName() {
        return $this->getImagePrefix()
        . $this->getType()
        . '-' . $this->getProject()
        . ':' . $this->getVersion();
    }

    /**
     * @return string Название образа без тэга
     */
    public function getName() {
        return $this->getImagePrefix() . $this->getType() . '-' . $this->getProject();
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getBranch() {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch) {
        $this->branch = $branch;
    }

    /**
     * @return string
     */
    public function getEnv() {
        return $this->env;
    }

    /**
     * @param string $env
     */
    public function setEnv($env) {
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getMainConfig() {
        return $this->mainConfig;
    }

    /**
     * @param string $mainConfig
     */
    public function setMainConfig($mainConfig) {
        $this->mainConfig = $mainConfig;
    }

    /**
     * @return string
     */
    public function getConsoleConfig() {
        return $this->consoleConfig;
    }

    /**
     * @param string $consoleConfig
     */
    public function setConsoleConfig($consoleConfig) {
        $this->consoleConfig = $consoleConfig;
    }

}