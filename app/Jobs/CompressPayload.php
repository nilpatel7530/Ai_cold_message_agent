<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\AIOutreach\HeadroomService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CompressPayload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Lead $lead;

    /**
     * Create a new job instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->queue = 'compression';
    }

    /**
     * Execute the job.
     */
    public function handle(HeadroomService $headroom): void
    {
        try {
            Log::info("CompressPayload: Starting context compression for lead {$this->lead->id}");

            $rawText = $this->lead->raw_research_data ?? '';
            if (empty(trim($rawText))) {
                throw new \Exception("Raw research data is empty. Cannot compress.");
            }

            // Step 2: Forward text to Headroom Proxy to optimize token counts
            $compressedText = $headroom->compressPayload($rawText);

            $this->lead->update([
                'compressed_context' => $compressedText,
                'status' => 'drafting',
            ]);

            Log::info("CompressPayload: Compression completed. Dispatching GenerateLeadCopy for lead {$this->lead->id}");

            // Dispatch Step 3
            GenerateLeadCopy::dispatch($this->lead);

        } catch (\Exception $e) {
            $errorMessage = "CompressPayload failed: " . $e->getMessage();
            Log::error($errorMessage);
            $this->lead->update([
                'status' => 'failed',
                'error_logs' => $errorMessage . "\n" . $this->lead->error_logs,
            ]);
        }
    }
}
