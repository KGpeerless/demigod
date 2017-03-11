<?php
namespace AppBundle\Exception;

use AppBundle\Model\RestfulError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class InvalidFormException extends \RuntimeException implements HttpExceptionInterface
{
    private $statusCode;
    private $headers;
    private $form;

    public function __construct($message = null, FormInterface $form = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        $this->statusCode = Response::HTTP_BAD_REQUEST;
        $this->form       = $form;
        $this->headers    = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set response headers.
     *
     * @param array $headers Response headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

}