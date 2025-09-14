<?php

namespace ConvoChat\LaravelSmsGateway;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Illuminate\Support\ServiceProvider;

class ConvoChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/convochat.php', 'convochat');

        $this->app->singleton('convochat.sms', function ($app) {
            return new ConvoChatSmsService();
        });

        $this->app->singleton('convochat.whatsapp', function ($app) {
            return new ConvoChatWhatsAppService();
        });

        $this->app->singleton('convochat', function ($app) {
            return new class($app) {
                protected $app;

                public function __construct($app)
                {
                    $this->app = $app;
                }

                public function sms(): ConvoChatSmsService
                {
                    return $this->app['convochat.sms'];
                }

                public function whatsapp(): ConvoChatWhatsAppService
                {
                    return $this->app['convochat.whatsapp'];
                }
            };
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/convochat.php' => config_path('convochat.php'),
            ], 'convochat-config');

            $this->commands([
                // Future: Add Artisan commands here
            ]);
        }
    }

    public function provides()
    {
        return [
            'convochat',
            'convochat.sms',
            'convochat.whatsapp',
        ];
    }
}