<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Exceptions;

/**
 * Exception thrown when authentication fails.
 *
 * This exception is thrown when the API rejects the credentials
 * or signature, or when the IP is not whitelisted.
 */
class AuthenticationException extends DigiflazzException
{
    /**
     * @param string $message Authentication error message
     * @param string|null $errorCode API error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = 'Authentication failed',
        private readonly ?string $errorCode = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 401, $previous);
    }

    /**
     * Get the API error code.
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Create exception for invalid credentials.
     */
    public static function invalidCredentials(): self
    {
        return new self('Invalid username or API key');
    }

    /**
     * Create exception for IP not whitelisted.
     */
    public static function ipNotWhitelisted(string $ip = ''): self
    {
        $message = 'Your IP address is not whitelisted';
        if ($ip !== '') {
            $message .= ": {$ip}";
        }

        return new self($message, 'IP_NOT_ALLOWED');
    }

    /**
     * Create exception for invalid signature.
     */
    public static function invalidSignature(): self
    {
        return new self('Invalid request signature', 'INVALID_SIGNATURE');
    }
}
