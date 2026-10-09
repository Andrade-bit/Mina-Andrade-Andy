<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class InventoryBatch extends Model
{
    protected $fillable = ['inventory_item_id', 'supply_purchase_id', 'expires_at', 'quantity', 'remaining_quantity'];

    protected $casts = [
        'expires_at' => 'date',
        'quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
    ];

    protected function expiresAt(): Attribute
    {
        return Attribute::make(set: fn (mixed $value): ?string => $value === null ? null : Carbon::parse($value)->toDateString());
    }

    public static function expiryToday(): Carbon
    {
        return Carbon::today('Asia/Manila');
    }

    public static function alerts(): Builder
    {
        return static::query()->with('inventoryItem', 'supplyPurchase')
            ->whereHas('inventoryItem')
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', static::expiryToday()->addDays(7)->toDateString())
            ->orderBy('expires_at')->orderBy('id');
    }

    public function daysUntilExpiry(): ?int
    {
        return $this->expires_at
            ? (int) static::expiryToday()->diffInDays(Carbon::parse($this->expires_at->toDateString(), 'Asia/Manila'), false)
            : null;
    }

    public function expiryLabel(): string
    {
        $days = $this->daysUntilExpiry();

        return match (true) {
            $days === null => 'No expiry recorded',
            $days < 0 => 'Expired '.abs($days).' day(s) ago',
            $days === 0 => 'Expires today',
            default => 'Expires in '.$days.' day(s)',
        };
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function supplyPurchase(): BelongsTo
    {
        return $this->belongsTo(SupplyPurchase::class);
    }
}
