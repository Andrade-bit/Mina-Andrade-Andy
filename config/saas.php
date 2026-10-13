<?php

return [
    'email_enabled' => env('SAAS_EMAIL_ENABLED', false),
    'daily_email_limit' => 200,
    'cron_secret' => env('CRON_SECRET'),
];
