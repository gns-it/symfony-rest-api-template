<?php

namespace App\Form\User;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ResettingPassType
 * @package App\Form\User
 */
class ResettingPassType extends UserRegistrationType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('accountType')->remove('email');
    }

}