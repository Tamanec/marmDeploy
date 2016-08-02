<?php

namespace AppBundle\Entity;

use AppBundle\Model\Config;

class AppConfig extends Config {

    protected $project;
    protected $env;
    protected $type;

    /**
     * @return string Относительный путь до конфига
     */
    public function getRelativePath() {
        $dirs = array_filter([
            $this->project,
            $this->env,
            $this->type,
            $this->name
        ]);
        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $dirs);
    }

    /**
     * @return mixed
     */
    public function getProject() {
        return $this->project;
    }

    /**
     * @param mixed $project
     * @return $this
     */
    public function setProject($project) {
        $this->project = $project;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnv() {
        return $this->env;
    }

    /**
     * @param mixed $env
     * @return $this
     */
    public function setEnv($env) {
        $this->env = $env;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

}