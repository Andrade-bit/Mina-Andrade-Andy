<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\UploadedImage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class InventoryPhotoController extends Controller
{
    public function __invoke(int $id): Response
    {
        $item = InventoryItem::withTrashed()->findOrFail($id);
        abort_unless($item->image && str_starts_with($item->image, 'inventory/') && ! str_contains($item->image, '..'), 404);
        $image = UploadedImage::where('path', $item->image)->first();
        if ($image) {
            return response(base64_decode($image->contents, true), 200, ['Content-Type' => $image->mime_type, 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
        }
        $disk = Storage::disk('public');
        abort_unless($disk->exists($item->image), 404);

        return response($disk->get($item->image), 200, ['Content-Type' => $disk->mimeType($item->image), 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
