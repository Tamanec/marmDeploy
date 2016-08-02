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
     * @Route("/config/create/{type}/{name}", name="config_create")
     * @param Request $request
     * @param $type
     * @param $name
     * @return Response
     */
    public function createAction(Request $request, $type, $name) {
        $confManager = $this->get('app.config.manager');

        // Дефолтные значения
        $defaultConfig = new AppConfig();
        $defaultConfig
            ->setProject('default')
            ->setEnv('default')
            ->setType($type)
            ->setName($name)
        ;

        $newConfig = new AppConfig();
        $newConfig
            ->setType($type)
            ->setName($name)
            ->setContent(
                $confManager->getConfigContent($defaultConfig)
            )
        ;

        // Готовим форму
        $form = $this->createForm(AppConfigType::class, $newConfig);
        $form->handleRequest($request);

        // Сохранение конфига
        if ($form->isSubmitted() && $form->isValid()) {
            $confManager->saveConfig($newConfig);
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
     * @Route("/config/{project}/{env}/{type}/{name}", name="config_edit")
     * @param Request $request
     * @param $project
     * @param $env
     * @param $type
     * @param $name
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request, $project, $env, $type, $name) {
        $confManager = $this->get('app.config.manager');

        // Дефолтные значения
        $config = new AppConfig();
        $config
            ->setProject($project)
            ->setEnv($env)
            ->setType($type)
            ->setName($name)
            ->setContent(
                $confManager->getConfigContent($config)
            )
        ;

        // Готовим форму
        $form = $this->createForm(AppConfigType::class, $config);
        $form->handleRequest($request);

        // Сохранение конфига
        if ($form->isSubmitted() && $form->isValid()) {
            $confManager->saveConfig($config);
            return $this->redirectToRoute('config_edit', [
                'project' => $project,
                'env' => $env,
                'type' => $type,
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
        $filesTree = $confManager->getConfigTree();
        $defaultConfigs = $confManager->getDefaultConfigs();

        return $this->render(':appConfig:list.html.twig', [
            'filesTree' => $filesTree,
            'defaultConfigs' => $defaultConfigs
        ]);
    }

}