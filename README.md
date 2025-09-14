# ConvoChat Laravel SMS Gateway

[![Latest Version](https://img.shields.io/packagist/v/renatoascencio/convochatsms.svg?style=flat-square)](https://packagist.org/packages/renatoascencio/convochatsms)
[![Total Downloads](https://img.shields.io/packagist/dt/renatoascencio/convochatsms.svg?style=flat-square)](https://packagist.org/packages/renatoascencio/convochatsms)
[![License](https://img.shields.io/packagist/l/renatoascencio/convochatsms.svg?style=flat-square)](https://packagist.org/packages/renatoascencio/convochatsms)

Un paquete Laravel para integración fácil con ConvoChat API para envío de SMS y WhatsApp.

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

## 🔧 Uso sin Facade

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

El archivo `config/convochat.php` permite personalizar:

- Timeouts de conexión
- URLs de API personalizadas
- Configuraciones por defecto
- Logging detallado
- Configuración por entorno

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