<?php

namespace AppBundle\Form;


use AppBundle\Service\AppConfigManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $configs = $configManager->findConfigs($projects[0], $environments[0]);

        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'box' => 'box',
                    'integrator' => 'integrator'
                ]
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

        // Связываем поля env и project
        $builder
            ->get('project')
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($configManager) {
                    $form = $event->getForm()->getParent();
                    $project = $event->getData();
                    $environments = $configManager->findEnvironmentsByProject($project);

                    $form->add('env', ChoiceType::class, [
                        'choices' => array_combine(
                            $environments,
                            $environments
                        )
                    ]);
                }
            )
        ;

        // Связываем поле env с полями mainConfig и consoleConfig
        $builder
            ->get('env')
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($configManager) {
                    $form = $event->getForm()->getParent();
                    $project = $form->get('project')->getData();
                    $env = $event->getData();
                    $configs = $configManager->findConfigs($project, $env);

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
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\AppDataBuildConf',
        ]);
    }

}