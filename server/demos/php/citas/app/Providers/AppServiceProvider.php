<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Load settings from DB into config() so they are globally accessible
        try {
            if (Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::allAsArray();
                foreach ($settings as $key => $value) {
                    config(["settings.{$key}" => $value]);
                }
            }
        } catch (\Throwable $e) {
            // Silently fail during migrations/install when table doesn't exist yet
        }
    }
}
