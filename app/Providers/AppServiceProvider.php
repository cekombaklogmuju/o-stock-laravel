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

            // Ensure session settings cannot expire immediately on Vercel
            Config::set('session.driver', 'cookie');
            Config::set('session.lifetime', 120);
            Config::set('session.expire_on_close', false);

            // Ensure hashing rounds and rehash settings on Vercel
            Config::set('hashing.bcrypt.rounds', 12);
            Config::set('hashing.rehash_on_login', false);

            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
