<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Feature;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ConvoChatIntegrationTest extends TestCase
{
    public function testItCanSendSmsViaFacade()
    {
        $this->mockHttpClient([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'SMS sent successfully',
                'id' => 'sms_123',
            ])),
        ]);

        $result = ConvoChat::sms()->sendSmsWithCredits(
            '+573001234567',
            'Test SMS from facade'
        );

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('SMS sent successfully', $result['message']);
        $this->assertEquals('sms_123', $result['id']);
    }

    public function testItCanSendWhatsappViaFacade()
    {
        $this->mockHttpClient([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'WhatsApp message sent',
                'id' => 'wa_123',
            ])),
        ]);

        $result = ConvoChat::whatsapp()->sendText(
            'account123',
            '+573001234567',
            'Test WhatsApp from facade'
        );

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('WhatsApp message sent', $result['message']);
    }

    public function testItHandlesApiErrorsProperly()
    {
        $this->mockHttpClient([
            new Response(401, [], json_encode([
                'status' => 'error',
                'message' => 'Invalid API key',
            ])),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ConvoChat SMS API Error:');

        ConvoChat::sms()->getDevices();
    }

    public function testItCanChainMultipleOperations()
    {
        $this->mockHttpClient([
            new Response(200, [], json_encode([
                'status' => 'success',
                'devices' => [['id' => 'device1', 'name' => 'Android 1']],
            ])),
            new Response(200, [], json_encode([
                'status' => 'success',
                'credits' => 50,
            ])),
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'SMS sent successfully',
            ])),
        ]);

        // Obtener dispositivos
        $devices = ConvoChat::sms()->getDevices();
        $this->assertEquals('success', $devices['status']);
        $this->assertCount(1, $devices['devices']);

        // Obtener créditos
        $credits = ConvoChat::sms()->getCredits();
        $this->assertEquals('success', $credits['status']);
        $this->assertEquals(50, $credits['credits']);

        // Enviar SMS
        $sms = ConvoChat::sms()->sendSmsWithDevice(
            '+573001234567',
            'Test message',
            'device1'
        );
        $this->assertEquals('success', $sms['status']);
    }

    public function testItValidatesConfigurationIsLoaded()
    {
        $this->assertEquals('test_api_key', config('convochat.api_key'));
        $this->assertEquals('https://test.convo.chat/api', config('convochat.base_url'));
        $this->assertEquals('devices', config('convochat.sms.default_mode'));
        $this->assertEquals(2, config('convochat.sms.default_priority'));
    }

    public function testItCanUseDifferentServiceInstances()
    {
        $sms1 = ConvoChat::sms();
        $sms2 = ConvoChat::sms();
        $whatsapp1 = ConvoChat::whatsapp();
        $whatsapp2 = ConvoChat::whatsapp();

        // Los services son singletons
        $this->assertSame($sms1, $sms2);
        $this->assertSame($whatsapp1, $whatsapp2);

        // Pero SMS y WhatsApp son diferentes
        $this->assertNotSame($sms1, $whatsapp1);
    }

    /**
     * Mock HTTP client for testing
     */
    protected function mockHttpClient(array $responses)
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Mock both services
        $smsService = ConvoChat::sms();
        $whatsappService = ConvoChat::whatsapp();

        $reflection = new \ReflectionClass($smsService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($smsService, $client);

        $reflection = new \ReflectionClass($whatsappService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($whatsappService, $client);
    }
}
