<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_transaction_id',
        'product_id',
        'cup_size_id',
        'quantity',
        'price_at_order',
        'subtotal',
    ];

    protected $casts = [
        'price_at_order' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function salesTransaction(): BelongsTo
    {
        return $this->belongsTo(SalesTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cupSize(): BelongsTo
    {
        return $this->belongsTo(CupSize::class);
    }
}