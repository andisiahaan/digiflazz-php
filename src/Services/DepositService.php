<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Services;

/**
 * Service for deposit/withdrawal operations.
 */
class DepositService extends AbstractService
{
    private const REQUIRED_FIELDS = ['amount', 'Bank', 'owner_name'];

    /**
     * Create a deposit withdrawal ticket.
     *
     * @param array<string, mixed> $data Deposit data (amount, Bank, owner_name required)
     * @return array<string, mixed> Deposit response
     */
    public function create(array $data): array
    {
        // Normalize bank key (accept both 'bank' and 'Bank')
        $data = $this->normalizeData($data);

        $this->validateRequired($data, self::REQUIRED_FIELDS);
        $this->validatePositiveInt($data['amount'], 'amount');

        $payload = $this->buildPayload('deposit', $data);

        return $this->request($payload, 'deposit');
    }

    /**
     * Alias for create() method.
     *
     * @param int $amount Amount to withdraw
     * @param string $bank Bank name
     * @param string $ownerName Account owner name
     * @return array<string, mixed>
     */
    public function withdraw(int $amount, string $bank, string $ownerName): array
    {
        return $this->create([
            'amount' => $amount,
            'Bank' => $bank,
            'owner_name' => $ownerName,
        ]);
    }

    /**
     * Normalize deposit data keys.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalizeData(array $data): array
    {
        // Accept both 'bank' and 'Bank'
        if (isset($data['bank']) && !isset($data['Bank'])) {
            $data['Bank'] = $data['bank'];
            unset($data['bank']);
        }

        return $data;
    }
}
