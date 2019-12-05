<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Form\User\Profile;

use App\Form\DisabledCsrfProtectionAbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class DeleteProfileType
 * @package App\Form\User\Profile
 */
class DeleteProfileType extends DisabledCsrfProtectionAbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'password',
            PasswordType::class,
            [
                'mapped' => false,
                'constraints' => [
                    new UserPassword(),
                    new NotBlank(),
                ],
                'documentation' => [
                    'description' => 'User password',
                    'example' => 'Qwerty123'
                ]
            ]
        );
    }
}