<?php

namespace App\Service\Security\GrantExtension;

use App\Entity\User\User;
use App\Service\Mailer\Mailer;
use App\Service\Security\Token\TokenGeneratorInterface;
use App\Service\User\Manager\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\OAuthServerBundle\Storage\GrantExtensionInterface;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use OAuth2\Model\IOAuth2Client;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Class SuperGrantExtension
 * @package App\Service\Security\GrantExtension
 */
abstract class SuperGrantExtension implements GrantExtensionInterface
{
    /**
     * @var string
     */
    const ERR_EMAIL_CONFLICT = 'email_conflict';

    /**
     * @var string
     */
    const ERR_EMAIL_NOT_DEFINED = 'email_not_defined';

    /**
     * @var string
     */
    const ERR_PASSWORD_NOT_DEFINED = 'email_not_defined';

    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var UserManagerInterface
     */
    protected $userManager;
    /**
     * @var TokenGeneratorInterface
     */
    protected $tokenGenerator;
    /**
     * @var OAuth2Client
     */
    private $oauthClient;
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * GoogleGrantExtension constructor.
     * @param EntityManagerInterface $em
     * @param TokenGeneratorInterface $tokenGenerator
     * @param UserManagerInterface $userManager
     * @param Mailer $mailer
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenGeneratorInterface $tokenGenerator,
        UserManagerInterface $userManager,
        Mailer $mailer
    ) {
        $this->em = $em;
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    /**
     * @return string
     */
    abstract function getFieldName(): string;

    /**
     * @param IOAuth2Client $client
     * @param array $inputData
     * @param array $authHeaders
     * @return array|bool
     * @see IOAuth2GrantExtension::checkGrantExtension
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {

        if (!isset($inputData['remote_token'])) {
            return false;
        }
        try {
            $remoteUser = $this->oauthClient->fetchUserFromToken(
                new AccessToken(['access_token' => $inputData['remote_token']])
            );
        } catch (IdentityProviderException $exception) {
            return false;
        }
        #Searching user by social_id
        $user = $this->em->getRepository(User::class)
            ->findOneBy([$this->getFieldName() => $remoteUser->getId()]);
        if (!$user) {
            $socialEmail = $remoteUser->getEmail();
            $customEmail = $inputData['email'] ?? null;
            $finalEmail = $customEmail;
            if (null !== $customEmail && null !== $socialEmail && $socialEmail !== $customEmail) {
                throw new ConflictHttpException(self::ERR_EMAIL_CONFLICT);
            }
            if (null === $customEmail) {
                $finalEmail = $socialEmail;
            }
            if (null === $finalEmail) {
                throw new BadRequestHttpException(self::ERR_EMAIL_NOT_DEFINED);
            }
            #Searching user by email
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $finalEmail]);
            if (!$user) {
                $user = new User();
                $user->setEmail($finalEmail);
                $user->setName($remoteUser->getName());
                $user->setPlainPassword($this->tokenGenerator->generateToken());
                $user->enable();
                if (null !== $customEmail && $customEmail !== $socialEmail) {
                    $user->setConfirmationToken($this->tokenGenerator->generateToken());
                    $this->mailer->sendConfirmationEmailMessage($user);
                }
            }
            $setter = 'set'.ucfirst($this->getFieldName());
            $user->$setter($remoteUser->getId());
            $this->userManager->updateUser($user);
        }
        if (!is_object($user) || !$user instanceof User) {
            return false;
        }

        return array(
            'data' => $user,
        );
    }

    /**
     * @param OAuth2Client $oauthClient
     */
    public function setOauthClient(OAuth2Client $oauthClient): void
    {
        $this->oauthClient = $oauthClient;
    }
}
