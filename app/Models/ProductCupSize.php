<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCupSize extends Model
{
    protected $fillable = [
        'product_id',
        'cup_size_id',
        'price',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cupSize(): BelongsTo
    {
        return $this->belongsTo(CupSize::class);
    }
}
