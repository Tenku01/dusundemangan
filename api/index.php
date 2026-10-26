<?php

// Suppress deprecations and warnings from leaking into HTTP output
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

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
    if (! is_dir($path)) {
        @mkdir($path, 0755, true);
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

if (! defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Register Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<h1>Laravel Vercel Error</h1>';
    echo '<pre>'.htmlspecialchars($e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n\n".$e->getTraceAsString()).'</pre>';
}
