<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class OutreachStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outreach:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the connectivity status of all local B2B outreach microservices';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $services = [
            'Odysseus Workspace' => [
                'url' => env('ODYSSEUS_URL') ?: env('SEARXNG_URL') ?: 'http://localhost:3000',
                'desc' => 'Deep Research Engine'
            ],
            'Headroom Proxy' => [
                'url' => env('HEADROOM_PROXY_URL') ?: 'http://localhost:8787',
                'desc' => 'Context Optimization & Compression'
            ],
            'LM Studio (Gemma)' => [
                'url' => env('LM_STUDIO_URL') ?: 'http://localhost:1234',
                'desc' => 'Local LLM Inference Engine'
            ],
            'OpenWA Gateway' => [
                'url' => env('OPENWA_URL') ?: 'http://localhost:8080',
                'desc' => 'WhatsApp REST Gateway'
            ]
        ];

        $this->info("Checking connectivity for local B2B outreach pipeline microservices...\n");

        $rows = [];
        foreach ($services as $name => $info) {
            $url = rtrim($info['url'], '/');
            $status = '🔴 OFFLINE';
            $error = '-';

            try {
                // Determine ping path depending on service
                $pingUrl = $url;
                if ($name === 'LM Studio (Gemma)') {
                    $pingUrl = "{$url}/v1/models";
                } elseif ($name === 'OpenWA Gateway') {
                    $pingUrl = "{$url}/api/sendText"; // Just check base url or specific endpoint
                }

                $response = Http::timeout(2)->get($pingUrl);
                // For OpenWA, a GET to sendText might return 405/404 but if it responds, the server is alive
                if ($response->status() < 500) {
                    $status = '🟢 ONLINE';
                } else {
                    $status = '🟡 RESPONDING (' . $response->status() . ')';
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $error = 'Connection Timeout/Refused';
            } catch (\Exception $e) {
                $error = $e->getMessage();
                // If we get a 405 (Method Not Allowed) or similar from posting/getting, it is still online
                if (str_contains($error, '405') || str_contains($error, '404') || str_contains($error, '401')) {
                    $status = '🟢 ONLINE';
                    $error = '-';
                }
            }

            $rows[] = [$name, $url, $info['desc'], $status, $error];
        }

        $this->table(
            ['Service Name', 'Endpoint', 'Description', 'Status', 'Error Log'],
            $rows
        );

        return 0;
    }
}
