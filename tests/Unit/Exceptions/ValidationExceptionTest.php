<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Tests\Unit\Exceptions;

use AndiSiahaan\Digiflazz\Exceptions\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_message_and_errors(): void
    {
        $exception = new ValidationException('Validation failed', [
            'email' => 'Invalid email format',
            'name' => 'Name is required',
        ]);

        $this->assertSame('Validation failed', $exception->getMessage());
        $this->assertCount(2, $exception->getErrors());
    }

    #[Test]
    public function it_can_check_if_field_has_error(): void
    {
        $exception = new ValidationException('Error', [
            'email' => 'Invalid email',
        ]);

        $this->assertTrue($exception->hasError('email'));
        $this->assertFalse($exception->hasError('name'));
    }

    #[Test]
    public function it_can_get_specific_field_error(): void
    {
        $exception = new ValidationException('Error', [
            'email' => 'Invalid email format',
        ]);

        $this->assertSame('Invalid email format', $exception->getError('email'));
        $this->assertNull($exception->getError('nonexistent'));
    }

    #[Test]
    public function it_can_create_for_missing_required_fields(): void
    {
        $exception = ValidationException::missingRequired(['buyer_sku_code', 'ref_id']);

        $this->assertStringContainsString('buyer_sku_code', $exception->getMessage());
        $this->assertStringContainsString('ref_id', $exception->getMessage());
        $this->assertTrue($exception->hasError('buyer_sku_code'));
        $this->assertTrue($exception->hasError('ref_id'));
    }

    #[Test]
    public function it_can_create_for_invalid_value(): void
    {
        $exception = ValidationException::invalidValue('amount', 'must be a positive integer');

        $this->assertStringContainsString('amount', $exception->getMessage());
        $this->assertTrue($exception->hasError('amount'));
    }
}
