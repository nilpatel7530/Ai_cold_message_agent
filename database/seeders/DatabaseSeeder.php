<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed default Services
        Service::create([
            'name' => 'Odysseus Workspace',
            'key' => 'odysseus',
            'port' => 3000,
            'startup_command' => 'docker compose up -d odysseus',
            'shutdown_command' => 'docker compose stop odysseus',
            'description' => 'Deep Research Engine running SearXNG & scraping backend.'
        ]);

        Service::create([
            'name' => 'Headroom Proxy',
            'key' => 'headroom',
            'port' => 8787,
            'startup_command' => 'docker compose up -d headroom',
            'shutdown_command' => 'docker compose stop headroom',
            'description' => 'Context optimizer using SmartCrusher logic to reduce token weight.'
        ]);

        Service::create([
            'name' => 'LM Studio (Gemma)',
            'key' => 'lm_studio',
            'port' => 1234,
            'startup_command' => 'lmstudio server start',
            'shutdown_command' => 'lmstudio server stop',
            'description' => 'Local LLM Inference Engine hosting gemma-4-e4b.'
        ]);

        Service::create([
            'name' => 'OpenWA Gateway',
            'key' => 'openwa',
            'port' => 8080,
            'startup_command' => 'docker compose up -d openwa',
            'shutdown_command' => 'docker compose stop openwa',
            'description' => 'WhatsApp REST Gateway interface container.'
        ]);
    }
}
