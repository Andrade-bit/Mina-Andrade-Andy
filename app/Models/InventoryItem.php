<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'image',
        'unit',
        'secondary_unit',
        'conversion_factor',
        'current_quantity',
        'reorder_level',
        'status',
    ];

    /**
     * A displayable URL for this item's photo, if one was uploaded.
     */
    public function imageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'conversion_factor' => 'decimal:4',
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function cupSizes(): HasMany
    {
        return $this->hasMany(CupSize::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
