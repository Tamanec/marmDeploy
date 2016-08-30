<?php

namespace AppBundle\Model\BuildConf;

class LogDataBuildConf extends ProjectRelatedBuildConf {

    public function getRelativeBuildContextPath() {
        return '/data/log';
    }

    protected function getImagePrefix() {
        return 'marm-data-log-';
    }

    public function getBuildArgs() {
        return json_encode(['PROJECT_NAME' => $this->getProject()]);
    }

}