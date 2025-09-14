<?php

namespace ConvoChat\LaravelSmsGateway\Facades;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ConvoChatSmsService sms()
 * @method static ConvoChatWhatsAppService whatsapp()
 *
 * SMS Methods:
 * @method static array sendSms(array $params)
 * @method static array sendSmsWithDevice(string $phone, string $message, string $deviceId, array $options = [])
 * @method static array sendSmsWithCredits(string $phone, string $message, ?string $gatewayId = null, array $options = [])
 * @method static array getDevices()
 * @method static array getCredits()
 * @method static array getGatewayRates()
 * @method static array getSubscription()
 *
 * WhatsApp Methods:
 * @method static array sendMessage(array $params)
 * @method static array sendText(string $account, string $recipient, string $message, int $priority = 2)
 * @method static array sendMedia(string $account, string $recipient, string $message, string $mediaUrl, string $mediaType = 'image', int $priority = 2)
 * @method static array sendDocument(string $account, string $recipient, string $message, string $documentUrl, string $documentName, string $documentType = 'pdf', int $priority = 2)
 * @method static array getWhatsAppServers()
 * @method static array linkWhatsAppAccount(int $serverId)
 * @method static array relinkWhatsAppAccount(int $serverId, string $uniqueId)
 * @method static array validateWhatsAppNumber(string $accountId, string $phone)
 * @method static array getWhatsAppAccounts()
 */
class ConvoChat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'convochat';
    }
}