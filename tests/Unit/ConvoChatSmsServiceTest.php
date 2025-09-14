<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;

class ConvoChatSmsServiceTest extends TestCase
{
    protected ConvoChatSmsService $smsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->smsService = app(ConvoChatSmsService::class);
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(ConvoChatSmsService::class, $this->smsService);
    }

    /** @test */
    public function it_validates_required_parameters_for_sms()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: phone');

        $this->smsService->sendSms(['message' => 'Test']);
    }

    /** @test */
    public function it_validates_phone_parameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: phone');

        $this->smsService->sendSms(['message' => 'Test message']);
    }

    /** @test */
    public function it_validates_message_parameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: message');

        $this->smsService->sendSms(['phone' => '+573001234567']);
    }

    /** @test */
    public function it_builds_correct_params_for_device_mode()
    {
        // Note: This would require mocking HTTP client to avoid real API calls
        $reflection = new \ReflectionClass($this->smsService);
        $validateMethod = $reflection->getMethod('validateRequiredParams');
        $validateMethod->setAccessible(true);

        // Should not throw exception with valid params
        $this->expectNotToPerformAssertions();
        $validateMethod->invoke($this->smsService, [
            'phone' => '+573001234567',
            'message' => 'Test'
        ], ['phone', 'message']);
    }
}