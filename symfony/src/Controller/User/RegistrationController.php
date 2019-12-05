<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Controller\User;

use App\Controller\SuperController;
use App\Entity\OAuthServer\Client;
use App\Entity\User\User;
use App\Event\User\BeforeTokenGenerateEvent;
use App\Exception\Form\FormValidationException;
use App\Form\FormHandler;
use App\Form\User\ResettingPassType;
use App\Form\User\UserRegistrationType;
use App\Service\Mailer\Mailer;
use App\Service\OAuth\OAuth2;
use App\Service\Security\Token\TokenGeneratorInterface;
use App\Service\User\Manager\UserManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/public/reg")
 * @SWG\Tag(name="User Registration")
 */
class RegistrationController extends SuperController
{
    /**
     * Reset user password.
     * @Route("/", methods={"POST"})
     * @SWG\Post(summary="Register user (sends email confirmation message)",
     *
     *      @SWG\Response(
     *          response=200,
     *          description="OK",
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Validation error",
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Server error",
     *          ),
     *       @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=UserRegistrationType::class)
     *      ),
     * )
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param FormHandler $formHandler
     * @param Mailer $mailer
     * @return Response
     */
    public function register(
        Request $request,
        UserManagerInterface $userManager,
        TokenGeneratorInterface $tokenGenerator,
        FormHandler $formHandler,
        Mailer $mailer
    ): Response {
        $user = $formHandler->handler(
            $request->request->all(),
            $userManager->createUser(),
            UserRegistrationType::class,
            false
        );
        $user->setConfirmationToken($tokenGenerator->generateToken());
        $mailer->sendConfirmationEmailMessage($user);
        $userManager->updateUser($user);

        return $this->okResponse();
    }

    /**
     * @Route("/reset/request", methods={"POST"})
     * @SWG\Post(summary="Reset password request",
     *
     *      @SWG\Response(
     *          response=200,
     *          description="OK",
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Validation error",
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Server error",
     *          ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @SWG\Definition(
     *              required={"email"},
     *              @SWG\Property(property="email", example="email.example@domain.com", type="email", description="Email"),
     *          )
     *      )
     * )
     * @param Request $request
     * @param Mailer $mailer
     * @param UserManagerInterface $userManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param string $retryTtl
     * @return null|Response
     * @throws \Exception
     */
    public function resetPasswordRequest(
        Request $request,
        Mailer $mailer,
        UserManagerInterface $userManager,
        TokenGeneratorInterface $tokenGenerator,
        string $retryTtl
    ): ?Response {
        $email = $request->request->get('email', 'mock');
        $user = $userManager->findUserByEmail($email);
        if (!$user) {
            throw new NotFoundHttpException('user_not_found');
        }
        if (!$user->isEnabled()) {
            throw new AccessDeniedHttpException('user_not_enabled');
        }
        if (!$user->isPasswordRequestNonExpired($retryTtl)) {

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }
            $user->setPasswordRequestedAt(new \DateTime());
        } else {

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }
        }
        $mailer->sendResettingEmailMessage($user);
        $userManager->updateUser($user);

        return $this->okResponse();
    }

    /**
     * Reset user password.
     * @Route("/reset/{token}/client/{client_id}", methods={"POST"})
     * @Entity(name="user", expr="repository.findOneByConfirmationToken(token)")
     * @Entity(name="client", expr="repository.findByClientId(client_id)")
     * @SWG\Post(summary="Reset password",
     *
     *      @SWG\Response(
     *          response=200,
     *          description="OK",
     *          @SWG\Schema(
     *              type="object",
     *              	@SWG\Property(property="access_token", type="string", example="ZDY1YTUyYzA2MjMyODIwMTY2M2JhMmZmN2Q2NGVmZjFjOTE0NDE4OTQxNTI0MWZiMGFhZWE2M2ZkNGZkNDNkYg"),
     *              	@SWG\Property(property="expires_in", type="string", example="3600"),
     *              	@SWG\Property(property="token_type", type="string", example="bearer"),
     *              	@SWG\Property(property="scope", type="string", example="null"),
     *              	@SWG\Property(property="refresh_token", type="string", example="YWQxMzI0MjVkMDc4YTA3NGNmM2ExNDgyZTY0MjkxMzVhZTk4MWI1OGQ1MjM0ZDI0MWM3YWZmOTQ2MWFiMjhiYQ"),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Validation error",
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="User not found or request was not made",
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Server error",
     *          ),
     *       @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=ResettingPassType::class)
     *      ),
     * )
     * @param Request $request
     * @param User $user
     * @param Client $client
     * @param OAuth2 $auth2
     * @param UserManagerInterface $userManager
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     */
    public function resetPassword(
        Request $request,
        User $user,
        Client $client,
        OAuth2 $auth2,
        UserManagerInterface $userManager,
        EventDispatcherInterface $dispatcher
    ): Response {
        $form = $this->createForm(ResettingPassType::class, $user);
        $form->handleRequest($request);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }
        $user->setConfirmationToken(null);
        $userManager->updateUser($user);
        $dispatcher->dispatch(new BeforeTokenGenerateEvent($user, $request));
        $token = $auth2->createAccessToken($client, $user);

        return $this->response($token);
    }

}
