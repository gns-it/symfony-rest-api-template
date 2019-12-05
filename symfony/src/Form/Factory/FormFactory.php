<?php

namespace App\Form\Factory;

use App\Form\DisabledCsrfProtectionInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormFactory as BaseFormFactory;
use Symfony\Component\Form\FormRegistryInterface;

/**
 * Class FormFactory
 * @package App\Form\Factory
 */
class FormFactory extends BaseFormFactory
{
    protected $registry;

    /**
     * FormFactory constructor.
     * @param FormRegistryInterface $registry
     */
    public function __construct(FormRegistryInterface $registry)
    {
        parent::__construct($registry);
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function createNamedBuilder($name, $type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = [])
    {
        if (null !== $data && !\array_key_exists('data', $options)) {
            $options['data'] = $data;
        }

        if (!\is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        $type = $this->registry->getType($type);

        if ($type->getInnerType() instanceof DisabledCsrfProtectionInterface) {
            $options['csrf_protection'] = false;
        }

        $builder = $type->createBuilder($this, $name, $options);

        // Explicitly call buildForm() in order to be able to override either
        // createBuilder() or buildForm() in the resolved form type
        $type->buildForm($builder, $builder->getOptions());

        return $builder;
    }
}
