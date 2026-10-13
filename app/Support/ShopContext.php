<?php

namespace App\Support;

use App\Models\Shop;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

class ShopContext
{
    private ?int $shopId = null;

    private bool $resolved = false;

    private ?Shop $currentShop = null;

    public function set(?int $shopId): void
    {
        $this->shopId = $shopId;
        $this->resolved = true;
        $this->currentShop = null;
    }

    public function reset(): void
    {
        $this->shopId = null;
        $this->resolved = false;
        $this->currentShop = null;
    }

    public function id(): ?int
    {
        return $this->resolved ? $this->shopId : (app()->runningUnitTests() ? 1 : null);
    }

    public function shop(): ?Shop
    {
        return $this->id() ? ($this->currentShop ??= Shop::with('plan')->find($this->id())) : null;
    }

    public static function exists(string $table, string $column = 'id'): Exists
    {
        return Rule::exists($table, $column)->where('shop_id', app(self::class)->id() ?? 0);
    }

    public static function unique(string $table, string $column): Unique
    {
        return Rule::unique($table, $column)->where('shop_id', app(self::class)->id() ?? 0);
    }
}
