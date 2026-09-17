<?php

// Serverless entrypoint for Vercel deployment with detailed error diagnostics
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo "<h1>PHP Fatal Error on Vercel</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($error['message']) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($error['file']) . " on line " . $error['line'] . "</p>";
    }
});

try {
    $_ENV['VERCEL'] = '1';
    $_SERVER['VERCEL'] = '1';

    $storagePath = '/tmp/storage';
    $tmpDirs = [
        '/tmp/views',
        '/tmp/cache',
        $storagePath,
        $storagePath . '/framework',
        $storagePath . '/framework/cache',
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/framework/views',
        $storagePath . '/logs',
    ];

    foreach ($tmpDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    $envVars = [
        'APP_STORAGE_PATH' => $storagePath,
        'VIEW_COMPILED_PATH' => $storagePath . '/framework/views',
        'APP_CONFIG_CACHE' => '/tmp/config.php',
        'APP_EVENTS_CACHE' => '/tmp/events.php',
        'APP_PACKAGES_CACHE' => '/tmp/packages.php',
        'APP_ROUTES_CACHE' => '/tmp/routes.php',
        'APP_SERVICES_CACHE' => '/tmp/services.php',
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => '/tmp/database.sqlite',
        'CACHE_STORE' => 'array',
        'CACHE_DRIVER' => 'array',
        'SESSION_DRIVER' => 'cookie',
        'LOG_CHANNEL' => 'stderr',
    ];

    foreach ($envVars as $k => $v) {
        putenv("{$k}={$v}");
        $_ENV[$k] = $v;
        $_SERVER[$k] = $v;
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

    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Laravel Boot Exception on Vercel</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    if ($prev = $e->getPrevious()) {
        echo "<p><strong>Previous Exception:</strong> " . htmlspecialchars($prev->getMessage()) . " in " . htmlspecialchars($prev->getFile()) . ":" . $prev->getLine() . "</p>";
    }
    echo "<h2>Stack Trace:</h2>";
    echo "<pre style='background:#f4f4f4;padding:15px;border:1px solid #ccc;font-size:12px;overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
