<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Form\User\Profile;

use App\Entity\Media\Media;
use App\Entity\User\User;
use App\Form\DisabledCsrfProtectionAbstractType;
use App\Form\Type\UuidEntityType;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class ProfileType
 * @package App\Form\User\Profile
 */
class ProfileType extends DisabledCsrfProtectionAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'documentation' => [
                        'description' => 'Username',
                        'example' => 'Username',
                    ],
                ]
            )
            ->add('profileName', TextType::class)
            ->add(
                'birthDate',
                DateTimeType::class,
                [
                    'format' => 'yyyy-MM-dd',
                    'widget' => 'single_text',
                    'documentation' => [
                        'example' => '2019-02-03',
                    ],
                ]
            )
            ->add(
                'phone',
                NumberType::class,
                [
                    'constraints' => [new NotNull(),],
                ]
            )
            ->add(
                'active',
                BooleanType::class,
                [
                    'documentation' => [
                        'description' => 'Active account sign',
                    ],
                ]
            )
            ->add(
                'private',
                BooleanType::class,
                [
                    'documentation' => [
                        'description' => 'Private account sign',
                    ],
                ]
            )
            ->add(
                'avatar',
                UuidEntityType::class,
                [
                    'constraints' => [new NotNull()],
                    'mapping_class' => User::class,
                    'class' => Media::class,
                    'documentation' => [
                        'description' => 'Avatar uuid',
                        'example' => 'bd61d8f2 - d161 - 4c40 - bb33 - 18243d1c6819',
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
            [
                'invalid_message' => 'entity.constraints.invalid',
                'data_class' => User::class,
            ]
        );
    }
}