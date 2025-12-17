<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Services\Concerns;

use AndiSiahaan\Digiflazz\Exceptions\ValidationException;

/**
 * Trait for parameter validation in service classes.
 */
trait ValidatesParameters
{
    /**
     * Validate that required parameters are present.
     *
     * @param array<string, mixed> $params Parameters to validate
     * @param array<int, string> $required Required field names
     * @throws ValidationException When required parameters are missing
     */
    protected function validateRequired(array $params, array $required): void
    {
        $missing = [];

        foreach ($required as $field) {
            if (!array_key_exists($field, $params) || $params[$field] === '' || $params[$field] === null) {
                $missing[] = $field;
            }
        }

        if ($missing !== []) {
            throw ValidationException::missingRequired($missing);
        }
    }

    /**
     * Validate that a string is not empty.
     *
     * @param string $value Value to validate
     * @param string $fieldName Field name for error message
     * @throws ValidationException When value is empty
     */
    protected function validateNotEmpty(string $value, string $fieldName): void
    {
        if (trim($value) === '') {
            throw ValidationException::invalidValue($fieldName, 'cannot be empty');
        }
    }

    /**
     * Validate that a value is a positive integer.
     *
     * @param mixed $value Value to validate
     * @param string $fieldName Field name for error message
     * @throws ValidationException When value is not a positive integer
     */
    protected function validatePositiveInt(mixed $value, string $fieldName): void
    {
        if (!is_int($value) || $value <= 0) {
            throw ValidationException::invalidValue($fieldName, 'must be a positive integer');
        }
    }

    /**
     * Validate that a value is a valid phone number format.
     *
     * @param string $value Value to validate
     * @param string $fieldName Field name for error message
     * @throws ValidationException When value is not a valid phone number
     */
    protected function validatePhoneNumber(string $value, string $fieldName): void
    {
        // Indonesian phone format: starts with 08 or 628, 10-15 digits
        if (!preg_match('/^(08|628)\d{8,13}$/', $value)) {
            throw ValidationException::invalidValue($fieldName, 'must be a valid Indonesian phone number');
        }
    }

    /**
     * Validate that a value is numeric.
     *
     * @param mixed $value Value to validate
     * @param string $fieldName Field name for error message
     * @throws ValidationException When value is not numeric
     */
    protected function validateNumeric(mixed $value, string $fieldName): void
    {
        if (!is_numeric($value)) {
            throw ValidationException::invalidValue($fieldName, 'must be numeric');
        }
    }
}
