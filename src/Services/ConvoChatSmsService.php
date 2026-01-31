<?php

namespace ConvoChat\LaravelSmsGateway\Services;

class ConvoChatSmsService extends BaseConvoChatService
{
    public const DEFAULT_MODE = 'devices';
    public const CREDITS_MODE = 'credits';
    public const SMS_ENDPOINT = '/send/sms';
    public const SMS_BULK_ENDPOINT = '/send/sms.bulk';
    public const SMS_PENDING_ENDPOINT = '/get/sms.pending';
    public const SMS_RECEIVED_ENDPOINT = '/get/sms.received';
    public const SMS_SENT_ENDPOINT = '/get/sms.sent';
    public const SMS_MESSAGE_ENDPOINT = '/get/sms.message';
    public const SMS_CAMPAIGNS_ENDPOINT = '/get/sms.campaigns';
    public const SMS_DELETE_RECEIVED_ENDPOINT = '/delete/sms.received';
    public const SMS_DELETE_SENT_ENDPOINT = '/delete/sms.sent';
    public const SMS_DELETE_CAMPAIGN_ENDPOINT = '/delete/sms.campaign';
    public const SMS_START_CAMPAIGN_ENDPOINT = '/remote/start.sms';
    public const SMS_STOP_CAMPAIGN_ENDPOINT = '/remote/stop.sms';
    public const DEVICES_ENDPOINT = '/get/devices';
    public const CREDITS_ENDPOINT = '/get/credits';
    public const SUBSCRIPTION_ENDPOINT = '/get/subscription';
    public const RATES_ENDPOINT = '/get/rates';
    public const EARNINGS_ENDPOINT = '/get/earnings';
    public const DELETE_NOTIFICATION_ENDPOINT = '/delete/notification';

    protected function getServiceName(): string
    {
        return 'SMS';
    }

    public function sendSms(array $params): array
    {
        $requiredParams = ['phone', 'message'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge($params, [
            'secret' => $this->apiKey,
            'mode' => $params['mode'] ?? self::DEFAULT_MODE,
        ]);

        return $this->makeRequest(self::SMS_ENDPOINT, $data);
    }

    public function sendSmsWithDevice(string $phone, string $message, string $deviceId, array $options = []): array
    {
        $params = array_merge([
            'phone' => $phone,
            'message' => $message,
            'device' => $deviceId,
            'mode' => self::DEFAULT_MODE,
        ], $options);

        return $this->sendSms($params);
    }

    public function sendSmsWithCredits(string $phone, string $message, ?string $gatewayId = null, array $options = []): array
    {
        $params = array_merge([
            'phone' => $phone,
            'message' => $message,
            'mode' => self::CREDITS_MODE,
        ], $options);

        if ($gatewayId) {
            $params['gateway'] = $gatewayId;
        }

        return $this->sendSms($params);
    }

    public function getDevices(int $limit = 10, int $page = 1): array
    {
        $data = [
            'secret' => $this->apiKey,
            'limit' => $limit,
            'page' => $page,
        ];

        return $this->makeRequest(self::DEVICES_ENDPOINT, $data, 'GET');
    }

    public function getCredits(): array
    {
        return $this->makeRequest(self::CREDITS_ENDPOINT, [
            'secret' => $this->apiKey,
        ]);
    }

    public function getRates(): array
    {
        return $this->makeRequest(self::RATES_ENDPOINT, [
            'secret' => $this->apiKey,
        ], 'GET');
    }

    public function getSubscription(): array
    {
        return $this->makeRequest(self::SUBSCRIPTION_ENDPOINT, [
            'secret' => $this->apiKey,
        ], 'GET');
    }

    public function sendBulkSms(array $recipients, string $message, array $options = []): array
    {
        $data = array_merge($options, [
            'secret' => $this->apiKey,
            'recipients' => $recipients,
            'message' => $message,
        ]);

        return $this->makeRequest(self::SMS_BULK_ENDPOINT, $data);
    }

    public function getSmsPending(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::SMS_PENDING_ENDPOINT, $data, 'GET');
    }

    public function getSmsReceived(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::SMS_RECEIVED_ENDPOINT, $data, 'GET');
    }

    public function getSmsSent(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::SMS_SENT_ENDPOINT, $data, 'GET');
    }

    public function getSmsMessage(int $messageId, string $type): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $messageId,
            'type' => $type,
        ];

        return $this->makeRequest(self::SMS_MESSAGE_ENDPOINT, $data, 'GET');
    }

    public function getSmsCampaigns(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::SMS_CAMPAIGNS_ENDPOINT, $data, 'GET');
    }

    public function deleteSmsReceived(int $messageId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $messageId,
        ];

        return $this->makeRequest(self::SMS_DELETE_RECEIVED_ENDPOINT, $data, 'GET');
    }

    public function deleteSmsSent(int $messageId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $messageId,
        ];

        return $this->makeRequest(self::SMS_DELETE_SENT_ENDPOINT, $data, 'GET');
    }

    public function deleteSmsCampaign(int $campaignId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $campaignId,
        ];

        return $this->makeRequest(self::SMS_DELETE_CAMPAIGN_ENDPOINT, $data, 'GET');
    }

    public function startSmsCampaign(int $campaignId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'campaign' => $campaignId,
        ];

        return $this->makeRequest(self::SMS_START_CAMPAIGN_ENDPOINT, $data, 'GET');
    }

    public function stopSmsCampaign(int $campaignId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'campaign' => $campaignId,
        ];

        return $this->makeRequest(self::SMS_STOP_CAMPAIGN_ENDPOINT, $data, 'GET');
    }

    public function getEarnings(): array
    {
        return $this->makeRequest(self::EARNINGS_ENDPOINT, [
            'secret' => $this->apiKey,
        ], 'GET');
    }

    public function deleteNotification(int $notificationId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $notificationId,
        ];

        return $this->makeRequest(self::DELETE_NOTIFICATION_ENDPOINT, $data, 'GET');
    }
}
