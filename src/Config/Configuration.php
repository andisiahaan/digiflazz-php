<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Config;

/**
 * Immutable configuration object for Digiflazz client.
 *
 * Encapsulates all configuration settings in a type-safe manner.
 */
final class Configuration
{
    public const DEFAULT_BASE_URI = 'https://api.digiflazz.com/v1/';
    public const DEFAULT_TIMEOUT = 30.0;

    /**
     * @param string $username Digiflazz username
     * @param string $apiKey Digiflazz API key
     * @param string $baseUri API base URI
     * @param float $timeout Request timeout in seconds
     * @param bool $verifySsl Whether to verify SSL certificates
     * @param array<string, mixed> $httpOptions Additional Guzzle HTTP options
     */
    public function __construct(
        private readonly string $username,
        private readonly string $apiKey,
        private readonly string $baseUri = self::DEFAULT_BASE_URI,
        private readonly float $timeout = self::DEFAULT_TIMEOUT,
        private readonly bool $verifySsl = true,
        private readonly array $httpOptions = [],
    ) {
        if (trim($username) === '') {
            throw new \InvalidArgumentException('Username cannot be empty');
        }

        if (trim($apiKey) === '') {
            throw new \InvalidArgumentException('API key cannot be empty');
        }
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get API key for internal signature generation.
     *
     * @internal This method should only be used internally for signature generation
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    public function shouldVerifySsl(): bool
    {
        return $this->verifySsl;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHttpOptions(): array
    {
        return $this->httpOptions;
    }

    /**
     * Create configuration from environment variables.
     *
     * @param string $usernameEnvKey Environment variable name for username
     * @param string $apiKeyEnvKey Environment variable name for API key
     */
    public static function fromEnvironment(
        string $usernameEnvKey = 'DIGIFLAZZ_USERNAME',
        string $apiKeyEnvKey = 'DIGIFLAZZ_APIKEY',
    ): self {
        $username = getenv($usernameEnvKey);
        $apiKey = getenv($apiKeyEnvKey);

        if ($username === false || $username === '') {
            throw new \RuntimeException("Environment variable {$usernameEnvKey} is not set");
        }

        if ($apiKey === false || $apiKey === '') {
            throw new \RuntimeException("Environment variable {$apiKeyEnvKey} is not set");
        }

        return new self($username, $apiKey);
    }

    /**
     * Create a new configuration with modified base URI.
     */
    public function withBaseUri(string $baseUri): self
    {
        return new self(
            $this->username,
            $this->apiKey,
            $baseUri,
            $this->timeout,
            $this->verifySsl,
            $this->httpOptions,
        );
    }

    /**
     * Create a new configuration with modified timeout.
     */
    public function withTimeout(float $timeout): self
    {
        return new self(
            $this->username,
            $this->apiKey,
            $this->baseUri,
            $timeout,
            $this->verifySsl,
            $this->httpOptions,
        );
    }
}
