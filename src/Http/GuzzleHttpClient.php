<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Http;

use AndiSiahaan\Digiflazz\Config\Configuration;
use AndiSiahaan\Digiflazz\Contracts\HttpClientInterface;
use AndiSiahaan\Digiflazz\Exceptions\HttpException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Guzzle HTTP client adapter.
 *
 * Wraps Guzzle HTTP client to implement the HttpClientInterface,
 * allowing for easy swapping of HTTP implementations.
 */
final class GuzzleHttpClient implements HttpClientInterface
{
    private Client $client;

    /**
     * @param Configuration $config Client configuration
     * @param Client|null $client Optional pre-configured Guzzle client
     */
    public function __construct(
        private readonly Configuration $config,
        ?Client $client = null,
    ) {
        $this->client = $client ?? $this->createClient();
    }

    /**
     * Send a POST request.
     *
     * @param string $uri Request URI (relative to base URI)
     * @param array<string, mixed> $options Request options
     * @return ResponseInterface PSR-7 response
     * @throws HttpException When the request fails
     */
    public function post(string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->client->post($uri, $options);
        } catch (GuzzleException $e) {
            throw HttpException::fromGuzzleException($e);
        }
    }

    /**
     * Get the underlying Guzzle client.
     */
    public function getGuzzleClient(): Client
    {
        return $this->client;
    }

    /**
     * Create a new Guzzle client with configuration.
     */
    private function createClient(): Client
    {
        $options = array_merge([
            'base_uri' => $this->config->getBaseUri(),
            'timeout' => $this->config->getTimeout(),
            'verify' => $this->config->shouldVerifySsl(),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ], $this->config->getHttpOptions());

        return new Client($options);
    }
}
