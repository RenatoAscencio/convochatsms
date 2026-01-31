<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatOtpService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ConvoChatOtpServiceTest extends TestCase
{
    protected ConvoChatOtpService $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->otpService = app(ConvoChatOtpService::class);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(ConvoChatOtpService::class, $this->otpService);
    }

    public function testItValidatesRequiredTypeParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: type');

        $this->otpService->sendOtp(['message' => 'Code: {{otp}}', 'phone' => '+573001234567']);
    }

    public function testItValidatesRequiredMessageParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: message');

        $this->otpService->sendOtp(['type' => 'sms', 'phone' => '+573001234567']);
    }

    public function testItValidatesRequiredPhoneParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: phone');

        $this->otpService->sendOtp(['type' => 'sms', 'message' => 'Code: {{otp}}']);
    }

    public function testItCanSendOtp()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'message' => 'OTP has been sent!',
                'data' => ['otp' => 123456],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatOtpService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->sendOtp([
            'type' => 'sms',
            'message' => 'Code: {{otp}}',
            'phone' => '+573001234567',
        ]);

        $this->assertEquals(200, $result['status']);
        $this->assertEquals(123456, $result['data']['otp']);
    }

    public function testItCanVerifyOtp()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'message' => 'OTP is valid',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatOtpService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->verifyOtp('123456');

        $this->assertEquals(200, $result['status']);
    }

    public function testItHandlesApiErrorsGracefully()
    {
        $mock = new MockHandler([
            new Response(400, [], json_encode([
                'status' => 'error',
                'message' => 'Invalid OTP',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatOtpService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ConvoChat OTP API Error:');

        $service->verifyOtp('invalid');
    }
}
