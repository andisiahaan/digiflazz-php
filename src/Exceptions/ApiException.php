<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Exceptions;

/**
 * Exception thrown when the API returns an error response.
 *
 * This exception is thrown when the API returns a successful HTTP response
 * but the response body indicates an error (e.g., transaction failed,
 * insufficient balance, product not found).
 */
class ApiException extends DigiflazzException
{
    /**
     * @param string $message Error message from API
     * @param string|null $errorCode API error code (rc field)
     * @param string|null $status Transaction status
     * @param array<string, mixed> $responseData Full response data
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = '',
        private readonly ?string $errorCode = null,
        private readonly ?string $status = null,
        private readonly array $responseData = [],
        ?\Throwable $previous = null,
    ) {
        $code = is_numeric($errorCode) ? (int) $errorCode : 0;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the API error code (rc field).
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Get the transaction status.
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Get the full response data.
     *
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }

    /**
     * Check if this is a retryable error.
     */
    public function isRetryable(): bool
    {
        // Some errors are temporary and can be retried
        $retryableCodes = ['SYSTEM_ERROR', 'TIMEOUT', 'PENDING'];

        return in_array($this->errorCode, $retryableCodes, true)
            || $this->status === 'Pending';
    }

    /**
     * Check if this is an insufficient balance error.
     */
    public function isInsufficientBalance(): bool
    {
        return $this->errorCode === 'INSUFFICIENT_BALANCE'
            || str_contains(strtolower($this->message), 'saldo tidak cukup');
    }

    /**
     * Create from API response data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromResponse(array $data): self
    {
        $message = $data['message'] ?? $data['data']['message'] ?? 'Unknown API error';
        $errorCode = $data['data']['rc'] ?? $data['rc'] ?? null;
        $status = $data['data']['status'] ?? $data['status'] ?? null;

        return new self(
            message: $message,
            errorCode: $errorCode,
            status: $status,
            responseData: $data,
        );
    }
}
