<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ConvoChatWhatsAppServiceTest extends TestCase
{
    protected ConvoChatWhatsAppService $whatsappService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->whatsappService = app(ConvoChatWhatsAppService::class);
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(ConvoChatWhatsAppService::class, $this->whatsappService);
    }

    /** @test */
    public function it_validates_required_account_parameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: account');

        $this->whatsappService->sendMessage([
            'recipient' => '+573001234567',
            'message' => 'Test message'
        ]);
    }

    /** @test */
    public function it_validates_required_recipient_parameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: recipient');

        $this->whatsappService->sendMessage([
            'account' => 'account123',
            'message' => 'Test message'
        ]);
    }

    /** @test */
    public function it_can_send_text_message()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'WhatsApp message sent'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->whatsappService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->whatsappService, $client);

        $result = $this->whatsappService->sendText(
            'account123',
            '+573001234567',
            'Hello WhatsApp!'
        );

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('WhatsApp message sent', $result['message']);
    }

    /** @test */
    public function it_can_send_media_message()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'Media sent successfully'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->whatsappService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->whatsappService, $client);

        $result = $this->whatsappService->sendMedia(
            'account123',
            '+573001234567',
            'Check this image',
            'https://example.com/image.jpg',
            'image'
        );

        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_send_document()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'Document sent successfully'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->whatsappService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->whatsappService, $client);

        $result = $this->whatsappService->sendDocument(
            'account123',
            '+573001234567',
            'Here is your report',
            'https://example.com/report.pdf',
            'Monthly Report.pdf',
            'pdf'
        );

        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_get_whatsapp_servers()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'servers' => [
                    ['id' => 1, 'name' => 'Server 1'],
                    ['id' => 2, 'name' => 'Server 2']
                ]
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->whatsappService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->whatsappService, $client);

        $result = $this->whatsappService->getWhatsAppServers();

        $this->assertEquals('success', $result['status']);
        $this->assertCount(2, $result['servers']);
    }

    /** @test */
    public function it_can_link_whatsapp_account()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'token' => 'abc123',
                'qr_code' => 'data:image/png;base64,iVBOR...'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->whatsappService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->whatsappService, $client);

        $result = $this->whatsappService->linkWhatsAppAccount(1);

        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('qr_code', $result);
    }

    /** @test */
    public function it_can_validate_whatsapp_number()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'valid' => true,
                'formatted' => '+573001234567'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->whatsappService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->whatsappService, $client);

        $result = $this->whatsappService->validateWhatsAppNumber(
            'account123',
            '+573001234567'
        );

        $this->assertEquals('success', $result['status']);
        $this->assertTrue($result['valid']);
    }

    /** @test */
    public function it_can_get_whatsapp_accounts()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'accounts' => [
                    ['unique' => 'acc1', 'phone' => '+573001111111'],
                    ['unique' => 'acc2', 'phone' => '+573002222222']
                ]
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->whatsappService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->whatsappService, $client);

        $result = $this->whatsappService->getWhatsAppAccounts();

        $this->assertEquals('success', $result['status']);
        $this->assertCount(2, $result['accounts']);
    }
}