<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Feature;

use ConvoChat\LaravelSmsGateway\ConvoChatManager;
use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatContactsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatOtpService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatUssdService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ConsumerSmokeTest extends TestCase
{
    public function testManagerResolvesAllServices(): void
    {
        $manager = app('convochat');

        $this->assertInstanceOf(ConvoChatManager::class, $manager);
        $this->assertInstanceOf(ConvoChatSmsService::class, $manager->sms());
        $this->assertInstanceOf(ConvoChatWhatsAppService::class, $manager->whatsapp());
        $this->assertInstanceOf(ConvoChatContactsService::class, $manager->contacts());
        $this->assertInstanceOf(ConvoChatOtpService::class, $manager->otp());
        $this->assertInstanceOf(ConvoChatUssdService::class, $manager->ussd());
    }

    public function testFacadeResolvesManager(): void
    {
        $this->assertInstanceOf(ConvoChatSmsService::class, ConvoChat::sms());
        $this->assertInstanceOf(ConvoChatWhatsAppService::class, ConvoChat::whatsapp());
        $this->assertInstanceOf(ConvoChatContactsService::class, ConvoChat::contacts());
        $this->assertInstanceOf(ConvoChatOtpService::class, ConvoChat::otp());
        $this->assertInstanceOf(ConvoChatUssdService::class, ConvoChat::ussd());
    }

    public function testConfigLoadsFromEnv(): void
    {
        $this->assertNotEmpty(config('convochat.api_key'));
        $this->assertNotEmpty(config('convochat.base_url'));
        $this->assertIsInt(config('convochat.timeout'));
        $this->assertArrayHasKey('sms', config('convochat'));
        $this->assertArrayHasKey('whatsapp', config('convochat'));
    }

    public function testSmsServiceWorksWithMockedHttp(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'data' => ['messageId' => 'abc123'],
            ])),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $service = new ConvoChatSmsService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->sendSmsWithCredits('+573001234567', 'Hello');

        $this->assertEquals(200, $result['status']);
        $this->assertEquals('abc123', $result['data']['messageId']);
    }

    public function testWhatsAppServiceWorksWithMockedHttp(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'data' => ['messageId' => 'wa456'],
            ])),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $service = new ConvoChatWhatsAppService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->sendText('account1', '+573001234567', 'Hello WA');

        $this->assertEquals(200, $result['status']);
    }

    public function testOtpServiceWorksWithMockedHttp(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'data' => ['otp' => '123456'],
            ])),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $service = new ConvoChatOtpService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->sendOtp([
            'type' => 'sms',
            'message' => 'Your code: {{otp}}',
            'phone' => '+573001234567',
        ]);

        $this->assertEquals(200, $result['status']);
    }
}
