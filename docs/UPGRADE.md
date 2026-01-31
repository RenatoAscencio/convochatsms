# Upgrading to v4.0.0

## From v3.x

### Breaking Changes

#### 1. Removed `ConvoChatCache` and `RateLimiter`

These classes were never registered in the ServiceProvider or referenced by any service. If you imported them directly (unlikely), remove those imports.

```php
// Remove these if present:
use ConvoChat\LaravelSmsGateway\Cache\ConvoChatCache;       // deleted
use ConvoChat\LaravelSmsGateway\Security\RateLimiter;        // deleted
```

#### 2. Service constructor signature changed

All services now extend `BaseConvoChatService` with:

```php
public function __construct(?Client $client = null, ?array $config = null)
```

**If you use the Facade or Laravel DI (recommended):** no changes needed.

**If you instantiate services directly:**

```php
// v3.x — still works, no change needed:
$sms = new ConvoChatSmsService();

// v3.x with custom client — still works:
$sms = new ConvoChatSmsService($client, [
    'api_key' => 'key',
    'base_url' => 'https://sms.convo.chat/api',
    'timeout' => 30,
]);
```

#### 3. `validateRequiredParams` behavior change

`empty()` was replaced with strict `=== ''` check. This means `0` and `"0"` are now accepted as valid parameter values. Previously they would throw `InvalidArgumentException`.

### Non-Breaking Changes

- `ConvoChatManager` replaces anonymous class (Facade works identically)
- New `BaseConvoChatService` reduces duplication (no public API change)
- Console command `getGatewayRates()` fixed to `getRates()`

### Migration Steps

1. Run `composer update convochatsms/laravel-sms-whatsapp-gateway`
2. Remove any imports of `ConvoChatCache` or `RateLimiter` if present
3. Run your test suite to verify
