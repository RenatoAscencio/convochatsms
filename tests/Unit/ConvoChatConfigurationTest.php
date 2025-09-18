<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use GuzzleHttp\Client;
use Orchestra\Testbench\TestCase;

class ConvoChatConfigurationTest extends TestCase
{
    public function testItThrowsExceptionWhenApiKeyIsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ConvoChat API key is required');

        new ConvoChatSmsService(null, ['api_key' => '']);
    }

    public function testItThrowsExceptionWhenApiKeyIsNull()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ConvoChat API key is required');

        new ConvoChatSmsService(null, ['api_key' => null]);
    }

    public function testItThrowsExceptionWhenBaseUrlIsInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ConvoChat base URL must be a valid URL');

        new ConvoChatSmsService(null, [
            'api_key' => 'test-key',
            'base_url' => 'invalid-url',
        ]);
    }

    public function testItThrowsExceptionWhenTimeoutIsNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Timeout must be a positive integer');

        new ConvoChatSmsService(null, [
            'api_key' => 'test-key',
            'base_url' => 'https://test.convo.chat/api',
            'timeout' => -5,
        ]);
    }

    public function testItAcceptsValidConfiguration()
    {
        $service = new ConvoChatSmsService(null, [
            'api_key' => 'test-key',
            'base_url' => 'https://test.convo.chat/api',
            'timeout' => 60,
        ]);

        $this->assertInstanceOf(ConvoChatSmsService::class, $service);
    }

    public function testItUsesDefaultValuesWhenConfigIsMissing()
    {
        config(['convochat.api_key' => 'test-key']);

        $service = new ConvoChatSmsService();

        $this->assertInstanceOf(ConvoChatSmsService::class, $service);
    }

    public function testItAllowsDependencyInjectionOfHttpClient()
    {
        $mockClient = new Client();

        $service = new ConvoChatSmsService($mockClient, [
            'api_key' => 'test-key',
            'base_url' => 'https://test.convo.chat/api',
        ]);

        $this->assertInstanceOf(ConvoChatSmsService::class, $service);
    }

    public function testWhatsappServiceValidatesConfigurationToo()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ConvoChat API key is required');

        new ConvoChatWhatsAppService(null, ['api_key' => '']);
    }

    protected function getPackageProviders($app)
    {
        return [
            \ConvoChat\LaravelSmsGateway\ConvoChatServiceProvider::class,
        ];
    }
}
