<?php

namespace ConvoChat\LaravelSmsGateway;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatAuthService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatCampaignsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatContactsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatListsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatReportsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSettingsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWebhooksService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Illuminate\Support\ServiceProvider;

class ConvoChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/convochat.php', 'convochat');

        // Registrar todos los servicios
        $this->app->singleton('convochat.auth', function ($app) {
            return new ConvoChatAuthService();
        });

        $this->app->singleton('convochat.sms', function ($app) {
            return new ConvoChatSmsService();
        });

        $this->app->singleton('convochat.whatsapp', function ($app) {
            return new ConvoChatWhatsAppService();
        });

        $this->app->singleton('convochat.contacts', function ($app) {
            return new ConvoChatContactsService();
        });

        $this->app->singleton('convochat.lists', function ($app) {
            return new ConvoChatListsService();
        });

        $this->app->singleton('convochat.campaigns', function ($app) {
            return new ConvoChatCampaignsService();
        });

        $this->app->singleton('convochat.webhooks', function ($app) {
            return new ConvoChatWebhooksService();
        });

        $this->app->singleton('convochat.settings', function ($app) {
            return new ConvoChatSettingsService();
        });

        $this->app->singleton('convochat.reports', function ($app) {
            return new ConvoChatReportsService();
        });

        $this->app->singleton('convochat', function ($app) {
            return new class ($app) {
                protected $app;

                public function __construct($app)
                {
                    $this->app = $app;
                }

                public function auth(): ConvoChatAuthService
                {
                    return $this->app['convochat.auth'];
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

                public function lists(): ConvoChatListsService
                {
                    return $this->app['convochat.lists'];
                }

                public function campaigns(): ConvoChatCampaignsService
                {
                    return $this->app['convochat.campaigns'];
                }

                public function webhooks(): ConvoChatWebhooksService
                {
                    return $this->app['convochat.webhooks'];
                }

                public function settings(): ConvoChatSettingsService
                {
                    return $this->app['convochat.settings'];
                }

                public function reports(): ConvoChatReportsService
                {
                    return $this->app['convochat.reports'];
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
            'convochat.auth',
            'convochat.sms',
            'convochat.whatsapp',
            'convochat.contacts',
            'convochat.lists',
            'convochat.campaigns',
            'convochat.webhooks',
            'convochat.settings',
            'convochat.reports',
        ];
    }
}
