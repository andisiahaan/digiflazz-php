<?php

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Digiflazz\DigiflazzClient;

// Credentials: prefer env vars (replace or export before running)
$username = getenv('DIGIFLAZZ_USERNAME') ?: 'your_username';
$apiKey = getenv('DIGIFLAZZ_APIKEY') ?: 'your_api_key';

$client = new DigiflazzClient($username, $apiKey);

$customers = [
    '530000000001',
    '530000000002',
    '530000000003',
    '530000000006',
];

foreach ($customers as $i => $customerNo) {
    $refId = 'paypasca-' . time() . '-' . ($i + 1);
    $payload = [
        'buyer_sku_code' => 'pln',
        'customer_no' => $customerNo,
        'ref_id' => $refId,
        'testing' => true,
    ];

    echo "\n=== pay-pasca Test for $customerNo (ref: $refId) ===\n";
    try {
        $resp = $client->payPasca($payload);
        echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } catch (Exception $e) {
        echo "Error (" . get_class($e) . "): " . $e->getMessage() . "\n";
        if (method_exists($e, 'getResponse')) {
            $r = $e->getResponse();
            if ($r) {
                echo "HTTP status: " . $r->getStatusCode() . "\n";
                echo "HTTP body: " . $r->getBody()->getContents() . "\n";
            }
        }
        echo "Payload sent: " . json_encode($payload, JSON_UNESCAPED_SLASHES) . "\n";
    }
}

echo "\npay-pasca tests done.\n";
