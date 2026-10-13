<?php

namespace Tests\Feature;

use App\Models\Credential;
use App\Models\InventoryItem;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Shop;
use App\Models\UploadedImage;
use App\Models\User;
use App\Support\ShopContext;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SaasPlatformTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function shop(string $slug = 'second-shop'): Shop
    {
        return Shop::create(['name' => 'Second Shop', 'slug' => $slug, 'plan_id' => Plan::firstOrFail()->id, 'status' => 'active', 'subscription_status' => 'active']);
    }

    private function superadmin(): User
    {
        return User::factory()->create(['name' => 'platform-admin', 'role' => 'superadmin', 'shop_id' => null]);
    }

    private function stock(int $shopId, string $name): InventoryItem
    {
        app(ShopContext::class)->set($shopId);
        $item = InventoryItem::create(['name' => $name, 'type' => 'ingredient', 'unit' => 'g', 'current_quantity' => 0, 'reorder_level' => 1]);
        app(ShopContext::class)->reset();

        return $item;
    }

    public function test_shared_login_redirects_superadmin_and_owner_to_their_own_dashboards(): void
    {
        $superadmin = $this->superadmin();
        $owner = User::factory()->create(['name' => 'shop-owner']);

        $this->post(route('admin.login.store'), ['username' => $superadmin->email, 'password' => 'password'])->assertRedirect(route('superadmin.dashboard'));
        $this->post(route('admin.logout'));
        $this->post(route('admin.login.store'), ['username' => $owner->name, 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
    }

    public function test_shop_owner_cannot_access_any_superadmin_page(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['dashboard', 'shops.index', 'subscriptions', 'plans.index', 'users.index', 'activity', 'settings'] as $route) {
            $this->get(route('superadmin.'.$route))->assertForbidden();
        }
        $this->post(route('superadmin.plans.store'), ['name' => 'Fake', 'monthly_price' => 100])->assertForbidden();
    }

    public function test_superadmin_pages_render_without_logo_and_cannot_open_operational_data(): void
    {
        $this->stock(1, 'PRIVATE INVENTORY');
        $this->actingAs($this->superadmin());
        foreach (['dashboard', 'shops.index', 'subscriptions', 'plans.index', 'users.index', 'activity', 'settings', 'shops.create'] as $route) {
            $this->get(route('superadmin.'.$route))->assertOk()->assertDontSee('<img', false)->assertDontSee('PRIVATE INVENTORY');
        }
        $this->get(route('superadmin.shops.edit', 1))->assertOk();
        $this->get(route('admin.inventory'))->assertForbidden();
        $this->get(route('admin.reports.index'))->assertForbidden();
    }

    public function test_inventory_search_counts_and_record_binding_are_isolated(): void
    {
        $shop = $this->shop();
        $private = $this->stock(1, 'Private Catbrews item');
        $own = $this->stock($shop->id, 'Own item');
        $this->actingAs(User::factory()->create(['shop_id' => $shop->id]));

        $this->get(route('admin.inventory', ['shop' => 'catbrews']))->assertOk()->assertSee('Own item')->assertDontSee('Private Catbrews item')->assertViewHas('lowStockCount', 1);
        $this->get(route('admin.inventory-items.edit', $private))->assertNotFound();
        $this->delete(route('admin.inventory-items.destroy', $private))->assertNotFound();
        $this->get(route('admin.inventory-items.edit', $own))->assertOk();
    }

    public function test_foreign_inventory_ids_cannot_be_submitted_for_stock_out_or_purchase(): void
    {
        $shop = $this->shop();
        $private = $this->stock(1, 'Private item');
        $this->actingAs(User::factory()->create(['shop_id' => $shop->id]));

        $this->post(route('admin.inventory-transactions.stock-out'), ['inventory_item_id' => $private->id, 'quantity' => 1, 'transaction_type' => 'Waste', 'inventory_transaction_date' => '2026-10-13'])->assertSessionHasErrors('inventory_item_id');
        $this->post(route('admin.supply-purchases.store'), ['purchase_date' => '2026-10-13', 'payment_method' => 'Cash', 'items' => [['inventory_item_id' => $private->id, 'quantity' => 1, 'unit_cost' => 1]]])->assertSessionHasErrors('items.0.inventory_item_id');
        $this->assertDatabaseCount('inventory_transactions', 0);
        $this->assertDatabaseCount('supply_purchases', 0);
    }

    public function test_suspended_shop_and_disabled_owner_cannot_continue_operations(): void
    {
        $shop = $this->shop();
        $owner = User::factory()->create(['shop_id' => $shop->id]);
        $shop->update(['status' => 'suspended']);
        $this->actingAs($owner)->get(route('admin.inventory'))->assertForbidden();
        $shop->update(['status' => 'active']);
        $owner->update(['is_active' => false]);
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_expired_trial_blocks_existing_sessions(): void
    {
        $this->freezeTime();
        $shop = $this->shop();
        $shop->update(['subscription_status' => 'trial', 'trial_ends_at' => now()->subMinute()]);
        $this->actingAs(User::factory()->create(['shop_id' => $shop->id]))->get(route('admin.inventory'))->assertForbidden();
    }

    public function test_superadmin_creates_empty_shop_and_owner_without_copying_data(): void
    {
        $this->stock(1, 'Existing stock');
        $this->actingAs($this->superadmin());
        $this->post(route('superadmin.shops.store'), ['name' => 'New Shop', 'slug' => 'new-shop', 'plan_id' => 1, 'owner_name' => 'new-owner', 'owner_email' => 'owner@example.test', 'owner_password' => 'NewOwnerPass123', 'owner_password_confirmation' => 'NewOwnerPass123'])->assertSessionHasNoErrors();
        $shop = Shop::where('slug', 'new-shop')->firstOrFail();
        $this->assertDatabaseHas('users', ['email' => 'owner@example.test', 'shop_id' => $shop->id, 'role' => 'owner']);
        $this->assertDatabaseHas('platform_activities', ['shop_id' => $shop->id, 'action' => 'shop.created']);
        $owner = User::where('email', 'owner@example.test')->firstOrFail();
        $this->assertNotNull($owner->invitation_token);
        $owner->forceFill(['invitation_token' => null, 'email_verified_at' => now()])->save();
        $this->actingAs($owner)->get(route('admin.inventory'))->assertViewHas('totalItems', 0)->assertDontSee('Existing stock');
    }

    public function test_pin_can_be_reused_in_another_shop_but_login_stays_scoped(): void
    {
        $shop = $this->shop();
        $one = Credential::create(['first_name' => 'First', 'last_name' => 'Cashier', 'role' => 'assistant', 'passcode' => '1234']);
        app(ShopContext::class)->set($shop->id);
        $two = Credential::create(['first_name' => 'Second', 'last_name' => 'Cashier', 'role' => 'assistant', 'passcode' => '1234']);
        app(ShopContext::class)->reset();

        $this->post(route('pos.login.store'), ['passcode' => '1234', 'shop' => $shop->slug])->assertSessionHas('pos_credential_id', $two->id);
        $this->get(route('pos.terminal', ['shop' => 'catbrews']))->assertSee('Second')->assertDontSee('First Cashier');
        $this->post(route('pos.logout'));
        $this->post(route('pos.login.store'), ['passcode' => '1234'])->assertSessionHas('pos_credential_id', $one->id);
    }

    public function test_uploaded_images_require_the_matching_shop(): void
    {
        $shop = $this->shop();
        app(ShopContext::class)->set($shop->id);
        UploadedImage::create(['path' => 'products/private.png', 'mime_type' => 'image/png', 'contents' => base64_encode('image-bytes')]);
        app(ShopContext::class)->reset();

        $this->get(route('product-images.show', ['filename' => 'private.png']))->assertNotFound();
        $this->get(route('product-images.show', ['filename' => 'private.png', 'shop' => $shop->slug]))->assertOk()->assertContent('image-bytes');
    }

    public function test_managers_cannot_manage_staff_or_promote_themselves(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'manager']));
        $this->get(route('admin.users'))->assertForbidden();
        $this->post(route('superadmin.users.store'), ['role' => 'superadmin'])->assertForbidden();
        $this->get(route('admin.inventory'))->assertOk();
    }

    public function test_staff_plan_limit_is_enforced(): void
    {
        Plan::findOrFail(1)->update(['max_staff' => 1]);
        Credential::create(['first_name' => 'First', 'last_name' => 'Cashier', 'role' => 'assistant', 'passcode' => '1234']);
        $this->actingAs(User::factory()->create())->post(route('admin.users.store'), ['first_name' => 'Second', 'last_name' => 'Cashier', 'role' => 'assistant', 'passcode' => '5678'])->assertSessionHasErrors('plan');
        $this->assertDatabaseCount('credentials', 1);
    }

    public function test_cannot_disable_self_or_the_last_shop_owner(): void
    {
        $admin = $this->superadmin();
        $owner = User::factory()->create();
        $this->actingAs($admin)->put(route('superadmin.users.update', $admin), ['role' => 'superadmin', 'is_active' => 0])->assertSessionHasErrors('is_active');
        $this->put(route('superadmin.users.update', $owner), ['role' => 'manager', 'is_active' => 1])->assertSessionHasErrors('role');
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'role' => 'owner']);
    }

    public function test_platform_changes_are_audited_and_user_content_is_escaped(): void
    {
        $shop = $this->shop();
        $this->actingAs($this->superadmin())->put(route('superadmin.shops.update', $shop), ['name' => '<script>alert(1)</script>', 'slug' => $shop->slug, 'plan_id' => 1, 'status' => 'suspended', 'subscription_status' => 'past_due'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('platform_activities', ['shop_id' => $shop->id, 'action' => 'shop.updated']);
        $this->get(route('superadmin.shops.index'))->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_public_menus_are_scoped_by_shop(): void
    {
        $firstCategory = ProductCategory::create(['category_name' => 'Original drinks']);
        Product::create(['product_name' => 'Original menu drink', 'product_category_id' => $firstCategory->id]);
        $shop = $this->shop();
        app(ShopContext::class)->set($shop->id);
        $category = ProductCategory::create(['category_name' => 'Second drinks']);
        Product::create(['product_name' => 'Second menu drink', 'product_category_id' => $category->id]);
        app(ShopContext::class)->reset();

        $this->get(route('home', ['shop' => $shop->slug]))->assertViewHas('groups', fn ($groups) => $groups->count() === 1 && $groups->first()['name'] === 'Second drinks');
        $this->get(route('home'))->assertViewHas('groups', fn ($groups) => $groups->count() === 1 && $groups->first()['name'] === 'Original drinks');
    }

    public function test_upgrade_preserves_existing_data_and_assigns_it_to_catbrews(): void
    {
        $owner = User::factory()->create();
        $stock = $this->stock(1, 'Original stock');
        $migration = require database_path('migrations/2026_10_13_130922_create_saas_platform_tables.php');
        $services = require database_path('migrations/2026_10_13_145217_add_saas_recovery_notifications_and_audit_tables.php');
        $services->down();
        $migration->down();
        $migration->up();
        $services->up();

        $this->assertDatabaseHas('inventory_items', ['id' => $stock->id, 'name' => 'Original stock', 'current_quantity' => 0, 'shop_id' => 1]);
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'email' => $owner->email, 'shop_id' => 1, 'role' => 'owner']);
        $this->assertDatabaseHas('shops', ['id' => 1, 'slug' => 'catbrews', 'subscription_status' => 'active']);
        $this->actingAs($owner->fresh())->get(route('admin.inventory'))->assertSee('Original stock');
    }

    public function test_superadmin_can_manage_plans_users_and_platform_defaults(): void
    {
        $this->actingAs($this->superadmin());
        $this->post(route('superadmin.plans.store'), ['name' => 'Starter', 'monthly_price' => 499, 'max_products' => 20, 'max_staff' => 3, 'features_text' => "Inventory\nPOS", 'is_active' => 1])->assertSessionHasNoErrors();
        $plan = Plan::where('name', 'Starter')->firstOrFail();
        $this->assertSame(['Inventory', 'POS'], $plan->features);
        $this->put(route('superadmin.plans.update', $plan), ['name' => 'Starter', 'monthly_price' => 599, 'is_active' => 1])->assertSessionHasNoErrors();
        $this->post(route('superadmin.users.store'), ['name' => 'new-manager', 'email' => 'manager@example.test', 'password' => 'ManagerPass123', 'password_confirmation' => 'ManagerPass123', 'shop_id' => 1, 'role' => 'manager'])->assertSessionHasNoErrors();
        $manager = User::where('name', 'new-manager')->firstOrFail();
        $this->put(route('superadmin.users.update', $manager), ['role' => 'manager', 'is_active' => 0])->assertSessionHasNoErrors();
        $this->assertFalse($manager->fresh()->is_active);
        $this->put(route('superadmin.settings.update'), ['platform_name' => 'Coffee Platform', 'support_email' => 'support@example.test', 'trial_days' => 21])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('platform_settings', ['platform_name' => 'Coffee Platform', 'trial_days' => 21]);
    }

    public function test_product_limit_is_enforced_and_foreign_recipe_ids_are_rejected(): void
    {
        $category = ProductCategory::create(['category_name' => 'Drinks']);
        Product::create(['product_category_id' => $category->id, 'product_name' => 'Existing drink']);
        Plan::findOrFail(1)->update(['max_products' => 1]);
        $this->actingAs(User::factory()->create())->post(route('admin.products.store'), ['product_category_id' => $category->id, 'product_name' => 'Another drink'])->assertSessionHasErrors('plan');
        $this->assertDatabaseCount('products', 1);
        Plan::findOrFail(1)->update(['max_products' => null]);
        $shop = $this->shop();
        $foreignStock = $this->stock($shop->id, 'Foreign recipe item');
        $this->post(route('admin.products.store'), ['product_category_id' => $category->id, 'product_name' => 'Rejected drink', 'ingredients' => [$foreignStock->id => [1 => 5]]])->assertSessionHasErrors('ingredients');
        $this->assertDatabaseCount('products', 1);
    }

    public function test_missing_shop_context_fails_closed(): void
    {
        $this->stock(1, 'Private stock');
        app(ShopContext::class)->set(null);
        $this->assertSame(0, InventoryItem::count());
        $this->expectException(\LogicException::class);
        InventoryItem::create(['name' => 'No shop', 'type' => 'ingredient', 'unit' => 'g']);
    }
}
