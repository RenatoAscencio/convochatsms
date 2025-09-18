<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ConvoChatSmsServiceTest extends TestCase
{
    protected ConvoChatSmsService $smsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->smsService = app(ConvoChatSmsService::class);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(ConvoChatSmsService::class, $this->smsService);
    }

    public function testItValidatesRequiredPhoneParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: phone');

        $this->smsService->sendSms(['message' => 'Test message']);
    }

    public function testItValidatesRequiredMessageParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: message');

        $this->smsService->sendSms(['phone' => '+573001234567']);
    }

    public function testItBuildsCorrectParamsForDeviceMode()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'SMS sent successfully',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Usar reflection para inyectar el cliente mockeado
        $reflection = new \ReflectionClass($this->smsService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->smsService, $client);

        $result = $this->smsService->sendSmsWithDevice(
            '+573001234567',
            'Test message',
            'device123',
            ['sim' => 1, 'priority' => 1]
        );

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('SMS sent successfully', $result['message']);
    }

    public function testItBuildsCorrectParamsForCreditsMode()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'SMS sent via credits',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->smsService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->smsService, $client);

        $result = $this->smsService->sendSmsWithCredits(
            '+573001234567',
            'Test message',
            'gateway123'
        );

        $this->assertEquals('success', $result['status']);
    }

    public function testItCanGetDevicesList()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'devices' => [
                    ['id' => 'device1', 'name' => 'Android 1'],
                    ['id' => 'device2', 'name' => 'Android 2'],
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->smsService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->smsService, $client);

        $result = $this->smsService->getDevices();

        $this->assertEquals('success', $result['status']);
        $this->assertCount(2, $result['devices']);
    }

    public function testItCanGetCreditsBalance()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'credits' => 100,
                'currency' => 'USD',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->smsService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->smsService, $client);

        $result = $this->smsService->getCredits();

        $this->assertEquals('success', $result['status']);
        $this->assertEquals(100, $result['credits']);
    }

    public function testItHandlesApiErrorsGracefully()
    {
        $mock = new MockHandler([
            new Response(400, [], json_encode([
                'status' => 'error',
                'message' => 'Invalid API key',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflection = new \ReflectionClass($this->smsService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->smsService, $client);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ConvoChat SMS API Error:');

        $this->smsService->getDevices();
    }
}
