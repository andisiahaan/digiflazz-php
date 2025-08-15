<?php

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Digiflazz\DigiflazzClient;

$client = new DigiflazzClient('username', 'api_key');

// convenience wrapper
try {
    $balance = $client->checkBalance();
    print_r($balance);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
