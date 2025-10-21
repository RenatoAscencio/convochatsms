# ConvoChat SDK - Ejemplos Prácticos

## 🚀 Ejemplos de Uso Real

### 📱 Sistema de Notificaciones SMS

```php
<?php

namespace App\Services;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use Illuminate\Support\Facades\Log;

class SmsNotificationService
{
    /**
     * Enviar notificación de bienvenida
     */
    public function sendWelcomeNotification($user)
    {
        try {
            // Verificar créditos antes de enviar
            $credits = ConvoChat::sms()->getCredits();
            
            if ($credits['data']['credits'] < 1) {
                Log::warning('Créditos insuficientes para SMS', [
                    'user_id' => $user->id,
                    'credits' => $credits['data']['credits']
                ]);
                return false;
            }
            
            // Enviar SMS de bienvenida
            $result = ConvoChat::sms()->sendSms([
                'phone' => $user->phone,
                'message' => "¡Bienvenido {$user->name}! Tu cuenta ha sido creada exitosamente.",
                'mode' => 'credits',
                'gateway' => 'gateway456',
                'priority' => 1
            ]);
            
            Log::info('SMS de bienvenida enviado', [
                'user_id' => $user->id,
                'message_id' => $result['data']['messageId']
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error enviando SMS de bienvenida', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Enviar recordatorio de pago
     */
    public function sendPaymentReminder($user, $amount, $dueDate)
    {
        try {
            $message = "Recordatorio: Tienes un pago pendiente de \${$amount} que vence el {$dueDate}. Por favor realiza el pago a tiempo.";
            
            $result = ConvoChat::sms()->sendSms([
                'phone' => $user->phone,
                'message' => $message,
                'mode' => 'credits',
                'gateway' => 'gateway456',
                'priority' => 0 // Alta prioridad
            ]);
            
            return $result['data']['messageId'];
            
        } catch (\Exception $e) {
            Log::error('Error enviando recordatorio de pago', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Enviar notificación masiva
     */
    public function sendBulkNotification($recipients, $message)
    {
        try {
            // Dividir en lotes de 100 para evitar límites
            $batches = array_chunk($recipients, 100);
            $totalSent = 0;
            
            foreach ($batches as $batch) {
                $phones = array_column($batch, 'phone');
                
                $result = ConvoChat::sms()->sendBulkSms(
                    $phones,
                    $message,
                    [
                        'mode' => 'credits',
                        'gateway' => 'gateway456',
                        'priority' => 2
                    ]
                );
                
                $totalSent += count($phones);
                
                // Pausa entre lotes para evitar límites de velocidad
                sleep(1);
            }
            
            Log::info('Notificación masiva enviada', [
                'total_recipients' => $totalSent,
                'message_preview' => substr($message, 0, 50) . '...'
            ]);
            
            return $totalSent;
            
        } catch (\Exception $e) {
            Log::error('Error en notificación masiva', [
                'error' => $e->getMessage(),
                'recipients_count' => count($recipients)
            ]);
            throw $e;
        }
    }
}
```

### 💬 Sistema de Soporte WhatsApp

```php
<?php

namespace App\Services;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use Illuminate\Support\Facades\Log;

class WhatsAppSupportService
{
    /**
     * Enviar mensaje de soporte
     */
    public function sendSupportMessage($customer, $message, $attachments = [])
    {
        try {
            // Obtener cuenta de soporte
            $accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
            $supportAccount = collect($accounts['data'])->firstWhere('status', 'connected');
            
            if (!$supportAccount) {
                throw new \Exception('No hay cuentas de WhatsApp disponibles para soporte');
            }
            
            // Enviar mensaje principal
            $result = ConvoChat::whatsapp()->sendText(
                $supportAccount['unique'],
                $customer->phone,
                $message,
                1 // Alta prioridad
            );
            
            // Enviar archivos adjuntos si existen
            foreach ($attachments as $attachment) {
                if ($attachment['type'] === 'image') {
                    ConvoChat::whatsapp()->sendMedia(
                        $supportAccount['unique'],
                        $customer->phone,
                        'Archivo adjunto: ' . $attachment['name'],
                        $attachment['url'],
                        'image',
                        1
                    );
                } elseif ($attachment['type'] === 'document') {
                    ConvoChat::whatsapp()->sendDocument(
                        $supportAccount['unique'],
                        $customer->phone,
                        'Documento: ' . $attachment['name'],
                        $attachment['url'],
                        $attachment['name'],
                        $attachment['extension'],
                        1
                    );
                }
            }
            
            Log::info('Mensaje de soporte enviado', [
                'customer_id' => $customer->id,
                'account' => $supportAccount['unique'],
                'message_id' => $result['data']['messageId']
            ]);
            
            return $result['data']['messageId'];
            
        } catch (\Exception $e) {
            Log::error('Error enviando mensaje de soporte', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Enviar confirmación de ticket
     */
    public function sendTicketConfirmation($customer, $ticketNumber)
    {
        $message = "Hola {$customer->name}, hemos recibido tu solicitud de soporte.\n\n";
        $message .= "Número de ticket: #{$ticketNumber}\n";
        $message .= "Nuestro equipo se pondrá en contacto contigo pronto.\n\n";
        $message .= "Gracias por contactarnos.";
        
        return $this->sendSupportMessage($customer, $message);
    }
    
    /**
     * Enviar actualización de ticket
     */
    public function sendTicketUpdate($customer, $ticketNumber, $update)
    {
        $message = "Actualización del ticket #{$ticketNumber}:\n\n";
        $message .= $update . "\n\n";
        $message .= "Si necesitas más ayuda, responde a este mensaje.";
        
        return $this->sendSupportMessage($customer, $message);
    }
    
    /**
     * Obtener historial de conversaciones
     */
    public function getConversationHistory($customerPhone, $limit = 50)
    {
        try {
            $accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
            $supportAccount = collect($accounts['data'])->firstWhere('status', 'connected');
            
            if (!$supportAccount) {
                return [];
            }
            
            // Obtener mensajes enviados
            $sent = ConvoChat::whatsapp()->getWhatsAppSent([
                'limit' => $limit,
                'account' => $supportAccount['unique']
            ]);
            
            // Obtener mensajes recibidos
            $received = ConvoChat::whatsapp()->getWhatsAppReceived([
                'limit' => $limit,
                'account' => $supportAccount['unique']
            ]);
            
            // Combinar y ordenar por fecha
            $allMessages = array_merge(
                $sent['data'] ?? [],
                $received['data'] ?? []
            );
            
            usort($allMessages, function($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });
            
            return $allMessages;
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo historial de conversación', [
                'customer_phone' => $customerPhone,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
```

### 👥 Sistema de Gestión de Contactos

```php
<?php

namespace App\Services;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use Illuminate\Support\Facades\Log;

class ContactManagementService
{
    /**
     * Importar contactos desde CSV
     */
    public function importContactsFromCsv($csvData)
    {
        try {
            // Crear grupo para la importación
            $group = ConvoChat::contacts()->createGroup([
                'name' => 'Importación CSV - ' . date('Y-m-d H:i:s')
            ]);
            $groupId = $group['data']['id'];
            
            $imported = [];
            $errors = [];
            
            foreach ($csvData as $row) {
                try {
                    $result = ConvoChat::contacts()->createContact([
                        'name' => $row['name'],
                        'phone' => $row['phone'],
                        'groups' => $groupId
                    ]);
                    
                    $imported[] = [
                        'name' => $row['name'],
                        'phone' => $row['phone'],
                        'status' => 'success'
                    ];
                    
                } catch (\Exception $e) {
                    $errors[] = [
                        'name' => $row['name'],
                        'phone' => $row['phone'],
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            Log::info('Importación de contactos completada', [
                'group_id' => $groupId,
                'imported_count' => count($imported),
                'error_count' => count($errors)
            ]);
            
            return [
                'success' => true,
                'group_id' => $groupId,
                'imported' => $imported,
                'errors' => $errors,
                'total_imported' => count($imported),
                'total_errors' => count($errors)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error en importación de contactos', [
                'error' => $e->getMessage(),
                'csv_rows' => count($csvData)
            ]);
            throw $e;
        }
    }
    
    /**
     * Sincronizar contactos con grupos
     */
    public function syncContactsWithGroups($contacts, $groupMappings)
    {
        try {
            $synced = [];
            
            foreach ($contacts as $contact) {
                $groups = [];
                
                // Determinar grupos basado en criterios
                foreach ($groupMappings as $criteria => $groupId) {
                    if ($this->contactMatchesCriteria($contact, $criteria)) {
                        $groups[] = $groupId;
                    }
                }
                
                if (!empty($groups)) {
                    $result = ConvoChat::contacts()->createContact([
                        'name' => $contact['name'],
                        'phone' => $contact['phone'],
                        'groups' => implode(',', $groups)
                    ]);
                    
                    $synced[] = [
                        'contact' => $contact,
                        'groups' => $groups,
                        'status' => 'success'
                    ];
                }
            }
            
            return $synced;
            
        } catch (\Exception $e) {
            Log::error('Error sincronizando contactos', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Limpiar contactos dados de baja
     */
    public function cleanUnsubscribedContacts()
    {
        try {
            $unsubscribed = ConvoChat::contacts()->getUnsubscribed(['limit' => 1000]);
            $cleaned = 0;
            
            foreach ($unsubscribed['data'] as $contact) {
                ConvoChat::contacts()->deleteUnsubscribed($contact['id']);
                $cleaned++;
            }
            
            Log::info('Contactos dados de baja limpiados', [
                'cleaned_count' => $cleaned
            ]);
            
            return $cleaned;
            
        } catch (\Exception $e) {
            Log::error('Error limpiando contactos dados de baja', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Obtener estadísticas de contactos
     */
    public function getContactStats()
    {
        try {
            $contacts = ConvoChat::contacts()->getContacts(['limit' => 1000]);
            $groups = ConvoChat::contacts()->getGroups(['limit' => 100]);
            $unsubscribed = ConvoChat::contacts()->getUnsubscribed(['limit' => 1000]);
            
            return [
                'total_contacts' => count($contacts['data']),
                'total_groups' => count($groups['data']),
                'unsubscribed_count' => count($unsubscribed['data']),
                'active_contacts' => count($contacts['data']) - count($unsubscribed['data'])
            ];
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de contactos', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    private function contactMatchesCriteria($contact, $criteria)
    {
        // Implementar lógica de criterios
        // Por ejemplo: 'vip', 'premium', 'new_customer', etc.
        return false; // Placeholder
    }
}
```

### 🔐 Sistema de Verificación OTP

```php
<?php

namespace App\Services;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpVerificationService
{
    /**
     * Enviar código de verificación
     */
    public function sendVerificationCode($phone, $type = 'sms', $purpose = 'verification')
    {
        try {
            // Generar código de 6 dígitos
            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Crear mensaje personalizado según el propósito
            $message = $this->getOtpMessage($purpose, $otpCode);
            
            // Enviar OTP
            $result = ConvoChat::otp()->sendOtp([
                'type' => $type,
                'message' => $message,
                'phone' => $phone,
                'expire' => 300, // 5 minutos
                'priority' => 1,
                'mode' => 'credits',
                'gateway' => 'gateway456'
            ]);
            
            // Guardar en caché para verificación
            $cacheKey = "otp_verification_{$phone}_{$purpose}";
            Cache::put($cacheKey, [
                'code' => $otpCode,
                'attempts' => 0,
                'sent_at' => now()
            ], 300); // 5 minutos
            
            Log::info('Código OTP enviado', [
                'phone' => $phone,
                'type' => $type,
                'purpose' => $purpose,
                'expires_in' => 300
            ]);
            
            return [
                'success' => true,
                'expires_in' => 300,
                'message' => 'Código enviado exitosamente'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error enviando código OTP', [
                'phone' => $phone,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Error enviando código de verificación'
            ];
        }
    }
    
    /**
     * Verificar código OTP
     */
    public function verifyCode($phone, $code, $purpose = 'verification')
    {
        try {
            $cacheKey = "otp_verification_{$phone}_{$purpose}";
            $cachedData = Cache::get($cacheKey);
            
            if (!$cachedData) {
                return [
                    'success' => false,
                    'error' => 'Código expirado o no encontrado'
                ];
            }
            
            // Verificar intentos
            if ($cachedData['attempts'] >= 3) {
                Cache::forget($cacheKey);
                return [
                    'success' => false,
                    'error' => 'Demasiados intentos fallidos'
                ];
            }
            
            // Verificar código
            if ($cachedData['code'] === $code) {
                Cache::forget($cacheKey);
                
                Log::info('Código OTP verificado exitosamente', [
                    'phone' => $phone,
                    'purpose' => $purpose
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Código verificado exitosamente'
                ];
            } else {
                // Incrementar intentos
                $cachedData['attempts']++;
                Cache::put($cacheKey, $cachedData, 300);
                
                return [
                    'success' => false,
                    'error' => 'Código incorrecto',
                    'remaining_attempts' => 3 - $cachedData['attempts']
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Error verificando código OTP', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Error verificando código'
            ];
        }
    }
    
    /**
     * Verificar número de teléfono
     */
    public function verifyPhoneNumber($phone, $type = 'sms')
    {
        try {
            // Enviar código de verificación
            $sendResult = $this->sendVerificationCode($phone, $type, 'phone_verification');
            
            if (!$sendResult['success']) {
                return $sendResult;
            }
            
            return [
                'success' => true,
                'message' => 'Código de verificación enviado',
                'expires_in' => 300
            ];
            
        } catch (\Exception $e) {
            Log::error('Error verificando número de teléfono', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Error verificando número de teléfono'
            ];
        }
    }
    
    /**
     * Verificar cambio de contraseña
     */
    public function verifyPasswordChange($phone, $type = 'sms')
    {
        try {
            $sendResult = $this->sendVerificationCode($phone, $type, 'password_change');
            
            if (!$sendResult['success']) {
                return $sendResult;
            }
            
            return [
                'success' => true,
                'message' => 'Código de verificación enviado para cambio de contraseña',
                'expires_in' => 300
            ];
            
        } catch (\Exception $e) {
            Log::error('Error enviando código para cambio de contraseña', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Error enviando código de verificación'
            ];
        }
    }
    
    private function getOtpMessage($purpose, $code)
    {
        $messages = [
            'verification' => "Tu código de verificación es: {$code}. Válido por 5 minutos.",
            'phone_verification' => "Código para verificar tu número: {$code}. No compartas este código.",
            'password_change' => "Código para cambiar tu contraseña: {$code}. Si no solicitaste este cambio, ignora este mensaje.",
            'login' => "Tu código de acceso es: {$code}. Válido por 5 minutos.",
            'transaction' => "Código para confirmar tu transacción: {$code}. No compartas este código con nadie."
        ];
        
        return $messages[$purpose] ?? $messages['verification'];
    }
}
```

### 📊 Sistema de Reportes y Analytics

```php
<?php

namespace App\Services;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use Illuminate\Support\Facades\Log;

class ConvoChatAnalyticsService
{
    /**
     * Generar reporte de SMS
     */
    public function generateSmsReport($dateFrom, $dateTo)
    {
        try {
            $report = [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo
                ],
                'summary' => [],
                'details' => []
            ];
            
            // Obtener mensajes enviados
            $sent = ConvoChat::sms()->getSmsSent([
                'limit' => 1000,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]);
            
            // Obtener mensajes recibidos
            $received = ConvoChat::sms()->getSmsReceived([
                'limit' => 1000,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]);
            
            // Calcular estadísticas
            $report['summary'] = [
                'total_sent' => count($sent['data']),
                'total_received' => count($received['data']),
                'delivery_rate' => $this->calculateDeliveryRate($sent['data']),
                'response_rate' => $this->calculateResponseRate($sent['data'], $received['data'])
            ];
            
            $report['details'] = [
                'sent_messages' => $sent['data'],
                'received_messages' => $received['data']
            ];
            
            return $report;
            
        } catch (\Exception $e) {
            Log::error('Error generando reporte de SMS', [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Generar reporte de WhatsApp
     */
    public function generateWhatsAppReport($dateFrom, $dateTo)
    {
        try {
            $report = [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo
                ],
                'summary' => [],
                'details' => []
            ];
            
            // Obtener mensajes enviados
            $sent = ConvoChat::whatsapp()->getWhatsAppSent([
                'limit' => 1000,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]);
            
            // Obtener mensajes recibidos
            $received = ConvoChat::whatsapp()->getWhatsAppReceived([
                'limit' => 1000,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]);
            
            // Calcular estadísticas
            $report['summary'] = [
                'total_sent' => count($sent['data']),
                'total_received' => count($received['data']),
                'delivery_rate' => $this->calculateDeliveryRate($sent['data']),
                'response_rate' => $this->calculateResponseRate($sent['data'], $received['data'])
            ];
            
            $report['details'] = [
                'sent_messages' => $sent['data'],
                'received_messages' => $received['data']
            ];
            
            return $report;
            
        } catch (\Exception $e) {
            Log::error('Error generando reporte de WhatsApp', [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Obtener estadísticas de cuenta
     */
    public function getAccountStats()
    {
        try {
            $stats = [];
            
            // Créditos disponibles
            $credits = ConvoChat::sms()->getCredits();
            $stats['credits'] = $credits['data'];
            
            // Suscripción
            $subscription = ConvoChat::sms()->getSubscription();
            $stats['subscription'] = $subscription['data'];
            
            // Dispositivos SMS
            $devices = ConvoChat::sms()->getDevices();
            $stats['sms_devices'] = count($devices['data']);
            
            // Cuentas WhatsApp
            $accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
            $stats['whatsapp_accounts'] = count($accounts['data']);
            $stats['whatsapp_connected'] = count(array_filter($accounts['data'], function($account) {
                return $account['status'] === 'connected';
            }));
            
            // Contactos
            $contacts = ConvoChat::contacts()->getContacts(['limit' => 1000]);
            $stats['total_contacts'] = count($contacts['data']);
            
            // Grupos
            $groups = ConvoChat::contacts()->getGroups(['limit' => 100]);
            $stats['total_groups'] = count($groups['data']);
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de cuenta', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Monitorear salud del servicio
     */
    public function checkServiceHealth()
    {
        try {
            $health = [
                'timestamp' => now(),
                'status' => 'healthy',
                'checks' => []
            ];
            
            // Verificar conectividad SMS
            try {
                $credits = ConvoChat::sms()->getCredits();
                $health['checks']['sms_api'] = [
                    'status' => 'ok',
                    'response_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
                ];
            } catch (\Exception $e) {
                $health['checks']['sms_api'] = [
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
                $health['status'] = 'degraded';
            }
            
            // Verificar conectividad WhatsApp
            try {
                $accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
                $health['checks']['whatsapp_api'] = [
                    'status' => 'ok',
                    'response_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
                ];
            } catch (\Exception $e) {
                $health['checks']['whatsapp_api'] = [
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
                $health['status'] = 'degraded';
            }
            
            return $health;
            
        } catch (\Exception $e) {
            Log::error('Error verificando salud del servicio', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'timestamp' => now(),
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function calculateDeliveryRate($sentMessages)
    {
        if (empty($sentMessages)) {
            return 0;
        }
        
        $delivered = array_filter($sentMessages, function($message) {
            return $message['status'] === 'delivered';
        });
        
        return (count($delivered) / count($sentMessages)) * 100;
    }
    
    private function calculateResponseRate($sentMessages, $receivedMessages)
    {
        if (empty($sentMessages)) {
            return 0;
        }
        
        // Lógica simplificada para calcular tasa de respuesta
        return (count($receivedMessages) / count($sentMessages)) * 100;
    }
}
```

### 🎯 Controladores de Ejemplo

```php
<?php

namespace App\Http\Controllers;

use App\Services\SmsNotificationService;
use App\Services\WhatsAppSupportService;
use App\Services\ContactManagementService;
use App\Services\OtpVerificationService;
use App\Services\ConvoChatAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConvoChatController extends Controller
{
    protected SmsNotificationService $smsService;
    protected WhatsAppSupportService $whatsappService;
    protected ContactManagementService $contactService;
    protected OtpVerificationService $otpService;
    protected ConvoChatAnalyticsService $analyticsService;
    
    public function __construct(
        SmsNotificationService $smsService,
        WhatsAppSupportService $whatsappService,
        ContactManagementService $contactService,
        OtpVerificationService $otpService,
        ConvoChatAnalyticsService $analyticsService
    ) {
        $this->smsService = $smsService;
        $this->whatsappService = $whatsappService;
        $this->contactService = $contactService;
        $this->otpService = $otpService;
        $this->analyticsService = $analyticsService;
    }
    
    /**
     * Enviar SMS de bienvenida
     */
    public function sendWelcomeSms(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $user = \App\Models\User::find($request->user_id);
        $result = $this->smsService->sendWelcomeNotification($user);
        
        return response()->json([
            'success' => $result,
            'message' => $result ? 'SMS enviado exitosamente' : 'Error enviando SMS'
        ]);
    }
    
    /**
     * Enviar mensaje de soporte WhatsApp
     */
    public function sendSupportMessage(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'message' => 'required|string|max:1000',
            'attachments' => 'array'
        ]);
        
        $customer = \App\Models\Customer::find($request->customer_id);
        $messageId = $this->whatsappService->sendSupportMessage(
            $customer,
            $request->message,
            $request->attachments ?? []
        );
        
        return response()->json([
            'success' => true,
            'message_id' => $messageId,
            'message' => 'Mensaje de soporte enviado'
        ]);
    }
    
    /**
     * Importar contactos desde CSV
     */
    public function importContacts(Request $request): JsonResponse
    {
        $request->validate([
            'csv_data' => 'required|array',
            'csv_data.*.name' => 'required|string',
            'csv_data.*.phone' => 'required|string'
        ]);
        
        $result = $this->contactService->importContactsFromCsv($request->csv_data);
        
        return response()->json($result);
    }
    
    /**
     * Enviar código OTP
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'type' => 'in:sms,whatsapp',
            'purpose' => 'string'
        ]);
        
        $result = $this->otpService->sendVerificationCode(
            $request->phone,
            $request->type ?? 'sms',
            $request->purpose ?? 'verification'
        );
        
        return response()->json($result);
    }
    
    /**
     * Verificar código OTP
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
            'purpose' => 'string'
        ]);
        
        $result = $this->otpService->verifyCode(
            $request->phone,
            $request->code,
            $request->purpose ?? 'verification'
        );
        
        return response()->json($result);
    }
    
    /**
     * Obtener estadísticas de cuenta
     */
    public function getAccountStats(): JsonResponse
    {
        $stats = $this->analyticsService->getAccountStats();
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
    
    /**
     * Generar reporte de SMS
     */
    public function generateSmsReport(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after:date_from'
        ]);
        
        $report = $this->analyticsService->generateSmsReport(
            $request->date_from,
            $request->date_to
        );
        
        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }
    
    /**
     * Verificar salud del servicio
     */
    public function checkHealth(): JsonResponse
    {
        $health = $this->analyticsService->checkServiceHealth();
        
        return response()->json($health);
    }
}
```

## 🚀 Comandos Artisan de Ejemplo

```php
<?php

namespace App\Console\Commands;

use App\Services\SmsNotificationService;
use App\Services\ContactManagementService;
use App\Services\ConvoChatAnalyticsService;
use Illuminate\Console\Command;

class ConvoChatCommands extends Command
{
    protected $signature = 'convochat:send-bulk-sms {message} {--group=} {--limit=100}';
    protected $description = 'Enviar SMS masivo a un grupo de contactos';
    
    public function handle()
    {
        $message = $this->argument('message');
        $groupId = $this->option('group');
        $limit = $this->option('limit');
        
        $this->info("Enviando SMS masivo: {$message}");
        
        // Obtener contactos del grupo
        $contacts = \ConvoChat\LaravelSmsGateway\Facades\ConvoChat::contacts()
            ->getContacts(['limit' => $limit]);
        
        $phones = array_column($contacts['data'], 'phone');
        
        // Enviar SMS masivo
        $smsService = app(SmsNotificationService::class);
        $result = $smsService->sendBulkNotification($phones, $message);
        
        $this->info("SMS enviados: {$result}");
    }
}
```

```php
<?php

namespace App\Console\Commands;

use App\Services\ConvoChatAnalyticsService;
use Illuminate\Console\Command;

class ConvoChatReportCommand extends Command
{
    protected $signature = 'convochat:generate-report {type} {--date-from=} {--date-to=}';
    protected $description = 'Generar reporte de ConvoChat';
    
    public function handle()
    {
        $type = $this->argument('type');
        $dateFrom = $this->option('date-from') ?? now()->subDays(30)->format('Y-m-d');
        $dateTo = $this->option('date-to') ?? now()->format('Y-m-d');
        
        $analyticsService = app(ConvoChatAnalyticsService::class);
        
        switch ($type) {
            case 'sms':
                $report = $analyticsService->generateSmsReport($dateFrom, $dateTo);
                break;
            case 'whatsapp':
                $report = $analyticsService->generateWhatsAppReport($dateFrom, $dateTo);
                break;
            case 'stats':
                $report = $analyticsService->getAccountStats();
                break;
            default:
                $this->error('Tipo de reporte no válido');
                return;
        }
        
        $this->info("Reporte generado exitosamente");
        $this->line(json_encode($report, JSON_PRETTY_PRINT));
    }
}
```

Estos ejemplos proporcionan una base sólida para implementar funcionalidades reales usando el SDK de ConvoChat en aplicaciones Laravel.
