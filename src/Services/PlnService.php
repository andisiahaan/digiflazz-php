<?php

namespace AndiSiahaan\Digiflazz\Services;

use AndiSiahaan\Digiflazz\DigiflazzClient;

class PlnService
{
    private DigiflazzClient $client;

    public function __construct(DigiflazzClient $client)
    {
        $this->client = $client;
    }

    /**
     * Inquiry PLN customer validation
     *
     * @param string $customerNo
     * @return array
     */
    public function inquiry(string $customerNo): array
    {
        if (trim($customerNo) === '') {
            throw new \InvalidArgumentException('customerNo is required');
        }

        $payload = [
            'username' => $this->client->getUsername(),
            'customer_no' => $customerNo,
            'sign' => $this->client->signature($customerNo),
        ];

        return $this->client->request($payload, 'inquiry-pln');
    }
}
