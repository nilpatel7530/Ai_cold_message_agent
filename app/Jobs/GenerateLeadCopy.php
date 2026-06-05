<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\AIOutreach\InferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateLeadCopy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Lead $lead;

    /**
     * Create a new job instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->queue = 'ai-generation';
    }

    /**
     * Execute the job.
     */
    public function handle(InferenceService $inference): void
    {
        try {
            Log::info("GenerateLeadCopy: Generating message copy for lead {$this->lead->id} ({$this->lead->company_name})");

            $compressedContext = $this->lead->compressed_context ?? '';
            if (empty(trim($compressedContext))) {
                throw new \Exception("Compressed context is empty. Cannot generate message.");
            }

            // Step 3: Send compressed context to LM Studio
            $generatedCopy = $inference->generateOutreach($compressedContext);

            if (empty(trim($generatedCopy))) {
                throw new \Exception("Generated outreach copy is empty.");
            }

            $this->lead->update([
                'generated_copy' => $generatedCopy,
                'status' => 'queued',
            ]);

            Log::info("GenerateLeadCopy: Message generated successfully. Dispatching DispatchWhatsAppMessage for lead {$this->lead->id}");

            // Dispatch Step 4
            DispatchWhatsAppMessage::dispatch($this->lead);

        } catch (\Exception $e) {
            $errorMessage = "GenerateLeadCopy failed: " . $e->getMessage();
            Log::error($errorMessage);
            $this->lead->update([
                'status' => 'failed',
                'error_logs' => $errorMessage . "\n" . $this->lead->error_logs,
            ]);
        }
    }
}
