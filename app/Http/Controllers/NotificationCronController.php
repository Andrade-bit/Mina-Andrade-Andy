<?php

namespace App\Http\Controllers;

use App\Support\SubscriptionReminders;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationCronController extends Controller
{
    public function __invoke(Request $request, SubscriptionReminders $reminders): JsonResponse
    {
        $secret = config('saas.cron_secret');
        abort_unless(is_string($secret) && strlen($secret) >= 32 && hash_equals($secret, $request->bearerToken() ?? ''), 401);
        $reminders->collect();

        return response()->json(['sent' => $reminders->process()]);
    }
}
