<?php

use Illuminate\Http\Request;

/*
 * Vercel entry point: every request that is not a static file in public/ lands here.
 *
 * Vercel's disk is read-only except for the temp folder, so Laravel's writable folders (storage, compiled
 * views, sessions, logs) are created there. Nothing in them needs to survive: sessions and cache live in the
 * database, and logs go to Vercel's log stream.
 */

$temp = sys_get_temp_dir();
$storage = "{$temp}/storage";

foreach (['app/public', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $folder) {
    if (! is_dir("{$storage}/{$folder}")) {
        mkdir("{$storage}/{$folder}", 0755, true);
    }
}

if (getenv('VERCEL')) {
    // The settings this deployment needs. Anything set in the Vercel dashboard wins over these, so only the
    // secrets (APP_KEY and the database host, name, user and password) have to be added there.
    $defaults = [
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
        'LOG_CHANNEL' => 'stderr',
        'DB_CONNECTION' => 'mysql',
        'DB_PORT' => '4000',
        'SESSION_DRIVER' => 'database',
        'SESSION_SECURE_COOKIE' => 'true',
        'CACHE_STORE' => 'database',
        'QUEUE_CONNECTION' => 'sync',
        'FILESYSTEM_DISK' => 'local',
        'APP_CONFIG_CACHE' => "{$temp}/config.php",
        'APP_EVENTS_CACHE' => "{$temp}/events.php",
        'APP_PACKAGES_CACHE' => "{$temp}/packages.php",
        'APP_ROUTES_CACHE' => "{$temp}/routes.php",
        'APP_SERVICES_CACHE' => "{$temp}/services.php",
        // TiDB Cloud only accepts secure connections: use the certificate bundle shipped with the app.
        'MYSQL_ATTR_SSL_CA' => dirname(__DIR__).'/certs/ca-bundle.crt',
    ];

    foreach ($defaults as $key => $value) {
        if (getenv($key) === false || getenv($key) === '') {
            putenv("{$key}={$value}");
            $_ENV[$key] = $_SERVER[$key] = $value;
        }
    }

    // Say what is wrong, in plain words, instead of a bare 500 when a required setting is missing or malformed.
    // Only the names are shown, never the values.
    $problems = [];

    foreach (['APP_KEY', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $name) {
        if ((string) getenv($name) === '') {
            $problems[] = "{$name} is not set.";
        }
    }

    $appKey = (string) getenv('APP_KEY');

    if ($appKey !== '') {
        $rawKey = str_starts_with($appKey, 'base64:') ? base64_decode(substr($appKey, 7), true) : $appKey;

        if ($rawKey === false || strlen($rawKey) !== 32) {
            $problems[] = 'APP_KEY is not valid. Use the whole line printed by "php artisan key:generate --show", starting with base64:, and nothing else.';
        }
    }

    if ($problems !== [] && ($_SERVER['REQUEST_URI'] ?? '') !== '/up') {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Catbrews cannot start yet. Fix these in Vercel > Settings > Environment Variables, then redeploy:\n";

        foreach ($problems as $problem) {
            echo " - {$problem}\n";
        }

        exit;
    }
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->useStoragePath($storage);

$app->handleRequest(Request::capture());
