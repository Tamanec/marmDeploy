<?php

namespace AppBundle\Model\BuildContext;

class LogDataBuildContext extends BuildContext {

    public function prepare() {
        return;
    }

    protected function getRelativePath() {
        return '/data/log';
    }

}