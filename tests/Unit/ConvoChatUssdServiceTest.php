<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatUssdService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ConvoChatUssdServiceTest extends TestCase
{
    protected ConvoChatUssdService $ussdService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ussdService = app(ConvoChatUssdService::class);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(ConvoChatUssdService::class, $this->ussdService);
    }

    public function testItCanSendUssd()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'message' => 'USSD request sent',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatUssdService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->sendUssd('*123#', 1, 'device123');

        $this->assertEquals(200, $result['status']);
    }

    public function testItCanGetUssdRequests()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'data' => [['id' => 1, 'code' => '*123#']],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatUssdService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->getUssdRequests();

        $this->assertEquals(200, $result['status']);
        $this->assertCount(1, $result['data']);
    }

    public function testItCanDeleteUssdRequest()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'message' => 'Deleted',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatUssdService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->deleteUssdRequest(1);

        $this->assertEquals(200, $result['status']);
    }

    public function testItHandlesApiErrorsGracefully()
    {
        $mock = new MockHandler([
            new Response(400, [], json_encode([
                'status' => 'error',
                'message' => 'Bad request',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatUssdService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ConvoChat USSD API Error:');

        $service->sendUssd('*123#', 1, 'device123');
    }
}
