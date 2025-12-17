<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Exceptions;

/**
 * Base exception for all Digiflazz library exceptions.
 *
 * All custom exceptions in this library extend from this class,
 * making it easy to catch all library-specific exceptions.
 */
class DigiflazzException extends \Exception
{
    /**
     * @param string $message Exception message
     * @param int $code Exception code
     * @param \Throwable|null $previous Previous exception for chaining
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get a formatted error message suitable for logging.
     */
    public function getFormattedMessage(): string
    {
        return sprintf(
            '[%s] %s (Code: %d)',
            static::class,
            $this->getMessage(),
            $this->getCode(),
        );
    }
}
