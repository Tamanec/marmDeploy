<?php

namespace AppBundle\Controller;


use AppBundle\Entity\AppDataBuildConf;
use AppBundle\Entity\LogDataBuildConf;
use AppBundle\Form\AppDataBuildConfType;
use AppBundle\Form\LogDataBuildConfType;
use AppBundle\Service\BuilderManager;
use Docker\Docker;
use GitElephant\GitBinary;
use GitElephant\Repository;
use Http\Client\Plugin\Exception\ClientErrorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

class BuilderController extends Controller {

    /**
     * @Route(
     *     "/image/build/{type}",
     *     name="image_build_app_data",
     *     requirements={"type"="box|integrator"}
     * )
     * @param Request $request
     * @param $type
     * @return Response
     */
    public function buildAppDataImageAction(Request $request, $type) {
        $buildConf = new AppDataBuildConf();
        $buildConf->setType($type);

        $form = $this->createForm(
            AppDataBuildConfType::class,
            $buildConf
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $buildInfo = $this->get('app.builder')->buildAppData($buildConf);

            return $this->render(':builder:build.html.twig', [
                'buildInfo' => $buildInfo
            ]);
        }

        return $this->render(
            'builder/app_data.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/image/build/log", name="image_build_log_data")
     * @param Request $request
     * @return Response
     */
    public function buildLogDataImageAction(Request $request) {
        $buildConf = new LogDataBuildConf();
        $form = $this->createForm(
            LogDataBuildConfType::class,
            $buildConf
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $buildInfo = $this->get('app.builder')->buildLogData($buildConf);

            return $this->render(':builder:build.html.twig', [
                'buildInfo' => $buildInfo
            ]);
        }

        return $this->render(':builder:log_data.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/image/list", name="image_list")
     * @return Response
     */
    public function imagesListAction() {
        return $this->render(':builder:list.html.twig', [
            'repoList' => $this->get('app.builder')->getImagesList(),
            'registryUrl' => $this->get('app.builder')->getRegistryUrl()
        ]);
    }

    /**
     * @Route("/image/push/{name}", name="image_push")
     * @param $name
     * @return Response
     */
    public function pushImageAction($name) {
        $pushInfo = $this->get('app.builder')->pushImage($name);

        return $this->render(':builder:push.html.twig', [
            'pushInfo' => $pushInfo
        ]);
    }

    /**
     * @Route("/image/delete/{name}", name="image_delete")
     * @param $name
     * @return Response
     */
    public function deleteImageAction($name) {
        $error = null;
        try {
            $this->get('app.builder')->deleteImage($name);
        } catch (ClientErrorException $e) {
            $error = $e->getMessage();
        }

        return $this->render(':builder:delete.html.twig', [
            'name' => $name,
            'error' => $error
        ]);
    }

    /**
     * @Route("/git/checkout/{branch}", name="builder_git", defaults={"branch" = "master"})
     * @param $branch
     * @return Response
     */
    public function gitAction($branch) {
        $repoPath = $this->getParameter('kernel.root_dir') . '/../var/repo';
        $integrator = $repoPath . '/integrator';

        $fs = new Filesystem();
        if (!$fs->exists($integrator)) {
            $git = new Repository(realpath($repoPath), new GitBinary('/usr/bin/git'));
            $git->cloneFrom(BuilderManager::REPO_INTEGRATOR);
            $fs->chmod($integrator, 0775, 0, true);

            $output = $git->getCaller()->getOutput();
            dump($output);
        }

        $git = new Repository(realpath($integrator), new GitBinary('/usr/bin/git'));
        $git->checkout($branch);


        //$this->get('cypress_git_elephant.repository_collection')

        //$branches = $box->getBranches(true, true);
        //dump($branches);

        // Получаем нужный код
        //$box->checkout($branch);

        // Подставляем конфиги

        // Запускаем сборку докер-образа

        // Пушим в репозиторий

        //$status = $box->getStatus();
        //$rawStatus = $box->getStatusOutput();
        //dump($rawStatus);
        //dump($status->all());

        return new Response('hi');
    }

    /**
     * @Route("/docker/image/find/{name}", name="builder_docker")
     * @param $name
     * @return Response
     */
    public function dockerAction($name) {
        $docker = new Docker();

        /*$containers = $docker->getContainerManager()->findAll();
        dump($containers);*/

        try {
            $res = $docker->getImageManager()->find($name);
            dump($res);
        } catch (ClientErrorException $e) {
            return new Response($e->getMessage() . ' // ' . $e->getCode() . ' // ' . $e->getResponse()->getBody());
        }


        $images = $docker->getImageManager()->findAll([
            'filter' => $name
        ]);
        if (empty($images)) {
            return new Response(sprintf('Image %s not found', $name), Response::HTTP_NOT_FOUND);
        }
        dump($images);

        return new Response("hi");
    }

    /**
     * @Route(
     *     "/docker/tag/{name}/{image}/{repo}",
     *     name="builder_docker_tag",
     *     defaults={
     *          "name"="latest",
     *          "image"="marm-server-box",
     *          "repo"="172.29.134.38:5000/marm-server-box-test"
     *     },
     *     requirements={"repo"=".+"}
     * )
     * @param $image
     * @param $repo
     * @param $name
     * @return Response
     */
    public function dockerTagAction($image, $repo, $name) {
        $docker = new Docker();
        $res = $docker->getImageManager()->tag($image, [
            'repo' => $repo,
            'force' => false,
            'tag' => $name,
        ]);
        dump($res);

        return new Response("hi");
    }

}