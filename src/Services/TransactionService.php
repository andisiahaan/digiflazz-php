<?php

namespace AndiSiahaan\Digiflazz\Services;

use AndiSiahaan\Digiflazz\DigiflazzClient;

class TransactionService
{
    private DigiflazzClient $client;

    public function __construct(DigiflazzClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a transaction (topup) - minimal example.
     *
     * @param array $params expects keys: buyer_sku_code, customer_no, ref_id, optional testing, max_price, cb_url, allow_dot
     * @return array
     */
    public function create(array $params): array
    {
        $required = ['buyer_sku_code', 'customer_no', 'ref_id'];
        foreach ($required as $key) {
            if (empty($params[$key])) {
                throw new \InvalidArgumentException(sprintf('Missing required parameter: %s', $key));
            }
        }

        // sign must be md5(username + apiKey + ref_id)
        $refId = (string)$params['ref_id'];

        $payload = array_merge([
            'username' => $this->client->getUsername(),
            'buyer_sku_code' => $params['buyer_sku_code'],
            'customer_no' => $params['customer_no'],
            'ref_id' => $refId,
            'sign' => $this->client->signature($refId),
        ], $params);

        // Optional: testing, max_price, cb_url, allow_dot
        return $this->client->request($payload, 'transaction');
    }

    public function status(string $transactionId): array
    {
        // Backwards compatibility: keep signature but prefer re-sending topup per Digiflazz docs
        throw new \BadMethodCallException('Use statusByRef(array $params) that resends topup with the same ref_id to check prepaid status.');
    }

    /**
     * Check prepaid status by resending topup with same ref_id (per Digiflazz docs).
     * Warning: do not check status for transactions older than 90 days (may create a new transaction).
     *
     * Required params: buyer_sku_code, customer_no, ref_id
     * Optional: testing, max_price, cb_url, allow_dot
     *
     * @param array $params
     * @return array
     */
    public function statusByRef(array $params): array
    {
        $required = ['buyer_sku_code', 'customer_no', 'ref_id'];
        foreach ($required as $key) {
            if (empty($params[$key])) {
                throw new \InvalidArgumentException(sprintf('Missing required parameter for status check: %s', $key));
            }
        }

        $refId = (string)$params['ref_id'];

        $payload = array_merge([
            'username' => $this->client->getUsername(),
            'buyer_sku_code' => $params['buyer_sku_code'],
            'customer_no' => $params['customer_no'],
            'ref_id' => $refId,
            'sign' => $this->client->signature($refId),
        ], $params);

        return $this->client->request($payload, 'transaction');
    }

    /**
     * Inquiry for pascabayar (cek tagihan) using commands = inq-pasca
     * Expected params: buyer_sku_code, customer_no, ref_id
     *
     * @param array $params
     * @return array
     */
    public function inqPasca(array $params): array
    {
        $required = ['buyer_sku_code', 'customer_no', 'ref_id'];
        foreach ($required as $key) {
            if (empty($params[$key])) {
                throw new \InvalidArgumentException(sprintf('Missing required parameter for inq-pasca: %s', $key));
            }
        }

        $refId = (string)$params['ref_id'];

        $payload = array_merge([
            'commands' => 'inq-pasca',
            'username' => $this->client->getUsername(),
            'buyer_sku_code' => $params['buyer_sku_code'],
            'customer_no' => $params['customer_no'],
            'ref_id' => $refId,
            'sign' => $this->client->signature($refId),
        ], $params);

        return $this->client->request($payload, 'transaction');
    }

    /**
     * Pay pascabayar (pay-pasca)
     * Expected params: buyer_sku_code, customer_no, ref_id
     *
     * @param array $params
     * @return array
     */
    public function payPasca(array $params): array
    {
        $required = ['buyer_sku_code', 'customer_no', 'ref_id'];
        foreach ($required as $key) {
            if (empty($params[$key])) {
                throw new \InvalidArgumentException(sprintf('Missing required parameter for pay-pasca: %s', $key));
            }
        }

        $refId = (string)$params['ref_id'];

        $payload = array_merge([
            'commands' => 'pay-pasca',
            'username' => $this->client->getUsername(),
            'buyer_sku_code' => $params['buyer_sku_code'],
            'customer_no' => $params['customer_no'],
            'ref_id' => $refId,
            'sign' => $this->client->signature($refId),
        ], $params);

        return $this->client->request($payload, 'transaction');
    }

    /**
     * Check postpaid status using commands = status-pasca
     * Required params: buyer_sku_code, customer_no, ref_id
     *
     * @param array $params
     * @return array
     */
    public function statusPasca(array $params): array
    {
        $required = ['buyer_sku_code', 'customer_no', 'ref_id'];
        foreach ($required as $key) {
            if (empty($params[$key])) {
                throw new \InvalidArgumentException(sprintf('Missing required parameter for status-pasca: %s', $key));
            }
        }

        $refId = (string)$params['ref_id'];

        $payload = array_merge([
            'commands' => 'status-pasca',
            'username' => $this->client->getUsername(),
            'buyer_sku_code' => $params['buyer_sku_code'],
            'customer_no' => $params['customer_no'],
            'ref_id' => $refId,
            'sign' => $this->client->signature($refId),
        ], $params);

        return $this->client->request($payload, 'transaction');
    }
}
