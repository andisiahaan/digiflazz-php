<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Exceptions;

use Psr\Http\Message\ResponseInterface;

/**
 * Exception thrown when HTTP communication fails.
 *
 * This exception wraps HTTP-level errors such as connection timeouts,
 * DNS resolution failures, or server errors (5xx status codes).
 */
class HttpException extends DigiflazzException
{
    /**
     * @param string $message Error message
     * @param int $statusCode HTTP status code (0 if not applicable)
     * @param string|null $responseBody Raw response body
     * @param ResponseInterface|null $response PSR-7 response object
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = '',
        private readonly int $statusCode = 0,
        private readonly ?string $responseBody = null,
        private readonly ?ResponseInterface $response = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the raw response body content.
     */
    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    /**
     * Get the PSR-7 response object.
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Check if the error is a client error (4xx).
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Check if the error is a server error (5xx).
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Create from a Guzzle exception.
     */
    public static function fromGuzzleException(\Throwable $e): self
    {
        $response = null;
        $statusCode = 0;
        $responseBody = null;

        if ($e instanceof \GuzzleHttp\Exception\RequestException) {
            $response = $e->getResponse();
            if ($response !== null) {
                $statusCode = $response->getStatusCode();
                $responseBody = (string) $response->getBody();
            }
        }

        return new self(
            message: 'HTTP request failed: ' . $e->getMessage(),
            statusCode: $statusCode,
            responseBody: $responseBody,
            response: $response,
            previous: $e,
        );
    }
}
