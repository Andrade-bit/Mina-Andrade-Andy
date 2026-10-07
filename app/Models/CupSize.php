<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CupSize extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'size_name',
        'inventory_item_id',
        'price',
        'volume_ml',
        'is_recipe_size',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'volume_ml' => 'decimal:2',
        'is_recipe_size' => 'boolean',
    ];

    /**
     * The size recipes are written for (e.g. Medium); other sizes scale from it by volume.
     */
    public static function recipeSize(): ?self
    {
        return static::where('is_recipe_size', true)->first();
    }

    /**
     * How much of a recipe a drink in this size uses, relative to the recipe size. Stays 1 when either
     * size has no volume set or no recipe size is chosen, so one-size drinks deduct the recipe as written.
     */
    public function recipeScale(?self $recipeSize): float
    {
        if (! $recipeSize || (float) $this->volume_ml <= 0 || (float) $recipeSize->volume_ml <= 0) {
            return 1.0;
        }

        return (float) $this->volume_ml / (float) $recipeSize->volume_ml;
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function salesTransactionItems(): HasMany
    {
        return $this->hasMany(SalesTransactionItem::class);
    }
}
