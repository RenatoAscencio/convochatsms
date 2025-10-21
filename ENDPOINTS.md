# ConvoChat SDK - Guía de Endpoints

## 📋 Endpoints Disponibles

### 📱 SMS Endpoints

| Endpoint | Método | Descripción | Parámetros Requeridos |
|----------|--------|-------------|----------------------|
| `/send/sms` | POST | Enviar SMS individual | `phone`, `message`, `mode` |
| `/send/sms.bulk` | POST | Envío masivo de SMS | `recipients`, `message` |
| `/get/sms.pending` | GET | Mensajes pendientes | - |
| `/get/sms.received` | GET | Mensajes recibidos | - |
| `/get/sms.sent` | GET | Mensajes enviados | - |
| `/get/sms.message` | GET | Mensaje específico | `id`, `type` |
| `/get/sms.campaigns` | GET | Campañas SMS | - |
| `/delete/sms.received` | GET | Eliminar recibido | `id` |
| `/delete/sms.sent` | GET | Eliminar enviado | `id` |
| `/delete/sms.campaign` | GET | Eliminar campaña | `id` |
| `/remote/start.sms` | GET | Iniciar campaña | `campaign` |
| `/remote/stop.sms` | GET | Detener campaña | `campaign` |
| `/get/devices` | GET | Dispositivos disponibles | `limit`, `page` |
| `/get/credits` | GET | Créditos restantes | - |
| `/get/rates` | GET | Tarifas de gateways | - |
| `/get/subscription` | GET | Paquete de suscripción | - |

### 💬 WhatsApp Endpoints

| Endpoint | Método | Descripción | Parámetros Requeridos |
|----------|--------|-------------|----------------------|
| `/send/whatsapp` | POST | Enviar WhatsApp individual | `account`, `recipient`, `type`, `message` |
| `/send/whatsapp.bulk` | POST | Envío masivo WhatsApp | `recipients`, `message` |
| `/get/wa.pending` | GET | Mensajes pendientes | - |
| `/get/wa.received` | GET | Mensajes recibidos | - |
| `/get/wa.sent` | GET | Mensajes enviados | - |
| `/get/wa.message` | GET | Mensaje específico | `id`, `type` |
| `/get/wa.campaigns` | GET | Campañas WhatsApp | - |
| `/get/wa.groups` | GET | Grupos WhatsApp | - |
| `/get/wa.group.contacts` | GET | Contactos de grupo | `group` |
| `/get/wa.qr` | GET | Código QR | `unique` |
| `/get/wa.servers` | GET | Servidores WhatsApp | - |
| `/get/wa.accounts` | GET | Cuentas WhatsApp | - |
| `/get/wa.info` | GET | Información de cuenta | `unique` |
| `/validate/whatsapp` | GET | Validar número | `unique`, `phone` |
| `/remote/start.chats` | GET | Iniciar campaña | `campaign` |
| `/remote/stop.chats` | GET | Detener campaña | `campaign` |

### 👥 Contacts Endpoints

| Endpoint | Método | Descripción | Parámetros Requeridos |
|----------|--------|-------------|----------------------|
| `/get/contacts` | GET | Listar contactos | - |
| `/create/contact` | POST | Crear contacto | `phone`, `name`, `groups` |
| `/delete/contact` | GET | Eliminar contacto | `id` |
| `/get/groups` | GET | Listar grupos | - |
| `/create/group` | POST | Crear grupo | `name` |
| `/delete/group` | GET | Eliminar grupo | `id` |
| `/get/unsubscribed` | GET | Contactos dados de baja | - |
| `/delete/unsubscribed` | GET | Eliminar dado de baja | `id` |

### 🔐 OTP Endpoints

| Endpoint | Método | Descripción | Parámetros Requeridos |
|----------|--------|-------------|----------------------|
| `/send/otp` | POST | Enviar código OTP | `type`, `message`, `phone` |
| `/get/otp` | GET | Verificar código OTP | `otp` |

## 🔧 Ejemplos Detallados por Endpoint

### 📱 SMS - Envío Individual

```php
// Envío básico con dispositivos
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
    'priority' => 0,
    'shortener' => 1
]);
```

### 📱 SMS - Envío Masivo

```php
$result = ConvoChat::sms()->sendBulkSms(
    ['+522221234567', '+522229876543', '+522225556667'],
    'Mensaje masivo de prueba',
    [
        'mode' => 'credits',
        'gateway' => 'gateway456',
        'priority' => 1
    ]
);
```

### 📱 SMS - Consultas

```php
// Mensajes pendientes
$pending = ConvoChat::sms()->getSmsPending(['limit' => 20, 'page' => 1]);

// Mensajes recibidos
$received = ConvoChat::sms()->getSmsReceived(['limit' => 50]);

// Mensajes enviados
$sent = ConvoChat::sms()->getSmsSent(['limit' => 100]);

// Mensaje específico
$message = ConvoChat::sms()->getSmsMessage(123, 'sent');

// Campañas SMS
$campaigns = ConvoChat::sms()->getSmsCampaigns(['limit' => 25]);
```

### 📱 SMS - Gestión de Campañas

```php
// Iniciar campaña
$result = ConvoChat::sms()->startSmsCampaign(123);

// Detener campaña
$result = ConvoChat::sms()->stopSmsCampaign(123);
```

### 📱 SMS - Eliminación

```php
// Eliminar mensaje recibido
$result = ConvoChat::sms()->deleteSmsReceived(123);

// Eliminar mensaje enviado
$result = ConvoChat::sms()->deleteSmsSent(123);

// Eliminar campaña
$result = ConvoChat::sms()->deleteSmsCampaign(123);
```

### 📱 SMS - Información

```php
// Dispositivos disponibles
$devices = ConvoChat::sms()->getDevices(10, 1); // limit=10, page=1

// Créditos restantes
$credits = ConvoChat::sms()->getCredits();

// Tarifas de gateways
$rates = ConvoChat::sms()->getRates();

// Paquete de suscripción
$subscription = ConvoChat::sms()->getSubscription();
```

### 💬 WhatsApp - Envío Individual

```php
// Mensaje de texto
$result = ConvoChat::whatsapp()->sendMessage([
    'account' => 'account123',
    'recipient' => '+522221234567',
    'type' => 'text',
    'message' => 'Hola desde WhatsApp!',
    'priority' => 2
]);

// Mensaje con imagen
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

### 💬 WhatsApp - Envío Masivo

```php
$result = ConvoChat::whatsapp()->sendBulkWhatsApp(
    ['+522221234567', '+522229876543', '+522225556667'],
    'Mensaje masivo de WhatsApp',
    [
        'account' => 'account123',
        'type' => 'text',
        'priority' => 1
    ]
);
```

### 💬 WhatsApp - Consultas

```php
// Mensajes pendientes
$pending = ConvoChat::whatsapp()->getWhatsAppPending(['limit' => 20]);

// Mensajes recibidos
$received = ConvoChat::whatsapp()->getWhatsAppReceived(['limit' => 50]);

// Mensajes enviados
$sent = ConvoChat::whatsapp()->getWhatsAppSent(['limit' => 100]);

// Mensaje específico
$message = ConvoChat::whatsapp()->getWhatsAppMessage(123, 'sent');

// Campañas WhatsApp
$campaigns = ConvoChat::whatsapp()->getWhatsAppCampaigns(['limit' => 25]);

// Grupos WhatsApp
$groups = ConvoChat::whatsapp()->getWhatsAppGroups(['limit' => 10]);

// Contactos de grupo
$contacts = ConvoChat::whatsapp()->getWhatsAppGroupContacts('group123', ['limit' => 50]);
```

### 💬 WhatsApp - Gestión de Cuentas

```php
// Servidores disponibles
$servers = ConvoChat::whatsapp()->getWhatsAppServers();

// Cuentas vinculadas
$accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();

// Información de cuenta
$info = ConvoChat::whatsapp()->getWhatsAppInfo('account123');

// Código QR
$qr = ConvoChat::whatsapp()->getWhatsAppQr('account123');

// Validar número
$validation = ConvoChat::whatsapp()->validateWhatsAppNumber('account123', '+522221234567');
```

### 💬 WhatsApp - Gestión de Campañas

```php
// Iniciar campaña
$result = ConvoChat::whatsapp()->startWhatsAppCampaign(123);

// Detener campaña
$result = ConvoChat::whatsapp()->stopWhatsAppCampaign(123);
```

### 👥 Contacts - Gestión de Contactos

```php
// Listar contactos
$contacts = ConvoChat::contacts()->getContacts(['limit' => 20, 'page' => 1]);

// Crear contacto
$result = ConvoChat::contacts()->createContact([
    'phone' => '+522221234567',
    'name' => 'Juan Pérez',
    'groups' => '1,2,3'
]);

// Eliminar contacto
$result = ConvoChat::contacts()->deleteContact(123);
```

### 👥 Contacts - Gestión de Grupos

```php
// Listar grupos
$groups = ConvoChat::contacts()->getGroups(['limit' => 15]);

// Crear grupo
$result = ConvoChat::contacts()->createGroup([
    'name' => 'Clientes VIP'
]);

// Eliminar grupo
$result = ConvoChat::contacts()->deleteGroup(123);
```

### 👥 Contacts - Gestión de Bajas

```php
// Contactos dados de baja
$unsubscribed = ConvoChat::contacts()->getUnsubscribed(['limit' => 25]);

// Eliminar dado de baja
$result = ConvoChat::contacts()->deleteUnsubscribed(123);
```

### 🔐 OTP - Envío y Verificación

```php
// Enviar OTP por SMS
$result = ConvoChat::otp()->sendOtp([
    'type' => 'sms',
    'message' => 'Tu código de verificación es {{otp}}',
    'phone' => '+522221234567',
    'expire' => 300,
    'priority' => 1,
    'mode' => 'credits',
    'gateway' => 'gateway456'
]);

// Enviar OTP por WhatsApp
$result = ConvoChat::otp()->sendOtp([
    'type' => 'whatsapp',
    'message' => 'Tu código de verificación es {{otp}}',
    'phone' => '+522221234567',
    'account' => 'account123',
    'expire' => 600,
    'priority' => 1
]);

// Verificar OTP
$result = ConvoChat::otp()->verifyOtp('123456');
```

## 🔄 Flujos de Trabajo Comunes

### Flujo de Envío de SMS

```php
class SmsWorkflow
{
    public function sendSmsWorkflow($phone, $message)
    {
        try {
            // 1. Verificar créditos
            $credits = ConvoChat::sms()->getCredits();
            if ($credits['data']['credits'] < 1) {
                throw new \Exception('Créditos insuficientes');
            }
            
            // 2. Obtener dispositivos disponibles
            $devices = ConvoChat::sms()->getDevices();
            if (empty($devices['data'])) {
                throw new \Exception('No hay dispositivos disponibles');
            }
            
            // 3. Enviar SMS
            $result = ConvoChat::sms()->sendSms([
                'phone' => $phone,
                'message' => $message,
                'mode' => 'devices',
                'device' => $devices['data'][0]['id'],
                'priority' => 1
            ]);
            
            // 4. Verificar envío
            if ($result['status'] == 200) {
                return [
                    'success' => true,
                    'message_id' => $result['data']['messageId']
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
```

### Flujo de Gestión de Contactos

```php
class ContactWorkflow
{
    public function importContactsWorkflow($contactsData)
    {
        try {
            // 1. Crear grupo
            $group = ConvoChat::contacts()->createGroup([
                'name' => 'Importación ' . date('Y-m-d H:i:s')
            ]);
            $groupId = $group['data']['id'];
            
            // 2. Importar contactos
            $imported = [];
            foreach ($contactsData as $contact) {
                $result = ConvoChat::contacts()->createContact([
                    'name' => $contact['name'],
                    'phone' => $contact['phone'],
                    'groups' => $groupId
                ]);
                $imported[] = $result;
            }
            
            // 3. Verificar importación
            $groupContacts = ConvoChat::contacts()->getContacts(['limit' => 1000]);
            
            return [
                'success' => true,
                'group_id' => $groupId,
                'imported_count' => count($imported),
                'total_contacts' => count($groupContacts['data'])
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
```

### Flujo de Verificación OTP

```php
class OtpWorkflow
{
    public function sendVerificationWorkflow($phone, $type = 'sms')
    {
        try {
            // 1. Enviar OTP
            $result = ConvoChat::otp()->sendOtp([
                'type' => $type,
                'message' => 'Tu código de verificación es {{otp}}. Válido por 5 minutos.',
                'phone' => $phone,
                'expire' => 300,
                'priority' => 1,
                'mode' => 'credits',
                'gateway' => 'gateway456'
            ]);
            
            // 2. Guardar en caché para verificación
            $otpCode = $result['data']['otp'];
            cache()->put("otp_{$phone}", $otpCode, 300); // 5 minutos
            
            return [
                'success' => true,
                'expires_in' => 300,
                'message' => 'Código enviado exitosamente'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function verifyCodeWorkflow($phone, $otp)
    {
        try {
            // 1. Verificar OTP
            $result = ConvoChat::otp()->verifyOtp($otp);
            
            // 2. Verificar en caché
            $cachedOtp = cache()->get("otp_{$phone}");
            
            if ($result['status'] == 200 && $cachedOtp == $otp) {
                // 3. Limpiar caché
                cache()->forget("otp_{$phone}");
                
                return [
                    'success' => true,
                    'message' => 'Código verificado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Código inválido o expirado'
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
```

## 📊 Respuestas de la API

### Estructura de Respuesta Exitosa

```json
{
    "status": 200,
    "message": "Message has been queued for sending!",
    "data": {
        "messageId": 123
    }
}
```

### Estructura de Respuesta de Error

```json
{
    "status": 400,
    "message": "Invalid Parameters!",
    "data": false
}
```

### Respuestas Específicas por Endpoint

#### SMS - Créditos
```json
{
    "status": 200,
    "message": "Remaining Credits",
    "data": {
        "credits": "798.634",
        "currency": "GBP"
    }
}
```

#### WhatsApp - Cuentas
```json
{
    "status": 200,
    "message": "WhatsApp Accounts",
    "data": [
        {
            "unique": "acc1",
            "phone": "+573001111111",
            "status": "connected"
        },
        {
            "unique": "acc2", 
            "phone": "+573002222222",
            "status": "disconnected"
        }
    ]
}
```

#### OTP - Envío
```json
{
    "status": 200,
    "message": "OTP has been sent!",
    "data": {
        "phone": "+522221234567",
        "message": "Your OTP is 345678",
        "messageid": 123,
        "otp": 345678
    }
}
```

## 🚨 Códigos de Error Comunes

| Código | Descripción | Solución |
|--------|-------------|----------|
| 400 | Parámetros inválidos | Verificar parámetros requeridos |
| 401 | API key inválida | Verificar CONVOCHAT_API_KEY |
| 403 | Permisos insuficientes | Contactar soporte para permisos |
| 404 | Recurso no encontrado | Verificar IDs de recursos |
| 429 | Límite de velocidad | Reducir frecuencia de peticiones |
| 500 | Error interno | Contactar soporte técnico |

## 🔧 Configuración de Desarrollo

### Variables de Entorno para Desarrollo

```env
# Desarrollo
CONVOCHAT_API_KEY=tu_api_key_desarrollo
CONVOCHAT_BASE_URL=https://sms.convo.chat/api
CONVOCHAT_TIMEOUT=30
CONVOCHAT_LOG_REQUESTS=true

# Producción
CONVOCHAT_API_KEY=tu_api_key_produccion
CONVOCHAT_BASE_URL=https://sms.convo.chat/api
CONVOCHAT_TIMEOUT=60
CONVOCHAT_LOG_REQUESTS=false
```

### Configuración de Logs para Desarrollo

```php
// En config/logging.php
'channels' => [
    'convochat_dev' => [
        'driver' => 'daily',
        'path' => storage_path('logs/convochat-dev.log'),
        'level' => 'debug',
        'days' => 7,
    ],
],
```

### Testing con Mocks

```php
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SmsServiceTest extends TestCase
{
    public function testSendSms()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'message' => 'Message has been queued for sending!',
                'data' => ['messageId' => 123]
            ])),
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        $smsService = new ConvoChatSmsService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30
        ]);
        
        $result = $smsService->sendSms([
            'phone' => '+522221234567',
            'message' => 'Test message',
            'mode' => 'devices'
        ]);
        
        $this->assertEquals(200, $result['status']);
        $this->assertEquals(123, $result['data']['messageId']);
    }
}
```
