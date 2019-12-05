<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Form\User\Profile;

use App\Entity\User\User;
use App\Form\DisabledCsrfProtectionAbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class ChangePassType
 * @package App\Form\User\Profile
 */
class ChangePassType extends DisabledCsrfProtectionAbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'oldPassword',
                TextType::class,
                [
                    'mapped' => false,
                    'constraints' => [
                        new NotNull(),
                        new UserPassword(),
                    ],
                    'documentation' => [
                        'description' => 'Old password',
                        'example' => 'Qwerty123'
                    ]
                ]
            )->add(
                'newPassword',
                PasswordType::class,
                [
                    'property_path' => 'plainPassword',
                    'constraints' => [
                        new NotNull(),
                        new Length(['min' => 6, 'max' => 60,]),
                    ],
                    'documentation' => [
                        'description' => 'New password',
                        'example' => 'Qwerty123'
                    ]
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
            )
        );
    }
}