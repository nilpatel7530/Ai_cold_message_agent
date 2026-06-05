<?php

namespace App\Services\AIOutreach;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HeadroomService
{
    protected string $baseUrl;

    public function __construct()
    {
        $url = env('HEADROOM_PROXY_URL') ?: 'http://localhost:8787';
        $this->baseUrl = rtrim($url, '/');
    }

    /**
     * Compress the raw data payload to reduce token weight.
     */
    public function compressPayload(string $rawData): string
    {
        if (empty(trim($rawData))) {
            return '';
        }

        try {
            Log::info("HeadroomService: Sending payload to proxy for compression...");
            $response = Http::timeout(10)->post("{$this->baseUrl}/v1/compress", [
                'content' => $rawData,
            ]);

            if ($response->successful()) {
                return $response->json('compressed_content') ?? $response->json('content') ?? $response->body();
            }

            Log::warning("HeadroomService: API call failed. Falling back to local compression algorithm.");
        } catch (\Exception $e) {
            Log::error("HeadroomService: API call failed with message: " . $e->getMessage() . ". Falling back to local compression.");
        }

        return $this->localCompress($rawData);
    }

    /**
     * Local SmartCrusher HTML parsing & text compression fallback.
     */
    protected function localCompress(string $html): string
    {
        // Remove scripts, styles, header, footer, nav elements, and comments
        $cleanHtml = preg_replace([
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/<style\b[^>]*>(.*?)<\/style>/is',
            '/<header\b[^>]*>(.*?)<\/header>/is',
            '/<footer\b[^>]*>(.*?)<\/footer>/is',
            '/<nav\b[^>]*>(.*?)<\/nav>/is',
            '/<!--(.*?)-->/is'
        ], '', $html);

        // Strip tag markup
        $text = strip_tags($cleanHtml);

        // Replace multiple spaces and newlines
        $text = preg_replace('/\s+/', ' ', $text);

        // Split into sentences/phrases
        $phrases = explode('. ', $text);
        $filteredPhrases = [];

        foreach ($phrases as $phrase) {
            $trimmed = trim($phrase);
            if (empty($trimmed)) {
                continue;
            }

            // Filter out boilerplate text
            if ($this->isBoilerplate($trimmed)) {
                continue;
            }

            $filteredPhrases[] = $trimmed;
        }

        // Join sentences back together
        $compressed = implode('. ', $filteredPhrases);

        // Truncate to maximum characters (3000 chars) to prevent context bloat
        return substr($compressed, 0, 3000);
    }

    /**
     * Detect common boilerplate text.
     */
    protected function isBoilerplate(string $text): bool
    {
        $patterns = [
            '/cookie/i',
            '/privacy policy/i',
            '/terms of (use|service)/i',
            '/all rights reserved/i',
            '/copyright \d{4}/i',
            '/designed by/i',
            '/click here/i',
            '/sign up/i',
            '/login/i',
            '/navigation/i',
            '/subscribe to/i',
            '/follow us/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        // Exclude very short isolated texts (likely menu elements or buttons)
        if (strlen($text) < 15 && !preg_match('/^[a-zA-Z0-9\s]+$/', $text)) {
            return true;
        }

        return false;
    }
}
