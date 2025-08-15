<?php

namespace Digiflazz;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class DigiflazzClient
{
    private Client $http;
    private string $username;
    private string $apiKey;
    private string $baseUri = 'https://api.digiflazz.com';

    public function __construct(string $username, string $apiKey, array $options = [])
    {
        $this->username = $username;
        $this->apiKey = $apiKey;
        $this->http = new Client(array_merge(['base_uri' => $this->baseUri, 'timeout' => 10.0], $options));
    }

    /**
     * Get account balance from Digiflazz.
     *
     * @return array Decoded JSON response
     */
    public function balance(): array
    {
        $payload = [
            'cmd' => 'check_balance',
            'username' => $this->username,
            'sign' => $this->signature('check_balance'),
        ];

        return $this->post($payload);
    }

    private function signature(string $cmd): string
    {
        return md5($this->username . $this->apiKey . $cmd);
    }

    private function post(array $payload): array
    {
        try {
            $response = $this->http->post('/v1/', [
                'form_params' => $payload,
            ]);

            $body = (string)$response->getBody();
            $json = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON response: ' . json_last_error_msg());
            }

            return $json;
        } catch (GuzzleException $e) {
            throw new \RuntimeException('HTTP error: ' . $e->getMessage(), 0, $e);
        }
    }
}
