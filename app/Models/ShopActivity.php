<?php

namespace App\Models;

use App\Models\Concerns\BelongsToShop;
use Illuminate\Database\Eloquent\Model;

class ShopActivity extends Model
{
    use BelongsToShop;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }
}
