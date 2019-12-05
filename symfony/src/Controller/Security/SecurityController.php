<?php

namespace App\Controller\Security;

use App\Entity\User\User;
use App\Service\User\Manager\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser() && $this->getUser()->isSuperAdmin()) {
            return  $this->redirectToRoute('admin_home');
         }
         $error = $authenticationUtils->getLastAuthenticationError();
         $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * Confirm user email
     * @Route("/public/confirm/{token}", methods={"GET"})
     * @Entity(name="user", expr="repository.findOneByConfirmationToken(token)")
     * @param Request $request
     * @param User $user
     * @param UserManagerInterface $userManager
     * @param EventDispatcherInterface $dispatcher
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmAction(
        User $user,
        UserManagerInterface $userManager
    ) {
        $user->setConfirmationToken(null);
        $user->enable();
        $userManager->updateUser($user);

        return $this->render('security/user_confirm_email.html.twig');

    }
}
