<?php

namespace ConvoChat\LaravelSmsGateway\Facades;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatContactsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatOtpService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatUssdService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ConvoChatSmsService sms()
 * @method static ConvoChatWhatsAppService whatsapp()
 * @method static ConvoChatContactsService contacts()
 * @method static ConvoChatOtpService otp()
 * @method static ConvoChatUssdService ussd()
 *
 * SMS Methods:
 * @method static array sendSms(array $params)
 * @method static array sendSmsWithDevice(string $phone, string $message, string $deviceId, array $options = [])
 * @method static array sendSmsWithCredits(string $phone, string $message, ?string $gatewayId = null, array $options = [])
 * @method static array sendBulkSms(array $recipients, string $message, array $options = [])
 * @method static array getSmsPending(array $filters = [])
 * @method static array getSmsReceived(array $filters = [])
 * @method static array getSmsSent(array $filters = [])
 * @method static array getSmsMessage(int $messageId, string $type)
 * @method static array getSmsCampaigns(array $filters = [])
 * @method static array deleteSmsReceived(int $messageId)
 * @method static array deleteSmsSent(int $messageId)
 * @method static array deleteSmsCampaign(int $campaignId)
 * @method static array startSmsCampaign(int $campaignId)
 * @method static array stopSmsCampaign(int $campaignId)
 * @method static array getDevices(int $limit = 10, int $page = 1)
 * @method static array getCredits()
 * @method static array getRates()
 * @method static array getSubscription()
 * @method static array getEarnings()
 * @method static array deleteNotification(int $notificationId)
 *
 * WhatsApp Methods:
 * @method static array sendMessage(array $params)
 * @method static array sendText(string $account, string $recipient, string $message, int $priority = 2)
 * @method static array sendMedia(string $account, string $recipient, string $message, string $mediaUrl, string $mediaType = 'image', int $priority = 2)
 * @method static array sendDocument(string $account, string $recipient, string $message, string $documentUrl, string $documentName, string $documentType = 'pdf', int $priority = 2)
 * @method static array sendBulkWhatsApp(array $recipients, string $message, array $options = [])
 * @method static array getWhatsAppPending(array $filters = [])
 * @method static array getWhatsAppReceived(array $filters = [])
 * @method static array getWhatsAppSent(array $filters = [])
 * @method static array getWhatsAppMessage(int $messageId, string $type)
 * @method static array getWhatsAppCampaigns(array $filters = [])
 * @method static array getWhatsAppGroups(array $filters = [])
 * @method static array getWhatsAppGroupContacts(string $groupId, array $filters = [])
 * @method static array getWhatsAppQr(string $accountId)
 * @method static array getWhatsAppServers()
 * @method static array getWhatsAppAccounts()
 * @method static array getWhatsAppInfo(string $accountId)
 * @method static array validateWhatsAppNumber(string $accountId, string $phone)
 * @method static array startWhatsAppCampaign(int $campaignId)
 * @method static array stopWhatsAppCampaign(int $campaignId)
 * @method static array getWhatsAppSubscription()
 * @method static array linkWhatsAppAccount(?int $serverId = null)
 * @method static array relinkWhatsAppAccount(string $uniqueId, ?int $serverId = null)
 * @method static array deleteWhatsAppReceived(int $messageId)
 * @method static array deleteWhatsAppSent(int $messageId)
 * @method static array deleteWhatsAppAccount(string $uniqueId)
 * @method static array deleteWhatsAppCampaign(int $campaignId)
 *
 * Contacts Methods:
 * @method static array getContacts(array $filters = [])
 * @method static array createContact(array $params)
 * @method static array deleteContact(int $contactId)
 * @method static array getGroups(array $filters = [])
 * @method static array createGroup(array $params)
 * @method static array deleteGroup(int $groupId)
 * @method static array getUnsubscribed(array $filters = [])
 * @method static array deleteUnsubscribed(int $contactId)
 *
 * OTP Methods:
 * @method static array sendOtp(array $params)
 * @method static array verifyOtp(string $otp)
 *
 * USSD Methods:
 * @method static array sendUssd(string $code, int $sim, string $device)
 * @method static array getUssdRequests(array $filters = [])
 * @method static array deleteUssdRequest(int $requestId)
 */
class ConvoChat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'convochat';
    }
}
