<?php

namespace ConvoChat\LaravelSmsGateway\Services;

class ConvoChatWhatsAppService extends BaseConvoChatService
{
    public const DEFAULT_TYPE = 'text';
    public const MEDIA_TYPE = 'media';
    public const DOCUMENT_TYPE = 'document';
    public const DEFAULT_PRIORITY = 2;
    public const WHATSAPP_ENDPOINT = '/send/whatsapp';
    public const WHATSAPP_BULK_ENDPOINT = '/send/whatsapp.bulk';
    public const WHATSAPP_PENDING_ENDPOINT = '/get/wa.pending';
    public const WHATSAPP_RECEIVED_ENDPOINT = '/get/wa.received';
    public const WHATSAPP_SENT_ENDPOINT = '/get/wa.sent';
    public const WHATSAPP_MESSAGE_ENDPOINT = '/get/wa.message';
    public const WHATSAPP_CAMPAIGNS_ENDPOINT = '/get/wa.campaigns';
    public const WHATSAPP_GROUPS_ENDPOINT = '/get/wa.groups';
    public const WHATSAPP_GROUP_CONTACTS_ENDPOINT = '/get/wa.group.contacts';
    public const WHATSAPP_QR_ENDPOINT = '/get/wa.qr';
    public const WHATSAPP_SERVERS_ENDPOINT = '/get/wa.servers';
    public const WHATSAPP_ACCOUNTS_ENDPOINT = '/get/wa.accounts';
    public const WHATSAPP_INFO_ENDPOINT = '/get/wa.info';
    public const WHATSAPP_VALIDATE_ENDPOINT = '/validate/whatsapp';
    public const WHATSAPP_START_CAMPAIGN_ENDPOINT = '/remote/start.chats';
    public const WHATSAPP_STOP_CAMPAIGN_ENDPOINT = '/remote/stop.chats';
    public const WHATSAPP_LINK_ENDPOINT = '/create/wa.link';
    public const WHATSAPP_RELINK_ENDPOINT = '/create/wa.relink';
    public const WHATSAPP_DELETE_RECEIVED_ENDPOINT = '/delete/wa.received';
    public const WHATSAPP_DELETE_SENT_ENDPOINT = '/delete/wa.sent';
    public const WHATSAPP_DELETE_ACCOUNT_ENDPOINT = '/delete/wa.account';
    public const WHATSAPP_DELETE_CAMPAIGN_ENDPOINT = '/delete/wa.campaign';
    public const SUBSCRIPTION_ENDPOINT = '/get/subscription';

    protected function getServiceName(): string
    {
        return 'WhatsApp';
    }

    public function sendMessage(array $params): array
    {
        $requiredParams = ['account', 'recipient', 'message'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge($params, [
            'secret' => $this->apiKey,
            'type' => $params['type'] ?? self::DEFAULT_TYPE,
        ]);

        return $this->makeRequest(self::WHATSAPP_ENDPOINT, $data);
    }

    public function sendText(string $account, string $recipient, string $message, int $priority = self::DEFAULT_PRIORITY): array
    {
        return $this->sendMessage([
            'account' => $account,
            'recipient' => $recipient,
            'message' => $message,
            'type' => self::DEFAULT_TYPE,
            'priority' => $priority,
        ]);
    }

    public function sendMedia(string $account, string $recipient, string $message, string $mediaUrl, string $mediaType = 'image', int $priority = self::DEFAULT_PRIORITY): array
    {
        return $this->sendMessage([
            'account' => $account,
            'recipient' => $recipient,
            'message' => $message,
            'type' => self::MEDIA_TYPE,
            'media_url' => $mediaUrl,
            'media_type' => $mediaType,
            'priority' => $priority,
        ]);
    }

    public function sendDocument(string $account, string $recipient, string $message, string $documentUrl, string $documentName, string $documentType = 'pdf', int $priority = self::DEFAULT_PRIORITY): array
    {
        return $this->sendMessage([
            'account' => $account,
            'recipient' => $recipient,
            'message' => $message,
            'type' => self::DOCUMENT_TYPE,
            'document_url' => $documentUrl,
            'document_name' => $documentName,
            'document_type' => $documentType,
            'priority' => $priority,
        ]);
    }

    public function sendBulkWhatsApp(array $recipients, string $message, array $options = []): array
    {
        $data = array_merge($options, [
            'secret' => $this->apiKey,
            'recipients' => $recipients,
            'message' => $message,
        ]);

        return $this->makeRequest(self::WHATSAPP_BULK_ENDPOINT, $data);
    }

    public function getWhatsAppPending(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::WHATSAPP_PENDING_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppReceived(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::WHATSAPP_RECEIVED_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppSent(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::WHATSAPP_SENT_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppMessage(int $messageId, string $type): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $messageId,
            'type' => $type,
        ];

        return $this->makeRequest(self::WHATSAPP_MESSAGE_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppCampaigns(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::WHATSAPP_CAMPAIGNS_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppGroups(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::WHATSAPP_GROUPS_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppGroupContacts(string $groupId, array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
            'group' => $groupId,
        ]);

        return $this->makeRequest(self::WHATSAPP_GROUP_CONTACTS_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppQr(string $accountId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'unique' => $accountId,
        ];

        return $this->makeRequest(self::WHATSAPP_QR_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppServers(): array
    {
        return $this->makeRequest(self::WHATSAPP_SERVERS_ENDPOINT, [
            'secret' => $this->apiKey,
        ], 'GET');
    }

    public function getWhatsAppAccounts(): array
    {
        return $this->makeRequest(self::WHATSAPP_ACCOUNTS_ENDPOINT, [
            'secret' => $this->apiKey,
        ], 'GET');
    }

    public function getWhatsAppInfo(string $accountId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'unique' => $accountId,
        ];

        return $this->makeRequest(self::WHATSAPP_INFO_ENDPOINT, $data, 'GET');
    }

    public function validateWhatsAppNumber(string $accountId, string $phone): array
    {
        $data = [
            'secret' => $this->apiKey,
            'unique' => $accountId,
            'phone' => $phone,
        ];

        return $this->makeRequest(self::WHATSAPP_VALIDATE_ENDPOINT, $data, 'GET');
    }

    public function startWhatsAppCampaign(int $campaignId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'campaign' => $campaignId,
        ];

        return $this->makeRequest(self::WHATSAPP_START_CAMPAIGN_ENDPOINT, $data, 'GET');
    }

    public function stopWhatsAppCampaign(int $campaignId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'campaign' => $campaignId,
        ];

        return $this->makeRequest(self::WHATSAPP_STOP_CAMPAIGN_ENDPOINT, $data, 'GET');
    }

    public function getWhatsAppSubscription(): array
    {
        return $this->makeRequest(self::SUBSCRIPTION_ENDPOINT, [
            'secret' => $this->apiKey,
        ], 'GET');
    }

    public function linkWhatsAppAccount(?int $serverId = null): array
    {
        $data = [
            'secret' => $this->apiKey,
        ];

        if ($serverId !== null) {
            $data['sid'] = $serverId;
        }

        return $this->makeRequest(self::WHATSAPP_LINK_ENDPOINT, $data, 'GET');
    }

    public function relinkWhatsAppAccount(string $uniqueId, ?int $serverId = null): array
    {
        $data = [
            'secret' => $this->apiKey,
            'unique' => $uniqueId,
        ];

        if ($serverId !== null) {
            $data['sid'] = $serverId;
        }

        return $this->makeRequest(self::WHATSAPP_RELINK_ENDPOINT, $data, 'GET');
    }

    public function deleteWhatsAppReceived(int $messageId): array
    {
        return $this->makeRequest(self::WHATSAPP_DELETE_RECEIVED_ENDPOINT, [
            'secret' => $this->apiKey,
            'id' => $messageId,
        ], 'GET');
    }

    public function deleteWhatsAppSent(int $messageId): array
    {
        return $this->makeRequest(self::WHATSAPP_DELETE_SENT_ENDPOINT, [
            'secret' => $this->apiKey,
            'id' => $messageId,
        ], 'GET');
    }

    public function deleteWhatsAppAccount(string $uniqueId): array
    {
        return $this->makeRequest(self::WHATSAPP_DELETE_ACCOUNT_ENDPOINT, [
            'secret' => $this->apiKey,
            'unique' => $uniqueId,
        ], 'GET');
    }

    public function deleteWhatsAppCampaign(int $campaignId): array
    {
        return $this->makeRequest(self::WHATSAPP_DELETE_CAMPAIGN_ENDPOINT, [
            'secret' => $this->apiKey,
            'id' => $campaignId,
        ], 'GET');
    }
}
