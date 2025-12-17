<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Services;

/**
 * Service for checking account balance.
 */
class BalanceService extends AbstractService
{
    /**
     * Check account deposit balance.
     *
     * @return array<string, mixed> Balance data from API
     */
    public function check(): array
    {
        $payload = $this->buildPayload('depo', [
            'cmd' => 'deposit',
        ]);

        return $this->request($payload, 'cek-saldo');
    }

    /**
     * Alias for check() method.
     *
     * @return array<string, mixed>
     */
    public function getDeposit(): array
    {
        return $this->check();
    }
}
