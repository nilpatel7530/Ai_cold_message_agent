<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lead;
use App\Jobs\ProcessLeadResearch;
use Illuminate\Support\Facades\Log;

class OutreachIngest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outreach:ingest {file? : The path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest leads from a CSV file and trigger the research pipeline';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');

        if (!$filePath) {
            $filePath = base_path('leads_sample.csv');
        }

        if (!file_exists($filePath)) {
            $this->error("CSV file not found at: {$filePath}");
            return 1;
        }

        $this->info("Parsing leads from CSV file: {$filePath}...");

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("Unable to open CSV file.");
            return 1;
        }

        // Parse headers
        $headers = fgetcsv($handle, 1000, ',');
        if (!$headers) {
            fclose($handle);
            $this->error("Empty CSV file.");
            return 1;
        }

        // Map header indices case-insensitively
        $companyNameIdx = -1;
        $websiteUrlIdx = -1;
        $phoneIdx = -1;

        foreach ($headers as $index => $header) {
            $header = strtolower(trim($header));
            if (str_contains($header, 'web') || str_contains($header, 'url') || str_contains($header, 'site')) {
                $websiteUrlIdx = $index;
            } elseif (str_contains($header, 'company') || str_contains($header, 'name')) {
                $companyNameIdx = $index;
            } elseif (str_contains($header, 'phone') || str_contains($header, 'number') || str_contains($header, 'mobile')) {
                $phoneIdx = $index;
            }
        }

        // Fallbacks
        if ($companyNameIdx === -1) $companyNameIdx = 0;
        if ($websiteUrlIdx === -1) $websiteUrlIdx = 1;
        if ($phoneIdx === -1) $phoneIdx = 2;

        $imported = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) <= max($companyNameIdx, $websiteUrlIdx, $phoneIdx)) {
                continue; // skip malformed row
            }

            $companyName = trim($row[$companyNameIdx] ?? '');
            $websiteUrl = trim($row[$websiteUrlIdx] ?? '');
            $phone = trim($row[$phoneIdx] ?? '');

            if (empty($companyName) || empty($phone)) {
                continue; // Skip rows lacking name or phone
            }

            // Create lead
            $lead = Lead::create([
                'company_name' => $companyName,
                'website_url' => $websiteUrl,
                'phone_number' => $phone,
                'status' => 'pending'
            ]);

            // Dispatch process research job
            ProcessLeadResearch::dispatch($lead);
            $imported++;

            $this->line("Queued lead: <info>{$companyName}</info> | Phone: {$phone}");
        }

        fclose($handle);

        $this->info("\nSuccessfully ingested {$imported} leads and dispatched pipeline jobs.");
        return 0;
    }
}
