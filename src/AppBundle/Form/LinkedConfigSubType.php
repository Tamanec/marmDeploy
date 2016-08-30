<?php

namespace AppBundle\Form;


use AppBundle\Model\AppConf\AppConfig;
use AppBundle\Service\AppConfigManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 *
 * @package AppBundle\Form
 */
class LinkedConfigSubType extends AbstractType {

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
        $projects = $this->configManager->findAllProjects();
        $defaultProject = $projects[0];

        $environments = $this->configManager->findEnvironmentsByProject($defaultProject);
        $defaultEnv = $environments[0];

        $type = $options['type'];

        $config = new AppConfig();
        $config
            ->setProject($defaultProject)
            ->setEnv($defaultEnv)
            ->setType($type)
        ;
        $configs = $this->configManager->findSiblingConfigs($config);

        $builder
            ->add('config', ChoiceType::class, [
                'choices' => array_combine(
                    $configs,
                    $configs
                ),
                'preferred_choices' => function ($val, $key) use ($defaultProject) {
                    return stripos($val, $defaultProject) !== false;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefined(['type']);
        $resolver->setAllowedValues('type', ['cron', 'logrotate']);
        $resolver->setDefaults([
            'inherit_data' => true
        ]);
    }

}