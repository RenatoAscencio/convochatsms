<?php

namespace ConvoChat\LaravelSmsGateway\Jobs;

use ConvoChat\LaravelSmsGateway\Services\ConvoChatSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkSmsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    /** @var array<int> */
    public array $backoff = [30, 60, 120];

    public function __construct(
        private array $recipients,
        private string $message,
        private array $options = []
    ) {
        $this->onQueue(config('convochat.queue.queue_name', 'convochat'));
    }

    public function handle(ConvoChatSmsService $smsService): void
    {
        foreach ($this->recipients as $phone) {
            try {
                if (isset($this->options['device_id'])) {
                    $result = $smsService->sendSmsWithDevice(
                        $phone,
                        $this->message,
                        $this->options['device_id']
                    );
                } else {
                    $result = $smsService->sendSmsWithCredits(
                        $phone,
                        $this->message,
                        $this->options['gateway_id'] ?? null
                    );
                }

                if ($result['status'] !== 'success') {
                    logger()->warning("Failed to send SMS to {$phone}", ['result' => $result]);
                }

                // Rate limiting entre mensajes
                if (config('convochat.queue.rate_limit_delay')) {
                    usleep(config('convochat.queue.rate_limit_delay', 100000));
                }

            } catch (\Exception $e) {
                logger()->error("SMS job failed for {$phone}: " . $e->getMessage());

                // Re-encolar mensaje fallido
                if ($this->attempts() < $this->tries) {
                    self::dispatch([$phone], $this->message, $this->options)
                        ->delay(now()->addMinutes(5));
                }
            }
        }
    }
}
