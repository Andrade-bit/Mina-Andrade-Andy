<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailMessage extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['payload', 'deduplication_key'];

    protected function casts(): array
    {
        return ['payload' => 'encrypted:array', 'available_at' => 'datetime', 'expires_at' => 'datetime', 'sent_at' => 'datetime'];
    }
}
