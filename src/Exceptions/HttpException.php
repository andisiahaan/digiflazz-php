<?php

namespace AndiSiahaan\Digiflazz\Exceptions;

use Psr\Http\Message\ResponseInterface;

class HttpException extends DigiflazzException
{
    private ?ResponseInterface $response;

    public function __construct(string $message = "", ?ResponseInterface $response = null, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
