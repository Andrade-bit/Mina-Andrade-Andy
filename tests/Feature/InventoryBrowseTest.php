<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InventoryBrowseTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_inventory_paginates_and_preserves_search_and_stock_filter(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $this->item('Milk '.$i);
        }
        $this->item('Sugar');
        $this->item('Milk stocked', quantity: 100);
        $this->actingAs(User::factory()->create());
        $query = ['search' => 'Milk', 'stock' => 'low', 'tab' => 'procurement'];
        $this->get(route('admin.inventory', $query))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->count() === 6 && $items->total() === 7 && str_contains($items->nextPageUrl(), 'search=Milk') && str_contains($items->nextPageUrl(), 'stock=low') && str_ends_with($items->nextPageUrl(), '#items'));
        $this->get(route('admin.inventory', $query + ['page' => 2]))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->pluck('name')->all() === ['Milk 7']);
    }

    public function test_search_respects_category_and_archived_status(): void
    {
        $this->item('Milk powder');
        $supply = $this->item('Milk cup', 'supply');
        $archived = $this->item('Milk archived');
        $archived->delete();
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.inventory', ['search' => ' Milk ', 'tab' => 'supplier']))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->pluck('id')->all() === [$supply->id])
            ->assertViewHas('archivedCount', 1)->assertViewHas('totalItems', 2);
        $this->get(route('admin.inventory', ['search' => 'Milk', 'tab' => 'archived']))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->pluck('id')->all() === [$archived->id]);
        $this->get(route('admin.inventory', ['search' => 'missing']))->assertOk()
            ->assertSee('No items match your search in this category.');
    }

    public function test_inventory_rejects_invalid_search_and_tab(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.inventory', ['search' => ['bad']]))->assertSessionHasErrors('search');
        $this->get(route('admin.inventory', ['tab' => 'bad']))->assertSessionHasErrors('tab');
    }

    private function item(string $name, string $type = 'ingredient', int $quantity = 0): InventoryItem
    {
        return InventoryItem::create(['name' => $name, 'type' => $type, 'unit' => 'g', 'current_quantity' => $quantity, 'reorder_level' => 0]);
    }
}
