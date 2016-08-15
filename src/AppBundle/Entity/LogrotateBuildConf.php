<?php

namespace AppBundle\Entity;


use AppBundle\Model\ProjectRelatedBuildConf;

class LogrotateBuildConf extends ProjectRelatedBuildConf {

    public function getRelativeBuildContextPath() {
        return '/logrotate';
    }

    protected function getImagePrefix() {
        return 'marm-logrotate-';
    }

}