<?php

namespace App\Form;

use App\Exception\Form\FormValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class SuperFormService
 * @package App\Form
 */
class FormHandler
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var FormFactoryInterface */
    private $formFactory;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
    }

    /**
     * @param array $data
     * @param $entity
     * @param string $type
     * @param bool $flush
     * @param bool $persist
     * @return mixed
     */
    public function handler(array $data, $entity, string $type, bool $flush = true, bool $persist = true)
    {
        $form = $this->formFactory->create($type, $entity);
        $clearMissing = null !== $entity ? null === $entity->getId() : false;
        $form->submit($data, $clearMissing);
        if ($form->isValid()) {
            if (null !== $entity) {

                if (null === $entity->getId() && $persist) {
                    $this->em->persist($entity);
                }
                if ($flush) {
                    $this->em->flush();
                }
            }

            return $entity;
        }
        throw new FormValidationException($form);
    }

    /**
     * @param array $data
     * @param $entity
     * @param string $type
     * @param array $options
     * @return mixed
     */
    public function handleNoneMapped(array $data, $entity, string $type,array $options = [])
    {
        $form = $this->formFactory->create($type, $entity, $options);
        $form->submit($data);
        if ($form->isValid()) {
            return $form->getData();
        }
        throw new FormValidationException($form);
    }

}