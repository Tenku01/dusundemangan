<?php

// Suppress deprecations so they do not leak into HTTP response stream
error_reporting(E_ALL & ~E_DEPRECATED);

// Pastikan direktori storage sementara di /tmp tersedia untuk Vercel Serverless
$storagePaths = [
    '/tmp/views',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storagePaths as $path) {
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
}

// Teruskan request ke public/index.php Laravel
require __DIR__ . '/../public/index.php';
