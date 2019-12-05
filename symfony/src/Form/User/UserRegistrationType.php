<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 23.11.18
 * Time: 10:04
 */

namespace App\Form\User;

use App\Entity\User\User;
use App\Form\DisabledCsrfProtectionAbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class UserRegistrationType
 * @package App\Form\User
 */
class UserRegistrationType extends DisabledCsrfProtectionAbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'constraints' => [
                        new NotNull(),
                        new Length(['min' => 6, 'max' => 60,]),
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
                'cascade_validation' => false,
                'allow_extra_fields' => true,
            )
        );
    }
}