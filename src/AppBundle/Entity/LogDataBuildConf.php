<?php

namespace AppBundle\Entity;

use AppBundle\Model\ProjectRelatedBuildConf;

class LogDataBuildConf extends ProjectRelatedBuildConf {

    public function getRelativeBuildContextPath() {
        return '/data/log';
    }

    protected function getImagePrefix() {
        return 'marm-data-log-';
    }

}