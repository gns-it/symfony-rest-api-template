<?php

namespace App\Form\Type;

use App\Service\Recaptcha\Validator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CaptchaType
 * @package App\Form\Type
 */
class CaptchaType extends AbstractType
{

    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var Validator
     */
    private $validator;

    /**
     * UuidEntityType constructor.
     * @param TranslatorInterface $translator
     * @param Validator $validator
     */
    public function __construct(TranslatorInterface $translator, Validator $validator)
    {
        $this->translator = $translator;
        $this->validator = $validator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($options) {
                $this->validator->validateForm($event, $options);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('mapped', false);
        $resolver->setDefault('min_score', Validator::DEFAULT_SCORE);
        $resolver->setDefault('invalid_message', 'capcha_is_invalid');
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return TextType::class;
    }

}