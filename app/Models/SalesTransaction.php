<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'payment_method',
        'total_amount',
        'credential_id',
        'status',
        'promo_id',
        'discount_amount',
        'void_reason',
        'voided_by_credential_id',
        'voided_at',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'voided_at' => 'datetime',
    ];

    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class)->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesTransactionItem::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class)->withTrashed();
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(Credential::class, 'voided_by_credential_id')->withTrashed();
    }
}
