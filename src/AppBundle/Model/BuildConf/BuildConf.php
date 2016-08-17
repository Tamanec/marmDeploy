<?php


namespace AppBundle\Model\BuildConf;

abstract class BuildConf {

    /**
     * @return string Название образа без тэга
     */
    abstract public function getName();

    /**
     * @return string Название образа включая тэг
     */
    abstract public function getFullName();

    /**
     * @return string Путь до папки контекста сборок относительно папки содержащей контексты
     */
    abstract public function getRelativeBuildContextPath();

    /**
     * @return string Префикс названия образа
     */
    abstract protected function getImagePrefix();

}