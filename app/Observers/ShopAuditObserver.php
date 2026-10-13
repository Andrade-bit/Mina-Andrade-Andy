<?php

namespace App\Observers;

use App\Models\ShopActivity;
use App\Support\ShopContext;
use Illuminate\Database\Eloquent\Model;

class ShopAuditObserver
{
    private const FIELDS = ['name', 'product_name', 'size_name', 'volume_ml', 'is_available', 'quantity_required', 'ingredient_id', 'cup_size_id', 'category_name', 'first_name', 'last_name', 'type', 'unit', 'current_quantity', 'reorder_level', 'status', 'active', 'price', 'quantity', 'amount', 'total_amount', 'discount_amount', 'transaction_type', 'inventory_item_id', 'product_id', 'supplier_id', 'payment_method', 'expiry_date', 'deleted_at', 'role'];

    public function created(Model $model): void
    {
        $this->record($model, 'created', []);
    }

    public function updated(Model $model): void
    {
        $before = array_intersect_key($model->getOriginal(), array_flip(array_keys($model->getChanges())));
        $this->record($model, $model->wasChanged('deleted_at') ? ($model->deleted_at ? 'archived' : 'restored') : 'updated', $before);
    }

    public function deleted(Model $model): void
    {
        $this->record($model, $model->getAttribute('deleted_at') ? 'archived' : 'deleted', $model->getOriginal());
    }

    private function record(Model $model, string $action, array $before): void
    {
        $shopId = app(ShopContext::class)->id();
        if (! $shopId || (int) $model->shop_id !== $shopId) {
            return;
        }
        $allowed = array_flip(self::FIELDS);
        $after = array_intersect_key($action === 'updated' ? $model->getChanges() : $model->getAttributes(), $allowed);
        if ($action === 'updated' && ! $after) {
            return;
        }
        $user = auth()->user();
        $credentialId = request()->is('pos/*') ? request()->session()->get('pos_credential_id') : null;
        ShopActivity::create(['user_id' => $user && ! $user->isSuperAdmin() && (int) $user->shop_id === $shopId ? $user->id : null, 'credential_id' => $credentialId, 'action' => $action, 'subject_type' => class_basename($model), 'subject_id' => $model->getKey(), 'changes' => ['before' => array_intersect_key($before, $allowed), 'after' => $after]]);
    }
}
