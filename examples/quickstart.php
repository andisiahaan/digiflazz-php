<?php

/**
 * Quick Start Example
 *
 * Basic usage of the Digiflazz PHP client.
 */

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Digiflazz\DigiflazzClient;
use AndiSiahaan\Digiflazz\Exceptions\ApiException;
use AndiSiahaan\Digiflazz\Exceptions\DigiflazzException;

// Create client (use environment variables)
// Set DIGIFLAZZ_USERNAME and DIGIFLAZZ_APIKEY before running
try {
    $client = DigiflazzClient::fromEnvironment();
} catch (\RuntimeException $e) {
    echo "Please set DIGIFLAZZ_USERNAME and DIGIFLAZZ_APIKEY environment variables.\n";
    echo "Example:\n";
    echo "  export DIGIFLAZZ_USERNAME='your_username'\n";
    echo "  export DIGIFLAZZ_APIKEY='your_api_key'\n";
    exit(1);
}

// Or create directly with credentials
// $client = new DigiflazzClient('username', 'api_key');

try {
    // Check balance
    echo "=== Checking Balance ===\n";
    $balance = $client->checkBalance();
    print_r($balance);

    // Get prepaid price list (limited output)
    echo "\n=== Prepaid Price List (first 3 items) ===\n";
    $priceList = $client->priceListPrepaid();
    $items = array_slice($priceList['data'] ?? [], 0, 3);
    foreach ($items as $item) {
        printf(
            "- %s (%s): Rp %s\n",
            $item['product_name'] ?? 'N/A',
            $item['buyer_sku_code'] ?? 'N/A',
            number_format($item['price'] ?? 0),
        );
    }

} catch (ApiException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getErrorCode() . "\n";
} catch (DigiflazzException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
