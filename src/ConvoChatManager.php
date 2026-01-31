<?php

namespace ConvoChat\LaravelSmsGateway;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatContactsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatOtpService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatUssdService;
use ConvoChat\LaravelSmsGateway\Services\ConvoChatWhatsAppService;
use Illuminate\Contracts\Foundation\Application;

class ConvoChatManager
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function sms(): ConvoChatSmsService
    {
        /** @var ConvoChatSmsService */
        return $this->app->make('convochat.sms');
    }

    public function whatsapp(): ConvoChatWhatsAppService
    {
        /** @var ConvoChatWhatsAppService */
        return $this->app->make('convochat.whatsapp');
    }

    public function contacts(): ConvoChatContactsService
    {
        /** @var ConvoChatContactsService */
        return $this->app->make('convochat.contacts');
    }

    public function otp(): ConvoChatOtpService
    {
        /** @var ConvoChatOtpService */
        return $this->app->make('convochat.otp');
    }

    public function ussd(): ConvoChatUssdService
    {
        /** @var ConvoChatUssdService */
        return $this->app->make('convochat.ussd');
    }
}
