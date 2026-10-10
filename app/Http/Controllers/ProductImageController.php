<?php

namespace App\Http\Controllers;

use App\Models\UploadedImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductImageController extends Controller
{
    public function __invoke(Request $request, string $filename): Response
    {
        $path = 'products/'.$filename;
        $image = UploadedImage::where('path', $path)->first();
        if ($image) {
            $response = response(base64_decode($image->contents), 200, [
                'Content-Type' => $image->mime_type,
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
            $response->setEtag(hash('sha256', $image->contents));
            $response->isNotModified($request);

            return $response;
        }

        if (is_file(public_path('images/uploads/'.$filename))) {
            return response()->file(public_path('images/uploads/'.$filename));
        }

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}
