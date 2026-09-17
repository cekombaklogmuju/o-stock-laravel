<?php

// Serverless entrypoint for Vercel deployment
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

$storagePath = '/tmp/storage';
$tmpDirs = [
    '/tmp/views',
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

putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
putenv('APP_STORAGE_PATH=' . $storagePath);

// Forward request to Laravel public front controller
require __DIR__ . '/../public/index.php';
