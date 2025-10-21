<?php

namespace ConvoChat\LaravelSmsGateway\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ConvoChatContactsService
{
    protected Client $client;
    protected string $apiKey;
    protected string $baseUrl;
    protected int $timeout;

    public const CONTACTS_ENDPOINT = '/get/contacts';
    public const CREATE_CONTACT_ENDPOINT = '/create/contact';
    public const DELETE_CONTACT_ENDPOINT = '/delete/contact';
    public const GROUPS_ENDPOINT = '/get/groups';
    public const CREATE_GROUP_ENDPOINT = '/create/group';
    public const DELETE_GROUP_ENDPOINT = '/delete/group';
    public const UNSUBSCRIBED_ENDPOINT = '/get/unsubscribed';
    public const DELETE_UNSUBSCRIBED_ENDPOINT = '/delete/unsubscribed';
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

    public function getContacts(array $filters = []): array
    {
        $data = array_merge([
            'secret' => $this->apiKey,
        ], $filters);

        return $this->makeRequest(self::CONTACTS_ENDPOINT, $data, 'GET');
    }

    public function createContact(array $params): array
    {
        $requiredParams = ['phone', 'name', 'groups'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge([
            'secret' => $this->apiKey,
        ], $params);

        return $this->makeRequest(self::CREATE_CONTACT_ENDPOINT, $data);
    }

    public function deleteContact(int $contactId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $contactId,
        ];

        return $this->makeRequest(self::DELETE_CONTACT_ENDPOINT, $data, 'GET');
    }

    public function getGroups(array $filters = []): array
    {
        $data = array_merge([
            'secret' => $this->apiKey,
        ], $filters);

        return $this->makeRequest(self::GROUPS_ENDPOINT, $data, 'GET');
    }

    public function createGroup(array $params): array
    {
        $requiredParams = ['name'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge([
            'secret' => $this->apiKey,
        ], $params);

        return $this->makeRequest(self::CREATE_GROUP_ENDPOINT, $data);
    }

    public function deleteGroup(int $groupId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $groupId,
        ];

        return $this->makeRequest(self::DELETE_GROUP_ENDPOINT, $data, 'GET');
    }

    public function getUnsubscribed(array $filters = []): array
    {
        $data = array_merge([
            'secret' => $this->apiKey,
        ], $filters);

        return $this->makeRequest(self::UNSUBSCRIBED_ENDPOINT, $data, 'GET');
    }

    public function deleteUnsubscribed(int $contactId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $contactId,
        ];

        return $this->makeRequest(self::DELETE_UNSUBSCRIBED_ENDPOINT, $data, 'GET');
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
                Log::info('ConvoChat Contacts API Request Success', [
                    'endpoint' => $endpoint,
                    'response_status' => $responseData['status'] ?? 'unknown',
                    'request_time' => now(),
                    'base_url' => $this->baseUrl,
                ]);
            }

            /** @var array $responseData */
            return $responseData;

        } catch (GuzzleException $e) {
            Log::error('ConvoChat Contacts API Error', [
                'endpoint' => $endpoint,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'request_data' => array_merge($data, ['secret' => '[REDACTED]']),
                'base_url' => $this->baseUrl,
                'timeout' => $this->timeout,
                'request_time' => now(),
            ]);

            throw new \Exception("ConvoChat Contacts API Error: " . $e->getMessage());
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