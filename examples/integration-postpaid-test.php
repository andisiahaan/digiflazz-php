<?php

/**
 * Postpaid Transaction Example
 *
 * Examples of postpaid (pascabayar) transactions with the Digiflazz API.
 */

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Digiflazz\DigiflazzClient;
use AndiSiahaan\Digiflazz\Exceptions\ApiException;
use AndiSiahaan\Digiflazz\Exceptions\DigiflazzException;
use AndiSiahaan\Digiflazz\Services\TransactionService;

try {
    $client = DigiflazzClient::fromEnvironment();
} catch (\RuntimeException $e) {
    echo "Please set DIGIFLAZZ_USERNAME and DIGIFLAZZ_APIKEY environment variables.\n";
    exit(1);
}

// Test case: PLN Postpaid
$testCustomer = '530000000001'; // Digiflazz test customer number
$refId = TransactionService::generateRefId('PLN');

echo "=== Postpaid (PLN) Transaction Test ===\n\n";

try {
    // Step 1: PLN Inquiry
    echo "Step 1: PLN Customer Inquiry\n";
    echo str_repeat('-', 40) . "\n";

    $inquiry = $client->inquiryPln($testCustomer);
    echo "Customer No: " . $testCustomer . "\n";
    echo "Customer Name: " . ($inquiry['data']['customer_name'] ?? 'N/A') . "\n";
    echo "Segment Power: " . ($inquiry['data']['segment_power'] ?? 'N/A') . "\n\n";

    // Step 2: Bill Inquiry (cek tagihan)
    echo "Step 2: Bill Inquiry\n";
    echo str_repeat('-', 40) . "\n";

    $bill = $client->transaction()->inquiry(
        skuCode: 'pln',
        customerNo: $testCustomer,
        refId: $refId,
        testing: true,
    );

    echo "Ref ID: " . $refId . "\n";
    echo "Status: " . ($bill['data']['status'] ?? 'Unknown') . "\n";
    echo "Bill Amount: Rp " . number_format($bill['data']['selling_price'] ?? 0) . "\n\n";

    // Step 3: Pay Bill (only if inquiry successful)
    if (($bill['data']['status'] ?? '') === 'Sukses') {
        echo "Step 3: Pay Bill\n";
        echo str_repeat('-', 40) . "\n";

        $payment = $client->transaction()->pay(
            skuCode: 'pln',
            customerNo: $testCustomer,
            refId: $refId,
            testing: true,
        );

        echo "Payment Status: " . ($payment['data']['status'] ?? 'Unknown') . "\n";
        echo "SN: " . ($payment['data']['sn'] ?? 'N/A') . "\n";
    } else {
        echo "Step 3: Skipped (inquiry not successful)\n";
    }

} catch (ApiException $e) {
    echo "\nAPI Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . ($e->getErrorCode() ?? 'N/A') . "\n";

    if ($e->isInsufficientBalance()) {
        echo "Please top up your Digiflazz balance.\n";
    }
} catch (DigiflazzException $e) {
    echo "\nError: " . $e->getMessage() . "\n";
}

echo "\n=== Done ===\n";
