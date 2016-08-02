<?php

namespace AppBundle\Form;


use AppBundle\Entity\AppConfig;
use AppBundle\Service\AppConfigManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppDataBuildConfType extends AbstractType {

    /**
     * @var AppConfigManager
     */
    protected $configManager;

    /**
     * AppDataBuildConfType constructor.
     * @param AppConfigManager $configManager
     */
    public function __construct(AppConfigManager $configManager) {
        $this->configManager = $configManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $configManager = $this->configManager;
        $projects = $configManager->findAllProjects();
        $environments = $configManager->findEnvironmentsByProject($projects[0]);
        $types = [
            'box',
            'integrator'
        ];

        $config = new AppConfig();
        $config
            ->setProject($projects[0])
            ->setEnv($environments[0])
            ->setType($types[0])
        ;
        $configs = $configManager->findSiblingConfigs($config);

        $builder
            ->add('type', ChoiceType::class, [
                'choices' => array_combine(
                    $types,
                    $types
                )
            ])
            ->add('version', TextType::class, [
                'attr' => [
                    'placeholder' => 'Тэг к докер-образу'
                ]
            ])
            ->add('branch', TextType::class, [
                'attr' => [
                    'placeholder' => 'Гит-ветка проекта'
                ]
            ])
            ->add('project', ChoiceType::class, [
                'choices' => array_combine(
                    $projects,
                    $projects
                )
            ])
            ->add('env', ChoiceType::class, [
                'choices' => array_combine(
                    $environments,
                    $environments
                )
            ])
            ->add('mainConfig', ChoiceType::class, [
                'choices' => array_combine(
                    $configs,
                    $configs
                ),
                'preferred_choices' => function ($val, $key) {
                    return stripos($val, 'main') !== false;
                }
            ])
            ->add('consoleConfig', ChoiceType::class, [
                'choices' => array_combine(
                    $configs,
                    $configs
                ),
                'preferred_choices' => function ($val, $key) {
                    return stripos($val, 'console') !== false;
                }
            ])
        ;

        $builder
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                $this->getPreSubmitListener()
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\AppDataBuildConf',
        ]);
    }

    /**
     * @return \Closure
     */
    protected function getPreSubmitListener() {
        return function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // Обновляем список окружений для проекта
            if (!isset($data['env']) && !isset($data['type'])) {
                $this->updateEnv($form, $this->configManager, $data['project']);
                return;
            }

            // Обновляем список конфигов по проекту, типу и окружению
            $config = new AppConfig();
            $config
                ->setProject($data['project'])
                ->setEnv($data['env'])
                ->setType($data['type'])
            ;

            $this->updateAppConfigs($form, $this->configManager, $config);
        };
    }

    /**
     * @param FormInterface $form
     * @param AppConfigManager $configManager
     * @param string $project
     */
    protected function updateEnv(FormInterface $form, AppConfigManager $configManager, $project) {
        $environments = $configManager->findEnvironmentsByProject($project);
        $form->add('env', ChoiceType::class, [
            'choices' => array_combine(
                $environments,
                $environments
            )
        ]);
    }

    /**
     * @param FormInterface $form
     * @param AppConfigManager $configManager
     * @param AppConfig $config
     */
    protected function updateAppConfigs(FormInterface $form, AppConfigManager $configManager, AppConfig $config) {
        try {
            $configs = $configManager->findSiblingConfigs($config);
        } catch (\InvalidArgumentException $e) {
            $configs = [];
        }

        $form
            ->add('mainConfig', ChoiceType::class, [
                'choices' => array_combine(
                    $configs,
                    $configs
                ),
                'preferred_choices' => function ($val, $key) {
                    return stripos($val, 'main') !== false;
                }
            ])
            ->add('consoleConfig', ChoiceType::class, [
                'choices' => array_combine(
                    $configs,
                    $configs
                ),
                'preferred_choices' => function ($val, $key) {
                    return stripos($val, 'console') !== false;
                }
            ])
        ;
    }

}