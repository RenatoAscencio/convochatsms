# Configuration

## Publishing the Config File

```bash
php artisan vendor:publish --tag=convochat-config
```

This creates `config/convochat.php` in your Laravel project.

## Environment Variables

Copy `.env.example` from the package root or add these to your `.env`:

```env
# Required
CONVOCHAT_API_KEY=your_api_key_here

# Optional (defaults shown)
CONVOCHAT_BASE_URL=https://sms.convo.chat/api
CONVOCHAT_TIMEOUT=30
CONVOCHAT_LOG_REQUESTS=false

# SMS defaults
CONVOCHAT_SMS_MODE=devices          # "devices" or "credits"
CONVOCHAT_SMS_PRIORITY=2            # 1=high, 2=normal
CONVOCHAT_SMS_DEVICE=               # Default device ID
CONVOCHAT_SMS_GATEWAY=              # Default gateway ID
CONVOCHAT_SMS_SIM=1                 # SIM slot (1 or 2)

# WhatsApp defaults
CONVOCHAT_WA_ACCOUNT=               # Default WhatsApp account ID
CONVOCHAT_WA_PRIORITY=2             # 1=high, 2=normal
```

## Config Reference

The full `config/convochat.php` file:

```php
return [
    'api_key'      => env('CONVOCHAT_API_KEY', ''),
    'base_url'     => env('CONVOCHAT_BASE_URL', 'https://sms.convo.chat/api'),
    'timeout'      => (int) env('CONVOCHAT_TIMEOUT', 30),
    'log_requests' => env('CONVOCHAT_LOG_REQUESTS', false),

    'sms' => [
        'default_mode'     => env('CONVOCHAT_SMS_MODE', 'devices'),
        'default_priority' => env('CONVOCHAT_SMS_PRIORITY', 2),
        'default_device'   => env('CONVOCHAT_SMS_DEVICE', null),
        'default_gateway'  => env('CONVOCHAT_SMS_GATEWAY', null),
        'default_sim'      => env('CONVOCHAT_SMS_SIM', 1),
    ],

    'whatsapp' => [
        'default_account'  => env('CONVOCHAT_WA_ACCOUNT', ''),
        'default_priority' => env('CONVOCHAT_WA_PRIORITY', 2),
    ],
];
```

## Logging

When `CONVOCHAT_LOG_REQUESTS=true`, all API calls are logged via Laravel's `Log` facade.

- Successful requests log at `info` level with endpoint and response status.
- Failed requests log at `error` level with full context.
- **API keys are always redacted** in log output (`[REDACTED]`).

## Custom HTTP Client

You can inject a custom Guzzle client for proxies, custom certificates, or testing:

```php
use GuzzleHttp\Client;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;

$client = new Client(['timeout' => 60, 'proxy' => 'tcp://proxy:8080']);

$sms = new ConvoChatSmsService($client, [
    'api_key'  => config('convochat.api_key'),
    'base_url' => config('convochat.base_url'),
    'timeout'  => 60,
]);
```
