<?php

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Digiflazz\DigiflazzClient;

// Credentials provided by user (use environment variables or replace with your credentials)
$username = getenv('DIGIFLAZZ_USERNAME') ?: 'your_username';
$apiKey = getenv('DIGIFLAZZ_APIKEY') ?: 'your_api_key';

$client = new DigiflazzClient($username, $apiKey);

$tests = [
    ['buyer_sku_code' => 'xld10', 'customer_no' => '087800001230', 'expect' => 'Sukses'],
    ['buyer_sku_code' => 'xld10', 'customer_no' => '087800001232', 'expect' => 'Gagal'],
    ['buyer_sku_code' => 'xld10', 'customer_no' => '087800001233', 'expect' => 'Pending -> Callback Sukses'],
    ['buyer_sku_code' => 'xld10', 'customer_no' => '087800001234', 'expect' => 'Pending -> Callback Gagal'],
];

foreach ($tests as $i => $t) {
    $refId = 'test-' . time() . '-' . ($i + 1);
    $payload = [
        'buyer_sku_code' => $t['buyer_sku_code'],
        'customer_no' => $t['customer_no'],
        'ref_id' => $refId,
        // use testing flag to avoid real charge if supported
        'testing' => true,
    ];

    echo "\n=== Test #" . ($i + 1) . " (expect: " . $t['expect'] . ") ===\n";
    try {
        $resp = $client->topup($payload);
        echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } catch (Exception $e) {
        echo "Error (" . get_class($e) . "): " . $e->getMessage() . "\n";

        // If this was an HTTP error with response body, print it for debugging
        if (method_exists($e, 'getResponse')) {
            $resp = $e->getResponse();
            if ($resp) {
                echo "HTTP status: " . $resp->getStatusCode() . "\n";
                // read full stream contents
                $body = $resp->getBody()->getContents();
                echo "HTTP body: " . $body . "\n";
            }
        }

        // Print the JSON payload that was sent
        echo "Payload sent: " . json_encode($payload, JSON_UNESCAPED_SLASHES) . "\n";
    }
}

echo "\nDone.\n";
