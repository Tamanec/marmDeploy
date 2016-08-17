<?php

namespace AppBundle\Model\BuildConf;

class LogDataBuildConf extends ProjectRelatedBuildConf {

    public function getRelativeBuildContextPath() {
        return '/data/log';
    }

    protected function getImagePrefix() {
        return 'marm-data-log-';
    }

}