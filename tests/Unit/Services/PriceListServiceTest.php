<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Tests\Unit\Services;

use AndiSiahaan\Digiflazz\DigiflazzClient;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class PriceListServiceTest extends TestCase
{
    #[Test]
    public function it_calls_prepaid_price_list(): void
    {
        $username = 'user';
        $apiKey = 'key';

        $client = new DigiflazzClient($username, $apiKey);
        $mockGuzzle = $this->createMock(Client::class);

        $expectedPayload = [
            'json' => [
                'username' => $username,
                'sign' => md5($username . $apiKey . 'pricelist'),
                'cmd' => 'prepaid',
            ],
        ];

        $responseBody = json_encode([
            'data' => [
                ['product_name' => 'XL 10rb', 'price' => 10500],
            ],
        ]);
        $response = new Response(200, [], $responseBody);

        $mockGuzzle->expects($this->once())
            ->method('post')
            ->with('price-list', $expectedPayload)
            ->willReturn($response);

        $this->injectMockGuzzle($client, $mockGuzzle);

        $result = $client->priceListPrepaid();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    #[Test]
    public function it_calls_pasca_price_list(): void
    {
        $client = $this->createMockedClient([
            'data' => [
                ['product_name' => 'PLN Pascabayar', 'buyer_sku_code' => 'pln'],
            ],
        ]);

        $result = $client->priceListPasca();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    #[Test]
    public function it_supports_all_method_alias(): void
    {
        $client = $this->createMockedClient(['data' => []]);

        $result = $client->priceList()->all();

        $this->assertArrayHasKey('prepaid', $result);
        $this->assertArrayHasKey('postpaid', $result);
    }

    private function injectMockGuzzle(DigiflazzClient $client, Client $mockGuzzle): void
    {
        $httpRef = new ReflectionProperty($client, 'http');
        $httpRef->setAccessible(true);
        $httpClient = $httpRef->getValue($client);

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
