<?php

namespace ConvoChat\LaravelSmsGateway\Facades;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatAuthService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatCampaignsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatContactsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatListsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatReportsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSettingsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWebhooksService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ConvoChatAuthService auth()
 * @method static ConvoChatSmsService sms()
 * @method static ConvoChatWhatsAppService whatsapp()
 * @method static ConvoChatContactsService contacts()
 * @method static ConvoChatListsService lists()
 * @method static ConvoChatCampaignsService campaigns()
 * @method static ConvoChatWebhooksService webhooks()
 * @method static ConvoChatSettingsService settings()
 * @method static ConvoChatReportsService reports()
 *
 * Auth Methods:
 * @method static array login(string $email, string $password)
 * @method static array register(array $userData)
 * @method static array logout()
 * @method static array getUser()
 *
 * SMS Methods:
 * @method static array sendSms(array $params)
 * @method static array sendSmsWithDevice(string $phone, string $message, string $deviceId, array $options = [])
 * @method static array sendSmsWithCredits(string $phone, string $message, ?string $gatewayId = null, array $options = [])
 * @method static array sendSmsApi(array $params)
 * @method static array sendBulkSms(array $recipients, string $message, array $options = [])
 * @method static array getSmsHistory(array $filters = [])
 * @method static array getSmsDetail(string $smsId)
 * @method static array deleteSms(string $smsId)
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
 * @method static array sendWhatsAppApi(array $params)
 * @method static array sendWhatsAppMedia(array $params)
 * @method static array getWhatsAppHistory(array $filters = [])
 * @method static array getWhatsAppDevices()
 * @method static array connectWhatsAppDevice(array $deviceData)
 * @method static array disconnectWhatsAppDevice(string $deviceId)
 * @method static array getWhatsAppServers()
 * @method static array linkWhatsAppAccount(int $serverId)
 * @method static array relinkWhatsAppAccount(int $serverId, string $uniqueId)
 * @method static array validateWhatsAppNumber(string $accountId, string $phone)
 * @method static array getWhatsAppAccounts()
 *
 * Contacts Methods:
 * @method static array getContacts(array $filters = [])
 * @method static array createContact(array $contactData)
 * @method static array getContact(string $contactId)
 * @method static array updateContact(string $contactId, array $contactData)
 * @method static array deleteContact(string $contactId)
 *
 * Lists Methods:
 * @method static array getLists(array $filters = [])
 * @method static array createList(array $listData)
 * @method static array getList(string $listId)
 * @method static array updateList(string $listId, array $listData)
 * @method static array deleteList(string $listId)
 *
 * Campaigns Methods:
 * @method static array getCampaigns(array $filters = [])
 * @method static array createCampaign(array $campaignData)
 * @method static array getCampaign(string $campaignId)
 * @method static array updateCampaign(string $campaignId, array $campaignData)
 * @method static array deleteCampaign(string $campaignId)
 *
 * Webhooks Methods:
 * @method static array getWebhooks(array $filters = [])
 * @method static array createWebhook(array $webhookData)
 * @method static array deleteWebhook(string $webhookId)
 *
 * Settings Methods:
 * @method static array getSettings()
 * @method static array updateSettings(array $settingsData)
 * @method static array getBalance()
 *
 * Reports Methods:
 * @method static array getSmsReport(array $filters = [])
 * @method static array getWhatsAppReport(array $filters = [])
 * @method static array getCampaignsReport(array $filters = [])
 */
class ConvoChat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'convochat';
    }
}
