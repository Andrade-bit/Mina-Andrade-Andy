<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ShopDates
{
    public static function today(): string
    {
        return CarbonImmutable::today('Asia/Manila')->toDateString();
    }

    public static function bounds(string $from, string $to): array
    {
        return [CarbonImmutable::parse($from, 'Asia/Manila')->startOfDay()->setTimezone(config('app.timezone')), CarbonImmutable::parse($to, 'Asia/Manila')->endOfDay()->setTimezone(config('app.timezone'))];
    }

    public static function validate(Request $request): void
    {
        $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
        ]);
    }
}
