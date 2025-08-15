<?php

use PHPUnit\Framework\TestCase;
use AndiSiahaan\Digiflazz\DigiflazzClient;

class PriceListServiceTest extends TestCase
{
    public function testPrepaidCallsPriceListAndReturnsData()
    {
        $username = 'user';
        $apiKey = 'key';

        $client = new DigiflazzClient($username, $apiKey, ['base_uri' => 'https://api.digiflazz.com/v1/']);

        $mockGuzzle = $this->createMock(\GuzzleHttp\Client::class);

        $expectedPayload = [
            'json' => [
                'cmd' => 'prepaid',
                'username' => $username,
                'sign' => md5($username . $apiKey . 'pricelist'),
            ],
        ];

        $responseBody = json_encode(['data' => ['prices' => [['code' => 'PULSA', 'price' => 10000]]]]);
        $response = new \GuzzleHttp\Psr7\Response(200, [], $responseBody);

        $mockGuzzle->expects($this->once())
            ->method('post')
            ->with('price-list', $expectedPayload)
            ->willReturn($response);

        // inject mock into client
        $ref = new \ReflectionProperty($client, 'http');
        $ref->setAccessible(true);
        $ref->setValue($client, $mockGuzzle);

        $result = $client->priceListPrepaid();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('prices', $result['data']);
    }
}
