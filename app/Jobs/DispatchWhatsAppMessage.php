<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\AIOutreach\OpenWAService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;

class DispatchWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Lead $lead;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 360;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        // Run on a dedicated queue string
        $this->queue = 'whatsapp-dispatch';
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [new RateLimited('whatsapp-outreach')];
    }

    /**
     * Execute the job.
     */
    public function handle(OpenWAService $openwa): void
    {
        // Check if the lead is in the expected status (queued)
        if ($this->lead->status !== 'queued') {
            Log::warning("DispatchWhatsAppMessage: Skipping lead {$this->lead->id} because status is '{$this->lead->status}', not 'queued'.");
            return;
        }

        // Transition status to sending
        $this->lead->update(['status' => 'sending']);

        try {
            if (empty(trim($this->lead->generated_copy))) {
                throw new \Exception("Generated outreach copy is empty.");
            }

            // Dispatch message via OpenWA
            $success = $openwa->dispatchWhatsApp($this->lead->phone_number, $this->lead->generated_copy);

            if ($success) {
                $this->lead->update([
                    'status' => 'sent',
                    'error_logs' => null,
                ]);
                Log::info("DispatchWhatsAppMessage: WhatsApp message successfully sent to lead {$this->lead->id}");

                // CRITICAL: Force the queue worker to sleep for a random interval 
                // between 60 to 180 seconds before processing the next job in line.
                $delay = rand(60, 180);
                Log::info("DispatchWhatsAppMessage: Forcing sleep delay of {$delay} seconds to maintain anti-ban pacing.");
                sleep($delay);
            } else {
                throw new \Exception("OpenWAService returned false.");
            }
        } catch (\Exception $e) {
            $errorMessage = "DispatchWhatsAppMessage failed: " . $e->getMessage();
            Log::error($errorMessage);
            $this->lead->update([
                'status' => 'failed',
                'error_logs' => $errorMessage . "\n" . $this->lead->error_logs,
            ]);
        }
    }
}
