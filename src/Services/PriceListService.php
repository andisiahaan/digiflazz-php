<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Services;

/**
 * Service for retrieving product price lists.
 */
class PriceListService extends AbstractService
{
    /**
     * Get prepaid product price list.
     *
     * @param array<string, mixed> $filters Optional filters (code, category, brand, type)
     * @return array<string, mixed> Price list data
     */
    public function prepaid(array $filters = []): array
    {
        $payload = $this->buildPayload('pricelist', array_merge([
            'cmd' => 'prepaid',
        ], $filters));

        return $this->request($payload, 'price-list');
    }

    /**
     * Get postpaid (pascabayar) product price list.
     *
     * @param array<string, mixed> $filters Optional filters (code, brand)
     * @return array<string, mixed> Price list data
     */
    public function pasca(array $filters = []): array
    {
        $payload = $this->buildPayload('pricelist', array_merge([
            'cmd' => 'pasca',
        ], $filters));

        return $this->request($payload, 'price-list');
    }

    /**
     * Alias for pasca() method.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function postpaid(array $filters = []): array
    {
        return $this->pasca($filters);
    }

    /**
     * Get all price lists (prepaid and postpaid).
     *
     * @return array{prepaid: array<string, mixed>, postpaid: array<string, mixed>}
     */
    public function all(): array
    {
        return [
            'prepaid' => $this->prepaid(),
            'postpaid' => $this->pasca(),
        ];
    }
}
