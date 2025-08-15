````markdown
# digiflazz-php

Digiflazz API client (personal library)

## Installation

1. Install dependencies with Composer:

```powershell
composer install
```

2. Require in your project:

```php
require 'vendor/autoload.php';

use AndiSiahaan\Digiflazz\DigiflazzClient;

$client = new DigiflazzClient('username', 'api_key');
$response = $client->balance();
print_r($response);
```

## Configuration

Store credentials in environment variables (recommended) or export them before running the examples:

PowerShell (one-off run):

```powershell
$env:DIGIFLAZZ_USERNAME='your_username'; $env:DIGIFLAZZ_APIKEY='your_api_key'; php .\examples\integration-prepaid-test.php
```

Or create a local `.env` and load it in your environment (do not commit `.env`).

## Usage / Examples

This repository includes example integration scripts under the `examples/` folder:

- `examples/integration-prepaid-test.php` — runs several prepaid test-cases.
- `examples/integration-postpaid-test.php` — runs PLN inquiry + payment test-cases (inq-pasca / pay-pasca).
- `examples/integration-pay-pasca-only.php` — runs pay-pasca for a set of customer numbers.

Run any example with the environment variables set as shown above. Example output will be printed to the console.

### Example: prepaid quickstart

```php
require 'vendor/autoload.php';
use AndiSiahaan\Digiflazz\DigiflazzClient;

$client = new DigiflazzClient(getenv('DIGIFLAZZ_USERNAME'), getenv('DIGIFLAZZ_APIKEY'));
$resp = $client->topup([
	'buyer_sku_code' => 'xld10',
	'customer_no' => '087800001230',
	'ref_id' => 'test-123',
	'testing' => true,
]);
print_r($resp);
```

## Running tests

Unit tests use PHPUnit. Run them with:

```powershell
vendor\bin\phpunit --testdox
```

Note: the tests are unit/mocked tests and do not require real credentials.

## Notes & Security

- Do not commit API keys or `.env` files into version control.
- Integration tests that call the real API require your IP to be whitelisted by Digiflazz and valid test credentials.
- This library is intentionally minimal and tailored for personal use; extend services as needed.

## Contributing

Open a PR or add issues for improvements. If you plan to run automated integration tests in CI, you must configure secrets and ensure the CI runner IP is whitelisted by the API provider.

````
