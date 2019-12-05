<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Exception\Model;

/**
 * Class AppException
 * @package App\Exception\Model
 */
class AppError implements \JsonSerializable
{
    /**
     * @var string
     */
    private $httpCode;
    /**
     * @var string
     */
    private $message;
    /**
     * @var array
     */
    private $formErrors;
    /**
     * @var string
     */
    private $messageDescription;

    public function __construct(int $httpCode, string $message, array $formErrors = null, string $messageDescription = null)
    {

        $this->httpCode = $httpCode;
        $this->message = $message;
        $this->formErrors = $formErrors;
        $this->messageDescription = $messageDescription;
    }

    /**
     * @return array
     */
    public function getData():array
    {
        $data = [
            'code' => $this->httpCode,
            'message' => $this->message,
        ];
        if($this->formErrors){
            $data['formErrors'] = $this->formErrors;
        }
        if($this->messageDescription){
            $data['messageDescription'] = $this->messageDescription;
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getHttpCode(): ?string
    {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getFormErrors(): ?array
    {
        return $this->formErrors;
    }

    /**
     * @return string
     */
    public function getMessageDescription(): ?string
    {
        return $this->messageDescription;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
       return $this->getData();
    }
}