<?php

namespace ConvoChat\LaravelSmsGateway;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatContactsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatOtpService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatUssdService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Illuminate\Support\ServiceProvider;

class ConvoChatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/convochat.php', 'convochat');

        $this->app->singleton('convochat.sms', function () {
            return new ConvoChatSmsService();
        });

        $this->app->singleton('convochat.whatsapp', function () {
            return new ConvoChatWhatsAppService();
        });

        $this->app->singleton('convochat.contacts', function () {
            return new ConvoChatContactsService();
        });

        $this->app->singleton('convochat.otp', function () {
            return new ConvoChatOtpService();
        });

        $this->app->singleton('convochat.ussd', function () {
            return new ConvoChatUssdService();
        });

        $this->app->singleton('convochat', function ($app) {
            return new ConvoChatManager($app);
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

    /** @return array<int, string> */
    public function provides(): array
    {
        return [
            'convochat',
            'convochat.sms',
            'convochat.whatsapp',
            'convochat.contacts',
            'convochat.otp',
            'convochat.ussd',
        ];
    }
}
