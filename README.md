# digiflazz-php

Digiflazz API client (personal library)

## Installation

1. Install dependencies with Composer:

```powershell
composer install
```

2. Require in your project (for local usage, you can use path repository or include via composer.json):

```php
require 'vendor/autoload.php';

use Digiflazz\DigiflazzClient;

$client = new DigiflazzClient('username', 'api_key');
$response = $client->balance();
print_r($response);
```

## How to push to GitHub

1. Create a new empty repository on GitHub (https://github.com/new). Note the repository URL.
2. From your project folder run (PowerShell):

```powershell
git remote add origin https://github.com/<your-username>/digiflazz-php.git
git branch -M main
git push -u origin main
```

If you use HTTPS and have 2FA enabled, create a personal access token and use it as your password when prompted, or set up SSH keys and use the SSH remote URL.

## Notes

- This library is intentionally minimal for personal use. Extend methods as needed to cover the Digiflazz API endpoints.
- Tests use PHPUnit (dev dependency).
