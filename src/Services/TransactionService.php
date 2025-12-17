<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Services;

/**
 * Service for transaction operations (topup, inquiry, payment).
 *
 * Handles both prepaid and postpaid (pascabayar) transactions.
 */
class TransactionService extends AbstractService
{
    private const REQUIRED_TRANSACTION = ['buyer_sku_code', 'customer_no', 'ref_id'];

    /**
     * Create a prepaid transaction (topup).
     *
     * @param array<string, mixed> $params Transaction parameters
     *   - buyer_sku_code: (required) Product SKU code
     *   - customer_no: (required) Customer phone number
     *   - ref_id: (required) Unique reference ID
     *   - testing: (optional) Test mode flag
     *   - max_price: (optional) Maximum price limit
     *   - cb_url: (optional) Callback URL
     *   - allow_dot: (optional) Allow dot in customer_no
     * @return array<string, mixed> Transaction response
     */
    public function create(array $params): array
    {
        $this->validateRequired($params, self::REQUIRED_TRANSACTION);

        $refId = (string) $params['ref_id'];

        $payload = $this->buildPayload($refId, $params);

        return $this->request($payload, 'transaction');
    }

    /**
     * Alias for create() - Create a topup transaction.
     *
     * @param string $skuCode Product SKU code
     * @param string $customerNo Customer phone number
     * @param string $refId Unique reference ID
     * @param bool $testing Test mode flag
     * @return array<string, mixed>
     */
    public function topup(
        string $skuCode,
        string $customerNo,
        string $refId,
        bool $testing = false,
    ): array {
        return $this->create([
            'buyer_sku_code' => $skuCode,
            'customer_no' => $customerNo,
            'ref_id' => $refId,
            'testing' => $testing,
        ]);
    }

    /**
     * Check prepaid transaction status by resending with same ref_id.
     *
     * Warning: Do not check status for transactions older than 90 days.
     *
     * @param array<string, mixed> $params Same parameters as create()
     * @return array<string, mixed> Transaction status
     */
    public function statusByRef(array $params): array
    {
        $this->validateRequired($params, self::REQUIRED_TRANSACTION);

        $refId = (string) $params['ref_id'];

        $payload = $this->buildPayload($refId, $params);

        return $this->request($payload, 'transaction');
    }

    /**
     * Postpaid inquiry (cek tagihan).
     *
     * @param array<string, mixed> $params Inquiry parameters
     *   - buyer_sku_code: (required) Product SKU code (e.g., 'pln')
     *   - customer_no: (required) Customer ID number
     *   - ref_id: (required) Unique reference ID
     *   - testing: (optional) Test mode flag
     * @return array<string, mixed> Inquiry response with bill details
     */
    public function inqPasca(array $params): array
    {
        $this->validateRequired($params, self::REQUIRED_TRANSACTION);

        $refId = (string) $params['ref_id'];

        $payload = $this->buildPayload($refId, array_merge([
            'commands' => 'inq-pasca',
        ], $params));

        return $this->request($payload, 'transaction');
    }

    /**
     * Alias for inqPasca() - Postpaid inquiry.
     *
     * @param string $skuCode Product SKU code
     * @param string $customerNo Customer ID number
     * @param string $refId Unique reference ID
     * @param bool $testing Test mode flag
     * @return array<string, mixed>
     */
    public function inquiry(
        string $skuCode,
        string $customerNo,
        string $refId,
        bool $testing = false,
    ): array {
        return $this->inqPasca([
            'buyer_sku_code' => $skuCode,
            'customer_no' => $customerNo,
            'ref_id' => $refId,
            'testing' => $testing,
        ]);
    }

    /**
     * Postpaid payment (pay-pasca).
     *
     * @param array<string, mixed> $params Payment parameters
     *   - buyer_sku_code: (required) Product SKU code
     *   - customer_no: (required) Customer ID number
     *   - ref_id: (required) Same ref_id from inquiry
     *   - testing: (optional) Test mode flag
     * @return array<string, mixed> Payment response
     */
    public function payPasca(array $params): array
    {
        $this->validateRequired($params, self::REQUIRED_TRANSACTION);

        $refId = (string) $params['ref_id'];

        $payload = $this->buildPayload($refId, array_merge([
            'commands' => 'pay-pasca',
        ], $params));

        return $this->request($payload, 'transaction');
    }

    /**
     * Alias for payPasca() - Postpaid payment.
     *
     * @param string $skuCode Product SKU code
     * @param string $customerNo Customer ID number
     * @param string $refId Same ref_id from inquiry
     * @param bool $testing Test mode flag
     * @return array<string, mixed>
     */
    public function pay(
        string $skuCode,
        string $customerNo,
        string $refId,
        bool $testing = false,
    ): array {
        return $this->payPasca([
            'buyer_sku_code' => $skuCode,
            'customer_no' => $customerNo,
            'ref_id' => $refId,
            'testing' => $testing,
        ]);
    }

    /**
     * Check postpaid transaction status.
     *
     * @param array<string, mixed> $params Status check parameters
     * @return array<string, mixed> Status response
     */
    public function statusPasca(array $params): array
    {
        $this->validateRequired($params, self::REQUIRED_TRANSACTION);

        $refId = (string) $params['ref_id'];

        $payload = $this->buildPayload($refId, array_merge([
            'commands' => 'status-pasca',
        ], $params));

        return $this->request($payload, 'transaction');
    }

    /**
     * Generate a unique reference ID.
     *
     * @param string $prefix Optional prefix
     */
    public static function generateRefId(string $prefix = ''): string
    {
        $unique = uniqid($prefix, true);

        return str_replace('.', '', $unique);
    }
}
