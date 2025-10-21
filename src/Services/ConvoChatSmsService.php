<?php

namespace ConvoChat\LaravelSmsGateway\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ConvoChatSmsService
{
    protected Client $client;
    protected string $apiKey;
    protected string $baseUrl;
    protected int $timeout;

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
    public const DEFAULT_BASE_URL = 'https://sms.convo.chat/api';
    public const DEFAULT_TIMEOUT = 30;

    public function __construct(?Client $client = null, ?array $config = null)
    {
        $this->client = $client ?? new Client();

        $config ??= [];
        $this->apiKey = $config['api_key'] ?? config('convochat.api_key');
        $this->baseUrl = $config['base_url'] ?? config('convochat.base_url', self::DEFAULT_BASE_URL);
        $this->timeout = $config['timeout'] ?? config('convochat.timeout', self::DEFAULT_TIMEOUT);

        $this->validateConfiguration();
    }

    public function sendSms(array $params): array
    {
        $requiredParams = ['phone', 'message'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge([
            'secret' => $this->apiKey,
            'mode' => $params['mode'] ?? self::DEFAULT_MODE,
        ], $params);

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
        $data = array_merge([
            'secret' => $this->apiKey,
            'recipients' => $recipients,
            'message' => $message,
        ], $options);

        return $this->makeRequest(self::SMS_BULK_ENDPOINT, $data);
    }

    public function getSmsPending(array $filters = []): array
    {
        $data = array_merge([
            'secret' => $this->apiKey,
        ], $filters);

        return $this->makeRequest(self::SMS_PENDING_ENDPOINT, $data, 'GET');
    }

    public function getSmsReceived(array $filters = []): array
    {
        $data = array_merge([
            'secret' => $this->apiKey,
        ], $filters);

        return $this->makeRequest(self::SMS_RECEIVED_ENDPOINT, $data, 'GET');
    }

    public function getSmsSent(array $filters = []): array
    {
        $data = array_merge([
            'secret' => $this->apiKey,
        ], $filters);

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
        $data = array_merge([
            'secret' => $this->apiKey,
        ], $filters);

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

    protected function makeRequest(string $endpoint, array $data, string $method = 'POST'): array
    {
        try {
            $requestOptions = [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'timeout' => $this->timeout,
            ];

            $response = $this->client->request($method, $this->baseUrl . $endpoint, $requestOptions);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (! is_array($responseData)) {
                throw new \Exception('Invalid JSON response from ConvoChat API');
            }

            if (config('convochat.log_requests', false)) {
                Log::info('ConvoChat SMS API Request Success', [
                    'endpoint' => $endpoint,
                    'response_status' => $responseData['status'] ?? 'unknown',
                    'request_time' => now(),
                    'base_url' => $this->baseUrl,
                ]);
            }

            /** @var array $responseData */
            return $responseData;

        } catch (GuzzleException $e) {
            Log::error('ConvoChat SMS API Error', [
                'endpoint' => $endpoint,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'request_data' => array_merge($data, ['secret' => '[REDACTED]']),
                'base_url' => $this->baseUrl,
                'timeout' => $this->timeout,
                'request_time' => now(),
            ]);

            throw new \Exception("ConvoChat SMS API Error: " . $e->getMessage());
        }
    }

    protected function validateRequiredParams(array $params, array $required): void
    {
        foreach ($required as $param) {
            if (! isset($params[$param]) || empty($params[$param])) {
                throw new \InvalidArgumentException("Missing required parameter: {$param}");
            }
        }
    }

    protected function validateConfiguration(): void
    {
        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('ConvoChat API key is required. Please set CONVOCHAT_API_KEY in your .env file or publish the config file.');
        }

        if (empty($this->baseUrl)) {
            throw new \InvalidArgumentException('ConvoChat base URL is required.');
        }

        if (! filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('ConvoChat base URL must be a valid URL.');
        }

        if ($this->timeout <= 0) {
            throw new \InvalidArgumentException('Timeout must be a positive integer.');
        }
    }
}
