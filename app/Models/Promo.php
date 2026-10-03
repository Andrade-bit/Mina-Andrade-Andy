<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'reason',
        'active',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'active' => 'boolean',
        'expires_at' => 'date',
    ];

    /**
     * A promo is usable if it's active and hasn't passed its expiry date.
     */
    public function isValid(): bool
    {
        if (! $this->active) {
            return false;
        }

        return ! $this->expires_at || ! $this->expires_at->isPast();
    }

    /**
     * The peso amount this promo takes off a given subtotal.
     */
    public function discountFor(float $subtotal): float
    {
        $discount = $this->type === 'percent'
            ? $subtotal * ((float) $this->value / 100)
            : (float) $this->value;

        return min($discount, $subtotal);
    }
}
