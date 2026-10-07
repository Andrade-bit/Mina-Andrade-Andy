<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductIngredientSize extends Model
{
    protected $fillable = [
        'product_id',
        'ingredient_id',
        'cup_size_id',
        'quantity_required',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function cupSize(): BelongsTo
    {
        return $this->belongsTo(CupSize::class)->withTrashed();
    }
}
