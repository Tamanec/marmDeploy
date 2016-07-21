<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppConfig;
use AppBundle\Form\AppConfigType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppConfigController extends Controller {

    /**
     * @Route("/config/create/{name}", name="config_create")
     * @param Request $request
     * @param $name
     * @return Response
     */
    public function createAction(Request $request, $name) {
        $confManager = $this->get('app.config.manager');

        // Дефолтные значения
        $config = new AppConfig();
        $config->setContent(
            $confManager->getConfigContent('default', 'default', $name)
        );
        $config->setName($name);

        // Готовим форму
        $form = $this->createForm(AppConfigType::class, $config);
        $form->handleRequest($request);

        // Сохранение конфига
        if ($form->isSubmitted() && $form->isValid()) {
            $confManager->saveConfig($config);
            return $this->redirectToRoute('config_list');
        }

        return $this->render(
            'appConfig/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/config/{project}/{env}/{name}", name="config_edit")
     * @param Request $request
     * @param $project
     * @param $env
     * @param $name
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request, $project, $env, $name) {
        $confManager = $this->get('app.config.manager');

        // Дефолтные значения
        $config = new AppConfig();
        $config->setProject($project);
        $config->setEnv($env);
        $config->setContent(
            $confManager->getConfigContent($project, $env, $name)
        );
        $config->setName($name);

        // Готовим форму
        $form = $this->createForm(AppConfigType::class, $config);
        $form->handleRequest($request);

        // Сохранение конфига
        if ($form->isSubmitted() && $form->isValid()) {
            $confManager->saveConfig($config);
            return $this->redirectToRoute('config_edit', [
                'project' => $project,
                'env' => $env,
                'name' => $name
            ]);
        }

        return $this->render(
            'appConfig/edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/config/list", name="config_list")
     * @return Response
     */
    public function listAction() {
        $confManager = $this->get('app.config.manager');
        $filesTree = $confManager->findAllConfig();
        $defaultConfigs = $confManager->getDefaultConfigs();

        return $this->render(':appConfig:list.html.twig', [
            'filesTree' => $filesTree,
            'defaultConfigs' => $defaultConfigs
        ]);
    }

    /**
     * @Route("/appConfig/success", name="appConfig_success")
     * @return Response
     */
    public function successAction() {
        return new Response("success!");
    }

    /**
     * @Route("/appConfig/test", name="appConfig_test")
     * @return Response
     */
    public function testAction() {
        $this->get('logger')->addDebug('TEST ACTION');
        return new Response("zxc");
    }

}