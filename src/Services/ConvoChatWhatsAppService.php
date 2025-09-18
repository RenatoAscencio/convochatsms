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
    protected int $timeout;

    public const DEFAULT_TYPE = 'text';
    public const MEDIA_TYPE = 'media';
    public const DOCUMENT_TYPE = 'document';
    public const DEFAULT_PRIORITY = 2;
    public const WHATSAPP_ENDPOINT = '/send/whatsapp';
    public const WA_SERVERS_ENDPOINT = '/get/wa_servers';
    public const WA_ACCOUNTS_ENDPOINT = '/get/wa_accounts';
    public const WA_LINK_ENDPOINT = '/create/wa/link';
    public const WA_RELINK_ENDPOINT = '/create/wa/relink';
    public const WA_VALIDATE_ENDPOINT = '/get/wa/validate_number';
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

    public function sendMessage(array $params): array
    {
        $requiredParams = ['account', 'recipient', 'message'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge([
            'secret' => $this->apiKey,
            'type' => self::DEFAULT_TYPE,
        ], $params);

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

    public function getWhatsAppServers(): array
    {
        return $this->makeRequest(self::WA_SERVERS_ENDPOINT, [
            'secret' => $this->apiKey,
        ]);
    }

    public function linkWhatsAppAccount(int $serverId): array
    {
        return $this->makeRequest(self::WA_LINK_ENDPOINT, [
            'secret' => $this->apiKey,
            'sid' => $serverId,
        ]);
    }

    public function relinkWhatsAppAccount(int $serverId, string $uniqueId): array
    {
        return $this->makeRequest(self::WA_RELINK_ENDPOINT, [
            'secret' => $this->apiKey,
            'sid' => $serverId,
            'unique' => $uniqueId,
        ]);
    }

    public function validateWhatsAppNumber(string $accountId, string $phone): array
    {
        return $this->makeRequest(self::WA_VALIDATE_ENDPOINT, [
            'secret' => $this->apiKey,
            'unique' => $accountId,
            'phone' => $phone,
        ]);
    }

    public function getWhatsAppAccounts(): array
    {
        return $this->makeRequest(self::WA_ACCOUNTS_ENDPOINT, [
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
                Log::info('ConvoChat WhatsApp API Request Success', [
                    'endpoint' => $endpoint,
                    'response_status' => $responseData['status'] ?? 'unknown',
                    'request_time' => now(),
                    'base_url' => $this->baseUrl,
                ]);
            }

            /** @var array $responseData */
            return $responseData;

        } catch (GuzzleException $e) {
            Log::error('ConvoChat WhatsApp API Error', [
                'endpoint' => $endpoint,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'request_data' => array_merge($data, ['secret' => '[REDACTED]']),
                'base_url' => $this->baseUrl,
                'timeout' => $this->timeout,
                'request_time' => now(),
            ]);

            throw new \Exception("ConvoChat WhatsApp API Error: " . $e->getMessage());
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
