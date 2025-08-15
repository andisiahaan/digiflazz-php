<?php

use PHPUnit\Framework\TestCase;
use Digiflazz\DigiflazzClient;

class DigiflazzClientTest extends TestCase
{
    public function testCanInstantiate()
    {
        $client = new DigiflazzClient('user', 'key', ['base_uri' => 'https://api.example.com']);
        $this->assertInstanceOf(DigiflazzClient::class, $client);
    }

    public function testSignatureInternal() 
    {
        $client = new DigiflazzClient('alice', 'secret');

        $ref = new \ReflectionClass($client);
        $method = $ref->getMethod('signature');
        $method->setAccessible(true);

        $sig = $method->invokeArgs($client, ['check_balance']);
        $this->assertEquals(md5('alice' . 'secret' . 'check_balance'), $sig);
    }
}
