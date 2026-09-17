<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
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

            $dirs = [
                $storagePath,
                $storagePath . '/framework',
                $storagePath . '/framework/views',
                $storagePath . '/framework/cache',
                $storagePath . '/framework/cache/data',
                $storagePath . '/framework/sessions',
                $storagePath . '/logs',
            ];

            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') || !is_writable($this->app->basePath('storage'));

        if ($isVercel) {
            // Provide fallback APP_KEY if missing in environment
            if (empty(config('app.key'))) {
                Config::set('app.key', 'base64:Syc3gzbvNetmYCaDIoLoRckRMqrmTEueHCbpoOJeEOo=');
            }

            // Serverless SQLite database handling
            if (config('database.default') === 'sqlite') {
                $dbPath = '/tmp/database.sqlite';
                $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;

                if ($isNew) {
                    @touch($dbPath);
                }

                Config::set('database.connections.sqlite.database', $dbPath);

                if ($isNew) {
                    try {
                        Artisan::call('migrate', ['--force' => true]);
                        Artisan::call('db:seed', ['--force' => true]);
                    } catch (\Throwable $e) {
                        // Handled silently
                    }
                }
            }
        }
    }
}
