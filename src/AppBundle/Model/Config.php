<?php

namespace AppBundle\Model;


use Symfony\Component\HttpFoundation\File\File;

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
     * @return string Относительный путь до конфига
     */
    public function getRelativePath() {
        return DIRECTORY_SEPARATOR . $this->name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent() {
        if (empty($this->content)) {
            $fullName = $this->getPath()
                . DIRECTORY_SEPARATOR
                . $this->name;
            $file = (new File($fullName))->openFile();
            $this->content = $file->fread($file->getSize());
        }
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

}