<?php

namespace AppBundle\Entity;


use AppBundle\Model\ProjectRelatedBuildConf;

class CronBuildConf extends ProjectRelatedBuildConf {

    public function getRelativeBuildContextPath() {
        return '/cron';
    }

    protected function getImagePrefix() {
        return 'marm-cron-';
    }

}