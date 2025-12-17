<?php

/**
 * Prepaid Transaction Example
 *
 * Examples of prepaid topup transactions with the Digiflazz API.
 */

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Digiflazz\DigiflazzClient;
use AndiSiahaan\Digiflazz\Exceptions\ApiException;
use AndiSiahaan\Digiflazz\Exceptions\DigiflazzException;
use AndiSiahaan\Digiflazz\Exceptions\ValidationException;
use AndiSiahaan\Digiflazz\Services\TransactionService;

try {
    $client = DigiflazzClient::fromEnvironment();
} catch (\RuntimeException $e) {
    echo "Please set DIGIFLAZZ_USERNAME and DIGIFLAZZ_APIKEY environment variables.\n";
    exit(1);
}

// Test cases for prepaid transactions
$testCases = [
    [
        'name' => 'XL 10rb - Success',
        'buyer_sku_code' => 'xld10',
        'customer_no' => '087800001230',
        'testing' => true,
    ],
    [
        'name' => 'Telkomsel - Pending',
        'buyer_sku_code' => 'htel5',
        'customer_no' => '081234567890',
        'testing' => true,
    ],
];

echo "=== Prepaid Transaction Tests ===\n\n";

foreach ($testCases as $index => $testCase) {
    echo sprintf("Test #%d: %s\n", $index + 1, $testCase['name']);
    echo str_repeat('-', 40) . "\n";

    try {
        // Generate unique reference ID
        $refId = TransactionService::generateRefId('TEST');

        // Create transaction using typed method
        $result = $client->transaction()->topup(
            skuCode: $testCase['buyer_sku_code'],
            customerNo: $testCase['customer_no'],
            refId: $refId,
            testing: $testCase['testing'],
        );

        echo "Status: " . ($result['data']['status'] ?? 'Unknown') . "\n";
        echo "Ref ID: " . $refId . "\n";
        echo "SN: " . ($result['data']['sn'] ?? 'N/A') . "\n";
        echo "Price: Rp " . number_format($result['data']['price'] ?? 0) . "\n";

    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        foreach ($e->getErrors() as $field => $error) {
            echo "  - {$field}: {$error}\n";
        }
    } catch (ApiException $e) {
        echo "API Error: " . $e->getMessage() . "\n";
        echo "Error Code: " . ($e->getErrorCode() ?? 'N/A') . "\n";

        if ($e->isRetryable()) {
            echo "(This error is retryable)\n";
        }
    } catch (DigiflazzException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

echo "=== Done ===\n";
