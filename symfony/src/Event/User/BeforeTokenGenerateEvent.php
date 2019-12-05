<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Event\User;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeTokenGenerateEvent extends Event
{

    /**
     * @var User
     */
    private $user;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;

    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
        $this->response = null;
    }

    /**
     * @return Response
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return Request
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}