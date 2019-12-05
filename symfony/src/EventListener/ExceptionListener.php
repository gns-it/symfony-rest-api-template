<?php

namespace App\EventListener;

use App\Entity\User\User;
use App\Exception\Charge\ChargeException;
use App\Exception\Form\FormValidationException;
use App\Exception\Model\AppError;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use OAuth2\OAuth2ServerException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ExceptionListener
 * @package App\EventListener
 */
class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ExceptionListener constructor.
     * @param ViewHandlerInterface $viewHandler
     * @param LoggerInterface $exceptionLogger
     */
    public function __construct(ViewHandlerInterface $viewHandler, LoggerInterface $exceptionLogger)
    {
        $this->viewHandler = $viewHandler;
        $this->logger = $exceptionLogger;
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if (strpos($event->getRequest()->getAcceptableContentTypes()[0], 'html')) {
            return;
        }
        $exception = $event->getException();
        if ($exception instanceof ChargeException) {
            $appError = $this->handleCharge($exception);
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $appError = $this->handleAccessDeniedException($exception);
        } elseif ($exception instanceof HttpException) {
            $appError = $this->handleHttp($exception);
        } elseif ($exception instanceof OAuth2ServerException) {
            $appError = $this->handleOauth2Server($exception);
        } elseif ($exception instanceof FormValidationException) {
            $appError = $this->handleFormValidation($exception);
        }
        if (!isset($appError)) {
            $appError = $this->handleDefault($exception);
        }
        if (getenv('APP_ENV') === 'dev') {
            dump($appError);
        }
        $view = View::create($appError->getData(), $appError->getHttpCode());
        $event->setResponse($this->viewHandler->handle($view));
    }

    /**
     * @param HttpException $e
     * @return AppError
     */
    private function handleHttp(HttpException $e): AppError
    {
        return new AppError($e->getStatusCode(), $e->getMessage());
    }

    /**
     * @param OAuth2ServerException $e
     * @return AppError
     */
    private function handleOauth2Server(OAuth2ServerException $e): AppError
    {
        return new AppError($e->getHttpCode(), $e->getMessage(), null, $e->getDescription());
    }

    /**
     * @param FormValidationException $e
     * @return AppError
     */
    private function handleFormValidation(FormValidationException $e): AppError
    {
        return new AppError(
            $e->getStatusCode(),
            $e->getMessage(),
            $this->createFormErrorsArray($e->getForm())
        );
    }

    /**
     * @param \Exception $e
     * @return AppError
     */
    private function handleDefault(\Exception $e): AppError
    {
        $message = $this->generateErrorMessage($e);
        $this->logger->error($message);
        if (getenv('APP_ENV') !== 'dev') {
            $message = 'server_error';
        } else {
            dump($e);
        }

        return new AppError(
            500,
            $message,
            null,
            'If you see this error, please inform the development department'
        );
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public function createFormErrorsArray(FormInterface $form): array
    {

        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->createFormErrorsArray($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    private function generateErrorMessage(\Exception $exception): string
    {
        return sprintf(
            'Uncaught PHP Exception %s: "%s" at %s line %s',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
    }

    /**
     * @param ChargeException $e
     * @return AppError
     */
    private function handleCharge(ChargeException $e): AppError
    {
        return new AppError($e->getStatusCode(), $e->getMessage(), null, $e->getDescription());
    }

    /**
     * @param AccessDeniedHttpException $exception
     * @return AppError
     */
    private function handleAccessDeniedException(AccessDeniedHttpException $exception): AppError
    {
        $message = $exception->getMessage();
        if ($exception->getPrevious() instanceof AccessDeniedException && isset(
                $exception->getPrevious()->getAttributes()[0]
            ) && $exception->getPrevious()->getAttributes()[0] === User::ROLE_PROFILE_FILLED) {
            $message = 'Profile not filled.';
        }

        return new AppError(
            403,
            $message,
            null,
            'You need to fill your profile to browse app.'
        );
    }
}