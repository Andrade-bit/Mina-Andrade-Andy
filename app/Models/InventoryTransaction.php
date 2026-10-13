<?php

namespace App\Models;

use App\Models\Concerns\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use BelongsToShop;
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'transaction_type',
        'quantity',
        'inventory_transaction_date',
        'reason',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'inventory_transaction_date' => 'date',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class)->withTrashed();
    }
}
