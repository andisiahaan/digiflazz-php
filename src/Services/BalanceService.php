<?php

namespace AndiSiahaan\Digiflazz\Services;

use AndiSiahaan\Digiflazz\DigiflazzClient;

class BalanceService
{
    private DigiflazzClient $client;

    public function __construct(DigiflazzClient $client)
    {
        $this->client = $client;
    }

    /**
     * Check account balance.
     *
     * @return array
     */
    public function check(): array
    {
        // Per Digiflazz docs, cek deposit uses cmd=deposit and endpoint /v1/cek-saldo
        $payload = [
            'cmd' => 'deposit',
            'username' => $this->client->getUsername(),
            'sign' => $this->client->signature('depo'),
        ];

    return $this->client->request($payload, 'cek-saldo');
    }

    /**
     * Check deposit (sisa deposit) via /v1/cek-saldo endpoint.
     * Documentation requires cmd=deposit and sign = md5(username + apiKey + 'depo')
     *
     * @return array
     */
}
