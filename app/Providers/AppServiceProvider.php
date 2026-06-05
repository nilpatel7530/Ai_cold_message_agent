<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('whatsapp-outreach', function (object $job) {
            // Enforce at most 1 dispatch per minute (which plays nicely with the 60-180s jitter)
            return Limit::perMinute(1);
        });
    }
}
