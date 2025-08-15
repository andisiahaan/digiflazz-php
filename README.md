````markdown
# digiflazz-php

[![Packagist](https://img.shields.io/packagist/v/andisiahaan/digiflazz-php.svg)](https://packagist.org/packages/andisiahaan/digiflazz-php)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![CI](https://github.com/andisiahaan/digiflazz-php/actions/workflows/php.yml/badge.svg)](https://github.com/andisiahaan/digiflazz-php/actions)

## Quick install

Install the package via Composer:
# digiflazz-php

[![Packagist](https://img.shields.io/packagist/v/andisiahaan/digiflazz-php.svg)](https://packagist.org/packages/andisiahaan/digiflazz-php)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![CI](https://github.com/andisiahaan/digiflazz-php/actions/workflows/php.yml/badge.svg)](https://github.com/andisiahaan/digiflazz-php/actions)

## Quick install

Install the package via Composer:

```bash
composer require andisiahaan/digiflazz-php
```

Lightweight PHP client for the Digiflazz API. This repository provides a small wrapper around Digiflazz endpoints (balance, price-list, transaction/topup, deposit, PLN inquiry/payment) and example integration scripts.

Supported PHP versions: >=7.4

Features
- Simple PSR-4 autoloaded client (AndiSiahaan\Digiflazz\DigiflazzClient)
- Service classes for each functional area (Balance, PriceList, Transaction, Deposit, PLN)
- Example scripts to run prepaid and postpaid test-cases
- Unit tests (PHPUnit) with mocked HTTP client

## Installation

Install dependencies with Composer:

```powershell
composer install
```

When the package is published, consumers will be able to install with:

```powershell
composer require andisiahaan/digiflazz-php
```

## Configuration

Provide your Digiflazz credentials via environment variables:

```powershell
$env:DIGIFLAZZ_USERNAME='your_username'
$env:DIGIFLAZZ_APIKEY='your_api_key'
```

Do not commit real credentials into the repository. Use `.env` locally and add it to `.gitignore`.

## Quick usage

Example: create client and check balance

```php
require 'vendor/autoload.php';
use AndiSiahaan\Digiflazz\DigiflazzClient;

$client = new DigiflazzClient(getenv('DIGIFLAZZ_USERNAME'), getenv('DIGIFLAZZ_APIKEY'));
$balance = $client->checkBalance();
print_r($balance);
```

Prepaid topup example

```php
$resp = $client->topup([
	'buyer_sku_code' => 'xld10',
	'customer_no' => '087800001230',
	'ref_id' => 'my-ref-123',
	'testing' => true,
]);
print_r($resp);
```

Postpaid (PLN) inquiry + payment

```php
$inq = $client->inqPasca([
	'buyer_sku_code' => 'pln',
	'customer_no' => '530000000001',
	'ref_id' => 'ref-001',
	'testing' => true,
]);
print_r($inq);

// if payable, call payPasca()
$pay = $client->payPasca([
	'buyer_sku_code' => 'pln',
	'customer_no' => '530000000001',
	'ref_id' => 'ref-001',
	'testing' => true,
]);
print_r($pay);
```

## Examples

See `examples/` for small scripts that exercise prepaid and postpaid flows:

- `examples/integration-prepaid-test.php` — multiple prepaid test-cases
- `examples/integration-postpaid-test.php` — PLN inquiry + payment flows
- `examples/integration-pay-pasca-only.php` — pay-pasca only runner

Run an example (PowerShell):

```powershell
$env:DIGIFLAZZ_USERNAME='your_username'; $env:DIGIFLAZZ_APIKEY='your_api_key'; php .\examples\integration-prepaid-test.php
```

## Running tests

Unit tests use PHPUnit and are mocked to avoid calling the real API. Run:

```powershell
vendor\bin\phpunit --testdox
```

## Notes & troubleshooting

- IP whitelist: Digiflazz may require your public IP to be whitelisted for integration tests. If you see an error about IP or permission, contact Digiflazz support and provide your public IP.
- Keep credentials out of version control. Use `.env` + `.gitignore`.
- This client is intentionally minimal — extend or submit PRs for new endpoints.

## License

MIT — see `LICENSE` file.
