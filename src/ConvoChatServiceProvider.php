<?php

namespace ConvoChat\LaravelSmsGateway;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatContactsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatOtpService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Illuminate\Support\ServiceProvider;

class ConvoChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/convochat.php', 'convochat');

        // Registrar todos los servicios
        $this->app->singleton('convochat.sms', function ($app) {
            return new ConvoChatSmsService();
        });

        $this->app->singleton('convochat.whatsapp', function ($app) {
            return new ConvoChatWhatsAppService();
        });

        $this->app->singleton('convochat.contacts', function ($app) {
            return new ConvoChatContactsService();
        });

        $this->app->singleton('convochat.otp', function ($app) {
            return new ConvoChatOtpService();
        });

        $this->app->singleton('convochat', function ($app) {
            return new class ($app) {
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

                public function contacts(): ConvoChatContactsService
                {
                    return $this->app['convochat.contacts'];
                }

                public function otp(): ConvoChatOtpService
                {
                    return $this->app['convochat.otp'];
                }
            };
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/convochat.php' => config_path('convochat.php'),
            ], 'convochat-config');

            $this->commands([
                \ConvoChat\LaravelSmsGateway\Console\TestConvoChatCommand::class,
            ]);
        }
    }

    public function provides()
    {
        return [
            'convochat',
            'convochat.sms',
            'convochat.whatsapp',
            'convochat.contacts',
            'convochat.otp',
        ];
    }
}
