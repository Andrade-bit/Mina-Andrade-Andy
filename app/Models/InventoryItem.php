<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'unit',
        'current_quantity',
        'reorder_level',
        'status',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'reorder_level' => 'decimal:2',
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