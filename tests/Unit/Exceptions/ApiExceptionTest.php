<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Tests\Unit\Exceptions;

use AndiSiahaan\Digiflazz\Exceptions\ApiException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ApiExceptionTest extends TestCase
{
    #[Test]
    public function it_stores_error_code_and_status(): void
    {
        $exception = new ApiException(
            message: 'Transaction failed',
            errorCode: 'TRX_FAILED',
            status: 'Gagal',
        );

        $this->assertSame('Transaction failed', $exception->getMessage());
        $this->assertSame('TRX_FAILED', $exception->getErrorCode());
        $this->assertSame('Gagal', $exception->getStatus());
    }

    #[Test]
    public function it_can_create_from_response(): void
    {
        $response = [
            'data' => [
                'message' => 'Saldo tidak cukup',
                'rc' => 'INSUFFICIENT_BALANCE',
                'status' => 'Gagal',
            ],
        ];

        $exception = ApiException::fromResponse($response);

        $this->assertSame('Saldo tidak cukup', $exception->getMessage());
        $this->assertSame('INSUFFICIENT_BALANCE', $exception->getErrorCode());
        $this->assertSame('Gagal', $exception->getStatus());
    }

    #[Test]
    public function it_detects_retryable_errors(): void
    {
        $exception1 = new ApiException('Error', 'TIMEOUT');
        $this->assertTrue($exception1->isRetryable());

        $exception2 = new ApiException('Error', 'TRX_FAILED');
        $this->assertFalse($exception2->isRetryable());

        $exception3 = new ApiException('Error', null, 'Pending');
        $this->assertTrue($exception3->isRetryable());
    }

    #[Test]
    public function it_detects_insufficient_balance(): void
    {
        $exception1 = new ApiException('Error', 'INSUFFICIENT_BALANCE');
        $this->assertTrue($exception1->isInsufficientBalance());

        $exception2 = new ApiException('Saldo tidak cukup untuk transaksi');
        $this->assertTrue($exception2->isInsufficientBalance());

        $exception3 = new ApiException('Transaction success');
        $this->assertFalse($exception3->isInsufficientBalance());
    }

    #[Test]
    public function it_stores_full_response_data(): void
    {
        $responseData = ['data' => ['id' => 123, 'status' => 'Gagal']];
        $exception = new ApiException('Error', null, null, $responseData);

        $this->assertSame($responseData, $exception->getResponseData());
    }
}
