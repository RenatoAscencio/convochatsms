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

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(ConvoChatWhatsAppService::class, $this->whatsappService);
    }

    public function testItValidatesRequiredAccountParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: account');

        $this->whatsappService->sendMessage([
            'recipient' => '+573001234567',
            'message' => 'Test message',
        ]);
    }

    public function testItValidatesRequiredRecipientParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: recipient');

        $this->whatsappService->sendMessage([
            'account' => 'account123',
            'message' => 'Test message',
        ]);
    }

    public function testItCanSendTextMessage()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'WhatsApp message sent',
            ])),
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

    public function testItCanSendMediaMessage()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'Media sent successfully',
            ])),
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

    public function testItCanSendDocument()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'Document sent successfully',
            ])),
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

    public function testItCanGetWhatsappServers()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'servers' => [
                    ['id' => 1, 'name' => 'Server 1'],
                    ['id' => 2, 'name' => 'Server 2'],
                ],
            ])),
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


    public function testItCanValidateWhatsappNumber()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'valid' => true,
                'formatted' => '+573001234567',
            ])),
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

    public function testItCanGetWhatsappAccounts()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'accounts' => [
                    ['unique' => 'acc1', 'phone' => '+573001111111'],
                    ['unique' => 'acc2', 'phone' => '+573002222222'],
                ],
            ])),
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
