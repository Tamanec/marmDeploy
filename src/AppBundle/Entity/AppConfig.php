<?php

namespace AppBundle\Entity;

use AppBundle\Model\Config;

class AppConfig extends Config {

    protected $project;
    protected $env;

    /**
     * @return mixed
     */
    public function getProject() {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project) {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getEnv() {
        return $this->env;
    }

    /**
     * @param mixed $env
     */
    public function setEnv($env) {
        $this->env = $env;
    }

}