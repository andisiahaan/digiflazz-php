<?php

use PHPUnit\Framework\TestCase;
use AndiSiahaan\Digiflazz\DigiflazzClient;
use AndiSiahaan\Digiflazz\Services\BalanceService;

class BalanceServiceTest extends TestCase
{
    public function testCheckCallsCekSaldoAndReturnsData()
    {
        $username = 'user';
        $apiKey = 'key';

        $client = new DigiflazzClient($username, $apiKey, ['base_uri' => 'https://api.digiflazz.com/v1/']);

        $mockGuzzle = $this->createMock(\GuzzleHttp\Client::class);

        $expectedPayload = [
            'json' => [
                'cmd' => 'deposit',
                'username' => $username,
                'sign' => md5($username . $apiKey . 'depo'),
            ],
        ];

        $responseBody = json_encode(['data' => ['deposit' => 500000]]);
        $response = new \GuzzleHttp\Psr7\Response(200, [], $responseBody);

        $mockGuzzle->expects($this->once())
            ->method('post')
            ->with('cek-saldo', $expectedPayload)
            ->willReturn($response);

        // inject mock into client
        $ref = new \ReflectionProperty($client, 'http');
        $ref->setAccessible(true);
        $ref->setValue($client, $mockGuzzle);

        $result = $client->checkBalance();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(500000, $result['data']['deposit']);
    }
}
