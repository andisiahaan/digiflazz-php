<?php

namespace AndiSiahaan\Digiflazz;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use AndiSiahaan\Digiflazz\Exceptions\DigiflazzException;
use AndiSiahaan\Digiflazz\Exceptions\HttpException;

class DigiflazzClient
{
    private Client $http;
    private string $username;
    private string $apiKey;
    // include API version in base URI so services can call relative paths
    private string $baseUri = 'https://api.digiflazz.com/v1/';

    public function __construct(string $username, string $apiKey, array $options = [])
    {
        $this->username = $username;
        $this->apiKey = $apiKey;
        $this->http = new Client(array_merge(['base_uri' => $this->baseUri, 'timeout' => 10.0], $options));
    }

    // ----------------------
    // Accessors / helpers
    // ----------------------
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Create service instances
     */
    public function balance(): \AndiSiahaan\Digiflazz\Services\BalanceService
    {
        return new \AndiSiahaan\Digiflazz\Services\BalanceService($this);
    }

    public function transaction(): \AndiSiahaan\Digiflazz\Services\TransactionService
    {
        return new \AndiSiahaan\Digiflazz\Services\TransactionService($this);
    }

    public function priceList(): \AndiSiahaan\Digiflazz\Services\PriceListService
    {
        return new \AndiSiahaan\Digiflazz\Services\PriceListService($this);
    }

    public function deposit(): \AndiSiahaan\Digiflazz\Services\DepositService
    {
        return new \AndiSiahaan\Digiflazz\Services\DepositService($this);
    }

    /**
     * Convenience: direct call to check balance
     */
    public function checkBalance(): array
    {
    // Digiflazz uses cmd="deposit" on /v1/cek-saldo to return remaining deposit.
    return $this->balance()->check();
    }

    /**
     * Convenience: get prepaid price list
     */
    public function priceListPrepaid(array $filters = []): array
    {
        return $this->priceList()->prepaid($filters);
    }

    /**
     * Convenience: get pascabayar price list
     */
    public function priceListPasca(array $filters = []): array
    {
        return $this->priceList()->pasca($filters);
    }

    /**
     * Convenience: request deposit ticket
     */
    public function requestDeposit(array $data): array
    {
        return $this->deposit()->create($data);
    }

    /**
     * Convenience: create topup transaction
     */
    public function topup(array $params): array
    {
        return $this->transaction()->create($params);
    }

    /**
     * Convenience: inquiry pascabayar (cek tagihan)
     */
    public function inqPasca(array $params): array
    {
        return $this->transaction()->inqPasca($params);
    }

    /**
     * Convenience: pay pascabayar (pay-pasca)
     */
    public function payPasca(array $params): array
    {
        return $this->transaction()->payPasca($params);
    }

    /**
     * Convenience: check prepaid status by resending topup with same ref_id
     */
    public function checkPrepaidStatus(array $params): array
    {
        return $this->transaction()->statusByRef($params);
    }

    /**
     * Convenience: check postpaid status (status-pasca)
     */
    public function statusPasca(array $params): array
    {
        return $this->transaction()->statusPasca($params);
    }

    public function pln(): \AndiSiahaan\Digiflazz\Services\PlnService
    {
        return new \AndiSiahaan\Digiflazz\Services\PlnService($this);
    }

    public function inquiryPln(string $customerNo): array
    {
        return $this->pln()->inquiry($customerNo);
    }

    public function signature(string $cmd): string
    {
        return md5($this->username . $this->apiKey . $cmd);
    }

    /**
     * Low-level request wrapper used by services.
     *
     * @param array $payload
     * @param string $path endpoint path (relative to base URI). Default is empty which posts to base URI (e.g. https://api.digiflazz.com/v1/).
     * @internal
     */
    public function request(array $payload, string $path = ''): array
    {
        try {
        // Guzzle will resolve relative $path against base_uri
            $response = $this->http->post($path, [
                'json' => $payload,
            ]);

            $body = (string)$response->getBody();
            $json = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new DigiflazzException('Invalid JSON response: ' . json_last_error_msg());
            }

            return $json;
        } catch (GuzzleException $e) {
            // Wrap Guzzle exceptions in a library-specific exception
            throw new HttpException('HTTP error: ' . $e->getMessage(), null, 0, $e);
        }
    }
}
