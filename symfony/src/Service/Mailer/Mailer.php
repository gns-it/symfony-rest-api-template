<?php

namespace App\Service\Mailer;

use App\Entity\Messenger\Message;
use App\Entity\Notification\Admin\Upgrade;
use App\Entity\User\UserInterface;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Templating\EngineInterface;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Mailer
 * @package App\Service\Mailer
 */
class Mailer
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $templating;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Mailer constructor.
     * @param Swift_Mailer $mailer
     * @param Environment $environment
     * @param LoggerInterface $mailerLogger
     */
    public function __construct(Swift_Mailer $mailer, Environment $environment, LoggerInterface $mailerLogger)
    {
        $this->mailer = $mailer;
        $this->templating = $environment;
        $this->logger = $mailerLogger;
    }

    /**
     * @param UserInterface $user
     */
    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        $template = 'Email/User/confirmation_email.html.twig';
        $url = getenv('APP_DOMAIN') . getenv('EMAIL_CONFIRMATION_URI') . $user->getConfirmationToken();
        $rendered = $this->templating->render($template, ['user' => $user, 'confirmationUrl' => $url]);
        $this->sendEmailMessage($rendered, getenv('MAILER_FROM'), (string)$user->getEmail(), 'Confirm your email.');
    }

    /**
     * @param UserInterface $user
     */
    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $template = 'Email/User/password_reset.html.twig';
        $getParams = ['token' => $user->getConfirmationToken()];
        $url = getenv('APP_DOMAIN').getenv('RESETTING_CONFIRMATION_URL').'?'.http_build_query($getParams);
        $rendered = $this->templating->render($template, ['url' => $url, "user" => $user]);
        $this->sendEmailMessage($rendered, getenv('MAILER_FROM'), (string)$user->getEmail(), 'Reset your password.');
    }

    /**
     * @param Upgrade $upgrade
     * @param string[] $receivers
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendUserUpgradeEmailMessage(Upgrade $upgrade, array $receivers): void
    {
        $template = 'Email/Notification/Admin/upgrade.html.twig';
        $rendered = $this->templating->render($template, compact('upgrade'));
        $this->sendEmailMessage($rendered, getenv('MAILER_FROM'), $receivers, 'Upgrade received.');
    }

    /**
     * @param UserInterface $sender
     * @param UserInterface $receiver
     * @param array $channelAttributes
     * @return int
     */
    public function sendNewPrivateMessageNotice(UserInterface $sender,UserInterface $receiver, array $channelAttributes): int
    {
        $template = 'Email/Notification/new_private_message.html.twig';
        $url = 'https://mock.url';
        $rendered = $this->templating->render($template, compact('url','sender','receiver','channelAttributes'));

        return $this->sendEmailMessage(
            $rendered,
            getenv('MAILER_FROM'),
            $receiver->getEmail(),
            'New private message received'
        );
    }

    /**
     * Send rendered email message
     * @param string $renderedTemplate
     * @param array|string $fromEmail
     * @param array|string $toEmail
     * @param string $subject
     * @return int
     */
    public function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail, $subject = ''): ?int
    {
        try {
            $body = $renderedTemplate;
            $message = (new Swift_Message())
                ->setSubject($subject)
                ->setFrom($fromEmail)
                ->setTo($toEmail)
                ->setBody($body, 'text/html');

            return $this->mailer->send($message);

        } catch (Throwable $e) {
            $this->logger->error('Failed on sending: '.$e->getMessage());
        }
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }
}