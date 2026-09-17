<?php

// Serverless entrypoint for Vercel deployment

$tmpDirs = [
    '/tmp/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv('VIEW_COMPILED_PATH=/tmp/views');

// Forward request to Laravel public front controller
require __DIR__ . '/../public/index.php';
