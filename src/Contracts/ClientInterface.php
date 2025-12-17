<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Contracts;

use AndiSiahaan\Digiflazz\Config\Configuration;

/**
 * Main client interface for Digiflazz API.
 */
interface ClientInterface
{
    /**
     * Get the client configuration.
     */
    public function getConfiguration(): Configuration;

    /**
     * Send a request to the API.
     *
     * @param array<string, mixed> $payload Request payload
     * @param string $endpoint API endpoint path
     * @return array<string, mixed> Decoded response
     */
    public function request(array $payload, string $endpoint = ''): array;

    /**
     * Generate signature for API request.
     *
     * @param string $command Command/reference for signature
     */
    public function signature(string $command): string;
}
