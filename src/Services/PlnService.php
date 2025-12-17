<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Services;

/**
 * Service for PLN (electricity) operations.
 */
class PlnService extends AbstractService
{
    /**
     * Inquiry PLN customer validation.
     *
     * @param string $customerNo PLN customer number (ID Pelanggan)
     * @return array<string, mixed> Customer data from API
     */
    public function inquiry(string $customerNo): array
    {
        $this->validateNotEmpty($customerNo, 'customer_no');
        $this->validateNumeric($customerNo, 'customer_no');

        $payload = $this->buildPayload($customerNo, [
            'customer_no' => $customerNo,
        ]);

        return $this->request($payload, 'inquiry-pln');
    }

    /**
     * Check if a customer number is valid for PLN.
     *
     * @param string $customerNo PLN customer number
     * @return bool True if valid
     */
    public function isValidCustomer(string $customerNo): bool
    {
        try {
            $result = $this->inquiry($customerNo);

            return isset($result['data']['customer_name'])
                && $result['data']['customer_name'] !== '';
        } catch (\Throwable) {
            return false;
        }
    }
}
