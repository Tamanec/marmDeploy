<?php

namespace AppBundle\Model\BuildConf;


class CronBuildConf extends ProjectRelatedBuildConf {

    /**
     * @var string Название окружения
     */
    protected $env;

    /**
     * @var string Название конфига
     */
    protected $config;

    public function getRelativeBuildContextPath() {
        return '/cron';
    }

    protected function getImagePrefix() {
        return 'marm-cron-';
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
     * @return CronBuildConf
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
     * @return CronBuildConf
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

}