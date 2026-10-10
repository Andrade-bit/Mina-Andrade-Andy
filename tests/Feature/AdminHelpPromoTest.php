<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminHelpPromoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_unchecked_new_promo_stays_inactive(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['OMITTED' => [], 'EXPLICIT' => ['active' => '0']] as $code => $state) {
            $this->post(route('admin.promos.store'), array_merge([
                'code' => $code, 'type' => 'percent', 'value' => 10,
            ], $state))->assertSessionHasNoErrors();
            $promo = Promo::where('code', $code)->firstOrFail();
            $this->assertFalse($promo->active);
            $this->assertFalse($promo->isValid());
        }
    }

    public function test_promo_can_be_disabled_saved_again_and_reenabled(): void
    {
        $this->actingAs(User::factory()->create());
        $promo = Promo::create(['code' => 'TOGGLE', 'type' => 'fixed', 'value' => 20, 'active' => true]);
        $payload = ['code' => 'TOGGLE', 'type' => 'fixed', 'value' => 20, 'active' => '0'];
        for ($i = 0; $i < 2; $i++) {
            $this->put(route('admin.promos.update', $promo), $payload)->assertSessionHasNoErrors();
            $this->assertFalse($promo->fresh()->active);
        }
        $this->put(route('admin.promos.update', $promo), array_replace($payload, ['active' => '1']))->assertSessionHasNoErrors();
        $this->assertTrue($promo->fresh()->isValid());
    }

    public function test_dashboard_counts_all_low_stock_items_and_includes_admin_help(): void
    {
        $this->actingAs(User::factory()->create());
        for ($i = 0; $i < 7; $i++) {
            InventoryItem::create(['name' => 'Low '.$i, 'type' => 'ingredient', 'unit' => 'g', 'current_quantity' => 1, 'reorder_level' => 5]);
        }
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('lowStockCount', 7)
            ->assertViewHas('lowStockItems', fn ($items) => $items->count() === 5)
            ->assertSee('Admin Help &amp; FAQ', false)
            ->assertSee('How do I disable a promo?')
            ->assertSee('At a glance');
    }
}
