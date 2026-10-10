<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadedImage extends Model
{
    protected $fillable = ['path', 'mime_type', 'contents'];

    protected $hidden = ['contents'];

    public static function storeUpload(UploadedFile $file): string
    {
        $path = 'products/'.Str::uuid().'.'.$file->guessExtension();
        static::create([
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'contents' => base64_encode($file->getContent()),
        ]);

        return $path;
    }
}
