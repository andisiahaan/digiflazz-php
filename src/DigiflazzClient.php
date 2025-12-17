<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz;

use AndiSiahaan\Digiflazz\Config\Configuration;
use AndiSiahaan\Digiflazz\Contracts\ClientInterface;
use AndiSiahaan\Digiflazz\Contracts\HttpClientInterface;
use AndiSiahaan\Digiflazz\Exceptions\ApiException;
use AndiSiahaan\Digiflazz\Exceptions\DigiflazzException;
use AndiSiahaan\Digiflazz\Http\GuzzleHttpClient;
use AndiSiahaan\Digiflazz\Services\BalanceService;
use AndiSiahaan\Digiflazz\Services\DepositService;
use AndiSiahaan\Digiflazz\Services\PlnService;
use AndiSiahaan\Digiflazz\Services\PriceListService;
use AndiSiahaan\Digiflazz\Services\TransactionService;
use AndiSiahaan\Digiflazz\Support\Signature;

/**
 * Main Digiflazz API Client.
 *
 * Provides access to all Digiflazz API services through a unified interface.
 *
 * @example
 * // Create client
 * $client = new DigiflazzClient('username', 'api_key');
 *
 * // Use services
 * $balance = $client->balance()->check();
 * $priceList = $client->priceList()->prepaid();
 *
 * // Or use convenience methods
 * $balance = $client->checkBalance();
 */
class DigiflazzClient implements ClientInterface
{
    private Configuration $config;
    private HttpClientInterface $http;
    private Signature $signature;

    /** @var array<string, object> Cached service instances */
    private array $services = [];

    /**
     * Create a new Digiflazz client.
     *
     * @param string|Configuration $usernameOrConfig Username or Configuration object
     * @param string|null $apiKey API key (required if first param is username)
     * @param array<string, mixed> $options Additional options (base_uri, timeout, etc.)
     */
    public function __construct(
        string|Configuration $usernameOrConfig,
        ?string $apiKey = null,
        array $options = [],
    ) {
        if ($usernameOrConfig instanceof Configuration) {
            $this->config = $usernameOrConfig;
        } else {
            if ($apiKey === null) {
                throw new \InvalidArgumentException('API key is required when passing username string');
            }

            $this->config = new Configuration(
                username: $usernameOrConfig,
                apiKey: $apiKey,
                baseUri: $options['base_uri'] ?? Configuration::DEFAULT_BASE_URI,
                timeout: $options['timeout'] ?? Configuration::DEFAULT_TIMEOUT,
                verifySsl: $options['verify'] ?? true,
                httpOptions: $options,
            );
        }

        $this->http = new GuzzleHttpClient($this->config);
        $this->signature = new Signature(
            $this->config->getUsername(),
            $this->config->getApiKey(),
        );
    }

    /**
     * Create client from environment variables.
     *
     * @param string $usernameEnvKey Environment variable for username
     * @param string $apiKeyEnvKey Environment variable for API key
     */
    public static function fromEnvironment(
        string $usernameEnvKey = 'DIGIFLAZZ_USERNAME',
        string $apiKeyEnvKey = 'DIGIFLAZZ_APIKEY',
    ): self {
        return new self(Configuration::fromEnvironment($usernameEnvKey, $apiKeyEnvKey));
    }

    // ==========================================
    // Contract Implementation
    // ==========================================

    /**
     * @inheritdoc
     */
    public function getConfiguration(): Configuration
    {
        return $this->config;
    }

    /**
     * Generate signature for API request.
     *
     * @param string $command Command/reference for signature
     */
    public function signature(string $command): string
    {
        return $this->signature->generate($command);
    }

    /**
     * Send a request to the API.
     *
     * @param array<string, mixed> $payload Request payload
     * @param string $endpoint API endpoint path
     * @return array<string, mixed> Decoded response
     * @throws DigiflazzException When request fails
     */
    public function request(array $payload, string $endpoint = ''): array
    {
        $response = $this->http->post($endpoint, [
            'json' => $payload,
        ]);

        $body = (string) $response->getBody();
        $json = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DigiflazzException('Invalid JSON response: ' . json_last_error_msg());
        }

        // Check for API error in response
        if ($this->isErrorResponse($json)) {
            throw ApiException::fromResponse($json);
        }

        return $json;
    }

    // ==========================================
    // Service Accessors
    // ==========================================

    /**
     * Get the Balance service.
     */
    public function balance(): BalanceService
    {
        if (!isset($this->services[BalanceService::class])) {
            $this->services[BalanceService::class] = new BalanceService($this);
        }

        /** @var BalanceService */
        return $this->services[BalanceService::class];
    }

    /**
     * Get the Transaction service.
     */
    public function transaction(): TransactionService
    {
        if (!isset($this->services[TransactionService::class])) {
            $this->services[TransactionService::class] = new TransactionService($this);
        }

        /** @var TransactionService */
        return $this->services[TransactionService::class];
    }

    /**
     * Get the Price List service.
     */
    public function priceList(): PriceListService
    {
        if (!isset($this->services[PriceListService::class])) {
            $this->services[PriceListService::class] = new PriceListService($this);
        }

        /** @var PriceListService */
        return $this->services[PriceListService::class];
    }

    /**
     * Get the Deposit service.
     */
    public function deposit(): DepositService
    {
        if (!isset($this->services[DepositService::class])) {
            $this->services[DepositService::class] = new DepositService($this);
        }

        /** @var DepositService */
        return $this->services[DepositService::class];
    }

    /**
     * Get the PLN service.
     */
    public function pln(): PlnService
    {
        if (!isset($this->services[PlnService::class])) {
            $this->services[PlnService::class] = new PlnService($this);
        }

        /** @var PlnService */
        return $this->services[PlnService::class];
    }

    // ==========================================
    // Convenience Methods
    // ==========================================

    /**
     * Check account balance (convenience method).
     *
     * @return array<string, mixed>
     */
    public function checkBalance(): array
    {
        return $this->balance()->check();
    }

    /**
     * Get prepaid price list (convenience method).
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function priceListPrepaid(array $filters = []): array
    {
        return $this->priceList()->prepaid($filters);
    }

    /**
     * Get postpaid price list (convenience method).
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function priceListPasca(array $filters = []): array
    {
        return $this->priceList()->pasca($filters);
    }

    /**
     * Create deposit request (convenience method).
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function requestDeposit(array $data): array
    {
        return $this->deposit()->create($data);
    }

    /**
     * Create topup transaction (convenience method).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function topup(array $params): array
    {
        return $this->transaction()->create($params);
    }

    /**
     * Inquiry postpaid bill (convenience method).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function inqPasca(array $params): array
    {
        return $this->transaction()->inqPasca($params);
    }

    /**
     * Pay postpaid bill (convenience method).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function payPasca(array $params): array
    {
        return $this->transaction()->payPasca($params);
    }

    /**
     * Check prepaid transaction status (convenience method).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function checkPrepaidStatus(array $params): array
    {
        return $this->transaction()->statusByRef($params);
    }

    /**
     * Check postpaid transaction status (convenience method).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function statusPasca(array $params): array
    {
        return $this->transaction()->statusPasca($params);
    }

    /**
     * PLN customer inquiry (convenience method).
     *
     * @param string $customerNo PLN customer number
     * @return array<string, mixed>
     */
    public function inquiryPln(string $customerNo): array
    {
        return $this->pln()->inquiry($customerNo);
    }

    // ==========================================
    // Internal Helpers
    // ==========================================

    /**
     * Check if an API response indicates an error.
     *
     * @param array<string, mixed> $response
     */
    private function isErrorResponse(array $response): bool
    {
        // Check for explicit error indicators
        $data = $response['data'] ?? $response;

        // RC (Response Code) != 00 usually means error
        if (isset($data['rc']) && $data['rc'] !== '00') {
            // Status 'Sukses' or 'Pending' are not errors
            if (isset($data['status'])) {
                $status = strtolower($data['status']);
                if (in_array($status, ['sukses', 'pending'], true)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
