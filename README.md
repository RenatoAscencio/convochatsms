# ConvoChat SDK - Documentación Completa

## 📋 Tabla de Contenidos

- [Instalación](#instalación)
- [Configuración](#configuración)
- [Servicios Disponibles](#servicios-disponibles)
- [SMS Service](#sms-service)
- [WhatsApp Service](#whatsapp-service)
- [Contacts Service](#contacts-service)
- [OTP Service](#otp-service)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Manejo de Errores](#manejo-de-errores)
- [Logging](#logging)

## 🚀 Instalación

```bash
composer require convochatsms/laravel-sms-whatsapp-gateway
```

## ⚙️ Configuración

### 1. Publicar archivo de configuración

```bash
php artisan vendor:publish --tag=convochat-config
```

### 2. Configurar variables de entorno

```env
CONVOCHAT_API_KEY=tu_api_key_aqui
CONVOCHAT_BASE_URL=https://sms.convo.chat/api
CONVOCHAT_TIMEOUT=30
CONVOCHAT_LOG_REQUESTS=true
```

### 3. Registrar el Service Provider

El Service Provider se registra automáticamente en Laravel 5.5+.

## 🔧 Servicios Disponibles

El SDK incluye 4 servicios principales:

- **ConvoChatSmsService** - Gestión completa de SMS
- **ConvoChatWhatsAppService** - Gestión completa de WhatsApp
- **ConvoChatContactsService** - Gestión de contactos y grupos
- **ConvoChatOtpService** - Envío y verificación de códigos OTP

## 📱 SMS Service

### Métodos Principales

#### `sendSms(array $params)`
Envía un SMS individual.

**Parámetros requeridos:**
- `phone` (string) - Número de teléfono destino (formato E.164 o local)
- `message` (string) - Texto del mensaje
- `mode` (string) - Modo de envío: "devices" o "credits"

**Parámetros opcionales:**
- `device` (string) - ID del dispositivo (modo devices)
- `gateway` (string|number) - ID del gateway (modo credits)
- `sim` (number) - Slot SIM (solo devices)
- `priority` (number) - Prioridad: 0=alta, 1=normal, 2=baja
- `shortener` (number) - ID del acortador de URL

**Ejemplo:**
```php
use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

// Envío con dispositivos
$result = ConvoChat::sms()->sendSms([
    'phone' => '+522221234567',
    'message' => 'Hola desde ConvoChat!',
    'mode' => 'devices',
    'device' => 'device123',
    'sim' => 1,
    'priority' => 1
]);

// Envío con créditos
$result = ConvoChat::sms()->sendSms([
    'phone' => '+522221234567',
    'message' => 'Hola desde ConvoChat!',
    'mode' => 'credits',
    'gateway' => 'gateway456',
    'priority' => 0
]);
```

#### `sendSmsWithDevice(string $phone, string $message, string $deviceId, array $options = [])`
Método helper para envío con dispositivos.

**Ejemplo:**
```php
$result = ConvoChat::sms()->sendSmsWithDevice(
    '+522221234567',
    'Mensaje de prueba',
    'device123',
    ['sim' => 1, 'priority' => 1]
);
```

#### `sendSmsWithCredits(string $phone, string $message, ?string $gatewayId = null, array $options = [])`
Método helper para envío con créditos.

**Ejemplo:**
```php
$result = ConvoChat::sms()->sendSmsWithCredits(
    '+522221234567',
    'Mensaje de prueba',
    'gateway456',
    ['priority' => 0]
);
```

#### `sendBulkSms(array $recipients, string $message, array $options = [])`
Envía SMS masivo a múltiples destinatarios.

**Ejemplo:**
```php
$result = ConvoChat::sms()->sendBulkSms(
    ['+522221234567', '+522229876543', '+522225556667'],
    'Mensaje masivo',
    ['mode' => 'credits', 'gateway' => 'gateway456']
);
```

### Métodos de Consulta

#### `getSmsPending(array $filters = [])`
Obtiene mensajes SMS pendientes.

**Parámetros opcionales:**
- `limit` (number) - Límite de resultados (default: 10)
- `page` (number) - Número de página (default: 1)

**Ejemplo:**
```php
$pending = ConvoChat::sms()->getSmsPending(['limit' => 20, 'page' => 1]);
```

#### `getSmsReceived(array $filters = [])`
Obtiene mensajes SMS recibidos.

**Ejemplo:**
```php
$received = ConvoChat::sms()->getSmsReceived(['limit' => 50]);
```

#### `getSmsSent(array $filters = [])`
Obtiene mensajes SMS enviados.

**Ejemplo:**
```php
$sent = ConvoChat::sms()->getSmsSent(['limit' => 100]);
```

#### `getSmsMessage(int $messageId, string $type)`
Obtiene un mensaje SMS específico.

**Parámetros:**
- `messageId` (int) - ID del mensaje
- `type` (string) - Tipo: "sent" o "received"

**Ejemplo:**
```php
$message = ConvoChat::sms()->getSmsMessage(123, 'sent');
```

#### `getSmsCampaigns(array $filters = [])`
Obtiene todas las campañas SMS.

**Ejemplo:**
```php
$campaigns = ConvoChat::sms()->getSmsCampaigns(['limit' => 25]);
```

### Métodos de Gestión de Campañas

#### `startSmsCampaign(int $campaignId)`
Inicia una campaña SMS existente.

**Ejemplo:**
```php
$result = ConvoChat::sms()->startSmsCampaign(123);
```

#### `stopSmsCampaign(int $campaignId)`
Detiene una campaña SMS activa.

**Ejemplo:**
```php
$result = ConvoChat::sms()->stopSmsCampaign(123);
```

### Métodos de Eliminación

#### `deleteSmsReceived(int $messageId)`
Elimina un SMS recibido.

**Ejemplo:**
```php
$result = ConvoChat::sms()->deleteSmsReceived(123);
```

#### `deleteSmsSent(int $messageId)`
Elimina un SMS enviado.

**Ejemplo:**
```php
$result = ConvoChat::sms()->deleteSmsSent(123);
```

#### `deleteSmsCampaign(int $campaignId)`
Elimina una campaña SMS.

**Ejemplo:**
```php
$result = ConvoChat::sms()->deleteSmsCampaign(123);
```

### Métodos de Información

#### `getDevices()`
Obtiene la lista de dispositivos disponibles.

**Ejemplo:**
```php
$devices = ConvoChat::sms()->getDevices();
```

#### `getCredits()`
Obtiene los créditos restantes.

**Ejemplo:**
```php
$credits = ConvoChat::sms()->getCredits();
```

#### `getRates()`
Obtiene las tarifas de los gateways.

**Ejemplo:**
```php
$rates = ConvoChat::sms()->getRates();
```

#### `getSubscription()`
Obtiene el paquete de suscripción actual.

**Ejemplo:**
```php
$subscription = ConvoChat::sms()->getSubscription();
```

## 💬 WhatsApp Service

### Métodos Principales

#### `sendMessage(array $params)`
Envía un mensaje de WhatsApp.

**Parámetros requeridos:**
- `account` (string) - ID único de la cuenta de WhatsApp
- `recipient` (string) - Número o grupo destino
- `type` (string) - Tipo de mensaje: "text", "media", "document"
- `message` (string) - Contenido del mensaje

**Parámetros opcionales:**
- `priority` (number) - Prioridad: 1=alta, 2=normal
- `media_file` (string) - Archivo multimedia (binary)
- `media_url` (string) - URL del archivo multimedia
- `media_type` (string) - Tipo de media: "image", "audio", "video"
- `document_file` (string) - Archivo de documento (binary)
- `document_url` (string) - URL del documento
- `document_name` (string) - Nombre del documento
- `document_type` (string) - Tipo: "pdf", "txt", "xls", "xlsx", "doc", "docx"
- `shortener` (number) - ID del shortener

**Ejemplo:**
```php
// Mensaje de texto
$result = ConvoChat::whatsapp()->sendMessage([
    'account' => 'account123',
    'recipient' => '+522221234567',
    'type' => 'text',
    'message' => 'Hola desde WhatsApp!',
    'priority' => 2
]);

// Mensaje con media
$result = ConvoChat::whatsapp()->sendMessage([
    'account' => 'account123',
    'recipient' => '+522221234567',
    'type' => 'media',
    'message' => 'Mira esta imagen',
    'media_url' => 'https://example.com/image.jpg',
    'media_type' => 'image',
    'priority' => 1
]);

// Mensaje con documento
$result = ConvoChat::whatsapp()->sendMessage([
    'account' => 'account123',
    'recipient' => '+522221234567',
    'type' => 'document',
    'message' => 'Aquí está el documento',
    'document_url' => 'https://example.com/document.pdf',
    'document_name' => 'documento.pdf',
    'document_type' => 'pdf'
]);
```

#### `sendText(string $account, string $recipient, string $message, int $priority = 2)`
Método helper para enviar mensajes de texto.

**Ejemplo:**
```php
$result = ConvoChat::whatsapp()->sendText(
    'account123',
    '+522221234567',
    'Mensaje de texto simple',
    1
);
```

#### `sendMedia(string $account, string $recipient, string $message, string $mediaUrl, string $mediaType = 'image', int $priority = 2)`
Método helper para enviar mensajes con multimedia.

**Ejemplo:**
```php
$result = ConvoChat::whatsapp()->sendMedia(
    'account123',
    '+522221234567',
    'Mira esta imagen',
    'https://example.com/image.jpg',
    'image',
    1
);
```

#### `sendDocument(string $account, string $recipient, string $message, string $documentUrl, string $documentName, string $documentType = 'pdf', int $priority = 2)`
Método helper para enviar documentos.

**Ejemplo:**
```php
$result = ConvoChat::whatsapp()->sendDocument(
    'account123',
    '+522221234567',
    'Aquí está el documento',
    'https://example.com/document.pdf',
    'documento.pdf',
    'pdf',
    1
);
```

#### `sendBulkWhatsApp(array $recipients, string $message, array $options = [])`
Envía WhatsApp masivo a múltiples destinatarios.

**Ejemplo:**
```php
$result = ConvoChat::whatsapp()->sendBulkWhatsApp(
    ['+522221234567', '+522229876543', '+522225556667'],
    'Mensaje masivo de WhatsApp',
    ['account' => 'account123', 'type' => 'text']
);
```

### Métodos de Consulta

#### `getWhatsAppPending(array $filters = [])`
Obtiene mensajes WhatsApp pendientes.

**Ejemplo:**
```php
$pending = ConvoChat::whatsapp()->getWhatsAppPending(['limit' => 20]);
```

#### `getWhatsAppReceived(array $filters = [])`
Obtiene mensajes WhatsApp recibidos.

**Ejemplo:**
```php
$received = ConvoChat::whatsapp()->getWhatsAppReceived(['limit' => 50]);
```

#### `getWhatsAppSent(array $filters = [])`
Obtiene mensajes WhatsApp enviados.

**Ejemplo:**
```php
$sent = ConvoChat::whatsapp()->getWhatsAppSent(['limit' => 100]);
```

#### `getWhatsAppMessage(int $messageId, string $type)`
Obtiene un mensaje WhatsApp específico.

**Ejemplo:**
```php
$message = ConvoChat::whatsapp()->getWhatsAppMessage(123, 'sent');
```

#### `getWhatsAppCampaigns(array $filters = [])`
Obtiene todas las campañas WhatsApp.

**Ejemplo:**
```php
$campaigns = ConvoChat::whatsapp()->getWhatsAppCampaigns(['limit' => 25]);
```

#### `getWhatsAppGroups(array $filters = [])`
Obtiene los grupos de WhatsApp.

**Ejemplo:**
```php
$groups = ConvoChat::whatsapp()->getWhatsAppGroups(['limit' => 10]);
```

#### `getWhatsAppGroupContacts(string $groupId, array $filters = [])`
Obtiene los contactos de un grupo específico.

**Ejemplo:**
```php
$contacts = ConvoChat::whatsapp()->getWhatsAppGroupContacts('group123', ['limit' => 50]);
```

### Métodos de Gestión de Cuentas

#### `getWhatsAppServers()`
Obtiene los servidores WhatsApp disponibles.

**Ejemplo:**
```php
$servers = ConvoChat::whatsapp()->getWhatsAppServers();
```

#### `getWhatsAppAccounts()`
Obtiene las cuentas WhatsApp vinculadas.

**Ejemplo:**
```php
$accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
```

#### `getWhatsAppInfo(string $accountId)`
Obtiene información de una cuenta específica.

**Ejemplo:**
```php
$info = ConvoChat::whatsapp()->getWhatsAppInfo('account123');
```

#### `getWhatsAppQr(string $accountId)`
Obtiene el código QR para vincular una cuenta.

**Ejemplo:**
```php
$qr = ConvoChat::whatsapp()->getWhatsAppQr('account123');
```

#### `validateWhatsAppNumber(string $accountId, string $phone)`
Valida si un número existe en WhatsApp.

**Ejemplo:**
```php
$validation = ConvoChat::whatsapp()->validateWhatsAppNumber('account123', '+522221234567');
```

### Métodos de Gestión de Campañas

#### `startWhatsAppCampaign(int $campaignId)`
Inicia una campaña WhatsApp existente.

**Ejemplo:**
```php
$result = ConvoChat::whatsapp()->startWhatsAppCampaign(123);
```

#### `stopWhatsAppCampaign(int $campaignId)`
Detiene una campaña WhatsApp activa.

**Ejemplo:**
```php
$result = ConvoChat::whatsapp()->stopWhatsAppCampaign(123);
```

## 👥 Contacts Service

### Métodos de Contactos

#### `getContacts(array $filters = [])`
Obtiene la lista de contactos guardados.

**Parámetros opcionales:**
- `limit` (number) - Límite de resultados (default: 10)
- `page` (number) - Número de página (default: 1)

**Ejemplo:**
```php
$contacts = ConvoChat::contacts()->getContacts(['limit' => 20, 'page' => 1]);
```

#### `createContact(array $params)`
Crea un nuevo contacto.

**Parámetros requeridos:**
- `phone` (string) - Número móvil del destinatario
- `name` (string) - Nombre del contacto
- `groups` (string) - Lista de IDs de grupos separados por comas

**Ejemplo:**
```php
$result = ConvoChat::contacts()->createContact([
    'phone' => '+522221234567',
    'name' => 'Juan Pérez',
    'groups' => '1,2,3'
]);
```

#### `deleteContact(int $contactId)`
Elimina un contacto existente.

**Ejemplo:**
```php
$result = ConvoChat::contacts()->deleteContact(123);
```

### Métodos de Grupos

#### `getGroups(array $filters = [])`
Obtiene los grupos de contactos existentes.

**Parámetros opcionales:**
- `limit` (number) - Límite de resultados (default: 10)
- `page` (number) - Número de página (default: 1)

**Ejemplo:**
```php
$groups = ConvoChat::contacts()->getGroups(['limit' => 15]);
```

#### `createGroup(array $params)`
Crea un nuevo grupo de contactos.

**Parámetros requeridos:**
- `name` (string) - Nombre del grupo

**Ejemplo:**
```php
$result = ConvoChat::contacts()->createGroup([
    'name' => 'Clientes VIP'
]);
```

#### `deleteGroup(int $groupId)`
Elimina un grupo de contactos.

**Ejemplo:**
```php
$result = ConvoChat::contacts()->deleteGroup(123);
```

### Métodos de Gestión de Bajas

#### `getUnsubscribed(array $filters = [])`
Obtiene los contactos que se dieron de baja.

**Parámetros opcionales:**
- `limit` (number) - Límite de resultados (default: 10)
- `page` (number) - Número de página (default: 1)

**Ejemplo:**
```php
$unsubscribed = ConvoChat::contacts()->getUnsubscribed(['limit' => 25]);
```

#### `deleteUnsubscribed(int $contactId)`
Elimina un contacto dado de baja.

**Ejemplo:**
```php
$result = ConvoChat::contacts()->deleteUnsubscribed(123);
```

## 🔐 OTP Service

### Métodos Principales

#### `sendOtp(array $params)`
Envía una contraseña de un solo uso (OTP).

**Parámetros requeridos:**
- `type` (string) - Tipo: "sms" o "whatsapp"
- `message` (string) - Mensaje con {{otp}} para incluir el código
- `phone` (string) - Número del destinatario

**Parámetros opcionales:**
- `expire` (number) - Expiración en segundos (default: 300)
- `priority` (number) - Prioridad: 1=alta, 2=normal (default: 2)
- `account` (string) - Solo para WhatsApp
- `mode` (string) - "devices" o "credits"
- `device` (string) - ID de dispositivo (modo devices)
- `gateway` (string|number) - ID del gateway (modo credits)
- `sim` (number) - SIM slot (solo devices)

**Ejemplo:**
```php
// OTP por SMS
$result = ConvoChat::otp()->sendOtp([
    'type' => 'sms',
    'message' => 'Tu código de verificación es {{otp}}',
    'phone' => '+522221234567',
    'expire' => 300,
    'priority' => 1,
    'mode' => 'credits',
    'gateway' => 'gateway456'
]);

// OTP por WhatsApp
$result = ConvoChat::otp()->sendOtp([
    'type' => 'whatsapp',
    'message' => 'Tu código de verificación es {{otp}}',
    'phone' => '+522221234567',
    'account' => 'account123',
    'expire' => 600,
    'priority' => 1
]);
```

#### `verifyOtp(string $otp)`
Verifica un OTP enviado.

**Ejemplo:**
```php
$result = ConvoChat::otp()->verifyOtp('123456');
```

## 📚 Ejemplos de Uso

### Ejemplo Completo de SMS

```php
<?php

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

class SmsController extends Controller
{
    public function sendWelcomeSms()
    {
        try {
            // Verificar créditos disponibles
            $credits = ConvoChat::sms()->getCredits();
            
            if ($credits['data']['credits'] < 1) {
                return response()->json(['error' => 'Créditos insuficientes'], 400);
            }
            
            // Enviar SMS
            $result = ConvoChat::sms()->sendSms([
                'phone' => '+522221234567',
                'message' => '¡Bienvenido a nuestro servicio!',
                'mode' => 'credits',
                'gateway' => 'gateway456',
                'priority' => 1
            ]);
            
            return response()->json([
                'success' => true,
                'message_id' => $result['data']['messageId']
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getSmsHistory()
    {
        try {
            $sent = ConvoChat::sms()->getSmsSent(['limit' => 50]);
            $received = ConvoChat::sms()->getSmsReceived(['limit' => 50]);
            
            return response()->json([
                'sent' => $sent['data'],
                'received' => $received['data']
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

### Ejemplo Completo de WhatsApp

```php
<?php

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

class WhatsAppController extends Controller
{
    public function sendWelcomeMessage()
    {
        try {
            // Obtener cuentas disponibles
            $accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
            
            if (empty($accounts['data'])) {
                return response()->json(['error' => 'No hay cuentas WhatsApp disponibles'], 400);
            }
            
            $accountId = $accounts['data'][0]['unique'];
            
            // Enviar mensaje de bienvenida
            $result = ConvoChat::whatsapp()->sendText(
                $accountId,
                '+522221234567',
                '¡Hola! Bienvenido a nuestro servicio de WhatsApp. ¿En qué podemos ayudarte?',
                1
            );
            
            return response()->json([
                'success' => true,
                'message_id' => $result['data']['messageId']
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function sendMediaMessage()
    {
        try {
            $result = ConvoChat::whatsapp()->sendMedia(
                'account123',
                '+522221234567',
                'Mira nuestra nueva promoción',
                'https://example.com/promotion.jpg',
                'image',
                1
            );
            
            return response()->json([
                'success' => true,
                'message_id' => $result['data']['messageId']
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

### Ejemplo Completo de Contactos

```php
<?php

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

class ContactController extends Controller
{
    public function importContacts()
    {
        try {
            // Crear grupo primero
            $group = ConvoChat::contacts()->createGroup([
                'name' => 'Importación ' . date('Y-m-d')
            ]);
            
            $groupId = $group['data']['id'];
            
            // Importar contactos
            $contacts = [
                ['name' => 'Juan Pérez', 'phone' => '+522221234567'],
                ['name' => 'María García', 'phone' => '+522229876543'],
                ['name' => 'Carlos López', 'phone' => '+522225556667']
            ];
            
            $imported = [];
            foreach ($contacts as $contact) {
                $result = ConvoChat::contacts()->createContact([
                    'name' => $contact['name'],
                    'phone' => $contact['phone'],
                    'groups' => $groupId
                ]);
                
                $imported[] = $result;
            }
            
            return response()->json([
                'success' => true,
                'group_id' => $groupId,
                'imported_count' => count($imported),
                'contacts' => $imported
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getContactStats()
    {
        try {
            $contacts = ConvoChat::contacts()->getContacts(['limit' => 1000]);
            $groups = ConvoChat::contacts()->getGroups(['limit' => 100]);
            $unsubscribed = ConvoChat::contacts()->getUnsubscribed(['limit' => 100]);
            
            return response()->json([
                'total_contacts' => count($contacts['data']),
                'total_groups' => count($groups['data']),
                'unsubscribed_count' => count($unsubscribed['data']),
                'contacts' => $contacts['data'],
                'groups' => $groups['data']
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

### Ejemplo Completo de OTP

```php
<?php

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;

class OtpController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $type = $request->input('type', 'sms'); // sms o whatsapp
            
            $result = ConvoChat::otp()->sendOtp([
                'type' => $type,
                'message' => 'Tu código de verificación es {{otp}}. Válido por 5 minutos.',
                'phone' => $phone,
                'expire' => 300,
                'priority' => 1,
                'mode' => 'credits',
                'gateway' => 'gateway456'
            ]);
            
            // Guardar el OTP en sesión o caché para verificación posterior
            session(['otp_phone' => $phone, 'otp_code' => $result['data']['otp']]);
            
            return response()->json([
                'success' => true,
                'message' => 'Código enviado exitosamente',
                'expires_in' => 300
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function verifyCode(Request $request)
    {
        try {
            $otp = $request->input('otp');
            $phone = $request->input('phone');
            
            // Verificar OTP
            $result = ConvoChat::otp()->verifyOtp($otp);
            
            if ($result['status'] == 200) {
                // OTP válido, proceder con la verificación
                return response()->json([
                    'success' => true,
                    'message' => 'Código verificado exitosamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Código inválido'
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

## ⚠️ Manejo de Errores

### Estructura de Respuesta de Error

```php
try {
    $result = ConvoChat::sms()->sendSms($params);
} catch (\Exception $e) {
    // El SDK lanza excepciones para errores de API
    Log::error('ConvoChat Error: ' . $e->getMessage());
    
    return response()->json([
        'success' => false,
        'error' => $e->getMessage()
    ], 500);
}
```

### Códigos de Error Comunes

- **400** - Parámetros inválidos o faltantes
- **401** - API key inválida o faltante
- **403** - Permisos insuficientes
- **404** - Recurso no encontrado
- **429** - Límite de velocidad excedido
- **500** - Error interno del servidor

### Validación de Parámetros

```php
// El SDK valida automáticamente parámetros requeridos
try {
    $result = ConvoChat::sms()->sendSms([
        'phone' => '+522221234567',
        // 'message' faltante - lanzará InvalidArgumentException
    ]);
} catch (\InvalidArgumentException $e) {
    // Manejar error de validación
    return response()->json(['error' => $e->getMessage()], 400);
}
```

## 📝 Logging

### Habilitar Logging

```env
CONVOCHAT_LOG_REQUESTS=true
```

### Configuración de Logs

```php
// En config/logging.php
'channels' => [
    'convochat' => [
        'driver' => 'single',
        'path' => storage_path('logs/convochat.log'),
        'level' => 'info',
    ],
],
```

### Ejemplo de Logs

```php
// Los logs incluyen:
// - Endpoint llamado
// - Parámetros de la petición (con datos sensibles redactados)
// - Respuesta de la API
// - Tiempo de ejecución
// - Errores detallados

[2024-01-15 10:30:45] local.INFO: ConvoChat SMS API Request Success {"endpoint":"/send/sms","response_status":"200","request_time":"2024-01-15 10:30:45","base_url":"https://sms.convo.chat/api"}

[2024-01-15 10:31:12] local.ERROR: ConvoChat SMS API Error {"endpoint":"/send/sms","error_message":"Invalid phone number","error_code":400,"request_data":{"phone":"+522221234567","message":"Test","secret":"[REDACTED]"},"base_url":"https://sms.convo.chat/api","timeout":30,"request_time":"2024-01-15 10:31:12"}
```

## 🔧 Configuración Avanzada

### Configuración Personalizada

```php
// En config/convochat.php
return [
    'api_key' => env('CONVOCHAT_API_KEY'),
    'base_url' => env('CONVOCHAT_BASE_URL', 'https://sms.convo.chat/api'),
    'timeout' => env('CONVOCHAT_TIMEOUT', 30),
    'log_requests' => env('CONVOCHAT_LOG_REQUESTS', false),
];
```

### Inyección de Dependencias

```php
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;

class MyController extends Controller
{
    protected ConvoChatSmsService $smsService;
    
    public function __construct(ConvoChatSmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    
    public function sendSms()
    {
        $result = $this->smsService->sendSms([
            'phone' => '+522221234567',
            'message' => 'Mensaje desde inyección de dependencias',
            'mode' => 'devices'
        ]);
        
        return response()->json($result);
    }
}
```

### Configuración de Cliente HTTP Personalizado

```php
use GuzzleHttp\Client;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;

$client = new Client([
    'timeout' => 60,
    'verify' => false, // Solo para desarrollo
    'headers' => [
        'User-Agent' => 'MyApp/1.0'
    ]
]);

$smsService = new ConvoChatSmsService($client, [
    'api_key' => 'tu_api_key',
    'base_url' => 'https://sms.convo.chat/api',
    'timeout' => 60
]);
```

## 📞 Soporte

Para soporte técnico o preguntas sobre el SDK:

- **Email:** support@convo.chat
- **Documentación:** https://github.com/RenatoAscencio/convochatsms#readme
- **Issues:** https://github.com/RenatoAscencio/convochatsms/issues

## 📄 Licencia

Este SDK está licenciado bajo la Licencia MIT. Ver el archivo LICENSE para más detalles.