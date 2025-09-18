<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Feature;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;

class FacadeTest extends TestCase
{
    public function testFacadeReturnsSmsService()
    {
        $smsService = ConvoChat::sms();

        $this->assertInstanceOf(ConvoChatSmsService::class, $smsService);
    }

    public function testFacadeReturnsWhatsappService()
    {
        $whatsappService = ConvoChat::whatsapp();

        $this->assertInstanceOf(ConvoChatWhatsAppService::class, $whatsappService);
    }

    public function testFacadeReturnsSameInstanceOnMultipleCalls()
    {
        $sms1 = ConvoChat::sms();
        $sms2 = ConvoChat::sms();

        $this->assertSame($sms1, $sms2);
    }

    public function testFacadeCanAccessSmsMethods()
    {
        $smsService = ConvoChat::sms();

        $this->assertTrue(method_exists($smsService, 'sendSms'));
        $this->assertTrue(method_exists($smsService, 'sendSmsWithDevice'));
        $this->assertTrue(method_exists($smsService, 'sendSmsWithCredits'));
        $this->assertTrue(method_exists($smsService, 'getDevices'));
    }

    public function testFacadeCanAccessWhatsappMethods()
    {
        $whatsappService = ConvoChat::whatsapp();

        $this->assertTrue(method_exists($whatsappService, 'sendMessage'));
        $this->assertTrue(method_exists($whatsappService, 'sendText'));
        $this->assertTrue(method_exists($whatsappService, 'sendMedia'));
        $this->assertTrue(method_exists($whatsappService, 'getWhatsAppAccounts'));
    }
}
