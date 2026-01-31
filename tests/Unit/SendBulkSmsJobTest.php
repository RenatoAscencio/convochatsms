<?php

namespace ConvoChat\LaravelSmsGateway\Tests\Unit;

use ConvoChat\LaravelSmsGateway\Jobs\SendBulkSmsJob;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery;

class SendBulkSmsJobTest extends TestCase
{
    public function testJobSendsToAllRecipients()
    {
        $smsService = Mockery::mock(ConvoChatSmsService::class);
        $smsService->shouldReceive('sendSmsWithCredits')
            ->times(3)
            ->andReturn(['status' => 'success']);

        $job = new SendBulkSmsJob(
            ['+573001111111', '+573002222222', '+573003333333'],
            'Test message'
        );

        $job->handle($smsService);

        $this->assertTrue(true); // no exception thrown
    }

    public function testJobDoesNotRedispatchOnPerRecipientFailure()
    {
        Queue::fake();

        $smsService = Mockery::mock(ConvoChatSmsService::class);
        $smsService->shouldReceive('sendSmsWithCredits')
            ->andThrow(new \Exception('API Error'));

        Log::shouldReceive('error')->times(2);

        $job = new SendBulkSmsJob(
            ['+573001111111', '+573002222222'],
            'Test message'
        );

        $job->handle($smsService);

        // No new jobs should be dispatched
        Queue::assertNothingPushed();
    }

    public function testJobUsesDeviceModeWhenDeviceIdProvided()
    {
        $smsService = Mockery::mock(ConvoChatSmsService::class);
        $smsService->shouldReceive('sendSmsWithDevice')
            ->with('+573001111111', 'Test', 'dev123')
            ->once()
            ->andReturn(['status' => 'success']);

        $job = new SendBulkSmsJob(
            ['+573001111111'],
            'Test',
            ['device_id' => 'dev123']
        );

        $job->handle($smsService);

        $this->assertTrue(true);
    }
}
