<?php

namespace App\Controller\Admin\User;

use App\Controller\SuperController;
use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user",name="admin_user_")
 */
class UserController extends SuperController
{

    /**
     * @Route("/{type}", methods={"GET"}, name="index", requirements={"type"="business|customer|all"}, options={"search"={"All users"={"type"="all"}}})
     * @param Request $request
     * @param string $type
     * @param UserRepository $repository
     * @return Response
     */
    public function index(Request $request, string $type, UserRepository $repository)
    {
        $role = 'ROLE_';
        $types = ['all'];
        if ($type !== 'all') {
            $role.= strtoupper($type);
        }
        return $this->render(
            'Admin/User/User/index.html.twig',
            [
                'entities' => $repository->htmlPaginate($request),
                'type' => ucfirst($type),
                'types' => $types,
            ]
        );
    }

    /**
     * @Route("/{uuid}/delete", methods={"GET"}, name="delete")
     * @Security("!target.isSuperAdmin()")
     * @param User $target
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(User $target, EntityManagerInterface $em)
    {
        $type = $target->getAccountType();
        $em->remove($target);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_user_index', ['type' => $type]));
    }

    /**
     * @Route("/{uuid}/toggle", methods={"GET"}, name="toggle")
     * @Security("!target.isSuperAdmin()")
     * @param Request $request
     * @param User $target
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function toggle(Request $request, User $target, EntityManagerInterface $em)
    {
        $target->setEnabled(!$target->isEnabled());
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

}