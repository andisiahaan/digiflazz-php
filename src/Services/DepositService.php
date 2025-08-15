<?php

namespace AndiSiahaan\Digiflazz\Services;

use AndiSiahaan\Digiflazz\DigiflazzClient;
use AndiSiahaan\Digiflazz\Exceptions\DigiflazzException;

class DepositService
{
    private DigiflazzClient $client;

    public function __construct(DigiflazzClient $client)
    {
        $this->client = $client;
    }

    /**
     * Request a deposit withdrawal ticket.
     * Required keys in $data: amount, bank (or Bank), owner_name
     *
     * @param array $data
     * @return array
     * @throws DigiflazzException
     */
    public function create(array $data): array
    {
        // normalize bank key
        if (isset($data['bank']) && !isset($data['Bank'])) {
            $data['Bank'] = $data['bank'];
            unset($data['bank']);
        }

        $required = ['amount', 'Bank', 'owner_name'];
        foreach ($required as $key) {
            if (empty($data[$key])) {
                throw new DigiflazzException(sprintf('Missing required field: %s', $key));
            }
        }

        $payload = array_merge([
            'username' => $this->client->getUsername(),
            'sign' => $this->client->signature('deposit'),
        ], $data);

        return $this->client->request($payload, 'deposit');
    }
}
