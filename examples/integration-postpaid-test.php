<?php

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Digiflazz\DigiflazzClient;

// Credentials: will prefer env vars if set (replace with your credentials or use env vars)
$username = getenv('DIGIFLAZZ_USERNAME') ?: 'your_username';
$apiKey = getenv('DIGIFLAZZ_APIKEY') ?: 'your_api_key';

$client = new DigiflazzClient($username, $apiKey);

$tests = [
    // PLN inquiry/payment test cases (from user)
    ['customer_no' => '530000000001', 'expect' => 'Sukses (1 Tagihan)'],
    ['customer_no' => '530000000002', 'expect' => 'Sukses (2 Tagihan)'],
    ['customer_no' => '530000000003', 'expect' => 'Inquiry Gagal'],
    ['customer_no' => '530000000006', 'expect' => 'Pembayaran Gagal'],
];

foreach ($tests as $i => $t) {
    $refId = 'postpaid-' . time() . '-' . ($i + 1);

    $inqPayload = [
        'buyer_sku_code' => 'pln',
        'customer_no' => $t['customer_no'],
        'ref_id' => $refId,
        'testing' => true,
    ];

    echo "\n=== PLN Inquiry Test #" . ($i + 1) . " (expect: " . $t['expect'] . ") ===\n";
    try {
        $resp = $client->inqPasca($inqPayload);
        echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } catch (Exception $e) {
        echo "Error during inquiry (" . get_class($e) . "): " . $e->getMessage() . "\n";
        if (method_exists($e, 'getResponse')) {
            $resp = $e->getResponse();
            if ($resp) {
                echo "HTTP status: " . $resp->getStatusCode() . "\n";
                echo "HTTP body: " . $resp->getBody()->getContents() . "\n";
            }
        }
        echo "Payload sent (inq): " . json_encode($inqPayload, JSON_UNESCAPED_SLASHES) . "\n";
        continue;
    }

    // If inquiry returned success and there is an amount, attempt payment (pay-pasca)
    $shouldPay = isset($resp['data']) && (!empty($resp['data']['amount']) || !empty($resp['data']['price']));

    if ($shouldPay) {
        $payPayload = [
            'buyer_sku_code' => 'pln',
            'customer_no' => $t['customer_no'],
            'ref_id' => $refId,
            'testing' => true,
        ];

        echo "\nAttempting payment for ref_id: $refId\n";
        try {
            $payResp = $client->payPasca($payPayload);
            echo json_encode($payResp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        } catch (Exception $e) {
            echo "Error during payment (" . get_class($e) . "): " . $e->getMessage() . "\n";
            if (method_exists($e, 'getResponse')) {
                $resp = $e->getResponse();
                if ($resp) {
                    echo "HTTP status: " . $resp->getStatusCode() . "\n";
                    echo "HTTP body: " . $resp->getBody()->getContents() . "\n";
                }
            }
            echo "Payload sent (pay): " . json_encode($payPayload, JSON_UNESCAPED_SLASHES) . "\n";
        }
    } else {
        echo "No payable amount found in inquiry response; skipping payment.\n";
    }
}

echo "\nPostpaid tests done.\n";
