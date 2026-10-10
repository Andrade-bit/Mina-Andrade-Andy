<?php

namespace Tests\Feature;

use App\Models\CupSize;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SalesTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ViewFiltersConsistencyTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function sale(string $date, int $amount = 100): SalesTransaction
    {
        return SalesTransaction::create(['transaction_date' => $date, 'payment_method' => 'Cash', 'total_amount' => $amount, 'discount_amount' => 0, 'status' => 'completed']);
    }

    public function test_sales_search_finds_product_names_and_formatted_receipt_numbers(): void
    {
        $category = ProductCategory::create(['category_name' => 'Drinks']);
        $product = Product::create(['product_name' => 'Hazelnut latte', 'product_category_id' => $category->id]);
        $cup = CupSize::create(['size_name' => 'Small', 'price' => 100]);
        $sale = $this->sale('2026-10-08 17:00:00');
        $sale->items()->create(['product_id' => $product->id, 'cup_size_id' => $cup->id, 'quantity' => 1, 'price_at_order' => 100, 'subtotal' => 100]);
        $this->sale('2026-10-09 10:00:00');
        $product->delete();
        $this->actingAs(User::factory()->create());
        foreach (['Hazelnut', 'CB-'.str_pad($sale->id, 5, '0', STR_PAD_LEFT)] as $search) {
            $this->get(route('pos.transactions', ['search' => $search]))->assertOk()->assertViewHas('transactions', fn ($rows) => $rows->pluck('id')->all() === [$sale->id]);
        }
    }

    public function test_sales_history_reports_and_daily_totals_use_the_same_philippine_day(): void
    {
        $this->travelTo(Carbon::parse('2026-10-08 17:00:00', 'UTC'));
        $this->sale('2026-10-08 15:59:59', 900);
        $start = $this->sale('2026-10-08 16:00:00', 100);
        $end = $this->sale('2026-10-09 15:59:59', 200);
        $this->sale('2026-10-09 16:00:00', 800);
        $expenseCategory = ExpenseCategory::create(['category_name' => 'Utilities']);
        Expense::create(['expense_category_id' => $expenseCategory->id, 'description' => 'Electricity', 'amount' => 50, 'expense_date' => '2026-10-09', 'payment_method' => 'Cash']);
        $this->actingAs(User::factory()->create());
        $this->get(route('pos.transactions', ['from' => '2026-10-09', 'to' => '2026-10-09', 'sort' => 'highest']))->assertOk()
            ->assertViewHas('transactions', fn ($rows) => $rows->pluck('id')->all() === [$end->id, $start->id])
            ->assertViewHas('todaysSales', fn ($total) => (float) $total === 300.0)->assertSee('Oct 9, 2026, 12:00 AM');
        $this->get(route('admin.reports.index', ['range' => 'today']))->assertOk()
            ->assertViewHas('summary', fn ($summary) => $summary['net_sales'] === 300.0)
            ->assertViewHas('daily', fn ($daily) => count($daily) === 1 && $daily[0]['date'] === '2026-10-09' && $daily[0]['gross'] === 300.0 && $daily[0]['expenses'] === 50.0 && $daily[0]['net'] === 250.0);
        $csv = $this->get(route('admin.reports.export', ['range' => 'today']))->assertOk()->streamedContent();
        $this->assertStringContainsString('2026-10-09', $csv);
    }

    public function test_dates_and_sort_values_are_validated(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['pos.transactions', 'admin.reports.index', 'admin.reports.export'] as $route) {
            $this->get(route($route, ['from' => '2026-10-10', 'to' => '2026-10-09']))->assertSessionHasErrors('to');
            $this->get(route($route, ['from' => '2026-02-30']))->assertSessionHasErrors('from');
        }
        $this->get(route('pos.transactions', ['sort' => 'bad']))->assertSessionHasErrors('sort');
        $this->get(route('admin.products.index', ['sort' => 'bad']))->assertSessionHasErrors('sort');
    }

    public function test_stock_activity_has_independent_pages_preserves_filters_and_sorting(): void
    {
        $item = InventoryItem::create(['name' => 'Milk', 'type' => 'ingredient', 'unit' => 'ml']);
        for ($i = 1; $i <= 23; $i++) {
            InventoryTransaction::create(['inventory_item_id' => $item->id, 'inventory_transaction_date' => '2026-10-09', 'transaction_type' => 'Restock', 'quantity' => $i]);
        }
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.inventory', ['search' => 'Milk', 'activity_sort' => 'oldest', 'activity_page' => 3]))->assertOk()
            ->assertViewHas('items', fn ($rows) => $rows->currentPage() === 1 && $rows->count() === 1)
            ->assertViewHas('recentTransactions', fn ($rows) => $rows->count() === 3 && $rows->total() === 23 && (float) $rows->first()->quantity === 21.0 && str_contains($rows->previousPageUrl(), 'search=Milk'));
        $this->get(route('admin.reports.index', ['type' => 'inventory', 'range' => 'custom', 'from' => '2026-10-09', 'to' => '2026-10-09', 'movement_sort' => 'oldest', 'movement_page' => 3]))->assertOk()
            ->assertViewHas('movementLog', fn ($rows) => $rows->total() === 23 && $rows->count() === 3);
        $this->get(route('admin.inventory-transactions.index'))->assertRedirect(route('admin.inventory').'#stock-activity');
    }

    public function test_products_sorting_combines_with_search_and_filters_wait_for_submit(): void
    {
        $category = ProductCategory::create(['category_name' => 'Drinks']);
        foreach (['Milk A', 'Milk Z', 'Coffee'] as $name) {
            Product::create(['product_name' => $name, 'product_category_id' => $category->id]);
        }
        $this->actingAs(User::factory()->create())->get(route('admin.products.index', ['search' => 'Milk', 'sort' => 'name_desc']))->assertOk()
            ->assertViewHas('products', fn ($rows) => $rows->pluck('product_name')->all() === ['Milk Z', 'Milk A'])
            ->assertSee('Search</button>', false)->assertDontSee('this.form.submit()', false);
    }
}
