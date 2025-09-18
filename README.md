# ConvoChat Laravel SMS Gateway

[![Latest Version](https://img.shields.io/packagist/v/renatoascencio/convochatsms.svg?style=flat-square)](https://packagist.org/packages/renatoascencio/convochatsms)
[![Total Downloads](https://img.shields.io/packagist/dt/renatoascencio/convochatsms.svg?style=flat-square)](https://packagist.org/packages/renatoascencio/convochatsms)
[![License](https://img.shields.io/packagist/l/renatoascencio/convochatsms.svg?style=flat-square)](https://packagist.org/packages/renatoascencio/convochatsms)
[![Tests](https://github.com/RenatoAscencio/convochatsms/actions/workflows/tests.yml/badge.svg)](https://github.com/RenatoAscencio/convochatsms/actions/workflows/tests.yml)
[![Coverage](https://codecov.io/gh/RenatoAscencio/convochatsms/branch/main/graph/badge.svg)](https://codecov.io/gh/RenatoAscencio/convochatsms)
[![PHPStan Level 8](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://phpstan.org/)
[![PHP Versions](https://img.shields.io/badge/PHP-8.0%20%7C%208.1%20%7C%208.2%20%7C%208.3%20%7C%208.4-blue.svg?style=flat-square)](https://php.net/)
[![Laravel Versions](https://img.shields.io/badge/Laravel-8%20%7C%209%20%7C%2010%20%7C%2011-red.svg?style=flat-square)](https://laravel.com/)

Un paquete Laravel moderno y robusto para integración completa con ConvoChat API para envío de SMS y WhatsApp con soporte para múltiples modos, configuración avanzada y monitoreo.

## 🚀 Instalación

```bash
composer require renatoascencio/convochatsms
```

### Publicar configuración

```bash
php artisan vendor:publish --tag=convochat-config
```

## ⚙️ Configuración

Agrega tu API Key en el archivo `.env`:

```env
CONVOCHAT_API_KEY=tu_api_key_aqui
CONVOCHAT_BASE_URL=https://sms.convo.chat/api

# Configuración SMS opcional
CONVOCHAT_SMS_MODE=devices
CONVOCHAT_SMS_PRIORITY=2
CONVOCHAT_SMS_DEVICE=tu_device_id
CONVOCHAT_SMS_SIM=1

# Configuración WhatsApp opcional
CONVOCHAT_WA_ACCOUNT=tu_account_id
CONVOCHAT_WA_PRIORITY=2

# Logging opcional
CONVOCHAT_LOG_REQUESTS=false
```

**Obtén tu API Key:** https://sms.convo.chat/dashboard/tools/keys

## 📱 Uso - SMS

### Enviar SMS básico

```php
use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

// Con dispositivo Android
$result = ConvoChat::sms()->sendSmsWithDevice(
    phone: '+573001234567',
    message: '¡Hola desde ConvoChat!',
    deviceId: 'abc123'
);

// Con créditos
$result = ConvoChat::sms()->sendSmsWithCredits(
    phone: '+573001234567',
    message: '¡Hola desde ConvoChat!',
    gatewayId: 'gateway123'
);
```

### Opciones avanzadas SMS

```php
$result = ConvoChat::sms()->sendSmsWithDevice(
    phone: '+573001234567',
    message: 'Mensaje importante',
    deviceId: 'abc123',
    options: [
        'sim' => 1,           // SIM 1 o 2
        'priority' => 1       // 1 = alta, 2 = normal
    ]
);
```

### Obtener información SMS

```php
// Dispositivos conectados
$devices = ConvoChat::sms()->getDevices();

// Créditos disponibles
$credits = ConvoChat::sms()->getCredits();

// Gateways y tarifas
$rates = ConvoChat::sms()->getGatewayRates();

// Información de suscripción
$subscription = ConvoChat::sms()->getSubscription();
```

## 📞 Uso - WhatsApp

### Enviar mensajes de texto

```php
use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

$result = ConvoChat::whatsapp()->sendText(
    account: 'wa_account_id',
    recipient: '+573001234567',
    message: '¡Hola por WhatsApp!'
);
```

### Enviar multimedia

```php
// Imagen/Video/Audio
$result = ConvoChat::whatsapp()->sendMedia(
    account: 'wa_account_id',
    recipient: '+573001234567',
    message: 'Mira esta imagen',
    mediaUrl: 'https://ejemplo.com/imagen.jpg',
    mediaType: 'image'
);

// Documento
$result = ConvoChat::whatsapp()->sendDocument(
    account: 'wa_account_id',
    recipient: '+573001234567',
    message: 'Te envío el reporte',
    documentUrl: 'https://ejemplo.com/reporte.pdf',
    documentName: 'Reporte-Mensual.pdf',
    documentType: 'pdf'
);
```

### Gestión de cuentas WhatsApp

```php
// Obtener servidores disponibles
$servers = ConvoChat::whatsapp()->getWhatsAppServers();

// Vincular nueva cuenta
$linkData = ConvoChat::whatsapp()->linkWhatsAppAccount(serverId: 1);
// Retorna QR code para escanear

// Re-vincular cuenta existente
$result = ConvoChat::whatsapp()->relinkWhatsAppAccount(
    serverId: 1,
    uniqueId: 'unique_account_id'
);

// Validar número
$validation = ConvoChat::whatsapp()->validateWhatsAppNumber(
    accountId: 'wa_account_id',
    phone: '+573001234567'
);

// Obtener cuentas vinculadas
$accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
```

### Casos de uso avanzados de WhatsApp

#### Envío de mensajes con botones

```php
// Mensaje con botones de respuesta rápida
$result = ConvoChat::whatsapp()->sendButtonMessage(
    account: 'wa_account_id',
    recipient: '+573001234567',
    message: '¿Deseas confirmar tu cita para mañana?',
    buttons: [
        ['id' => 'confirm', 'title' => 'Confirmar'],
        ['id' => 'reschedule', 'title' => 'Reprogramar'],
        ['id' => 'cancel', 'title' => 'Cancelar']
    ]
);
```

#### Envío masivo con plantillas

```php
// Envío de mensajes promocionales usando plantillas aprobadas
$contacts = [
    '+573001234567' => ['name' => 'Juan', 'discount' => '20%'],
    '+573007654321' => ['name' => 'María', 'discount' => '15%']
];

foreach ($contacts as $phone => $data) {
    $result = ConvoChat::whatsapp()->sendTemplate(
        account: 'wa_account_id',
        recipient: $phone,
        templateName: 'promocion_personalizada',
        templateParams: [
            $data['name'],
            $data['discount']
        ]
    );
}
```

#### Envío de ubicación

```php
$result = ConvoChat::whatsapp()->sendLocation(
    account: 'wa_account_id',
    recipient: '+573001234567',
    latitude: 4.6097100,
    longitude: -74.0817500,
    address: 'Bogotá, Colombia',
    name: 'Nuestra oficina principal'
);
```

#### Gestión de estado de mensajes

```php
// Verificar estado de entrega
$messageStatus = ConvoChat::whatsapp()->getMessageStatus(
    messageId: 'msg_123456789'
);

// Marcar mensaje como leído
$result = ConvoChat::whatsapp()->markAsRead(
    account: 'wa_account_id',
    messageId: 'msg_123456789'
);

// Obtener historial de conversación
$conversation = ConvoChat::whatsapp()->getConversationHistory(
    account: 'wa_account_id',
    recipient: '+573001234567',
    limit: 50
);
```

#### WhatsApp Business API Features

```php
// Configurar perfil de negocio
$result = ConvoChat::whatsapp()->updateBusinessProfile(
    account: 'wa_account_id',
    data: [
        'description' => 'Empresa líder en tecnología',
        'email' => 'contacto@empresa.com',
        'website' => 'https://empresa.com',
        'address' => 'Calle 123 #45-67, Bogotá',
        'category' => 'SOFTWARE'
    ]
);

// Crear catálogo de productos
$result = ConvoChat::whatsapp()->createProduct(
    account: 'wa_account_id',
    data: [
        'name' => 'Producto Premium',
        'description' => 'Descripción detallada del producto',
        'price' => 99900,
        'currency' => 'COP',
        'image_url' => 'https://ejemplo.com/producto.jpg',
        'category' => 'ELECTRONICS'
    ]
);

// Enviar catálogo a cliente
$result = ConvoChat::whatsapp()->sendCatalog(
    account: 'wa_account_id',
    recipient: '+573001234567',
    message: 'Mira nuestro catálogo de productos'
);
```

## 🔧 Uso sin Facade

### Inyección de dependencias básica

```php
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;

// SMS
$smsService = app(ConvoChatSmsService::class);
$result = $smsService->sendSmsWithDevice('+573001234567', 'Mensaje', 'device123');

// WhatsApp
$waService = app(ConvoChatWhatsAppService::class);
$result = $waService->sendText('account123', '+573001234567', 'Mensaje WA');
```

### Inyección de dependencias avanzada

#### En Controladores

```php
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;

class NotificationController extends Controller
{
    public function __construct(
        private ConvoChatSmsService $smsService,
        private ConvoChatWhatsAppService $whatsappService
    ) {}

    public function sendMultiChannelNotification(Request $request)
    {
        $phone = $request->phone;
        $message = $request->message;

        // Intentar primero por WhatsApp
        try {
            $result = $this->whatsappService->sendText(
                config('convochat.whatsapp.default_account'),
                $phone,
                $message
            );

            if ($result['status'] === 'success') {
                return response()->json(['channel' => 'whatsapp', 'result' => $result]);
            }
        } catch (Exception $e) {
            logger('WhatsApp failed, trying SMS: ' . $e->getMessage());
        }

        // Fallback a SMS si WhatsApp falla
        try {
            $result = $this->smsService->sendSmsWithCredits($phone, $message);
            return response()->json(['channel' => 'sms', 'result' => $result]);
        } catch (Exception $e) {
            return response()->json(['error' => 'All channels failed'], 500);
        }
    }
}
```

#### En Service Providers

```php
use Illuminate\Support\ServiceProvider;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Configuración personalizada del servicio
        $this->app->singleton('convochat.custom', function ($app) {
            return new ConvoChatSmsService(
                apiKey: config('convochat.api_key'),
                baseUrl: config('convochat.base_url'),
                timeout: 60, // timeout personalizado
                retries: 3   // reintentos personalizados
            );
        });
    }
}
```

#### En Jobs/Queues

```php
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;

class SendBulkSmsJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public function __construct(
        private array $recipients,
        private string $message
    ) {}

    public function handle(ConvoChatSmsService $smsService)
    {
        foreach ($this->recipients as $phone) {
            try {
                $result = $smsService->sendSmsWithCredits($phone, $this->message);

                if ($result['status'] !== 'success') {
                    // Re-encolar mensaje fallido
                    dispatch(new SendBulkSmsJob([$phone], $this->message))
                        ->delay(now()->addMinutes(5));
                }
            } catch (Exception $e) {
                logger("Failed to send SMS to {$phone}: " . $e->getMessage());
                $this->fail($e);
            }
        }
    }
}
```

#### Patrón Repository

```php
interface NotificationRepositoryInterface
{
    public function sendSms(string $phone, string $message): array;
    public function sendWhatsApp(string $phone, string $message): array;
}

class ConvoChatNotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(
        private ConvoChatSmsService $smsService,
        private ConvoChatWhatsAppService $whatsappService
    ) {}

    public function sendSms(string $phone, string $message): array
    {
        return $this->smsService->sendSmsWithCredits($phone, $message);
    }

    public function sendWhatsApp(string $phone, string $message): array
    {
        return $this->whatsappService->sendText(
            config('convochat.whatsapp.default_account'),
            $phone,
            $message
        );
    }
}

// En AppServiceProvider
public function register()
{
    $this->app->bind(
        NotificationRepositoryInterface::class,
        ConvoChatNotificationRepository::class
    );
}
```

## 📝 Notificaciones Laravel

Puedes crear notificaciones personalizadas:

```php
use Illuminate\Notifications\Notification;
use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

class OrderNotification extends Notification
{
    public function via($notifiable)
    {
        return ['convochat'];
    }

    public function toConvoChat($notifiable)
    {
        // Para SMS
        ConvoChat::sms()->sendSmsWithDevice(
            $notifiable->phone,
            'Tu pedido #123 ha sido confirmado',
            config('convochat.sms.default_device')
        );

        // Para WhatsApp
        ConvoChat::whatsapp()->sendText(
            config('convochat.whatsapp.default_account'),
            $notifiable->phone,
            'Tu pedido #123 ha sido confirmado ✅'
        );
    }
}
```

## ⚡ Ejemplos de Controlador

```php
use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

class NotificationController extends Controller
{
    public function sendWelcomeSms(Request $request)
    {
        try {
            $result = ConvoChat::sms()->sendSmsWithCredits(
                phone: $request->phone,
                message: "¡Bienvenido a nuestra plataforma! 🎉"
            );

            return response()->json(['success' => true, 'data' => $result]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendWhatsAppPromo(Request $request)
    {
        try {
            $result = ConvoChat::whatsapp()->sendText(
                account: config('convochat.whatsapp.default_account'),
                recipient: $request->phone,
                message: "🔥 Oferta especial solo por hoy: 50% de descuento"
            );

            return response()->json(['success' => true, 'data' => $result]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

## 🛠️ Manejo de Errores

### Manejo básico de errores

```php
try {
    $result = ConvoChat::sms()->sendSmsWithDevice('+573001234567', 'Test', 'device123');

    if (isset($result['status']) && $result['status'] === 'success') {
        // Mensaje enviado exitosamente
        logger('SMS enviado: ' . json_encode($result));
    } else {
        // Error en la respuesta de la API
        logger('Error API: ' . json_encode($result));
    }
} catch (Exception $e) {
    // Error de conexión o configuración
    logger('Error ConvoChat: ' . $e->getMessage());
}
```

### Patrones avanzados de manejo de errores

#### Manejo granular de errores por tipo

```php
use ConvoChat\LaravelSmsGateway\Exceptions\ConvoChatApiException;
use ConvoChat\LaravelSmsGateway\Exceptions\ConvoChatConnectionException;
use ConvoChat\LaravelSmsGateway\Exceptions\ConvoChatValidationException;

try {
    $result = ConvoChat::sms()->sendSmsWithDevice($phone, $message, $deviceId);

    // Verificar respuesta exitosa
    if ($result['status'] === 'success') {
        return ['success' => true, 'data' => $result];
    }

    // Manejar errores específicos de la API
    return $this->handleApiError($result);

} catch (ConvoChatValidationException $e) {
    // Errores de validación (datos inválidos)
    return [
        'success' => false,
        'error' => 'validation',
        'message' => 'Datos inválidos: ' . $e->getMessage(),
        'details' => $e->getValidationErrors()
    ];

} catch (ConvoChatApiException $e) {
    // Errores específicos de la API ConvoChat
    return [
        'success' => false,
        'error' => 'api',
        'message' => 'Error de API: ' . $e->getMessage(),
        'code' => $e->getApiErrorCode()
    ];

} catch (ConvoChatConnectionException $e) {
    // Errores de conexión
    return [
        'success' => false,
        'error' => 'connection',
        'message' => 'Error de conexión: ' . $e->getMessage(),
        'retry_after' => 60
    ];

} catch (Exception $e) {
    // Otros errores no esperados
    logger()->error('ConvoChat unexpected error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);

    return [
        'success' => false,
        'error' => 'unexpected',
        'message' => 'Error interno del servidor'
    ];
}
```

#### Sistema de reintentos automáticos

```php
class ConvoChatRetryService
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAYS = [1, 3, 5]; // segundos

    public function sendWithRetry(callable $sendFunction, array $params): array
    {
        $lastException = null;

        for ($attempt = 0; $attempt < self::MAX_RETRIES; $attempt++) {
            try {
                $result = call_user_func_array($sendFunction, $params);

                if ($result['status'] === 'success') {
                    return $result;
                }

                // Si es error recuperable, intentar de nuevo
                if ($this->isRetryableError($result)) {
                    $this->waitBeforeRetry($attempt);
                    continue;
                }

                // Error no recuperable, no reintentar
                return $result;

            } catch (ConvoChatConnectionException $e) {
                $lastException = $e;
                if ($attempt < self::MAX_RETRIES - 1) {
                    $this->waitBeforeRetry($attempt);
                    continue;
                }
            } catch (Exception $e) {
                // Error no recuperable
                throw $e;
            }
        }

        throw $lastException ?? new Exception('Máximo de reintentos alcanzado');
    }

    private function isRetryableError(array $result): bool
    {
        $retryableCodes = ['rate_limit', 'temporary_error', 'server_busy'];
        return in_array($result['error_code'] ?? '', $retryableCodes);
    }

    private function waitBeforeRetry(int $attempt): void
    {
        if (isset(self::RETRY_DELAYS[$attempt])) {
            sleep(self::RETRY_DELAYS[$attempt]);
        }
    }
}

// Uso del servicio de reintentos
$retryService = new ConvoChatRetryService();

$result = $retryService->sendWithRetry(
    fn() => ConvoChat::sms()->sendSmsWithCredits($phone, $message),
    []
);
```

#### Circuit Breaker Pattern

```php
class ConvoChatCircuitBreaker
{
    private const FAILURE_THRESHOLD = 5;
    private const RECOVERY_TIMEOUT = 300; // 5 minutos

    private string $state = 'closed'; // closed, open, half-open
    private int $failureCount = 0;
    private ?int $lastFailureTime = null;

    public function call(callable $function, array $params)
    {
        if ($this->state === 'open') {
            if ($this->shouldAttemptReset()) {
                $this->state = 'half-open';
            } else {
                throw new Exception('Circuit breaker is OPEN');
            }
        }

        try {
            $result = call_user_func_array($function, $params);
            $this->onSuccess();
            return $result;
        } catch (Exception $e) {
            $this->onFailure();
            throw $e;
        }
    }

    private function onSuccess(): void
    {
        $this->failureCount = 0;
        $this->state = 'closed';
    }

    private function onFailure(): void
    {
        $this->failureCount++;
        $this->lastFailureTime = time();

        if ($this->failureCount >= self::FAILURE_THRESHOLD) {
            $this->state = 'open';
        }
    }

    private function shouldAttemptReset(): bool
    {
        return $this->lastFailureTime !== null &&
               (time() - $this->lastFailureTime) >= self::RECOVERY_TIMEOUT;
    }
}
```

#### Logging estructurado de errores

```php
class ConvoChatErrorLogger
{
    public static function logError(Exception $e, array $context = []): void
    {
        $errorData = [
            'error_type' => get_class($e),
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'timestamp' => now()->toISOString(),
            'context' => $context
        ];

        // Log específico por tipo de error
        if ($e instanceof ConvoChatApiException) {
            $errorData['api_error_code'] = $e->getApiErrorCode();
            $errorData['api_response'] = $e->getApiResponse();
        }

        if ($e instanceof ConvoChatConnectionException) {
            $errorData['connection_timeout'] = $e->getTimeout();
            $errorData['endpoint'] = $e->getEndpoint();
        }

        logger()->error('ConvoChat Error', $errorData);

        // Enviar a servicio de monitoreo si está configurado
        if (config('convochat.monitoring.enabled')) {
            self::sendToMonitoring($errorData);
        }
    }

    private static function sendToMonitoring(array $errorData): void
    {
        // Integración con Sentry, Bugsnag, etc.
        if (app()->bound('sentry')) {
            app('sentry')->captureException(
                new Exception($errorData['error_message']),
                $errorData
            );
        }
    }
}

// Uso en try-catch
try {
    $result = ConvoChat::sms()->sendSmsWithDevice($phone, $message, $deviceId);
} catch (Exception $e) {
    ConvoChatErrorLogger::logError($e, [
        'phone' => $phone,
        'device_id' => $deviceId,
        'message_length' => strlen($message)
    ]);

    throw $e;
}
```

## 📊 Logging

Para debug, habilita logs en `.env`:

```env
CONVOCHAT_LOG_REQUESTS=true
```

Los logs aparecerán en `storage/logs/laravel.log`.

## 🎯 Casos de Uso Comunes

### Sistema de OTP/Verificación

```php
class OTPService
{
    public function sendOTP(string $phone): string
    {
        $otp = rand(100000, 999999);

        ConvoChat::sms()->sendSmsWithCredits(
            phone: $phone,
            message: "Tu código de verificación es: {$otp}"
        );

        // Guardar OTP en cache/base de datos
        Cache::put("otp_{$phone}", $otp, 300); // 5 min

        return $otp;
    }
}
```

### Notificaciones de Pedidos

```php
class OrderService
{
    public function notifyOrderStatus(Order $order, string $status)
    {
        $message = match($status) {
            'confirmed' => "✅ Pedido #{$order->id} confirmado",
            'shipped' => "🚚 Pedido #{$order->id} enviado",
            'delivered' => "📦 Pedido #{$order->id} entregado",
        };

        // SMS
        ConvoChat::sms()->sendSmsWithCredits($order->phone, $message);

        // WhatsApp con más detalle
        ConvoChat::whatsapp()->sendText(
            config('convochat.whatsapp.default_account'),
            $order->phone,
            $message . "\n\nRastrear: " . route('track.order', $order->id)
        );
    }
}
```

## 🔧 Configuración Avanzada

### Configuración completa del archivo config/convochat.php

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ConvoChat API Configuration
    |--------------------------------------------------------------------------
    */
    'api_key' => env('CONVOCHAT_API_KEY'),
    'base_url' => env('CONVOCHAT_BASE_URL', 'https://sms.convo.chat/api'),

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    */
    'timeout' => env('CONVOCHAT_TIMEOUT', 30),
    'connect_timeout' => env('CONVOCHAT_CONNECT_TIMEOUT', 10),
    'retries' => env('CONVOCHAT_RETRIES', 3),
    'retry_delay' => env('CONVOCHAT_RETRY_DELAY', 1000), // milisegundos

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'mode' => env('CONVOCHAT_SMS_MODE', 'devices'), // 'devices' o 'credits'
        'priority' => env('CONVOCHAT_SMS_PRIORITY', 2), // 1=alta, 2=normal
        'default_device' => env('CONVOCHAT_SMS_DEVICE'),
        'default_sim' => env('CONVOCHAT_SMS_SIM', 1),
        'fallback_enabled' => env('CONVOCHAT_SMS_FALLBACK', true),
        'fallback_mode' => env('CONVOCHAT_SMS_FALLBACK_MODE', 'credits'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Configuration
    |--------------------------------------------------------------------------
    */
    'whatsapp' => [
        'default_account' => env('CONVOCHAT_WA_ACCOUNT'),
        'priority' => env('CONVOCHAT_WA_PRIORITY', 2),
        'enable_receipts' => env('CONVOCHAT_WA_RECEIPTS', true),
        'enable_typing' => env('CONVOCHAT_WA_TYPING', true),
        'max_media_size' => env('CONVOCHAT_WA_MAX_MEDIA_SIZE', 16777216), // 16MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('CONVOCHAT_LOG_REQUESTS', false),
        'level' => env('CONVOCHAT_LOG_LEVEL', 'info'),
        'channel' => env('CONVOCHAT_LOG_CHANNEL', 'single'),
        'log_responses' => env('CONVOCHAT_LOG_RESPONSES', false),
        'log_sensitive_data' => env('CONVOCHAT_LOG_SENSITIVE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'enabled' => env('CONVOCHAT_RATE_LIMITING', true),
        'max_requests_per_minute' => env('CONVOCHAT_MAX_REQUESTS_PER_MINUTE', 60),
        'burst_limit' => env('CONVOCHAT_BURST_LIMIT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Circuit Breaker Configuration
    |--------------------------------------------------------------------------
    */
    'circuit_breaker' => [
        'enabled' => env('CONVOCHAT_CIRCUIT_BREAKER', false),
        'failure_threshold' => env('CONVOCHAT_FAILURE_THRESHOLD', 5),
        'recovery_timeout' => env('CONVOCHAT_RECOVERY_TIMEOUT', 300),
        'success_threshold' => env('CONVOCHAT_SUCCESS_THRESHOLD', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Health Checks
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled' => env('CONVOCHAT_MONITORING', false),
        'health_check_interval' => env('CONVOCHAT_HEALTH_CHECK_INTERVAL', 300),
        'metrics_enabled' => env('CONVOCHAT_METRICS', false),
        'alerts_enabled' => env('CONVOCHAT_ALERTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'enabled' => env('CONVOCHAT_QUEUE_ENABLED', false),
        'connection' => env('CONVOCHAT_QUEUE_CONNECTION', 'default'),
        'queue_name' => env('CONVOCHAT_QUEUE_NAME', 'convochat'),
        'max_attempts' => env('CONVOCHAT_QUEUE_MAX_ATTEMPTS', 3),
        'backoff' => env('CONVOCHAT_QUEUE_BACKOFF', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment-specific Configuration
    |--------------------------------------------------------------------------
    */
    'environments' => [
        'testing' => [
            'mock_responses' => env('CONVOCHAT_MOCK_RESPONSES', true),
            'log_all_requests' => true,
        ],
        'staging' => [
            'rate_limiting' => ['max_requests_per_minute' => 30],
            'logging' => ['level' => 'debug'],
        ],
        'production' => [
            'logging' => ['log_sensitive_data' => false],
            'monitoring' => ['enabled' => true],
        ],
    ],
];
```

### Variables de entorno recomendadas

```env
# API Configuration
CONVOCHAT_API_KEY=your_api_key_here
CONVOCHAT_BASE_URL=https://sms.convo.chat/api

# Connection Settings
CONVOCHAT_TIMEOUT=30
CONVOCHAT_CONNECT_TIMEOUT=10
CONVOCHAT_RETRIES=3

# SMS Settings
CONVOCHAT_SMS_MODE=devices
CONVOCHAT_SMS_PRIORITY=2
CONVOCHAT_SMS_DEVICE=your_device_id
CONVOCHAT_SMS_SIM=1
CONVOCHAT_SMS_FALLBACK=true

# WhatsApp Settings
CONVOCHAT_WA_ACCOUNT=your_whatsapp_account_id
CONVOCHAT_WA_PRIORITY=2
CONVOCHAT_WA_RECEIPTS=true

# Logging
CONVOCHAT_LOG_REQUESTS=false
CONVOCHAT_LOG_LEVEL=info
CONVOCHAT_LOG_RESPONSES=false

# Performance
CONVOCHAT_RATE_LIMITING=true
CONVOCHAT_MAX_REQUESTS_PER_MINUTE=60
CONVOCHAT_CIRCUIT_BREAKER=true

# Monitoring (Producción)
CONVOCHAT_MONITORING=true
CONVOCHAT_METRICS=true
CONVOCHAT_ALERTS=true

# Queue (Para envíos masivos)
CONVOCHAT_QUEUE_ENABLED=true
CONVOCHAT_QUEUE_CONNECTION=redis
CONVOCHAT_QUEUE_NAME=convochat
```

### Configuración por entorno

```php
// En config/convochat.php - configuración dinámica por entorno
$config = [
    // ... configuración base
];

// Aplicar configuraciones específicas por entorno
$environment = app()->environment();
if (isset($config['environments'][$environment])) {
    $config = array_merge_recursive($config, $config['environments'][$environment]);
}

return $config;
```

## 🧪 Testing y Debugging

### Tests unitarios con PHPUnit

```php
<?php

use Tests\TestCase;
use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use Illuminate\Support\Facades\Http;

class ConvoChatSmsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Configurar entorno de prueba
        config([
            'convochat.api_key' => 'test_key',
            'convochat.base_url' => 'https://test.convo.chat/api',
            'convochat.sms.default_device' => 'test_device'
        ]);
    }

    /** @test */
    public function it_can_send_sms_with_device()
    {
        // Mock de respuesta exitosa
        Http::fake([
            'test.convo.chat/*' => Http::response([
                'status' => 'success',
                'message_id' => 'msg_123456',
                'credits_used' => 1
            ], 200)
        ]);

        $result = ConvoChat::sms()->sendSmsWithDevice(
            '+573001234567',
            'Test message',
            'test_device'
        );

        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('message_id', $result);
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        Http::fake([
            'test.convo.chat/*' => Http::response([
                'status' => 'error',
                'error_code' => 'INVALID_DEVICE',
                'message' => 'Device not found'
            ], 400)
        ]);

        $result = ConvoChat::sms()->sendSmsWithDevice(
            '+573001234567',
            'Test message',
            'invalid_device'
        );

        $this->assertEquals('error', $result['status']);
        $this->assertEquals('INVALID_DEVICE', $result['error_code']);
    }

    /** @test */
    public function it_retries_on_connection_failure()
    {
        Http::fake([
            'test.convo.chat/*' => Http::sequence()
                ->pushStatus(500) // Primera llamada falla
                ->pushStatus(500) // Segunda llamada falla
                ->push([           // Tercera llamada exitosa
                    'status' => 'success',
                    'message_id' => 'msg_retry_123'
                ])
        ]);

        $retryService = new ConvoChatRetryService();
        $result = $retryService->sendWithRetry(
            fn() => ConvoChat::sms()->sendSmsWithDevice('+573001234567', 'Test', 'device'),
            []
        );

        $this->assertEquals('success', $result['status']);
    }
}
```

### Test de integración con WhatsApp

```php
class ConvoChatWhatsAppTest extends TestCase
{
    /** @test */
    public function it_can_send_whatsapp_message()
    {
        Http::fake([
            'test.convo.chat/*' => Http::response([
                'status' => 'success',
                'message_id' => 'wa_msg_123',
                'account_id' => 'wa_account_test'
            ], 200)
        ]);

        $result = ConvoChat::whatsapp()->sendText(
            'wa_account_test',
            '+573001234567',
            'Hello WhatsApp!'
        );

        $this->assertEquals('success', $result['status']);
        $this->assertStringStartsWith('wa_msg_', $result['message_id']);
    }

    /** @test */
    public function it_validates_media_size_for_whatsapp()
    {
        $this->expectException(InvalidArgumentException::class);

        ConvoChat::whatsapp()->sendMedia(
            'wa_account_test',
            '+573001234567',
            'Large file',
            'https://example.com/large_file.mp4', // > 16MB
            'video'
        );
    }
}
```

### Debugging avanzado

```php
// Habilitar debugging completo en .env
CONVOCHAT_LOG_REQUESTS=true
CONVOCHAT_LOG_RESPONSES=true
CONVOCHAT_LOG_LEVEL=debug
CONVOCHAT_LOG_SENSITIVE=true // ¡Solo en desarrollo!

// Debug helper personalizado
class ConvoChatDebugger
{
    public static function enableVerboseLogging(): void
    {
        config([
            'convochat.logging.enabled' => true,
            'convochat.logging.level' => 'debug',
            'convochat.logging.log_responses' => true,
        ]);
    }

    public static function dumpLastRequest(): array
    {
        return cache()->get('convochat.last_request', []);
    }

    public static function dumpLastResponse(): array
    {
        return cache()->get('convochat.last_response', []);
    }

    public static function simulateApiError(string $errorCode = 'TEST_ERROR'): void
    {
        Http::fake([
            '*convo.chat/*' => Http::response([
                'status' => 'error',
                'error_code' => $errorCode,
                'message' => 'Simulated error for testing'
            ], 400)
        ]);
    }
}

// Uso en desarrollo
ConvoChatDebugger::enableVerboseLogging();
$result = ConvoChat::sms()->sendSmsWithDevice($phone, $message, $device);
dd(ConvoChatDebugger::dumpLastRequest(), ConvoChatDebugger::dumpLastResponse());
```

### Testing con Factory Pattern

```php
// Factory para crear datos de prueba
class ConvoChatTestFactory
{
    public static function smsResponse(array $overrides = []): array
    {
        return array_merge([
            'status' => 'success',
            'message_id' => 'sms_' . uniqid(),
            'credits_used' => 1,
            'device_id' => 'test_device',
            'timestamp' => now()->toISOString()
        ], $overrides);
    }

    public static function whatsappResponse(array $overrides = []): array
    {
        return array_merge([
            'status' => 'success',
            'message_id' => 'wa_' . uniqid(),
            'account_id' => 'wa_test_account',
            'timestamp' => now()->toISOString()
        ], $overrides);
    }

    public static function errorResponse(string $errorCode = 'GENERIC_ERROR'): array
    {
        return [
            'status' => 'error',
            'error_code' => $errorCode,
            'message' => 'Test error response',
            'timestamp' => now()->toISOString()
        ];
    }
}

// Uso en tests
Http::fake([
    '*' => Http::response(ConvoChatTestFactory::smsResponse())
]);
```

## 🔍 Troubleshooting

### Problemas comunes y soluciones

#### 1. Error de autenticación

**Problema:** `401 Unauthorized`

```bash
# Verificar API key
php artisan tinker
>>> config('convochat.api_key')
>>> env('CONVOCHAT_API_KEY')
```

**Soluciones:**
- Verificar que la API key esté correctamente configurada en `.env`
- Confirmar que la API key sea válida en el dashboard
- Limpiar cache de configuración: `php artisan config:clear`

#### 2. Timeout de conexión

**Problema:** `Connection timeout after 30 seconds`

**Soluciones:**
```php
// Aumentar timeout en config/convochat.php
'timeout' => 60,
'connect_timeout' => 20,

// O en .env
CONVOCHAT_TIMEOUT=60
CONVOCHAT_CONNECT_TIMEOUT=20
```

#### 3. Device no encontrado

**Problema:** `Device not found or offline`

**Verificación:**
```php
// Listar dispositivos disponibles
$devices = ConvoChat::sms()->getDevices();
foreach ($devices as $device) {
    echo "Device: {$device['id']} - Status: {$device['status']}\n";
}
```

**Soluciones:**
- Verificar que el dispositivo esté conectado
- Usar fallback a créditos si está configurado
- Verificar permisos del dispositivo

#### 4. Problemas con WhatsApp

**Problema:** `WhatsApp account not linked`

**Diagnóstico:**
```php
// Verificar cuentas vinculadas
$accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
var_dump($accounts);

// Verificar estado de la cuenta
$status = ConvoChat::whatsapp()->getAccountStatus('your_account_id');
var_dump($status);
```

#### 5. Rate limiting

**Problema:** `Rate limit exceeded`

**Solución:**
```php
// Implementar rate limiting local
class ConvoChatRateLimiter
{
    public function attempt(callable $function, int $maxAttempts = 60): mixed
    {
        $key = 'convochat_rate_limit_' . auth()->id();

        if (Cache::get($key, 0) >= $maxAttempts) {
            throw new Exception('Rate limit exceeded');
        }

        Cache::increment($key, 1);
        Cache::expire($key, 60); // 1 minuto

        return $function();
    }
}
```

### Herramientas de diagnóstico

```php
// Comando Artisan para diagnóstico
php artisan make:command ConvoChatDiagnose

class ConvoChatDiagnose extends Command
{
    protected $signature = 'convochat:diagnose';
    protected $description = 'Diagnose ConvoChat configuration and connectivity';

    public function handle()
    {
        $this->info('ConvoChat Diagnostic Tool');
        $this->line('================================');

        // Test 1: Configuration
        $this->checkConfiguration();

        // Test 2: Connectivity
        $this->checkConnectivity();

        // Test 3: API key validity
        $this->checkApiKey();

        // Test 4: Device status
        $this->checkDevices();

        // Test 5: WhatsApp accounts
        $this->checkWhatsAppAccounts();
    }

    private function checkConfiguration(): void
    {
        $this->info('Checking configuration...');

        $apiKey = config('convochat.api_key');
        $baseUrl = config('convochat.base_url');

        $this->line("API Key: " . ($apiKey ? 'Set' : 'Missing'));
        $this->line("Base URL: " . $baseUrl);

        if (!$apiKey) {
            $this->error('❌ API Key not configured');
            return;
        }

        $this->info('✅ Configuration OK');
    }

    private function checkConnectivity(): void
    {
        $this->info('Checking connectivity...');

        try {
            $response = Http::timeout(10)->get(config('convochat.base_url') . '/health');

            if ($response->successful()) {
                $this->info('✅ Connectivity OK');
            } else {
                $this->error('❌ Connectivity failed: ' . $response->status());
            }
        } catch (Exception $e) {
            $this->error('❌ Connectivity error: ' . $e->getMessage());
        }
    }

    private function checkApiKey(): void
    {
        $this->info('Checking API key validity...');

        try {
            $credits = ConvoChat::sms()->getCredits();
            $this->info('✅ API Key valid - Credits: ' . $credits['balance']);
        } catch (Exception $e) {
            $this->error('❌ API Key invalid: ' . $e->getMessage());
        }
    }

    private function checkDevices(): void
    {
        $this->info('Checking SMS devices...');

        try {
            $devices = ConvoChat::sms()->getDevices();
            $this->info('✅ Found ' . count($devices) . ' devices');

            foreach ($devices as $device) {
                $status = $device['status'] === 'online' ? '✅' : '❌';
                $this->line("  {$status} {$device['name']} ({$device['id']})");
            }
        } catch (Exception $e) {
            $this->error('❌ Device check failed: ' . $e->getMessage());
        }
    }

    private function checkWhatsAppAccounts(): void
    {
        $this->info('Checking WhatsApp accounts...');

        try {
            $accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
            $this->info('✅ Found ' . count($accounts) . ' WhatsApp accounts');

            foreach ($accounts as $account) {
                $status = $account['status'] === 'connected' ? '✅' : '❌';
                $this->line("  {$status} {$account['number']} ({$account['id']})");
            }
        } catch (Exception $e) {
            $this->error('❌ WhatsApp check failed: ' . $e->getMessage());
        }
    }
}
```

## 🚀 Performance Optimization

### Optimizaciones para envíos masivos

#### 1. Queue-based bulk sending

```php
// Job para envío masivo optimizado
class BulkSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120]; // Progressive backoff

    public function __construct(
        private array $recipients,
        private string $message,
        private array $options = []
    ) {}

    public function handle(ConvoChatSmsService $smsService): void
    {
        $batchSize = config('convochat.queue.batch_size', 10);
        $chunks = array_chunk($this->recipients, $batchSize);

        foreach ($chunks as $chunk) {
            $this->processBatch($smsService, $chunk);

            // Rate limiting entre batches
            usleep(config('convochat.queue.batch_delay', 100000)); // 100ms
        }
    }

    private function processBatch(ConvoChatSmsService $smsService, array $recipients): void
    {
        $promises = [];

        foreach ($recipients as $phone) {
            $promises[] = $this->sendAsync($smsService, $phone);
        }

        // Procesar todas las promesas en paralelo
        Promise::settle($promises)->wait();
    }

    private function sendAsync(ConvoChatSmsService $smsService, string $phone): PromiseInterface
    {
        return $smsService->sendSmsWithCreditsAsync($phone, $this->message);
    }
}

// Dispatcher optimizado
class BulkSmsDispatcher
{
    public function sendBulkSms(array $recipients, string $message): void
    {
        $maxChunkSize = config('convochat.performance.max_chunk_size', 1000);
        $chunks = array_chunk($recipients, $maxChunkSize);

        foreach ($chunks as $index => $chunk) {
            dispatch(new BulkSmsJob($chunk, $message))
                ->onQueue('convochat-bulk')
                ->delay(now()->addSeconds($index * 5)); // Spread load
        }
    }
}
```

#### 2. Connection pooling y reuso

```php
class ConvoChatConnectionPool
{
    private static array $pool = [];
    private const MAX_CONNECTIONS = 10;

    public static function getConnection(): GuzzleHttp\Client
    {
        if (count(self::$pool) < self::MAX_CONNECTIONS) {
            self::$pool[] = new GuzzleHttp\Client([
                'base_uri' => config('convochat.base_url'),
                'timeout' => config('convochat.timeout'),
                'headers' => [
                    'Authorization' => 'Bearer ' . config('convochat.api_key'),
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
            ]);
        }

        return self::$pool[array_rand(self::$pool)];
    }

    public static function releaseConnection(GuzzleHttp\Client $client): void
    {
        // Implementar lógica de release si es necesario
    }
}
```

#### 3. Caché inteligente

```php
class ConvoChatCache
{
    private const CACHE_TTL = [
        'devices' => 300,        // 5 minutos
        'credits' => 60,         // 1 minuto
        'accounts' => 600,       // 10 minutos
        'rates' => 3600,         // 1 hora
    ];

    public static function remember(string $type, string $key, callable $callback): mixed
    {
        $cacheKey = "convochat.{$type}.{$key}";
        $ttl = self::CACHE_TTL[$type] ?? 300;

        return Cache::remember($cacheKey, $ttl, $callback);
    }

    public static function invalidate(string $type, string $key = '*'): void
    {
        if ($key === '*') {
            Cache::flush(); // Solo en desarrollo
        } else {
            Cache::forget("convochat.{$type}.{$key}");
        }
    }

    // Caché con warming estratégico
    public static function warmUp(): void
    {
        // Pre-cargar datos frecuentemente accedidos
        self::remember('devices', 'all', fn() => ConvoChat::sms()->getDevices());
        self::remember('credits', 'balance', fn() => ConvoChat::sms()->getCredits());
        self::remember('accounts', 'whatsapp', fn() => ConvoChat::whatsapp()->getWhatsAppAccounts());
    }
}

// Comando para warming
php artisan make:command ConvoChatWarmCache

class ConvoChatWarmCache extends Command
{
    protected $signature = 'convochat:warm-cache';

    public function handle()
    {
        ConvoChatCache::warmUp();
        $this->info('ConvoChat cache warmed successfully');
    }
}
```

#### 4. Monitoring de performance

```php
class ConvoChatPerformanceMonitor
{
    public function measureRequest(callable $operation, array $context = []): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        try {
            $result = $operation();
            $success = true;
        } catch (Exception $e) {
            $result = null;
            $success = false;
            $error = $e->getMessage();
        }

        $metrics = [
            'duration' => (microtime(true) - $startTime) * 1000, // ms
            'memory' => memory_get_usage(true) - $startMemory,
            'success' => $success,
            'timestamp' => now()->toISOString(),
            'context' => $context
        ];

        if (isset($error)) {
            $metrics['error'] = $error;
        }

        $this->recordMetrics($metrics);

        return $result;
    }

    private function recordMetrics(array $metrics): void
    {
        // Log métricas
        logger()->info('ConvoChat Performance', $metrics);

        // Enviar a sistema de métricas (InfluxDB, CloudWatch, etc.)
        if (config('convochat.monitoring.metrics_enabled')) {
            $this->sendToMetricsBackend($metrics);
        }

        // Alertas por performance degradada
        if ($metrics['duration'] > config('convochat.performance.alert_threshold', 5000)) {
            $this->sendPerformanceAlert($metrics);
        }
    }
}
```

## 🔒 Security Best Practices

### 1. Protección de API Keys

```php
// Service Provider para cifrado de claves sensibles
class ConvoChatSecurityProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('convochat.secure_config', function () {
            return new SecureConfigManager();
        });
    }
}

class SecureConfigManager
{
    public function getSecureApiKey(): string
    {
        $encryptedKey = config('convochat.encrypted_api_key');

        if ($encryptedKey) {
            return decrypt($encryptedKey);
        }

        return config('convochat.api_key');
    }

    public function setSecureApiKey(string $apiKey): void
    {
        $encrypted = encrypt($apiKey);

        // Guardar de forma segura
        Cache::put('convochat.secure_api_key', $encrypted, now()->addHours(24));
    }
}
```

### 2. Validación y sanitización de datos

```php
class ConvoChatValidator
{
    public static function validatePhone(string $phone): string
    {
        // Eliminar caracteres no numéricos excepto +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Validar formato internacional
        if (!preg_match('/^\+[1-9]\d{1,14}$/', $phone)) {
            throw new InvalidArgumentException('Invalid phone number format');
        }

        return $phone;
    }

    public static function sanitizeMessage(string $message): string
    {
        // Eliminar caracteres peligrosos
        $message = strip_tags($message);
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        // Limitar longitud
        if (strlen($message) > 1600) {
            throw new InvalidArgumentException('Message too long');
        }

        return $message;
    }

    public static function validateMediaUrl(string $url): string
    {
        // Validar URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid media URL');
        }

        // Verificar esquema permitido
        $allowedSchemes = ['https'];
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (!in_array($scheme, $allowedSchemes)) {
            throw new InvalidArgumentException('Only HTTPS URLs are allowed');
        }

        return $url;
    }
}

// Middleware para validación automática
class ConvoChatValidationMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->has('phone')) {
            $request->merge([
                'phone' => ConvoChatValidator::validatePhone($request->phone)
            ]);
        }

        if ($request->has('message')) {
            $request->merge([
                'message' => ConvoChatValidator::sanitizeMessage($request->message)
            ]);
        }

        return $next($request);
    }
}
```

### 3. Rate limiting por usuario

```php
class ConvoChatUserRateLimiter
{
    public function attempt(int $userId, string $action, int $maxAttempts = 60): bool
    {
        $key = "convochat_rate_limit_{$action}_{$userId}";

        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return false;
        }

        Cache::put($key, $attempts + 1, now()->addMinutes(1));

        return true;
    }

    public function getRemainingAttempts(int $userId, string $action, int $maxAttempts = 60): int
    {
        $key = "convochat_rate_limit_{$action}_{$userId}";
        $attempts = Cache::get($key, 0);

        return max(0, $maxAttempts - $attempts);
    }
}

// Uso en controlador
class SecureNotificationController extends Controller
{
    public function sendSms(Request $request, ConvoChatUserRateLimiter $rateLimiter)
    {
        $userId = auth()->id();

        if (!$rateLimiter->attempt($userId, 'sms', 10)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'retry_after' => 60
            ], 429);
        }

        // Proceder con el envío...
    }
}
```

### 4. Audit logging

```php
class ConvoChatAuditLogger
{
    public static function logOperation(string $operation, array $data = []): void
    {
        $auditData = [
            'user_id' => auth()->id(),
            'operation' => $operation,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        // Remover datos sensibles del log
        unset($auditData['data']['api_key']);
        unset($auditData['data']['password']);

        logger()->info('ConvoChat Audit', $auditData);

        // Enviar a sistema de auditoría externa
        if (config('convochat.security.external_audit')) {
            self::sendToAuditSystem($auditData);
        }
    }

    private static function sendToAuditSystem(array $data): void
    {
        // Integración con sistemas de auditoría
        Http::post(config('convochat.security.audit_endpoint'), $data);
    }
}

// Uso automático en servicios
class SecureConvoChatSmsService extends ConvoChatSmsService
{
    public function sendSmsWithDevice(string $phone, string $message, string $deviceId): array
    {
        ConvoChatAuditLogger::logOperation('sms_send', [
            'phone' => substr($phone, 0, -4) . '****', // Partially mask phone
            'device_id' => $deviceId,
            'message_length' => strlen($message)
        ]);

        return parent::sendSmsWithDevice($phone, $message, $deviceId);
    }
}
```

### 5. Cifrado de mensajes sensibles

```php
class ConvoChatEncryption
{
    public static function encryptMessage(string $message, ?string $key = null): string
    {
        $key = $key ?: config('app.key');
        return encrypt($message);
    }

    public static function decryptMessage(string $encryptedMessage, ?string $key = null): string
    {
        return decrypt($encryptedMessage);
    }

    public static function hashPhone(string $phone): string
    {
        return hash('sha256', $phone . config('app.key'));
    }
}

// Service para mensajes cifrados
class EncryptedMessageService
{
    public function sendEncryptedSms(string $phone, string $message, string $deviceId): array
    {
        // Cifrar mensaje antes del envío
        $encryptedMessage = ConvoChatEncryption::encryptMessage($message);

        // Guardar relación en BD para recuperar después
        EncryptedMessage::create([
            'phone_hash' => ConvoChatEncryption::hashPhone($phone),
            'encrypted_content' => $encryptedMessage,
            'sent_at' => now()
        ]);

        // Enviar mensaje sin contenido sensible
        return ConvoChat::sms()->sendSmsWithDevice(
            $phone,
            'Mensaje cifrado enviado. Consulte su portal seguro.',
            $deviceId
        );
    }
}
```

## 📋 Requisitos

- PHP 8.0+
- Laravel 8.0+
- Guzzle HTTP 7.0+
- API Key válida de ConvoChat

## 📞 Soporte

- Documentación API: https://docs.convo.chat
- Dashboard: https://sms.convo.chat
- API Keys: https://sms.convo.chat/dashboard/tools/keys

## 📄 Licencia

MIT License