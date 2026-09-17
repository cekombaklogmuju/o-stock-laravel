<?php

use App\Http\Middleware\BranchScopeMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Serverless / Read-only filesystem handling
$isServerless = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') || !empty($_SERVER['LAMBDA_TASK_ROOT']) || !is_writable(__DIR__ . '/cache');

if ($isServerless) {
    $cacheOverrides = [
        'APP_CONFIG_CACHE' => '/tmp/config.php',
        'APP_EVENTS_CACHE' => '/tmp/events.php',
        'APP_PACKAGES_CACHE' => '/tmp/packages.php',
        'APP_ROUTES_CACHE' => '/tmp/routes.php',
        'APP_SERVICES_CACHE' => '/tmp/services.php',
    ];
    foreach ($cacheOverrides as $key => $val) {
        putenv("{$key}={$val}");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            BranchScopeMiddleware::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Serverless / Read-only filesystem handling
if ($isServerless || !is_writable($app->basePath('storage'))) {
    $storagePath = '/tmp/storage';
    $app->useStoragePath($storagePath);

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

    // Serverless SQLite database preparation
    $tmpDb = '/tmp/database.sqlite';
    $origDb = dirname(__DIR__) . '/database/database.sqlite';
    if (!file_exists($tmpDb) || (file_exists($origDb) && filemtime($origDb) > filemtime($tmpDb))) {
        if (file_exists($origDb)) {
            @copy($origDb, $tmpDb);
            @chmod($tmpDb, 0666);
        } else {
            @touch($tmpDb);
            @chmod($tmpDb, 0666);
        }
    }
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;

    // Direct maintenance mode contract to FileBasedMaintenanceMode to avoid Manager driver resolution issues
    $app->singleton(
        \Illuminate\Contracts\Foundation\MaintenanceMode::class,
        \Illuminate\Foundation\FileBasedMaintenanceMode::class
    );
}

return $app;
