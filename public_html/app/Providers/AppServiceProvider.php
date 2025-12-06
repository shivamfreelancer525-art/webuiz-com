<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dotenv\Dotenv;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // IMPORTANT: Laravel automatically loads .env file BEFORE service providers run.
        // So .env is always loaded first, and this code only adds .env.local as an override.
        
        // Load .env.local if it exists and we're in local/development environment
        // This ensures .env.local only overrides .env in dev, not in production
        $envLocalPath = base_path('.env.local');
        
        // Check APP_ENV from already-loaded .env file
        // At this point, Laravel has already loaded .env, so $_ENV should have APP_ENV
        $appEnv = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'production';
        
        // Only load .env.local in local/development environments
        // If .env.local doesn't exist, .env will be used (Laravel's default behavior)
        // In production, .env.local will be ignored even if it exists
        if (file_exists($envLocalPath) && in_array(strtolower($appEnv), ['local', 'development', 'dev', 'testing'])) {
            $dotenvLocal = Dotenv::createImmutable(base_path(), '.env.local');
            $dotenvLocal->load(); // This overrides values from .env
        }
        // If .env.local doesn't exist or we're in production, .env values are used as-is
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
