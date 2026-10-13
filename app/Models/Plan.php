<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = ['name', 'monthly_price', 'max_products', 'max_staff', 'is_active', 'features'];

    protected function casts(): array
    {
        return ['monthly_price' => 'decimal:2', 'is_active' => 'boolean', 'features' => 'array'];
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
