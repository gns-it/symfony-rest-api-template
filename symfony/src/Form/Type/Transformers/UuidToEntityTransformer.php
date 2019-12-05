<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 26.12.18
 * Time: 16:50
 */

namespace App\Form\Type\Transformers;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UuidToEntityTransformer
 * @package App\Form\Type\Transformers
 */
class UuidToEntityTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $entityClass;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var string
     */
    private $fieldName;
    /**
     * @var string
     */
    private $mappingClass;
    /**
     * @var \Closure
     */
    private $queryBuilder;
    /**
     * @var FormBuilderInterface
     */
    private $builder;
    /**
     * @var string
     */
    private $repositoryMethod;

    /**
     * UuidToEntityTransformer constructor.
     *
     * @param string                 $entityClass
     * @param string                 $mappingClass
     * @param string                 $fieldName
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param bool                   $queryBuilder
     * @param FormBuilderInterface   $builder
     * @param string                 $repositoryMethod
     */
    public function __construct(
        string $entityClass,
        string $mappingClass,
        string $fieldName,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        FormBuilderInterface $builder,
        $queryBuilder = false,
        string $repositoryMethod = ''
    )
    {

        $this->entityClass = $entityClass;
        $this->em = $em;
        $this->translator = $translator;
        $this->fieldName = $fieldName;
        $this->mappingClass = $mappingClass;
        if (!class_exists($this->entityClass)) {
            throw new TransformationFailedException("class does not exists '$this->entityClass'");
        }
        if (!class_exists($this->mappingClass)) {
            throw new TransformationFailedException("class does not exists '$this->mappingClass'");
        }
        $this->queryBuilder = $queryBuilder;
        $this->builder = $builder;
        $this->repositoryMethod = $repositoryMethod;
    }

    /**
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException when the transformation fails
     */
    public function transform($value)
    {

        return '';
    }

    /**
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException when the transformation fails
     */
    public function reverseTransform($value)
    {

        $metadata = $this->em->getClassMetadata($this->mappingClass);
        if ($metadata->isSingleValuedAssociation($this->fieldName)) {
            if ('' === $value || null == $value) {
                return null;
            }
            if (is_array($value)) {
                throw new TransformationFailedException('invalid_entity_reference');
            }

            return $this->mapManyToOne($value);
        }
        if ($metadata->isCollectionValuedAssociation($this->fieldName)) {
            if (null == $value || empty($value)) {
                return new ArrayCollection();
            }
            if (!is_array($value)) {
                $value = [$value];
            }

            return $this->mapManyToMany($value);
        }

        return null;
    }


    /**
     * repositoryMethod - метод репозитория entityClass(сlass в UuidEntityType), должен возвращать QueryBuilder
     * @param string $value
     *
     * @return mixed|object|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function mapManyToOne(string $value)
    {

        if (!Uuid::isValid($value)) {
            throw new TransformationFailedException(
                $this->translator->trans('invalid_entity_reference')
            );
        }

        if ($this->repositoryMethod !== '') {

            if (method_exists($this->em->getRepository($this->entityClass), $this->repositoryMethod)) {

                /** @var QueryBuilder $qb */
                $qb = $this->em->getRepository($this->entityClass)->{$this->repositoryMethod}();

                $reference = $qb->andWhere($qb->getRootAliases()[0] . '.uuid = :uuid')->setParameter('uuid', $value)->getQuery()->getOneOrNullResult();
            } else {

                throw new TransformationFailedException('repository method did not exists');
            }

        } else if ($this->queryBuilder) {

            /** @var QueryBuilder $qb */
            $qb = call_user_func($this->queryBuilder->bindTo($this->builder), $this->em->getRepository($this->entityClass));

            $reference = $qb->andWhere($qb->getRootAliases()[0] . '.uuid = :uuid')->setParameter('uuid', $value)->getQuery()->getOneOrNullResult();

        } else {

            $reference = $this->em
                ->getRepository($this->entityClass)
                ->findOneBy(['uuid' => $value]);
        }

        if (null == $reference) {
            throw new TransformationFailedException(
                $this->translator->trans('invalid_entity_reference')
            );
        }

        return $reference;
    }

    /**
     * @param array $values
     *
     * @return ArrayCollection
     */
    public
    function mapManyToMany(array $values)
    {

        if (!$this->valid($values)) {
            throw new TransformationFailedException(
                $this->translator->trans('invalid_entity_reference')
            );
        }

        $reference = $this->em
            ->getRepository($this->entityClass)
            ->findBy(['uuid' => $values]);

        if (empty($reference) || is_null($reference) || count($reference) < count($values)) {
            throw new TransformationFailedException(
                $this->translator->trans('invalid_entity_reference')
            );
        }

        return new ArrayCollection($reference);
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    private
    function valid(array $values)
    {

        foreach (array_values($values) as $value) {
            if (is_array($value)) {
                return false;
            }

            return true;
        }
    }
}