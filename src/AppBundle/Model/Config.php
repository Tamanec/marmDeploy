<?php

namespace AppBundle\Model;


class Config {

    /**
     * @var string Название конфигв
     */
    protected $name;

    /**
     * @var string Содержимое конфига
     */
    protected $content;

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

}