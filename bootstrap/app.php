<?php

use App\Http\Middleware\BranchScopeMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
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
$isServerless = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') || !is_writable($app->basePath('storage'));

if ($isServerless) {
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
    if (!file_exists($tmpDb)) {
        $origDb = dirname(__DIR__) . '/database/database.sqlite';
        if (file_exists($origDb)) {
            @copy($origDb, $tmpDb);
        } else {
            @touch($tmpDb);
        }
    }
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

return $app;
