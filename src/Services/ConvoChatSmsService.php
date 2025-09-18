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
    public const DEVICES_ENDPOINT = '/get/devices';
    public const CREDITS_ENDPOINT = '/get/credits';
    public const GATEWAY_RATES_ENDPOINT = '/get/gateway/rates';
    public const SUBSCRIPTION_ENDPOINT = '/get/subscription/package';
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

    public function getDevices(): array
    {
        return $this->makeRequest(self::DEVICES_ENDPOINT, [
            'secret' => $this->apiKey,
        ]);
    }

    public function getCredits(): array
    {
        return $this->makeRequest(self::CREDITS_ENDPOINT, [
            'secret' => $this->apiKey,
        ]);
    }

    public function getGatewayRates(): array
    {
        return $this->makeRequest(self::GATEWAY_RATES_ENDPOINT, [
            'secret' => $this->apiKey,
        ]);
    }

    public function getSubscription(): array
    {
        return $this->makeRequest(self::SUBSCRIPTION_ENDPOINT, [
            'secret' => $this->apiKey,
        ]);
    }

    protected function makeRequest(string $endpoint, array $data): array
    {
        try {
            $response = $this->client->post($this->baseUrl . $endpoint, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'timeout' => $this->timeout,
            ]);

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
