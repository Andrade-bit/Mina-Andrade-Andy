<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupplyPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'purchase_source',
        'invoice_number',
        'payment_method',
        'total_amount',
        'tax_amount',
        'payment_terms',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'supply_purchase_items', 'supply_purchase_id', 'inventory_item_id')
            ->withPivot('quantity', 'unit_cost', 'subtotal')
            ->withTimestamps();
    }

    public function expense(): HasOne
    {
        return $this->hasOne(Expense::class);
    }
}
