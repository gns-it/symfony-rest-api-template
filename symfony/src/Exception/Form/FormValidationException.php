<?php

namespace App\Exception\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class FormValidationException
 * @package App\Exception\Form
 */
class FormValidationException extends \RuntimeException implements HttpExceptionInterface
{
    /**
     * @var FormInterface
     */
    private $form;
    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var
     */
    private $headers;

    public function __construct(FormInterface $form)
    {
        $this->statusCode = 400;
        $this->form = $form;
        parent::__construct('Validation error', 0, null);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set response headers.
     * @param array $headers Response headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): ?FormInterface
    {
        return $this->form;
    }

    /**
     * Returns response headers.
     * @return array Response headers
     */
    public function getHeaders()
    {
        return [];
    }
}