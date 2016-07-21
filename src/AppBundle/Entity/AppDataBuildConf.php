<?php

namespace AppBundle\Entity;


class AppDataBuildConf {

    /**
     * @var string Тип образа (box, integrator)
     */
    protected $type;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string Название ветки гита содержащую нужную версию кода
     */
    protected $branch;

    /**
     * @var string Название проекта
     */
    protected $project;

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

    /**
     * @return string Название образа включая тэг
     */
    public function getFullName() {
        return 'marm-data-' . $this->getType() . ':' . $this->getVersion();
    }

    /**
     * @return string Название образа без тэга
     */
    public function getName() {
        return 'marm-data-' . $this->getType();
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
    public function getVersion() {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version) {
        $this->version = $version;
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
    public function getProject() {
        return $this->project;
    }

    /**
     * @param string $project
     */
    public function setProject($project) {
        $this->project = $project;
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