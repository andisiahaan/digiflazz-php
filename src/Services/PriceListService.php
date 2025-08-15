<?php

namespace AndiSiahaan\Digiflazz\Services;

use AndiSiahaan\Digiflazz\DigiflazzClient;

class PriceListService
{
    private DigiflazzClient $client;

    public function __construct(DigiflazzClient $client)
    {
        $this->client = $client;
    }

    /**
     * Request price list for prepaid products.
     * Optional filters: code, category, brand, type
     *
     * @param array $filters
     * @return array
     */
    public function prepaid(array $filters = []): array
    {
        $payload = array_merge([
            'cmd' => 'prepaid',
            'username' => $this->client->getUsername(),
            'sign' => $this->client->signature('pricelist'),
        ], $filters);

    return $this->client->request($payload, 'price-list');
    }

    /**
     * Request price list for pascabayar products.
     * Optional filters: code, brand
     *
     * @param array $filters
     * @return array
     */
    public function pasca(array $filters = []): array
    {
        $payload = array_merge([
            'cmd' => 'pasca',
            'username' => $this->client->getUsername(),
            'sign' => $this->client->signature('pricelist'),
        ], $filters);

        return $this->client->request($payload, 'price-list');
    }
}
