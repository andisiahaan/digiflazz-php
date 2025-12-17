# Digiflazz PHP

[![Packagist Version](https://img.shields.io/packagist/v/andisiahaan/digiflazz-php.svg)](https://packagist.org/packages/andisiahaan/digiflazz-php)
[![PHP Version](https://img.shields.io/packagist/php-v/andisiahaan/digiflazz-php.svg)](https://packagist.org/packages/andisiahaan/digiflazz-php)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![CI](https://github.com/andisiahaan/digiflazz-php/actions/workflows/php.yml/badge.svg)](https://github.com/andisiahaan/digiflazz-php/actions)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)

Modern PHP client for the Digiflazz API. Supports prepaid topup, postpaid bill payment, PLN inquiry, and more.

## Requirements

- PHP 8.1 or higher
- ext-json
- Guzzle 7.0+

## Installation

```bash
composer require andisiahaan/digiflazz-php
```

## Quick Start

```php
use AndiSiahaan\Digiflazz\DigiflazzClient;

// Create client with credentials
$client = new DigiflazzClient('your_username', 'your_api_key');

// Check balance
$balance = $client->checkBalance();
print_r($balance);

// Or create from environment variables
$client = DigiflazzClient::fromEnvironment();
```

## Configuration

### Using Environment Variables

Set your credentials:

```bash
# Linux/macOS
export DIGIFLAZZ_USERNAME='your_username'
export DIGIFLAZZ_APIKEY='your_api_key'

# PowerShell
$env:DIGIFLAZZ_USERNAME='your_username'
$env:DIGIFLAZZ_APIKEY='your_api_key'
```

Then:

```php
$client = DigiflazzClient::fromEnvironment();
```

### Using Configuration Object

```php
use AndiSiahaan\Digiflazz\Config\Configuration;

$config = new Configuration(
    username: 'your_username',
    apiKey: 'your_api_key',
    timeout: 30.0,
    verifySsl: true,
);

$client = new DigiflazzClient($config);
```

## Usage

### Check Balance

```php
$balance = $client->checkBalance();
// or
$balance = $client->balance()->check();
```

### Price List

```php
// Prepaid products
$prepaid = $client->priceListPrepaid();

// Postpaid products
$postpaid = $client->priceListPasca();

// With filters
$filtered = $client->priceList()->prepaid([
    'category' => 'Pulsa',
    'brand' => 'TELKOMSEL',
]);

// Get all at once
$all = $client->priceList()->all();
```

### Prepaid Transaction (Topup)

```php
// Using array
$result = $client->topup([
    'buyer_sku_code' => 'xld10',
    'customer_no' => '087800001230',
    'ref_id' => 'unique-ref-123',
    'testing' => true, // Use sandbox
]);

// Using typed method
$result = $client->transaction()->topup(
    skuCode: 'xld10',
    customerNo: '087800001230',
    refId: 'unique-ref-123',
    testing: true,
);

// Generate unique ref_id
use AndiSiahaan\Digiflazz\Services\TransactionService;
$refId = TransactionService::generateRefId('TRX');
```

### Postpaid Bill (Pascabayar)

```php
// Step 1: Inquiry (check bill)
$inquiry = $client->inqPasca([
    'buyer_sku_code' => 'pln',
    'customer_no' => '530000000001',
    'ref_id' => 'ref-001',
    'testing' => true,
]);

// Step 2: Pay the bill
if ($inquiry['data']['status'] === 'Sukses') {
    $payment = $client->payPasca([
        'buyer_sku_code' => 'pln',
        'customer_no' => '530000000001',
        'ref_id' => 'ref-001',
        'testing' => true,
    ]);
}

// Check status
$status = $client->statusPasca([...]);
```

### PLN Inquiry

```php
// Quick inquiry
$pln = $client->inquiryPln('530000000001');

// Check if valid
$isValid = $client->pln()->isValidCustomer('530000000001');
```

### Deposit Request

```php
$deposit = $client->requestDeposit([
    'amount' => 1000000,
    'Bank' => 'BCA',
    'owner_name' => 'John Doe',
]);

// Or using typed method
$deposit = $client->deposit()->withdraw(1000000, 'BCA', 'John Doe');
```

## Error Handling

The library throws specific exceptions for different error types:

```php
use AndiSiahaan\Digiflazz\Exceptions\DigiflazzException;
use AndiSiahaan\Digiflazz\Exceptions\ApiException;
use AndiSiahaan\Digiflazz\Exceptions\HttpException;
use AndiSiahaan\Digiflazz\Exceptions\ValidationException;

try {
    $result = $client->topup([...]);
} catch (ValidationException $e) {
    // Missing or invalid parameters
    echo "Validation error: " . $e->getMessage();
    print_r($e->getErrors());
} catch (ApiException $e) {
    // API returned an error
    echo "API error: " . $e->getMessage();
    echo "Error code: " . $e->getErrorCode();
    
    if ($e->isInsufficientBalance()) {
        echo "Please top up your balance";
    }
    
    if ($e->isRetryable()) {
        // Safe to retry
    }
} catch (HttpException $e) {
    // Network or HTTP error
    echo "HTTP error: " . $e->getMessage();
    echo "Status code: " . $e->getStatusCode();
} catch (DigiflazzException $e) {
    // Base exception (catch all library exceptions)
    echo "Error: " . $e->getMessage();
}
```

## Service Classes

Direct access to service classes for more control:

```php
$balanceService = $client->balance();
$transactionService = $client->transaction();
$priceListService = $client->priceList();
$depositService = $client->deposit();
$plnService = $client->pln();
```

## Development

### Install Dependencies

```bash
composer install
```

### Run Tests

```bash
# All tests
composer test

# With coverage
composer test:coverage
```

### Static Analysis

```bash
composer analyse
```

### Code Style

```bash
# Check code style
composer cs-check

# Fix code style
composer cs-fix
```

## Architecture

```
src/
├── Config/
│   └── Configuration.php       # Immutable configuration
├── Contracts/
│   ├── ClientInterface.php     # Client contract
│   ├── HttpClientInterface.php # HTTP abstraction
│   └── ServiceInterface.php    # Service contract
├── Exceptions/
│   ├── DigiflazzException.php  # Base exception
│   ├── ApiException.php        # API errors
│   ├── AuthenticationException.php
│   ├── HttpException.php       # HTTP errors
│   └── ValidationException.php # Validation errors
├── Http/
│   └── GuzzleHttpClient.php    # Guzzle adapter
├── Services/
│   ├── Concerns/
│   │   └── ValidatesParameters.php
│   ├── AbstractService.php     # Base service
│   ├── BalanceService.php
│   ├── DepositService.php
│   ├── PlnService.php
│   ├── PriceListService.php
│   └── TransactionService.php
├── Support/
│   └── Signature.php           # Signature generator
└── DigiflazzClient.php         # Main client
```

## Notes

- **IP Whitelist**: Digiflazz requires your server IP to be whitelisted. Contact Digiflazz support.
- **Testing Mode**: Use `testing: true` for sandbox transactions.
- **Credentials**: Never commit credentials. Use environment variables or `.env` file.

## License

MIT - see [LICENSE](LICENSE) file.
