<?php

namespace ConvoChat\LaravelSmsGateway\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ConvoChatWhatsAppService
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

    public function sendMessage(array $params): array
    {
        $requiredParams = ['account', 'recipient', 'message'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge([
            'secret' => $this->apiKey,
            'type' => 'text',
        ], $params);

        return $this->makeRequest('/send/whatsapp', $data);
    }

    public function sendText(string $account, string $recipient, string $message, int $priority = 2): array
    {
        return $this->sendMessage([
            'account' => $account,
            'recipient' => $recipient,
            'message' => $message,
            'type' => 'text',
            'priority' => $priority,
        ]);
    }

    public function sendMedia(string $account, string $recipient, string $message, string $mediaUrl, string $mediaType = 'image', int $priority = 2): array
    {
        return $this->sendMessage([
            'account' => $account,
            'recipient' => $recipient,
            'message' => $message,
            'type' => 'media',
            'media_url' => $mediaUrl,
            'media_type' => $mediaType,
            'priority' => $priority,
        ]);
    }

    public function sendDocument(string $account, string $recipient, string $message, string $documentUrl, string $documentName, string $documentType = 'pdf', int $priority = 2): array
    {
        return $this->sendMessage([
            'account' => $account,
            'recipient' => $recipient,
            'message' => $message,
            'type' => 'document',
            'document_url' => $documentUrl,
            'document_name' => $documentName,
            'document_type' => $documentType,
            'priority' => $priority,
        ]);
    }

    public function getWhatsAppServers(): array
    {
        return $this->makeRequest('/get/wa_servers', [
            'secret' => $this->apiKey
        ]);
    }

    public function linkWhatsAppAccount(int $serverId): array
    {
        return $this->makeRequest('/create/wa/link', [
            'secret' => $this->apiKey,
            'sid' => $serverId
        ]);
    }

    public function relinkWhatsAppAccount(int $serverId, string $uniqueId): array
    {
        return $this->makeRequest('/create/wa/relink', [
            'secret' => $this->apiKey,
            'sid' => $serverId,
            'unique' => $uniqueId
        ]);
    }

    public function validateWhatsAppNumber(string $accountId, string $phone): array
    {
        return $this->makeRequest('/get/wa/validate_number', [
            'secret' => $this->apiKey,
            'unique' => $accountId,
            'phone' => $phone
        ]);
    }

    public function getWhatsAppAccounts(): array
    {
        return $this->makeRequest('/get/wa_accounts', [
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
                Log::info('ConvoChat WhatsApp API Request', [
                    'endpoint' => $endpoint,
                    'response' => $responseData
                ]);
            }

            return $responseData;

        } catch (GuzzleException $e) {
            Log::error('ConvoChat WhatsApp API Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            throw new \Exception("ConvoChat WhatsApp API Error: " . $e->getMessage());
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