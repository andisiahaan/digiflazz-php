<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Tests\Unit;

use AndiSiahaan\Digiflazz\DigiflazzClient;
use AndiSiahaan\Digiflazz\Config\Configuration;
use AndiSiahaan\Digiflazz\Services\BalanceService;
use AndiSiahaan\Digiflazz\Services\DepositService;
use AndiSiahaan\Digiflazz\Services\PlnService;
use AndiSiahaan\Digiflazz\Services\PriceListService;
use AndiSiahaan\Digiflazz\Services\TransactionService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DigiflazzClientTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated_with_username_and_api_key(): void
    {
        $client = new DigiflazzClient('user', 'key');

        $this->assertInstanceOf(DigiflazzClient::class, $client);
        $this->assertSame('user', $client->getConfiguration()->getUsername());
    }

    #[Test]
    public function it_can_be_instantiated_with_configuration_object(): void
    {
        $config = new Configuration('config_user', 'config_key');
        $client = new DigiflazzClient($config);

        $this->assertSame('config_user', $client->getConfiguration()->getUsername());
    }

    #[Test]
    public function it_can_be_instantiated_with_custom_options(): void
    {
        $client = new DigiflazzClient('user', 'key', [
            'base_uri' => 'https://custom.api.com/',
            'timeout' => 60.0,
        ]);

        $this->assertSame('https://custom.api.com/', $client->getConfiguration()->getBaseUri());
        $this->assertSame(60.0, $client->getConfiguration()->getTimeout());
    }

    #[Test]
    public function it_throws_exception_when_api_key_missing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API key is required');

        new DigiflazzClient('username');
    }

    #[Test]
    public function it_generates_correct_signature(): void
    {
        $client = new DigiflazzClient('alice', 'secret');

        $signature = $client->signature('check_balance');
        $expected = md5('alice' . 'secret' . 'check_balance');

        $this->assertSame($expected, $signature);
    }

    #[Test]
    public function it_returns_balance_service(): void
    {
        $client = new DigiflazzClient('user', 'key');

        $this->assertInstanceOf(BalanceService::class, $client->balance());
    }

    #[Test]
    public function it_returns_transaction_service(): void
    {
        $client = new DigiflazzClient('user', 'key');

        $this->assertInstanceOf(TransactionService::class, $client->transaction());
    }

    #[Test]
    public function it_returns_price_list_service(): void
    {
        $client = new DigiflazzClient('user', 'key');

        $this->assertInstanceOf(PriceListService::class, $client->priceList());
    }

    #[Test]
    public function it_returns_deposit_service(): void
    {
        $client = new DigiflazzClient('user', 'key');

        $this->assertInstanceOf(DepositService::class, $client->deposit());
    }

    #[Test]
    public function it_returns_pln_service(): void
    {
        $client = new DigiflazzClient('user', 'key');

        $this->assertInstanceOf(PlnService::class, $client->pln());
    }

    #[Test]
    public function it_caches_service_instances(): void
    {
        $client = new DigiflazzClient('user', 'key');

        $service1 = $client->balance();
        $service2 = $client->balance();

        $this->assertSame($service1, $service2);
    }

    #[Test]
    public function it_can_create_from_environment(): void
    {
        putenv('TEST_DIGIFLAZZ_USER=env_user');
        putenv('TEST_DIGIFLAZZ_KEY=env_key');

        $client = DigiflazzClient::fromEnvironment('TEST_DIGIFLAZZ_USER', 'TEST_DIGIFLAZZ_KEY');

        $this->assertSame('env_user', $client->getConfiguration()->getUsername());

        putenv('TEST_DIGIFLAZZ_USER');
        putenv('TEST_DIGIFLAZZ_KEY');
    }
}
