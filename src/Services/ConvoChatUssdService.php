<?php

namespace ConvoChat\LaravelSmsGateway\Services;

class ConvoChatUssdService extends BaseConvoChatService
{
    public const USSD_SEND_ENDPOINT = '/send/ussd';
    public const USSD_GET_ENDPOINT = '/get/ussd';
    public const USSD_DELETE_ENDPOINT = '/delete/ussd';

    protected function getServiceName(): string
    {
        return 'USSD';
    }

    public function sendUssd(string $code, int $sim, string $device): array
    {
        $requiredParams = ['code', 'sim', 'device'];
        $this->validateRequiredParams(compact('code', 'sim', 'device'), $requiredParams);

        $data = [
            'secret' => $this->apiKey,
            'code' => $code,
            'sim' => $sim,
            'device' => $device,
        ];

        return $this->makeRequest(self::USSD_SEND_ENDPOINT, $data);
    }

    public function getUssdRequests(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::USSD_GET_ENDPOINT, $data, 'GET');
    }

    public function deleteUssdRequest(int $requestId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $requestId,
        ];

        return $this->makeRequest(self::USSD_DELETE_ENDPOINT, $data, 'GET');
    }
}
