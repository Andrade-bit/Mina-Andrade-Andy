<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    public const FEATURES = ['inventory' => 'Inventory & purchasing', 'pos' => 'Point of sale', 'reports' => 'Reports & exports', 'expenses' => 'Expenses', 'promos' => 'Promotions', 'notifications' => 'Subscription email reminders'];

    protected $fillable = ['name', 'monthly_price', 'max_products', 'max_staff', 'is_active', 'features', 'enabled_features'];

    public function allows(string $feature): bool
    {
        return in_array($feature, $this->enabled_features ?? array_keys(self::FEATURES), true);
    }

    protected function casts(): array
    {
        return ['monthly_price' => 'decimal:2', 'is_active' => 'boolean', 'features' => 'array', 'enabled_features' => 'array'];
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
