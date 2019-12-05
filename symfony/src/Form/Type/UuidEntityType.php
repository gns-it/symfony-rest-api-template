<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 14.12.18
 * Time: 12:33
 */

namespace App\Form\Type;


use App\Form\Type\Transformers\UuidToEntityTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UuidEntityType
 * @package App\Form\Type
 */
class UuidEntityType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UuidEntityType constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->resetViewTransformers();
        $builder->resetModelTransformers();
        $builder->addModelTransformer(
            new UuidToEntityTransformer(
                $options['class'],
                $options['mapping_class'],
                $builder->getName(),
                $this->em,
                $this->translator,
                $builder,
                $options['query_builder'],
                $options['repository_method']
            )
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('invalid_message', 'entity.constraints.invalid_reference');
        $resolver->setDefault('query_builder', false);
        $resolver->setDefault('repository_method', false);
        $resolver->setRequired(
            array(
                'class',
                'mapping_class',
            )
        );
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

}