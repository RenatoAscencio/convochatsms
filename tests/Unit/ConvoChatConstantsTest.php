<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Orchestra\Testbench\TestCase;

class ConvoChatConstantsTest extends TestCase
{
    public function testSmsServiceHasCorrectConstants()
    {
        $this->assertEquals('devices', ConvoChatSmsService::DEFAULT_MODE);
        $this->assertEquals('credits', ConvoChatSmsService::CREDITS_MODE);
        $this->assertEquals('/send/sms', ConvoChatSmsService::SMS_ENDPOINT);
        $this->assertEquals('/get/devices', ConvoChatSmsService::DEVICES_ENDPOINT);
        $this->assertEquals('/get/credits', ConvoChatSmsService::CREDITS_ENDPOINT);
        $this->assertEquals('/get/gateway/rates', ConvoChatSmsService::GATEWAY_RATES_ENDPOINT);
        $this->assertEquals('/get/subscription/package', ConvoChatSmsService::SUBSCRIPTION_ENDPOINT);
        $this->assertEquals('https://sms.convo.chat/api', ConvoChatSmsService::DEFAULT_BASE_URL);
        $this->assertEquals(30, ConvoChatSmsService::DEFAULT_TIMEOUT);
    }

    public function testWhatsappServiceHasCorrectConstants()
    {
        $this->assertEquals('text', ConvoChatWhatsAppService::DEFAULT_TYPE);
        $this->assertEquals('media', ConvoChatWhatsAppService::MEDIA_TYPE);
        $this->assertEquals('document', ConvoChatWhatsAppService::DOCUMENT_TYPE);
        $this->assertEquals(2, ConvoChatWhatsAppService::DEFAULT_PRIORITY);
        $this->assertEquals('/send/whatsapp', ConvoChatWhatsAppService::WHATSAPP_ENDPOINT);
        $this->assertEquals('/get/wa_servers', ConvoChatWhatsAppService::WA_SERVERS_ENDPOINT);
        $this->assertEquals('/get/wa_accounts', ConvoChatWhatsAppService::WA_ACCOUNTS_ENDPOINT);
        $this->assertEquals('/create/wa/link', ConvoChatWhatsAppService::WA_LINK_ENDPOINT);
        $this->assertEquals('/create/wa/relink', ConvoChatWhatsAppService::WA_RELINK_ENDPOINT);
        $this->assertEquals('/get/wa/validate_number', ConvoChatWhatsAppService::WA_VALIDATE_ENDPOINT);
        $this->assertEquals('https://sms.convo.chat/api', ConvoChatWhatsAppService::DEFAULT_BASE_URL);
        $this->assertEquals(30, ConvoChatWhatsAppService::DEFAULT_TIMEOUT);
    }

    protected function getPackageProviders($app)
    {
        return [
            \ConvoChat\LaravelSmsGateway\ConvoChatServiceProvider::class,
        ];
    }
}
