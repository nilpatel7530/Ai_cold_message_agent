<?php

namespace App\Services\AIOutreach;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenWAService
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('OPENWA_URL', 'http://localhost:8080'), '/');
        $this->apiKey = env('OPENWA_API_KEY');
    }

    /**
     * Dispatch WhatsApp message using local OpenWA container.
     */
    public function dispatchWhatsApp(string $phone, string $message): bool
    {
        // Clean phone number: remove non-numeric characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phone);

        // Standard OpenWA format appends @c.us for single chats
        $to = $cleanNumber;
        if (!str_contains($to, '@c.us')) {
            $to .= '@c.us';
        }

        try {
            Log::info("OpenWAService: Dispatching WhatsApp message to {$to} via OpenWA...");

            $request = Http::timeout(15);

            // Add authentication headers if API key is provided
            if ($this->apiKey) {
                $request = $request->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'key' => $this->apiKey
                ]);
            }

            $response = $request->post("{$this->baseUrl}/api/sendText", [
                'to' => $to,
                'content' => $message,
            ]);

            if ($response->successful()) {
                Log::info("OpenWAService: WhatsApp message successfully dispatched to {$to}");
                return true;
            }

            Log::error("OpenWAService: Failed to dispatch message. Status: " . $response->status() . " Response: " . $response->body());
            throw new \Exception("OpenWA returned error status " . $response->status());
        } catch (\Exception $e) {
            Log::error("OpenWAService exception: " . $e->getMessage());
            throw $e;
        }
    }
}
