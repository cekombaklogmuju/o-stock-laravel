<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') || !is_writable($this->app->basePath('storage'));

        if ($isVercel) {
            $storagePath = '/tmp/storage';
            $this->app->useStoragePath($storagePath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') || !is_writable($this->app->basePath('storage'));

        if ($isVercel) {
            // Provide fallback encryption key if not configured in Vercel dashboard
            if (empty(config('app.key'))) {
                Config::set('app.key', 'base64:Syc3gzbvNetmYCaDIoLoRckRMqrmTEueHCbpoOJeEOo=');
            }

            // Route SQLite to writable /tmp
            if (config('database.default') === 'sqlite') {
                $dbPath = '/tmp/database.sqlite';
                if (!file_exists($dbPath)) {
                    @touch($dbPath);
                }
                Config::set('database.connections.sqlite.database', $dbPath);
            }
        }
    }
}
