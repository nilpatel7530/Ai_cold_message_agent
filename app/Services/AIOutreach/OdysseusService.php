<?php

namespace App\Services\AIOutreach;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OdysseusService
{
    protected string $baseUrl;

    public function __construct()
    {
        $url = env('ODYSSEUS_URL') ?: env('SEARXNG_URL') ?: 'http://localhost:3000';
        $this->baseUrl = rtrim($url, '/');
    }

    /**
     * Fetch company intelligence using SearXNG.
     * Returns a raw text payload containing matching results.
     */
    public function fetchCompanyIntel(string $companyName, string $url): string
    {
        $query = $companyName;
        if ($url) {
            $domain = parse_url($url, PHP_URL_HOST) ?: $url;
            $query .= " site:{$domain} OR \"{$companyName}\" about services";
        } else {
            $query .= " about services profile";
        }

        try {
            Log::info("OdysseusService: Querying SearXNG for query: {$query}");
            $response = Http::timeout(15)->get("{$this->baseUrl}/search", [
                'q' => $query,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['results'] ?? [];
                
                $rawText = "";
                foreach ($results as $result) {
                    $rawText .= "Title: " . ($result['title'] ?? '') . "\n";
                    $rawText .= "Snippet: " . ($result['content'] ?? '') . "\n";
                    $rawText .= "URL: " . ($result['url'] ?? '') . "\n\n";
                }

                return !empty(trim($rawText)) ? $rawText : "No intelligence found for company: {$companyName}";
            }

            Log::error("OdysseusService: SearXNG request failed with status: " . $response->status());
            throw new \Exception("SearXNG request failed with status: " . $response->status());
        } catch (\Exception $e) {
            Log::error("OdysseusService exception: " . $e->getMessage());
            throw $e;
        }
    }
}
