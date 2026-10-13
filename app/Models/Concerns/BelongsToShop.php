<?php

namespace App\Models\Concerns;

use App\Support\ShopContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

trait BelongsToShop
{
    public static function bootBelongsToShop(): void
    {
        static::addGlobalScope('shop', function (Builder $query): void {
            $query->where($query->getModel()->qualifyColumn('shop_id'), app(ShopContext::class)->id() ?? 0);
        });

        static::saving(function (Model $model): void {
            $shopId = app(ShopContext::class)->id();
            if (! $shopId || ($model->exists && (int) $model->getOriginal('shop_id') !== $shopId)) {
                throw new LogicException('A shop context is required to save this record.');
            }
            if ($model->shop_id && (int) $model->shop_id !== $shopId) {
                throw new LogicException('Records cannot be moved between shops.');
            }
            $model->shop_id = $shopId;
            $references = ['product_category_id' => 'product_categories', 'inventory_item_id' => 'inventory_items', 'product_id' => 'products', 'ingredient_id' => 'ingredients', 'cup_size_id' => 'cup_sizes', 'supplier_id' => 'suppliers', 'supply_purchase_id' => 'supply_purchases', 'sales_transaction_id' => 'sales_transactions', 'credential_id' => 'credentials', 'expense_category_id' => 'expense_categories'];
            foreach ($references as $column => $table) {
                if ($model->getAttribute($column) && ! DB::table($table)->where('id', $model->getAttribute($column))->where('shop_id', $shopId)->exists()) {
                    throw ValidationException::withMessages([$column => 'Choose a record belonging to this shop.']);
                }
            }
        });
        static::deleting(function (Model $model): void {
            if ((int) $model->shop_id !== app(ShopContext::class)->id()) {
                throw new LogicException('Records cannot be deleted outside their shop.');
            }
        });
    }
}
