<?php

namespace App\Models;

use App\Models\Concerns\BelongsToShop;
use App\Support\ShopContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use BelongsToShop;
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
            ->withPivotValue('shop_id', app(ShopContext::class)->id())
            ->withPivot('quantity_required')
            ->withTimestamps();
    }
}
