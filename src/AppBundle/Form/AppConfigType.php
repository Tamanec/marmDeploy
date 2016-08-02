<?php

namespace AppBundle\Form;

use AppBundle\Entity\AppConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppConfigType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('project', TextType::class)
            ->add('env', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'box' => 'box',
                    'integrator' => 'integrator',
                    'cron' => 'cron',
                    'logrotate' => 'logrotate',
                ]
            ])
            ->add('name', TextType::class)
            ->add('content', TextareaType::class, [
                'attr' => [
                    'cols' => 120,
                    'rows' => 45
                ]
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                /** @var AppConfig $config */
                $config = $event->getData();
                $form = $event->getForm();

                // редактирование конфига
                if (!is_null($config->getProject())) {
                    $disabledOptions = ['disabled' => true];
                    $form
                        ->add('project', TextType::class, $disabledOptions)
                        ->add('env', TextType::class, $disabledOptions)
                        ->add('type', TextType::class, $disabledOptions)
                        ->add('name', TextType::class, $disabledOptions)
                    ;
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\AppConfig'
        ]);
    }

}