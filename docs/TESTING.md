# Testing

## Requirements

- PHP 8.1+
- Composer dev dependencies installed (`composer install`)

## Running Tests

```bash
# All tests
composer test

# With coverage (requires Xdebug or PCOV)
./vendor/bin/phpunit --coverage-text

# Specific test file
./vendor/bin/phpunit tests/Unit/ConvoChatSmsServiceTest.php
```

## Static Analysis

```bash
# PHPStan level 8
composer analyse

# PHP CS Fixer (check only)
composer format:check

# PHP CS Fixer (auto-fix)
composer format
```

## Full Quality Gate

```bash
composer quality
```

This runs `test` + `analyse` + `format:check` sequentially.

## Test Structure

```
tests/
  Unit/
    ConvoChatSmsServiceTest.php        # SMS service methods
    ConvoChatWhatsAppServiceTest.php   # WhatsApp service methods
    ConvoChatContactsServiceTest.php   # Contacts service methods
    ConvoChatOtpServiceTest.php        # OTP service methods
    ConvoChatUssdServiceTest.php       # USSD service methods
    ConvoChatConfigurationTest.php     # Config validation
    ConvoChatConstantsTest.php         # Service constants
    SendBulkSmsJobTest.php             # Bulk SMS job
  Feature/
    ConvoChatIntegrationTest.php       # End-to-end via Facade
    FacadeTest.php                     # Facade resolution
    ConvoChatTimeoutTest.php           # Timeout handling
    ConsumerSmokeTest.php              # Consumer integration
```

## Writing Tests

All HTTP calls are mocked with Guzzle's `MockHandler`. Example:

```php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

$mock = new MockHandler([
    new Response(200, [], json_encode(['status' => 200, 'data' => []])),
]);

$client = new Client(['handler' => HandlerStack::create($mock)]);

$service = new ConvoChatSmsService($client, [
    'api_key'  => 'test_key',
    'base_url' => 'https://sms.convo.chat/api',
    'timeout'  => 30,
]);

$result = $service->getCredits();
$this->assertEquals(200, $result['status']);
```

## CI

GitHub Actions runs the full matrix (PHP 8.1-8.4 x Laravel 10-12) plus a separate code-quality job (PHPStan + CS Fixer) and a coverage job.
