<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Support;

/**
 * Signature generator for Digiflazz API requests.
 *
 * Handles the MD5 signature generation required by Digiflazz API.
 * Each API endpoint requires a specific signature format.
 */
final class Signature
{
    /**
     * @param string $username Digiflazz username
     * @param string $apiKey Digiflazz API key
     */
    public function __construct(
        private readonly string $username,
        private readonly string $apiKey,
    ) {}

    /**
     * Generate signature for API request.
     *
     * @param string $command The command/reference ID for signature
     * @return string MD5 hash signature
     */
    public function generate(string $command): string
    {
        return md5($this->username . $this->apiKey . $command);
    }

    /**
     * Generate signature for balance check.
     * Uses 'depo' as the command per Digiflazz documentation.
     */
    public function forBalance(): string
    {
        return $this->generate('depo');
    }

    /**
     * Generate signature for price list request.
     * Uses 'pricelist' as the command per Digiflazz documentation.
     */
    public function forPriceList(): string
    {
        return $this->generate('pricelist');
    }

    /**
     * Generate signature for deposit request.
     * Uses 'deposit' as the command per Digiflazz documentation.
     */
    public function forDeposit(): string
    {
        return $this->generate('deposit');
    }

    /**
     * Generate signature for transaction by reference ID.
     *
     * @param string $refId Transaction reference ID
     */
    public function forTransaction(string $refId): string
    {
        return $this->generate($refId);
    }

    /**
     * Generate signature for PLN inquiry.
     *
     * @param string $customerNo Customer number
     */
    public function forPlnInquiry(string $customerNo): string
    {
        return $this->generate($customerNo);
    }
}
