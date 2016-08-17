<?php

namespace AppBundle\Model\BuildConf;


abstract class ProjectRelatedBuildConf extends BuildConf {

    /**
     * @var string
     */
    protected $project;

    /**
     * @var string
     */
    protected $version;

    /**
     * @return string
     */
    public function getName() {
        return $this->getImagePrefix() . $this->getProject();
    }

    /**
     * @return string
     */
    public function getFullName() {
        return $this->getImagePrefix() . $this->getProject() . ':' . $this->getVersion();
    }

    /**
     * @return string
     */
    public function getProject() {
        return $this->project;
    }

    /**
     * @param string $project
     * @return $this
     */
    public function setProject($project) {
        $this->project = $project;
        return $this;
    }

    /**
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

}