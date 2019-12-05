<?php
/**
 * @author Sergey Hashimov <hashimov.sergey@gmail.com>
 */

namespace App\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class HtmlFormProcessor
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var FormFactoryInterface */
    private $formFactory;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @param $entity
     * @param string $type
     * @param string $actionRoute
     * @param array $params
     * @return mixed
     */
    public function process(Request $request, $entity, string $type, string $actionRoute, array $params = [])
    {
        $form = $this->formFactory->create(
            $type,
            $entity,
            [
                'method' => 'POST',
                'action' => $this->router->generate($actionRoute, $params, UrlGeneratorInterface::ABSOLUTE_PATH),
            ]
        );
        $submitLabel = null === $entity->getId()?'Submit':'Update';
        $form->add(
            'submit',
            SubmitType::class,
            ['label' => $submitLabel, 'attr' => ['class' => 'd-none  d-sm-inline-block btn btn-sm btn-primary shadow-sm']]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $entity->getId()) {
                $this->em->persist($entity);
            }
            return true;
        }
        return $form;
    }
}