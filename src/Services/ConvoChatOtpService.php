<?php

namespace ConvoChat\LaravelSmsGateway\Services;

class ConvoChatOtpService extends BaseConvoChatService
{
    public const SEND_OTP_ENDPOINT = '/send/otp';
    public const VERIFY_OTP_ENDPOINT = '/get/otp';

    protected function getServiceName(): string
    {
        return 'OTP';
    }

    public function sendOtp(array $params): array
    {
        $requiredParams = ['type', 'message', 'phone'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge($params, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::SEND_OTP_ENDPOINT, $data);
    }

    public function verifyOtp(string $otp): array
    {
        $data = [
            'secret' => $this->apiKey,
            'otp' => $otp,
        ];

        return $this->makeRequest(self::VERIFY_OTP_ENDPOINT, $data, 'GET');
    }
}
