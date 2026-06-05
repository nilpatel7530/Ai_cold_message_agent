<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\AIOutreach\OdysseusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLeadResearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Lead $lead;

    /**
     * Create a new job instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->queue = 'research';
    }

    /**
     * Execute the job.
     */
    public function handle(OdysseusService $odysseus): void
    {
        // Transition status to researching
        $this->lead->update([
            'status' => 'researching',
            'attempts' => $this->lead->attempts + 1,
        ]);

        try {
            Log::info("ProcessLeadResearch: Starting research for lead {$this->lead->id} ({$this->lead->company_name})");

            // Step 1: Deep research via Odysseus
            $rawText = $odysseus->fetchCompanyIntel($this->lead->company_name, $this->lead->website_url ?? '');
            
            $this->lead->update([
                'raw_research_data' => $rawText,
                'status' => 'compressing',
            ]);

            Log::info("ProcessLeadResearch: Research completed. Dispatching CompressPayload for lead {$this->lead->id}");

            // Dispatch Step 2
            CompressPayload::dispatch($this->lead);

        } catch (\Exception $e) {
            $errorMessage = "ProcessLeadResearch failed: " . $e->getMessage();
            Log::error($errorMessage);
            $this->lead->update([
                'status' => 'failed',
                'error_logs' => $errorMessage . "\n" . $this->lead->error_logs,
            ]);
        }
    }
}
