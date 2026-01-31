<?php

namespace ConvoChat\LaravelSmsGateway\Console;

use ConvoChat\LaravelSmsGateway\Facades\ConvoChat;
use Illuminate\Console\Command;

class TestConvoChatCommand extends Command
{
    protected $signature = 'convochat:test
                            {--sms : Test SMS functionality}
                            {--whatsapp : Test WhatsApp functionality}
                            {--phone= : Phone number to test (E.164 format)}
                            {--account= : WhatsApp account ID for testing}
                            {--device= : SMS device ID for testing}
                            {--all : Test all functionality}';

    protected $description = 'Test ConvoChat SMS and WhatsApp functionality';

    public function handle(): int
    {
        $this->info('🚀 ConvoChat Laravel Gateway Test Tool');
        $this->info('=====================================');

        if (! config('convochat.api_key')) {
            $this->error('❌ API Key not configured. Add CONVOCHAT_API_KEY to your .env file');

            return 1;
        }

        $this->info('✅ API Key configured: ' . substr(config('convochat.api_key'), 0, 8) . '...');
        $this->info('🌐 Base URL: ' . config('convochat.base_url'));
        $this->newLine();

        if ($this->option('all')) {
            $this->testConfiguration();
            $this->testSmsInfo();
            $this->testWhatsAppInfo();
        } elseif ($this->option('sms')) {
            $this->testSms();
        } elseif ($this->option('whatsapp')) {
            $this->testWhatsApp();
        } else {
            $this->testConfiguration();
            $this->testSmsInfo();
            $this->testWhatsAppInfo();
        }

        $this->newLine();
        $this->info('✨ Test completed!');

        return 0;
    }

    protected function testConfiguration(): void
    {
        $this->info('🔧 Testing Configuration...');

        try {
            $subscription = ConvoChat::sms()->getSubscription();

            if (isset($subscription['status']) && $subscription['status'] === 'success') {
                $this->info('✅ Connection successful!');

                if (isset($subscription['package'])) {
                    $this->info('📦 Package: ' . ($subscription['package']['name'] ?? 'N/A'));
                    $this->info('📅 Expires: ' . ($subscription['package']['expires_at'] ?? 'N/A'));
                }
            } else {
                $this->warn('⚠️ Connection issue: ' . ($subscription['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error('❌ Connection failed: ' . $e->getMessage());
        }

        $this->newLine();
    }

    protected function testSmsInfo(): void
    {
        $this->info('📱 Testing SMS Information...');

        try {
            // Test devices
            $devices = ConvoChat::sms()->getDevices();
            if (isset($devices['devices'])) {
                $this->info('📱 Available devices: ' . count($devices['devices']));
                foreach ($devices['devices'] as $device) {
                    $this->line('   - ' . ($device['name'] ?? $device['id']) . ' (' . $device['id'] . ')');
                }
            } else {
                $this->warn('⚠️ No devices found or error: ' . ($devices['message'] ?? 'Unknown'));
            }

            // Test credits
            $credits = ConvoChat::sms()->getCredits();
            if (isset($credits['credits'])) {
                $this->info('💰 Credits balance: ' . $credits['credits']);
            } else {
                $this->warn('⚠️ Credits info not available: ' . ($credits['message'] ?? 'Unknown'));
            }

            // Test gateway rates
            $rates = ConvoChat::sms()->getRates();
            if (isset($rates['rates'])) {
                $this->info('💸 Gateway rates available: ' . count($rates['rates']) . ' gateways');
            } else {
                $this->warn('⚠️ Gateway rates not available: ' . ($rates['message'] ?? 'Unknown'));
            }

        } catch (\Exception $e) {
            $this->error('❌ SMS info test failed: ' . $e->getMessage());
        }

        $this->newLine();
    }

    protected function testWhatsAppInfo(): void
    {
        $this->info('📞 Testing WhatsApp Information...');

        try {
            // Test WhatsApp accounts
            $accounts = ConvoChat::whatsapp()->getWhatsAppAccounts();
            if (isset($accounts['accounts'])) {
                $this->info('📞 WhatsApp accounts: ' . count($accounts['accounts']));
                foreach ($accounts['accounts'] as $account) {
                    $this->line('   - ' . ($account['phone'] ?? 'N/A') . ' (' . $account['unique'] . ')');
                }
            } else {
                $this->warn('⚠️ No WhatsApp accounts found: ' . ($accounts['message'] ?? 'Unknown'));
            }

            // Test WhatsApp servers
            $servers = ConvoChat::whatsapp()->getWhatsAppServers();
            if (isset($servers['servers'])) {
                $this->info('🖥️ Available servers: ' . count($servers['servers']));
            } else {
                $this->warn('⚠️ No servers available: ' . ($servers['message'] ?? 'Unknown'));
            }

        } catch (\Exception $e) {
            $this->error('❌ WhatsApp info test failed: ' . $e->getMessage());
        }

        $this->newLine();
    }

    protected function testSms(): void
    {
        /** @var string|null $phone */
        $phone = $this->option('phone');
        /** @var string|null $device */
        $device = $this->option('device');

        if (! $phone) {
            $phone = $this->ask('Enter phone number (E.164 format, e.g., +573001234567)');
        }

        if (! $phone) {
            $this->error('❌ Phone number is required for SMS testing');

            return;
        }

        $this->info("📱 Testing SMS to: {$phone}");

        try {
            if ($device) {
                $result = ConvoChat::sms()->sendSmsWithDevice(
                    $phone,
                    'Test SMS from ConvoChat Laravel Gateway - ' . now(),
                    $device
                );
            } else {
                $result = ConvoChat::sms()->sendSmsWithCredits(
                    $phone,
                    'Test SMS from ConvoChat Laravel Gateway - ' . now()
                );
            }

            if (isset($result['status']) && $result['status'] === 'success') {
                $this->info('✅ SMS sent successfully!');
                if (isset($result['id'])) {
                    $this->info('📧 Message ID: ' . $result['id']);
                }
            } else {
                $this->warn('⚠️ SMS sending failed: ' . ($result['message'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            $this->error('❌ SMS test failed: ' . $e->getMessage());
        }
    }

    protected function testWhatsApp(): void
    {
        $phone = $this->option('phone');
        $account = $this->option('account');

        if (! $phone) {
            $phone = $this->ask('Enter phone number (E.164 format, e.g., +573001234567)');
        }

        if (! $account) {
            $account = $this->ask('Enter WhatsApp account ID');
        }

        if (! $phone || ! $account) {
            $this->error('❌ Phone number and account ID are required for WhatsApp testing');

            return;
        }

        $this->info("📞 Testing WhatsApp to: {$phone}");

        try {
            $result = ConvoChat::whatsapp()->sendText(
                $account,
                $phone,
                'Test WhatsApp message from ConvoChat Laravel Gateway - ' . now()
            );

            if (isset($result['status']) && $result['status'] === 'success') {
                $this->info('✅ WhatsApp message sent successfully!');
                if (isset($result['id'])) {
                    $this->info('📧 Message ID: ' . $result['id']);
                }
            } else {
                $this->warn('⚠️ WhatsApp sending failed: ' . ($result['message'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            $this->error('❌ WhatsApp test failed: ' . $e->getMessage());
        }
    }
}
