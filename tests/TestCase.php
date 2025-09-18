<?php

namespace ConvoChat\LaravelSmsGateway\Tests;

use ConvoChat\LaravelSmsGateway\ConvoChatServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('convochat.api_key', 'test_api_key');
        $this->app['config']->set('convochat.base_url', 'https://test.convo.chat/api');
    }

    protected function getPackageProviders($app)
    {
        return [
            ConvoChatServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'ConvoChat' => \ConvoChat\LaravelSmsGateway\Facades\ConvoChat::class,
        ];
    }
}
