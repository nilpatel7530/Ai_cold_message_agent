<?php

namespace App\Services\AIOutreach;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InferenceService
{
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('LM_STUDIO_URL', 'http://localhost:1234'), '/');
        $this->model = env('INFERENCE_MODEL', 'gemma-4-e4b');
    }

    /**
     * Generate B2B outreach message using local Gemma model on LM Studio.
     */
    public function generateOutreach(string $compressedContext): string
    {
        $systemPrompt = <<<PROMPT
You are an Expert B2B Consultant.
Analyze the compressed context about the target company and identify their potential pain points or needs.
Draft a highly targeted cold message targeting their pain-point.

Rigid Execution Constraints:
- Strict length: Maximum of 3 sentences.
- Zero corporate fluff (Do NOT say "Hope this finds you well", "Dear [Name]", "Best regards", etc.).
- Conversational and direct tone (write as if you are sending a quick text message on WhatsApp).
- Direct value/pain-point alignment.
PROMPT;

        $userPrompt = "Compressed Context:\n{$compressedContext}\n\nDraft Outreach WhatsApp message:";

        try {
            Log::info("InferenceService: Sending generation request to LM Studio model '{$this->model}'...");
            
            $response = Http::timeout(60)->post("{$this->baseUrl}/v1/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? '';
                return trim($content);
            }

            Log::error("InferenceService: LM Studio request failed with status: " . $response->status() . " Response: " . $response->body());
            throw new \Exception("Failed to generate outreach from LM Studio: Status " . $response->status());
        } catch (\Exception $e) {
            Log::error("InferenceService exception: " . $e->getMessage());
            throw $e;
        }
    }
}
