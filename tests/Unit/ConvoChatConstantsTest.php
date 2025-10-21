<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatUssdService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Orchestra\Testbench\TestCase;

class ConvoChatConstantsTest extends TestCase
{
    private const EXPECTED_BASE_URL = 'https://sms.convo.chat/api';

    public function testSmsServiceHasCorrectConstants()
    {
        $this->assertEquals('devices', ConvoChatSmsService::DEFAULT_MODE);
        $this->assertEquals('credits', ConvoChatSmsService::CREDITS_MODE);
        $this->assertEquals('/send/sms', ConvoChatSmsService::SMS_ENDPOINT);
        $this->assertEquals('/get/devices', ConvoChatSmsService::DEVICES_ENDPOINT);
        $this->assertEquals('/get/credits', ConvoChatSmsService::CREDITS_ENDPOINT);
        $this->assertEquals('/get/subscription', ConvoChatSmsService::SUBSCRIPTION_ENDPOINT);
        $this->assertEquals('/get/rates', ConvoChatSmsService::RATES_ENDPOINT);
        $this->assertEquals('/get/earnings', ConvoChatSmsService::EARNINGS_ENDPOINT);
        $this->assertEquals('/delete/notification', ConvoChatSmsService::DELETE_NOTIFICATION_ENDPOINT);
        $this->assertEquals(self::EXPECTED_BASE_URL, ConvoChatSmsService::DEFAULT_BASE_URL);
        $this->assertEquals(30, ConvoChatSmsService::DEFAULT_TIMEOUT);
    }

    public function testWhatsappServiceHasCorrectConstants()
    {
        $this->assertEquals('text', ConvoChatWhatsAppService::DEFAULT_TYPE);
        $this->assertEquals('media', ConvoChatWhatsAppService::MEDIA_TYPE);
        $this->assertEquals('document', ConvoChatWhatsAppService::DOCUMENT_TYPE);
        $this->assertEquals(2, ConvoChatWhatsAppService::DEFAULT_PRIORITY);
        $this->assertEquals('/send/whatsapp', ConvoChatWhatsAppService::WHATSAPP_ENDPOINT);
        $this->assertEquals('/get/wa.servers', ConvoChatWhatsAppService::WHATSAPP_SERVERS_ENDPOINT);
        $this->assertEquals('/get/wa.accounts', ConvoChatWhatsAppService::WHATSAPP_ACCOUNTS_ENDPOINT);
        $this->assertEquals('/validate/whatsapp', ConvoChatWhatsAppService::WHATSAPP_VALIDATE_ENDPOINT);
        $this->assertEquals('/get/subscription', ConvoChatWhatsAppService::SUBSCRIPTION_ENDPOINT);
        $this->assertEquals('/create/wa.link', ConvoChatWhatsAppService::WHATSAPP_LINK_ENDPOINT);
        $this->assertEquals('/create/wa.relink', ConvoChatWhatsAppService::WHATSAPP_RELINK_ENDPOINT);
        $this->assertEquals('/delete/wa.received', ConvoChatWhatsAppService::WHATSAPP_DELETE_RECEIVED_ENDPOINT);
        $this->assertEquals('/delete/wa.sent', ConvoChatWhatsAppService::WHATSAPP_DELETE_SENT_ENDPOINT);
        $this->assertEquals('/delete/wa.account', ConvoChatWhatsAppService::WHATSAPP_DELETE_ACCOUNT_ENDPOINT);
        $this->assertEquals('/delete/wa.campaign', ConvoChatWhatsAppService::WHATSAPP_DELETE_CAMPAIGN_ENDPOINT);
        $this->assertEquals(self::EXPECTED_BASE_URL, ConvoChatWhatsAppService::DEFAULT_BASE_URL);
        $this->assertEquals(30, ConvoChatWhatsAppService::DEFAULT_TIMEOUT);
    }

    public function testUssdServiceHasCorrectConstants()
    {
        $this->assertEquals('/send/ussd', ConvoChatUssdService::USSD_SEND_ENDPOINT);
        $this->assertEquals('/get/ussd', ConvoChatUssdService::USSD_GET_ENDPOINT);
        $this->assertEquals('/delete/ussd', ConvoChatUssdService::USSD_DELETE_ENDPOINT);
        $this->assertEquals(self::EXPECTED_BASE_URL, ConvoChatUssdService::DEFAULT_BASE_URL);
        $this->assertEquals(30, ConvoChatUssdService::DEFAULT_TIMEOUT);
    }

    protected function getPackageProviders($app)
    {
        return [
            \ConvoChat\LaravelSmsGateway\ConvoChatServiceProvider::class,
        ];
    }
}
