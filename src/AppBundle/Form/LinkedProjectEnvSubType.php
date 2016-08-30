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
 * Сабформа со связанными полями project и env
 *
 * @package AppBundle\Form
 */
class LinkedProjectEnvSubType extends AbstractType {

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
        $environments = $this->configManager->findEnvironmentsByProject($projects[0]);

        $builder
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'inherit_data' => true
        ]);
    }

}