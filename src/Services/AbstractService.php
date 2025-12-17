<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Services;

use AndiSiahaan\Digiflazz\Contracts\ClientInterface;
use AndiSiahaan\Digiflazz\Contracts\ServiceInterface;
use AndiSiahaan\Digiflazz\Services\Concerns\ValidatesParameters;

/**
 * Abstract base class for all service classes.
 *
 * Provides common functionality shared across all services including
 * client access, parameter validation, and request helpers.
 */
abstract class AbstractService implements ServiceInterface
{
    use ValidatesParameters;

    /**
     * @param ClientInterface $client The API client instance
     */
    public function __construct(
        protected readonly ClientInterface $client,
    ) {}

    /**
     * Get the underlying client instance.
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Get the username from configuration.
     */
    protected function getUsername(): string
    {
        return $this->client->getConfiguration()->getUsername();
    }

    /**
     * Generate a signature for the given command.
     */
    protected function signature(string $command): string
    {
        return $this->client->signature($command);
    }

    /**
     * Send a request to the API.
     *
     * @param array<string, mixed> $payload Request payload
     * @param string $endpoint API endpoint
     * @return array<string, mixed> Response data
     */
    protected function request(array $payload, string $endpoint): array
    {
        return $this->client->request($payload, $endpoint);
    }

    /**
     * Build a basic payload with username and signature.
     *
     * @param string $signatureCommand Command for signature generation
     * @param array<string, mixed> $additionalData Additional payload data
     * @return array<string, mixed>
     */
    protected function buildPayload(string $signatureCommand, array $additionalData = []): array
    {
        return array_merge([
            'username' => $this->getUsername(),
            'sign' => $this->signature($signatureCommand),
        ], $additionalData);
    }
}
