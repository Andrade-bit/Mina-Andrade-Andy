<?php

use Illuminate\Http\Request;

/*
 * Vercel entry point: every request that is not a static file in public/ lands here.
 *
 * Vercel's disk is read-only except for the temp folder, so Laravel's writable folders (storage, compiled
 * views, sessions, logs) are created there. Nothing in them needs to survive: sessions and cache live in the
 * database, and logs go to Vercel's log stream.
 */

$storage = sys_get_temp_dir().'/storage';

foreach (['app/public', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $folder) {
    if (! is_dir("{$storage}/{$folder}")) {
        mkdir("{$storage}/{$folder}", 0755, true);
    }
}

// TiDB Cloud only accepts secure connections. On Vercel, unless a certificate is configured, use the bundle shipped with the app.
if (getenv('VERCEL') && ! getenv('MYSQL_ATTR_SSL_CA')) {
    $bundle = dirname(__DIR__).'/certs/ca-bundle.crt';

    putenv("MYSQL_ATTR_SSL_CA={$bundle}");
    $_ENV['MYSQL_ATTR_SSL_CA'] = $_SERVER['MYSQL_ATTR_SSL_CA'] = $bundle;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->useStoragePath($storage);

$app->handleRequest(Request::capture());
