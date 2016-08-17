<?php

namespace AppBundle\Form;

use AppBundle\Service\AppConfigManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProjectRelatedBuildConfType extends AbstractType {

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

        $builder
            ->add('project', ChoiceType::class, [
                'choices' => array_combine(
                    $projects,
                    $projects
                )
            ])
            ->add('version', TextType::class, [
                'data' => 'latest',
                'attr' => [
                    'placeholder' => 'Тэг к докер-образу'
                ]
            ])
        ;
    }

}