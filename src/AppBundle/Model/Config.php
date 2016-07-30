<?php

namespace AppBundle\Model;


use Symfony\Component\HttpFoundation\File\File;

abstract class Config {

    /**
     * @var string Название конфигв
     */
    protected $name;

    /**
     * @var string Содержимое конфига
     */
    protected $content;

    /**
     * @var string Путь до папки с конфигами
     */
    protected $rootPath;

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
     */
    public function setContent($content) {
        $this->content = $content;
    }

}