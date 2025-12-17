<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Tests\Unit\Services;

use AndiSiahaan\Digiflazz\DigiflazzClient;
use AndiSiahaan\Digiflazz\Services\BalanceService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class BalanceServiceTest extends TestCase
{
    #[Test]
    public function it_calls_cek_saldo_endpoint_with_correct_payload(): void
    {
        $username = 'user';
        $apiKey = 'key';

        $client = new DigiflazzClient($username, $apiKey, [
            'base_uri' => 'https://api.digiflazz.com/v1/',
        ]);

        $mockGuzzle = $this->createMock(Client::class);

        $expectedPayload = [
            'json' => [
                'username' => $username,
                'sign' => md5($username . $apiKey . 'depo'),
                'cmd' => 'deposit',
            ],
        ];

        $responseBody = json_encode(['data' => ['deposit' => 500000]]);
        $response = new Response(200, [], $responseBody);

        $mockGuzzle->expects($this->once())
            ->method('post')
            ->with('cek-saldo', $expectedPayload)
            ->willReturn($response);

        // Inject mock Guzzle into HTTP client
        $this->injectMockGuzzle($client, $mockGuzzle);

        $result = $client->checkBalance();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertSame(500000, $result['data']['deposit']);
    }

    #[Test]
    public function balance_service_can_get_deposit(): void
    {
        $client = $this->createMockedClient([
            'data' => ['deposit' => 1000000],
        ]);

        $service = new BalanceService($client);
        $result = $service->getDeposit();

        $this->assertArrayHasKey('data', $result);
    }

    private function injectMockGuzzle(DigiflazzClient $client, Client $mockGuzzle): void
    {
        // Access the HTTP client
        $httpRef = new ReflectionProperty($client, 'http');
        $httpRef->setAccessible(true);
        $httpClient = $httpRef->getValue($client);

        // Inject mock into HTTP client
        $guzzleRef = new ReflectionProperty($httpClient, 'client');
        $guzzleRef->setAccessible(true);
        $guzzleRef->setValue($httpClient, $mockGuzzle);
    }

    private function createMockedClient(array $responseData): DigiflazzClient
    {
        $client = new DigiflazzClient('user', 'key');

        $mockGuzzle = $this->createMock(Client::class);
        $response = new Response(200, [], json_encode($responseData));

        $mockGuzzle->method('post')->willReturn($response);

        $this->injectMockGuzzle($client, $mockGuzzle);

        return $client;
    }
}
