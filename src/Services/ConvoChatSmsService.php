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

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('convochat.api_key');
        $this->baseUrl = config('convochat.base_url', 'https://sms.convo.chat/api');
    }

    public function sendSms(array $params): array
    {
        $requiredParams = ['phone', 'message'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge([
            'secret' => $this->apiKey,
            'mode' => $params['mode'] ?? 'devices',
        ], $params);

        return $this->makeRequest('/send/sms', $data);
    }

    public function sendSmsWithDevice(string $phone, string $message, string $deviceId, array $options = []): array
    {
        $params = array_merge([
            'phone' => $phone,
            'message' => $message,
            'device' => $deviceId,
            'mode' => 'devices',
        ], $options);

        return $this->sendSms($params);
    }

    public function sendSmsWithCredits(string $phone, string $message, ?string $gatewayId = null, array $options = []): array
    {
        $params = array_merge([
            'phone' => $phone,
            'message' => $message,
            'mode' => 'credits',
        ], $options);

        if ($gatewayId) {
            $params['gateway'] = $gatewayId;
        }

        return $this->sendSms($params);
    }

    public function getDevices(): array
    {
        return $this->makeRequest('/get/devices', [
            'secret' => $this->apiKey
        ]);
    }

    public function getCredits(): array
    {
        return $this->makeRequest('/get/credits', [
            'secret' => $this->apiKey
        ]);
    }

    public function getGatewayRates(): array
    {
        return $this->makeRequest('/get/gateway/rates', [
            'secret' => $this->apiKey
        ]);
    }

    public function getSubscription(): array
    {
        return $this->makeRequest('/get/subscription/package', [
            'secret' => $this->apiKey
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
                'timeout' => 30,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (config('convochat.log_requests', false)) {
                Log::info('ConvoChat SMS API Request', [
                    'endpoint' => $endpoint,
                    'response' => $responseData
                ]);
            }

            return $responseData;

        } catch (GuzzleException $e) {
            Log::error('ConvoChat SMS API Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            throw new \Exception("ConvoChat SMS API Error: " . $e->getMessage());
        }
    }

    protected function validateRequiredParams(array $params, array $required): void
    {
        foreach ($required as $param) {
            if (!isset($params[$param]) || empty($params[$param])) {
                throw new \InvalidArgumentException("Missing required parameter: {$param}");
            }
        }
    }
}