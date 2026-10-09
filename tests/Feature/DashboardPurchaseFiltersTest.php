<?php

namespace Tests\Feature;

use App\Models\CupSize;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SalesTransaction;
use App\Models\Supplier;
use App\Models\SupplyPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DashboardPurchaseFiltersTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_filters_require_admin_authentication(): void
    {
        $this->get(route('admin.dashboard', ['from' => '2026-10-09']))->assertRedirect(route('admin.login'));
        $this->get(route('admin.supply-purchases.index', ['search' => 'milk']))->assertRedirect(route('admin.login'));
    }

    public function test_dashboard_filters_sales_rankings_and_recent_sales_with_inclusive_philippine_dates(): void
    {
        $this->travelTo(Carbon::parse('2026-10-09 10:00:00', 'Asia/Manila'));
        $category = ProductCategory::create(['category_name' => 'Drinks']);
        $product = Product::create(['product_name' => 'Milk drink', 'product_category_id' => $category->id]);
        $cup = CupSize::create(['size_name' => 'Small', 'price' => 100]);
        $before = $this->sale('2026-10-08 15:59:59', 900);
        $start = $this->sale('2026-10-08 16:00:00', 100);
        $end = $this->sale('2026-10-09 15:59:59', 200);
        $after = $this->sale('2026-10-09 16:00:00', 800);
        $voided = $this->sale('2026-10-09 10:00:00', 500, 'voided');
        foreach ([$before, $start, $end, $after, $voided] as $sale) {
            $sale->items()->create(['product_id' => $product->id, 'cup_size_id' => $cup->id, 'quantity' => 1, 'price_at_order' => $sale->total_amount, 'subtotal' => $sale->total_amount]);
        }
        $item = InventoryItem::create(['name' => 'Current milk', 'type' => 'ingredient', 'unit' => 'ml', 'current_quantity' => 100, 'reorder_level' => 0]);
        $item->batches()->create(['quantity' => 100, 'remaining_quantity' => 100, 'expires_at' => '2026-10-11']);

        $response = $this->actingAs(User::factory()->create())->get(route('admin.dashboard', ['from' => '2026-10-09', 'to' => '2026-10-09']));

        $response->assertOk()->assertViewHas('periodSales', fn ($total) => (float) $total === 300.0)
            ->assertViewHas('periodTransactionCount', 2)
            ->assertViewHas('recentTransactions', fn ($rows) => $rows->pluck('id')->all() === [$end->id, $voided->id, $start->id])
            ->assertViewHas('topSellers', fn ($rows) => $rows->first()->sold === 2)
            ->assertViewHas('expiryAlerts', fn ($rows) => $rows->count() === 1)
            ->assertSee('Oct 9, 2026')->assertSee('name="from"', false)->assertSee('name="to"', false);
    }

    public function test_dashboard_defaults_to_today_in_philippine_time_and_accepts_single_date(): void
    {
        $this->travelTo(Carbon::parse('2026-10-08 17:00:00', 'UTC'));
        $this->sale('2026-10-08 17:00:00', 100);

        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))
            ->assertViewHas('from', '2026-10-09')->assertViewHas('to', '2026-10-09')
            ->assertViewHas('periodTransactionCount', 1);
        $this->get(route('admin.dashboard', ['to' => '2026-10-08']))
            ->assertViewHas('from', '2026-10-08')->assertViewHas('to', '2026-10-08')->assertViewHas('periodTransactionCount', 0);
    }

    #[TestWith(['2026-10-10', '2026-10-09', 'to'])]
    #[TestWith(['invalid', '2026-10-09', 'from'])]
    #[TestWith(['2026-10-09', '2026-02-30', 'to'])]
    public function test_dashboard_rejects_invalid_or_reversed_dates(string $from, string $to, string $error): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard', compact('from', 'to')))->assertSessionHasErrors($error);
    }

    #[TestWith(['INV-MILK'])]
    #[TestWith(['Local Dairy'])]
    #[TestWith(['NCCC'])]
    #[TestWith(['Vanilla'])]
    public function test_purchase_search_matches_reference_supplier_store_and_item(string $search): void
    {
        $supplier = Supplier::create(['supplier_name' => 'Local Dairy']);
        $match = $this->purchase(['invoice_number' => 'INV-MILK', 'purchase_source' => 'NCCC', 'supplier_id' => $supplier->id]);
        $item = InventoryItem::create(['name' => 'Vanilla syrup', 'type' => 'ingredient', 'unit' => 'ml']);
        $match->items()->attach($item->id, ['quantity' => 100, 'unit_cost' => 1, 'subtotal' => 100]);
        $this->purchase(['invoice_number' => 'OTHER']);

        $this->actingAs(User::factory()->create())->get(route('admin.supply-purchases.index', ['search' => $search]))
            ->assertOk()->assertViewHas('purchases', fn ($rows) => $rows->pluck('id')->all() === [$match->id]);
    }

    public function test_search_by_numeric_reference_combines_with_payment_filter(): void
    {
        $match = $this->purchase(['payment_method' => 'Cash']);
        $this->purchase(['invoice_number' => '#'.$match->id, 'payment_method' => 'Card']);

        $this->actingAs(User::factory()->create())->get(route('admin.supply-purchases.index', ['search' => '#'.$match->id, 'payment_method' => 'Cash']))
            ->assertViewHas('purchases', fn ($rows) => $rows->pluck('id')->all() === [$match->id]);
    }

    #[TestWith(['newest', ['C', 'B', 'A']])]
    #[TestWith(['oldest', ['A', 'B', 'C']])]
    #[TestWith(['highest', ['C', 'A', 'B']])]
    #[TestWith(['lowest', ['B', 'A', 'C']])]
    public function test_purchase_sort_orders_results_with_a_stable_tiebreaker(string $sort, array $expected): void
    {
        $this->purchase(['invoice_number' => 'A', 'purchase_date' => '2026-10-01', 'total_amount' => 200]);
        $this->purchase(['invoice_number' => 'B', 'purchase_date' => '2026-10-09', 'total_amount' => 100]);
        $this->purchase(['invoice_number' => 'C', 'purchase_date' => '2026-10-09', 'total_amount' => 300]);

        $this->actingAs(User::factory()->create())->get(route('admin.supply-purchases.index', compact('sort')))
            ->assertViewHas('purchases', fn ($rows) => $rows->pluck('invoice_number')->all() === $expected);
    }

    public function test_search_and_sort_survive_pagination_and_empty_search_shows_a_helpful_message(): void
    {
        for ($index = 1; $index <= 21; $index++) {
            $this->purchase(['invoice_number' => 'MILK-'.$index, 'total_amount' => $index]);
        }

        $this->actingAs(User::factory()->create())->get(route('admin.supply-purchases.index', ['search' => 'MILK', 'sort' => 'lowest', 'page' => 2]))
            ->assertViewHas('purchases', fn ($rows) => $rows->count() === 1 && $rows->first()->invoice_number === 'MILK-21' && str_contains($rows->url(1), 'search=MILK') && str_contains($rows->url(1), 'sort=lowest'));
        $this->get(route('admin.supply-purchases.index', ['search' => 'missing']))->assertSee('No purchases match your filters.');
    }

    public function test_purchase_filters_reject_invalid_sort_and_non_string_search(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.supply-purchases.index', ['sort' => 'total_amount desc; DROP TABLE users']))->assertSessionHasErrors('sort');
        $this->get(route('admin.supply-purchases.index', ['search' => ['milk']]))->assertSessionHasErrors('search');
    }

    private function sale(string $date, float $amount, string $status = 'completed'): SalesTransaction
    {
        return SalesTransaction::create(['transaction_date' => $date, 'total_amount' => $amount, 'payment_method' => 'Cash', 'status' => $status]);
    }

    private function purchase(array $attributes = []): SupplyPurchase
    {
        return SupplyPurchase::create($attributes + ['purchase_date' => '2026-10-09', 'payment_method' => 'Cash', 'total_amount' => 100]);
    }
}
