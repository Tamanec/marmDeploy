<?php

namespace AppBundle\Test;

class MyTest {

    protected $arg;

    /**
     * MyTest constructor.
     * @param $arg
     */
    public function __construct($arg) {
        $this->arg = $arg;
    }

    /**
     * @return mixed
     */
    public function getArg() {
        return $this->arg;
    }

    /**
     * @param mixed $arg
     */
    public function setArg($arg) {
        $this->arg = $arg;
    }



}