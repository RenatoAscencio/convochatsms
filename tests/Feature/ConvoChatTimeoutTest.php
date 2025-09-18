<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Feature;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;

class ConvoChatTimeoutTest extends TestCase
{
    public function testItUsesCustomTimeoutConfiguration()
    {
        $customTimeout = 60;

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'SMS sent successfully',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatSmsService($client, [
            'api_key' => 'test-key',
            'base_url' => 'https://test.convo.chat/api',
            'timeout' => $customTimeout,
        ]);

        $result = $service->sendSms([
            'phone' => '+573001234567',
            'message' => 'Test message',
        ]);

        $this->assertEquals('success', $result['status']);
    }

    public function testItHandlesTimeoutExceptions()
    {
        $mock = new MockHandler([
            new ConnectException(
                'Connection timeout',
                new Request('POST', 'test')
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatSmsService($client, [
            'api_key' => 'test-key',
            'base_url' => 'https://test.convo.chat/api',
            'timeout' => 1,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ConvoChat SMS API Error:');

        $service->sendSms([
            'phone' => '+573001234567',
            'message' => 'Test message',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            \ConvoChat\LaravelSmsGateway\ConvoChatServiceProvider::class,
        ];
    }
}
