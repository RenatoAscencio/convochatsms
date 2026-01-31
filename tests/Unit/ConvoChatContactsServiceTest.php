<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatContactsService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ConvoChatContactsServiceTest extends TestCase
{
    protected ConvoChatContactsService $contactsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contactsService = app(ConvoChatContactsService::class);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(ConvoChatContactsService::class, $this->contactsService);
    }

    public function testItValidatesRequiredContactParams()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: phone');

        $this->contactsService->createContact(['name' => 'John', 'groups' => '1']);
    }

    public function testItValidatesRequiredGroupNameParam()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: name');

        $this->contactsService->createGroup([]);
    }

    public function testItCanGetContacts()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'data' => [['id' => 1, 'name' => 'John', 'phone' => '+573001234567']],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatContactsService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->getContacts();

        $this->assertEquals(200, $result['status']);
        $this->assertCount(1, $result['data']);
    }

    public function testItCanCreateContact()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'message' => 'Contact created',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatContactsService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->createContact([
            'phone' => '+573001234567',
            'name' => 'John Doe',
            'groups' => '1,2',
        ]);

        $this->assertEquals(200, $result['status']);
    }

    public function testItCanCreateGroup()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'data' => ['id' => 1],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new ConvoChatContactsService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $result = $service->createGroup(['name' => 'VIP Clients']);

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

        $service = new ConvoChatContactsService($client, [
            'api_key' => 'test_key',
            'base_url' => 'https://sms.convo.chat/api',
            'timeout' => 30,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ConvoChat Contacts API Error:');

        $service->getContacts();
    }
}
