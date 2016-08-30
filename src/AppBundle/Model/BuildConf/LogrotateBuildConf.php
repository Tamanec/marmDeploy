<?php

namespace AppBundle\Model\BuildConf;


class LogrotateBuildConf extends ProjectRelatedBuildConf {

    /**
     * @var string Название окружения
     */
    protected $env;

    /**
     * @var string Название конфига
     */
    protected $config;

    public function getRelativeBuildContextPath() {
        return '/logrotate';
    }

    protected function getImagePrefix() {
        return 'marm-logrotate-';
    }

    public function getBuildArgs() {
        return json_encode(['PROJECT_NAME' => $this->getProject()]);
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param string $env
     * @return LogrotateBuildConf
     */
    public function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $config
     * @return LogrotateBuildConf
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

}