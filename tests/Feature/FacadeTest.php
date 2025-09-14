<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Feature;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;

class FacadeTest extends TestCase
{
    /** @test */
    public function facade_returns_sms_service()
    {
        $smsService = ConvoChat::sms();

        $this->assertInstanceOf(ConvoChatSmsService::class, $smsService);
    }

    /** @test */
    public function facade_returns_whatsapp_service()
    {
        $whatsappService = ConvoChat::whatsapp();

        $this->assertInstanceOf(ConvoChatWhatsAppService::class, $whatsappService);
    }

    /** @test */
    public function facade_returns_same_instance_on_multiple_calls()
    {
        $sms1 = ConvoChat::sms();
        $sms2 = ConvoChat::sms();

        $this->assertSame($sms1, $sms2);
    }

    /** @test */
    public function facade_can_access_sms_methods()
    {
        $smsService = ConvoChat::sms();

        $this->assertTrue(method_exists($smsService, 'sendSms'));
        $this->assertTrue(method_exists($smsService, 'sendSmsWithDevice'));
        $this->assertTrue(method_exists($smsService, 'sendSmsWithCredits'));
        $this->assertTrue(method_exists($smsService, 'getDevices'));
    }

    /** @test */
    public function facade_can_access_whatsapp_methods()
    {
        $whatsappService = ConvoChat::whatsapp();

        $this->assertTrue(method_exists($whatsappService, 'sendMessage'));
        $this->assertTrue(method_exists($whatsappService, 'sendText'));
        $this->assertTrue(method_exists($whatsappService, 'sendMedia'));
        $this->assertTrue(method_exists($whatsappService, 'getWhatsAppAccounts'));
    }
}