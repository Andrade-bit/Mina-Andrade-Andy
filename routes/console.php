<?php

use App\Models\InventoryItem;
use App\Models\Shop;
use App\Models\UploadedImage;
use App\Support\ShopContext;
use App\Support\SubscriptionReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('saas:send-reminders {--limit=20}', function (): void {
    $reminders = app(SubscriptionReminders::class);
    $reminders->collect();
    $this->info('Emails sent: '.$reminders->process(max(1, min(200, (int) $this->option('limit')))));
})->purpose('Collect subscription reminders and retry pending service emails');

Artisan::command('saas:import-inventory-images {--dry-run}', function (): void {
    $context = app(ShopContext::class);
    $imported = 0;
    $missing = 0;
    try {
        foreach (Shop::all() as $shop) {
            $context->set($shop->id);
            foreach (InventoryItem::withTrashed()->whereNotNull('image')->cursor() as $item) {
                if (UploadedImage::where('path', $item->image)->exists()) {
                    continue;
                }
                $disk = Storage::disk('public');
                if (! str_starts_with($item->image, 'inventory/') || str_contains($item->image, '..') || ! $disk->exists($item->image)) {
                    $missing++;

                    continue;
                }
                if (! $this->option('dry-run')) {
                    UploadedImage::create(['path' => $item->image, 'mime_type' => $disk->mimeType($item->image), 'contents' => base64_encode($disk->get($item->image))]);
                }
                $imported++;
            }
        }
    } finally {
        $context->reset();
    }
    $this->info('Available photos: '.$imported.'; missing legacy files: '.$missing);
})->purpose('Copy available legacy inventory photos into persistent database storage');
