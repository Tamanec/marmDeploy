<?php

namespace AppBundle\Form;


use AppBundle\Model\AppConf\AppConfig;
use AppBundle\Service\AppConfigManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для получения данных сборки образов logrotate
 *
 * @package AppBundle\Form
 */
class LogrotateBuildConfType extends AbstractType {

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
        $builder
            ->add('projectEnv', LinkedProjectEnvSubType::class, [
                'label_attr' => [
                    'hidden' => true
                ]
            ])
            ->add('conf', LinkedConfigSubType::class, [
                'type' => 'logrotate',
                'label_attr' => [
                    'hidden' => true
                ]
            ])
            ->add('version', TextType::class, [
                'data' => 'latest',
                'attr' => [
                    'placeholder' => 'Тэг к докер-образу'
                ]
            ])
        ;

        $builder
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                $this->getEnvLinker()
            )
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                $this->getConfigLinker()
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', 'AppBundle\Model\BuildConf\LogrotateBuildConf');
    }

    protected function getEnvLinker() {
        return function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (
                isset($data['projectEnv']['project'])
                && isset($data['projectEnv']['env'])
            ) {
                return;
            }

            // Обновляем список окружений для проекта
            $environments = $this->configManager->findEnvironmentsByProject($data['projectEnv']['project']);

            $form
                ->get('projectEnv')
                ->add('env', ChoiceType::class, [
                    'choices' => array_combine(
                        $environments,
                        $environments
                    )
                ])
            ;
        };
    }

    protected function getConfigLinker() {
        return function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (
                !isset($data['projectEnv']['project'])
                || !isset($data['projectEnv']['env'])
            ) {
                return;
            }

            // Обновляем список конфигов по проекту, типу и окружению
            $config = new AppConfig();
            $config
                ->setProject($data['projectEnv']['project'])
                ->setEnv($data['projectEnv']['env'])
                ->setType('logrotate')
            ;

            try {
                $configs = $this->configManager->findSiblingConfigs($config);
            } catch (\InvalidArgumentException $e) {
                $configs = [];
            }

            $form
                ->get('conf')
                ->add('config', ChoiceType::class, [
                    'choices' => array_combine(
                        $configs,
                        $configs
                    ),
                    'preferred_choices' => function ($val, $key) use ($config) {
                        return stripos($val, $config->getProject()) !== false;
                    }
                ])
            ;
        };
    }

}