<?php

namespace AppBundle\Entity;


class LogDataBuildConf {

    /**
     * @var string
     */
    protected $project;

    /**
     * @return string Название образа
     */
    public function getFullName() {
        return 'marm-data-log-' . $this->getProject();
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

}