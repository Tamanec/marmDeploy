<?php

namespace AppBundle\Controller;

use AppBundle\Form\AppDataBuildConfType;
use AppBundle\Form\CronBuildConfType;
use AppBundle\Form\LogDataBuildConfType;
use AppBundle\Form\LogrotateBuildConfType;
use AppBundle\Model\BuildConf\AppDataBuildConf;
use AppBundle\Model\BuildConf\CronBuildConf;
use AppBundle\Model\BuildConf\LogDataBuildConf;
use AppBundle\Model\BuildConf\LogrotateBuildConf;
use AppBundle\Model\BuildContext\AppDataBuildContext;
use AppBundle\Model\BuildContext\CronBuildContext;
use AppBundle\Model\BuildContext\LogDataBuildContext;
use AppBundle\Model\BuildContext\LogrotateBuildContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BuildController extends Controller {

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
    public function buildAppDataAction(Request $request, $type) {
        $buildConf = new AppDataBuildConf();
        $buildConf->setType($type);

        $form = $this->createForm(
            AppDataBuildConfType::class,
            $buildConf
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $buildContext = new AppDataBuildContext(
                $this->container->getParameter('build.context.path'),
                $buildConf,
                $this->get('app.project.repository'),
                $this->get('filesystem'),
                $this->get('app.config.manager')
            );
            $buildInfo = $this->get('app.builder')->buildImage($buildConf, $buildContext);

            return $this->render(':builder:build_info.html.twig', [
                'buildInfo' => $buildInfo
            ]);
        }

        return $this->render(':builder:app_data.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/image/build/log", name="image_build_log_data")
     * @param Request $request
     * @return Response
     */
    public function buildLogDataAction(Request $request) {
        $buildConf = new LogDataBuildConf();

        $form = $this->createForm(
            LogDataBuildConfType::class,
            $buildConf
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $buildContext = new LogDataBuildContext(
                $this->container->getParameter('build.context.path'),
                $buildConf
            );
            $buildInfo = $this->get('app.builder')->buildImage($buildConf, $buildContext);

            return $this->render(':builder:build_info.html.twig', [
                'buildInfo' => $buildInfo
            ]);
        }

        return $this->render(':builder:log_data.html.twig', [
            'form' => $form->createView(),
            'title' => 'Подготовка сборки data-контейнера для логов приложения'
        ]);
    }

    /**
     * @Route("/image/build/cron", name="image_build_cron")
     * @param Request $request
     * @return Response
     */
    public function buildCronAction(Request $request) {
        $buildConf = new CronBuildConf();

        $form = $this->createForm(
            CronBuildConfType::class,
            $buildConf
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $buildContext = new CronBuildContext(
                $this->container->getParameter('build.context.path'),
                $buildConf,
                $this->get('filesystem'),
                $this->get('app.config.manager')
            );
            $buildInfo = $this->get('app.builder')->buildImage($buildConf, $buildContext);

            return $this->render(':builder:build_info.html.twig', [
                'buildInfo' => $buildInfo
            ]);
        }

        return $this->render(':builder:cron.html.twig', [
            'form' => $form->createView(),
            'title' => 'Подготовка сборки cron-контейнера'
        ]);
    }

    /**
     * @Route("/image/build/logrotate", name="image_build_logrotate")
     * @param Request $request
     * @return Response
     */
    public function buildLogrotateAction(Request $request) {
        $buildConf = new LogrotateBuildConf();

        $form = $this->createForm(
            LogrotateBuildConfType::class,
            $buildConf
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $buildContext = new LogrotateBuildContext(
                $this->container->getParameter('build.context.path'),
                $buildConf,
                $this->get('filesystem'),
                $this->get('app.config.manager')
            );
            $buildInfo = $this->get('app.builder')->buildImage($buildConf, $buildContext);

            return $this->render(':builder:build_info.html.twig', [
                'buildInfo' => $buildInfo
            ]);
        }

        return $this->render(':builder:logrotate.html.twig', [
            'form' => $form->createView(),
            'title' => 'Подготовка сборки logrotate-контейнера'
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
     * @param string $name
     * @return Response
     */
    public function deleteImageAction($name) {
        $error = null;
        try {
            $builder = $this->get('app.builder');
            $builder->deleteImage($name);

            $pushedImage = $builder->getRegistryUrl() . '/' . $name;
            if ($builder->isExists($pushedImage)) {
                $builder->deleteImage($pushedImage);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->render(':builder:delete.html.twig', [
            'name' => $name,
            'error' => $error
        ]);
    }

}