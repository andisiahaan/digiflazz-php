<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Tests\Unit;

use AndiSiahaan\Digiflazz\Support\Signature;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    private Signature $signature;

    protected function setUp(): void
    {
        $this->signature = new Signature('alice', 'secret');
    }

    #[Test]
    public function it_generates_md5_signature(): void
    {
        $result = $this->signature->generate('test_command');

        $expected = md5('alice' . 'secret' . 'test_command');
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_generates_signature_for_balance(): void
    {
        $result = $this->signature->forBalance();

        $expected = md5('alice' . 'secret' . 'depo');
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_generates_signature_for_price_list(): void
    {
        $result = $this->signature->forPriceList();

        $expected = md5('alice' . 'secret' . 'pricelist');
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_generates_signature_for_deposit(): void
    {
        $result = $this->signature->forDeposit();

        $expected = md5('alice' . 'secret' . 'deposit');
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_generates_signature_for_transaction(): void
    {
        $result = $this->signature->forTransaction('ref-123');

        $expected = md5('alice' . 'secret' . 'ref-123');
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_generates_signature_for_pln_inquiry(): void
    {
        $result = $this->signature->forPlnInquiry('530000000001');

        $expected = md5('alice' . 'secret' . '530000000001');
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function signatures_are_different_for_different_inputs(): void
    {
        $sig1 = $this->signature->generate('command1');
        $sig2 = $this->signature->generate('command2');

        $this->assertNotSame($sig1, $sig2);
    }

    #[Test]
    public function signatures_are_consistent_for_same_input(): void
    {
        $sig1 = $this->signature->generate('same_command');
        $sig2 = $this->signature->generate('same_command');

        $this->assertSame($sig1, $sig2);
    }
}
