<?php

namespace Tests\Feature;

use App\Models\Credential;
use App\Models\CupSize;
use App\Models\Ingredient;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SupplyPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class InventoryExpiryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_cannot_record_a_purchase_or_dispose_of_a_batch(): void
    {
        $this->post(route('admin.supply-purchases.store'), [])->assertRedirect(route('admin.login'));
        $this->post(route('admin.inventory-transactions.stock-out'), [])->assertRedirect(route('admin.login'));
    }

    public function test_purchase_preserves_separate_expiry_batches_and_converts_pack_sizes(): void
    {
        $item = $this->item(['current_quantity' => 100, 'secondary_unit' => 'bottle', 'conversion_factor' => 750]);

        $this->actingAs(User::factory()->create())->post(route('admin.supply-purchases.store'), [
            'purchase_date' => '2026-10-09', 'payment_method' => 'Cash', 'tax_rate' => 10,
            'items' => [
                ['inventory_item_id' => $item->id, 'quantity' => 2, 'unit_cost' => 95, 'quantity_unit' => 'secondary', 'unit_size' => 1000, 'has_expiry' => 1, 'expires_at' => '2026-10-12'],
                ['inventory_item_id' => $item->id, 'quantity' => 1, 'unit_cost' => 100, 'quantity_unit' => 'secondary', 'has_expiry' => 1, 'expires_at' => '2026-10-20'],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.supply-purchases.index'));

        $purchase = SupplyPurchase::firstOrFail();
        $this->assertSame('2850.00', $item->fresh()->current_quantity);
        $this->assertSame('319.00', $purchase->total_amount);
        $this->assertSame('29.00', $purchase->tax_amount);
        $this->assertDatabaseHas('inventory_batches', ['inventory_item_id' => $item->id, 'quantity' => 2000, 'remaining_quantity' => 2000, 'expires_at' => '2026-10-12']);
        $this->assertDatabaseHas('inventory_batches', ['inventory_item_id' => $item->id, 'quantity' => 750, 'expires_at' => '2026-10-20']);
        $this->assertDatabaseHas('supply_purchase_items', ['supply_purchase_id' => $purchase->id, 'quantity' => 2750, 'subtotal' => 290]);
        $this->assertDatabaseHas('expenses', ['supply_purchase_id' => $purchase->id, 'amount' => 319]);
        $this->assertDatabaseCount('inventory_transactions', 2);
        $this->get(route('admin.supply-purchases.show', $purchase))->assertSee('Oct 12, 2026')->assertSee('Oct 20, 2026');
    }

    public function test_new_ingredient_can_be_purchased_with_expiry(): void
    {
        $this->actingAs(User::factory()->create())->post(route('admin.supply-purchases.store'), [
            'purchase_date' => '2026-10-09', 'payment_method' => 'Cash',
            'items' => [[
                'inventory_item_id' => 'new', 'quantity' => 1000, 'unit_cost' => 0.1,
                'has_expiry' => 1, 'expires_at' => '2026-10-23',
                'new_item' => ['name' => 'Vanilla syrup', 'type' => 'ingredient', 'unit' => 'ml'],
            ]],
        ])->assertSessionHasNoErrors();

        $item = InventoryItem::where('name', 'Vanilla syrup')->firstOrFail();
        $this->assertSame('1000.00', $item->current_quantity);
        $this->assertDatabaseHas('inventory_batches', ['inventory_item_id' => $item->id, 'expires_at' => '2026-10-23', 'remaining_quantity' => 1000]);
    }

    public function test_purchase_without_expiry_ignores_a_stale_date(): void
    {
        $item = $this->item();

        $this->actingAs(User::factory()->create())->post(route('admin.supply-purchases.store'), [
            'purchase_date' => '2026-10-09', 'payment_method' => 'Cash',
            'items' => [['inventory_item_id' => $item->id, 'quantity' => 100, 'unit_cost' => 1, 'has_expiry' => 0, 'expires_at' => 'invalid']],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('inventory_batches', ['inventory_item_id' => $item->id, 'expires_at' => null, 'remaining_quantity' => 100]);
    }

    #[TestWith(['', 'Enter the expiry date or turn off Has expiry date.'])]
    #[TestWith(['not-a-date', 'Enter a valid expiry date.'])]
    #[TestWith(['2026-10-08', 'Expiry date cannot be before the purchase date.'])]
    public function test_invalid_expiry_does_not_record_stock_or_expense(string $date, string $message): void
    {
        $item = $this->item();

        $this->actingAs(User::factory()->create())->post(route('admin.supply-purchases.store'), [
            'purchase_date' => '2026-10-09', 'payment_method' => 'Cash',
            'items' => [['inventory_item_id' => $item->id, 'quantity' => 100, 'unit_cost' => 1, 'has_expiry' => 1, 'expires_at' => $date]],
        ])->assertSessionHasErrors(['items.0.expires_at' => $message]);

        $this->assertDatabaseCount('supply_purchases', 0);
        $this->assertDatabaseCount('inventory_batches', 0);
        $this->assertDatabaseCount('expenses', 0);
        $this->assertSame('0.00', $item->fresh()->current_quantity);
    }

    public function test_alerts_use_philippine_dates_and_exclude_empty_undated_distant_and_archived_stock(): void
    {
        $this->travelTo(Carbon::parse('2026-10-08 17:00:00', 'UTC'));
        $item = $this->item(['current_quantity' => 1000]);
        $this->batch($item, '2026-10-08');
        $this->batch($item, '2026-10-09');
        $this->batch($item, '2026-10-16');
        $this->batch($item, '2026-10-17');
        $this->batch($item, null);
        $this->batch($item, '2026-10-10', 0);
        $archived = $this->item(['name' => 'Archived milk']);
        $this->batch($archived, '2026-10-10');
        $archived->delete();

        $this->actingAs(User::factory()->create())->get(route('admin.inventory'))
            ->assertSee('1 expired batch')->assertSee('2 batches expiring soon')
            ->assertSee('Expired 1 day(s) ago')->assertSee('Expires today')->assertSee('Expires in 7 day(s)');

        $this->assertCount(3, InventoryBatch::alerts()->get());
        $this->get(route('admin.dashboard'))->assertSee('Expiry alerts')->assertSee('Review affected inventory');
    }

    public function test_sales_consume_earliest_unexpired_batch_and_preserve_expired_stock(): void
    {
        $this->travelTo(Carbon::parse('2026-10-09 10:00:00', 'Asia/Manila'));
        $item = $this->item(['current_quantity' => 500]);
        $expired = $this->batch($item, '2026-10-08', 100);
        $later = $this->batch($item, '2026-10-20', 150);
        $soon = $this->batch($item, '2026-10-10', 200);
        [$credential, $product, $cup] = $this->saleFixture($item, 250);

        $this->withSession(['pos_credential_id' => $credential->id])->postJson(route('pos.terminal.store'), [
            'payment_method' => 'Cash', 'items' => [['product_id' => $product->id, 'cup_size_id' => $cup->id, 'quantity' => 1, 'price_at_order' => 100]],
        ])->assertCreated();

        $this->assertSame('100.00', $expired->fresh()->remaining_quantity);
        $this->assertSame('0.00', $soon->fresh()->remaining_quantity);
        $this->assertSame('100.00', $later->fresh()->remaining_quantity);
        $this->assertSame('250.00', $item->fresh()->current_quantity);
        $this->assertSame(150.0, $item->fresh()->usableQuantity());
        $this->assertDatabaseHas('inventory_transactions', ['inventory_item_id' => $item->id, 'transaction_type' => 'Sales', 'quantity' => 250]);
        $this->assertCount(0, InventoryBatch::alerts()->whereKey($soon->id)->get());
    }

    public function test_expired_only_stock_rejects_sale_without_partial_writes(): void
    {
        $this->travelTo(Carbon::parse('2026-10-09 10:00:00', 'Asia/Manila'));
        $item = $this->item(['current_quantity' => 100]);
        $batch = $this->batch($item, '2026-10-08', 100);
        [$credential, $product, $cup] = $this->saleFixture($item, 50);

        $this->withSession(['pos_credential_id' => $credential->id])->postJson(route('pos.terminal.store'), [
            'payment_method' => 'Cash', 'items' => [['product_id' => $product->id, 'cup_size_id' => $cup->id, 'quantity' => 1, 'price_at_order' => 100]],
        ])->assertUnprocessable()->assertJsonValidationErrors('items');

        $this->assertSame('100.00', $batch->fresh()->remaining_quantity);
        $this->assertSame('100.00', $item->fresh()->current_quantity);
        $this->assertDatabaseCount('sales_transactions', 0);
        $this->assertDatabaseCount('inventory_transactions', 0);
        $this->assertFalse($product->sizesWithStock(collect([$cup]), $cup)->first()->available);
    }

    public function test_expired_batch_can_be_disposed_without_changing_other_batches(): void
    {
        $this->travelTo(Carbon::parse('2026-10-09 10:00:00', 'Asia/Manila'));
        $item = $this->item(['current_quantity' => 300]);
        $expired = $this->batch($item, '2026-10-08', 100);
        $fresh = $this->batch($item, '2026-10-20', 200);

        $this->actingAs(User::factory()->create())->post(route('admin.inventory-transactions.stock-out'), [
            'inventory_item_id' => $item->id, 'batch_id' => $expired->id, 'quantity' => 100,
            'transaction_type' => 'Waste', 'inventory_transaction_date' => '2026-10-09', 'reason' => 'Expired batch',
        ])->assertSessionHasNoErrors();

        $this->assertSame('0.00', $expired->fresh()->remaining_quantity);
        $this->assertSame('200.00', $fresh->fresh()->remaining_quantity);
        $this->assertSame('200.00', $item->fresh()->current_quantity);
        $this->assertCount(0, InventoryBatch::alerts()->get());
        $this->assertDatabaseHas('inventory_transactions', ['transaction_type' => 'Waste', 'quantity' => 100]);
    }

    public function test_wrong_item_batch_or_excess_batch_quantity_cannot_be_removed(): void
    {
        $item = $this->item(['current_quantity' => 500]);
        $other = $this->item(['name' => 'Other milk', 'current_quantity' => 100]);
        $batch = $this->batch($other, '2026-10-12', 100);

        $this->actingAs(User::factory()->create())->post(route('admin.inventory-transactions.stock-out'), [
            'inventory_item_id' => $item->id, 'batch_id' => $batch->id, 'quantity' => 50,
            'transaction_type' => 'Waste', 'inventory_transaction_date' => '2026-10-09',
        ])->assertSessionHasErrors('quantity');
        $this->post(route('admin.inventory-transactions.stock-out'), [
            'inventory_item_id' => $other->id, 'batch_id' => $batch->id, 'quantity' => 101,
            'transaction_type' => 'Waste', 'inventory_transaction_date' => '2026-10-09',
        ])->assertSessionHasErrors('quantity');

        $this->assertSame('100.00', $batch->fresh()->remaining_quantity);
        $this->assertSame('500.00', $item->fresh()->current_quantity);
        $this->assertDatabaseCount('inventory_transactions', 0);
    }

    public function test_legacy_stock_remains_usable_and_generic_stock_out_reduces_batches(): void
    {
        $item = $this->item(['current_quantity' => 500]);
        $batch = $this->batch($item, null, 100);

        $this->actingAs(User::factory()->create())->post(route('admin.inventory-transactions.stock-out'), [
            'inventory_item_id' => $item->id, 'quantity' => 150, 'transaction_type' => 'Adjustment', 'inventory_transaction_date' => '2026-10-09',
        ])->assertSessionHasNoErrors();

        $this->assertSame('0.00', $batch->fresh()->remaining_quantity);
        $this->assertSame(350.0, $item->fresh()->usableQuantity());
    }

    public function test_unit_conversion_updates_batch_quantities_and_preserves_expiry(): void
    {
        $item = $this->item(['unit' => 'g', 'current_quantity' => 200]);
        $batch = $this->batch($item, '2026-10-12', 100);

        $this->actingAs(User::factory()->create())->put(route('admin.inventory-items.update', $item), [
            'name' => $item->name, 'type' => 'ingredient', 'unit' => 'ml', 'unit_conversion' => 2, 'reorder_level' => 0,
        ])->assertSessionHasNoErrors();

        $this->assertSame('400.00', $item->fresh()->current_quantity);
        $this->assertSame('200.00', $batch->fresh()->remaining_quantity);
        $this->assertSame('200.00', $batch->fresh()->quantity);
        $this->assertSame('2026-10-12', $batch->fresh()->expires_at->toDateString());
    }

    private function item(array $attributes = []): InventoryItem
    {
        return InventoryItem::create($attributes + ['name' => 'Fresh Milk', 'type' => 'ingredient', 'unit' => 'ml', 'current_quantity' => 0, 'reorder_level' => 0, 'status' => 'active']);
    }

    private function batch(InventoryItem $item, ?string $expiry, float $quantity = 100): InventoryBatch
    {
        return $item->batches()->create(['expires_at' => $expiry, 'quantity' => $quantity, 'remaining_quantity' => $quantity]);
    }

    private function saleFixture(InventoryItem $item, float $quantity): array
    {
        $credential = Credential::create(['first_name' => 'Test', 'last_name' => 'Cashier', 'role' => 'assistant', 'passcode' => '1234']);
        $category = ProductCategory::create(['category_name' => 'Drinks']);
        $product = Product::create(['product_name' => 'Milk drink', 'product_category_id' => $category->id]);
        $ingredient = Ingredient::create(['inventory_item_id' => $item->id]);
        $product->ingredients()->attach($ingredient->id, ['quantity_required' => $quantity]);
        $cup = CupSize::create(['size_name' => 'Small', 'price' => 100, 'volume_ml' => 250, 'is_recipe_size' => true]);

        return [$credential, $product, $cup];
    }
}
