<?php

namespace AppBundle\Service;


use GitElephant\Repository;
use Symfony\Component\Filesystem\Filesystem;

class ProjectRepository {

    const BOX = 'ssh://git@party.altarix.ru:2222/box.git';
    const INTEGRATOR = 'ssh://git@party.altarix.ru:2222/integrator.git';

    /**
     * @var string Директория для скачивания проектов
     */
    protected $basePath;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @param string $basePath
     * @param Filesystem $fs
     */
    public function __construct($basePath, Filesystem $fs) {
        $this->basePath = $basePath;
        $this->fs = $fs;
    }

    /**
     * @param string $repoUrl self::BOX || self::INTEGRATOR
     */
    public function cloneProject($repoUrl) {
        $git = new Repository($this->basePath);
        $git->cloneFrom($repoUrl);
        $this->fs->chmod($this->basePath, 0775, 0, true);
    }

    /**
     * @param string $projectPath
     * @param string $branch
     */
    public function checkout($projectPath, $branch) {
        $git = new Repository($projectPath);
        $git->fetch();
        $git->checkout($branch);
    }

    /**
     * @return string
     */
    public function getBasePath() {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     * @return ProjectRepository
     */
    public function setBasePath($basePath) {
        $this->basePath = $basePath;
        return $this;
    }

}