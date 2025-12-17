<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Exceptions;

/**
 * Exception thrown when input validation fails.
 *
 * This exception is thrown when required parameters are missing
 * or when parameter values fail validation rules.
 */
class ValidationException extends DigiflazzException
{
    /**
     * @param string $message Validation error message
     * @param array<string, string> $errors Validation errors by field
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = '',
        private readonly array $errors = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * Get all validation errors.
     *
     * @return array<string, string> Field => error message
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if a specific field has a validation error.
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Get the validation error for a specific field.
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Create exception for missing required parameters.
     *
     * @param array<int, string> $fields Missing field names
     */
    public static function missingRequired(array $fields): self
    {
        $errors = [];
        foreach ($fields as $field) {
            $errors[$field] = "The {$field} field is required";
        }

        return new self(
            message: 'Missing required parameters: ' . implode(', ', $fields),
            errors: $errors,
        );
    }

    /**
     * Create exception for invalid parameter value.
     */
    public static function invalidValue(string $field, string $message): self
    {
        return new self(
            message: "Invalid value for {$field}: {$message}",
            errors: [$field => $message],
        );
    }
}
