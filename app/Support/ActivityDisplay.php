<?php

namespace App\Support;

use App\Models\ShopActivity;
use Illuminate\Support\Str;

class ActivityDisplay
{
    public const TYPES = ['InventoryItem' => 'Inventory item', 'InventoryTransaction' => 'Stock movement', 'InventoryBatch' => 'Stock batch', 'Product' => 'Product', 'ProductCupSize' => 'Product price', 'ProductCategory' => 'Product category', 'CupSize' => 'Cup size', 'SupplyPurchase' => 'Supply purchase', 'SalesTransaction' => 'Sale', 'Expense' => 'Expense', 'ExpenseCategory' => 'Expense category', 'Promo' => 'Promotion', 'Supplier' => 'Supplier', 'Credential' => 'POS staff', 'User' => 'Admin account'];

    public const FIELDS = ['inventory_item_id' => 'Inventory item', 'product_id' => 'Product', 'supplier_id' => 'Supplier', 'ingredient_id' => 'Ingredient', 'cup_size_id' => 'Cup size', 'current_quantity' => 'Stock on hand', 'quantity' => 'Quantity', 'quantity_required' => 'Recipe quantity', 'transaction_type' => 'Movement type', 'reorder_level' => 'Low stock threshold', 'payment_method' => 'Payment method', 'total_amount' => 'Total amount', 'discount_amount' => 'Discount', 'deleted_at' => 'Archived on', 'is_active' => 'Account enabled', 'active' => 'Enabled', 'is_available' => 'Available', 'is_recipe_size' => 'Used for recipes', 'password_changed' => 'Password changed', 'username_changed' => 'Username changed', 'volume_ml' => 'Volume (ml)'];

    public static function rows(ShopActivity $activity, array $inventoryNames): array
    {
        $changes = $activity->changes ?? [];
        $before = is_array($changes['before'] ?? null) ? $changes['before'] : [];
        $after = is_array($changes['after'] ?? null) ? $changes['after'] : array_diff_key($changes, array_flip(['before', 'after']));
        $rows = [];
        foreach (array_unique([...array_keys($before), ...array_keys($after)]) as $field) {
            if (in_array($field, ['password', 'passcode', 'remember_token', 'invitation_token'], true)) {
                continue;
            }
            $rows[] = ['label' => self::FIELDS[$field] ?? Str::headline($field), 'before' => self::value($field, $before[$field] ?? null, $inventoryNames), 'after' => self::value($field, $after[$field] ?? null, $inventoryNames)];
        }

        return $rows;
    }

    private static function value(string $field, mixed $value, array $inventoryNames): string
    {
        if ($value === null || $value === '') {
            return '—';
        }
        if ($field === 'inventory_item_id' && isset($inventoryNames[$value])) {
            return $inventoryNames[$value];
        }
        if (is_bool($value) || in_array($field, ['active', 'is_active', 'is_available', 'is_recipe_size', 'password_changed', 'username_changed'], true)) {
            return $value ? 'Yes' : 'No';
        }
        if (is_array($value)) {
            return collect($value)->map(fn ($item) => is_scalar($item) ? (string) $item : 'Updated')->implode(', ');
        }
        if (str_ends_with($field, '_id')) {
            return (self::FIELDS[$field] ?? 'Record').' #'.$value;
        }

        return is_numeric($value) ? number_format((float) $value, str_contains((string) $value, '.') ? 2 : 0) : (string) $value;
    }
}
