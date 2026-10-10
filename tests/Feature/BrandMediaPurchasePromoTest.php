<?php

namespace Tests\Feature;

use App\Models\Credential;
use App\Models\InventoryItem;
use App\Models\Promo;
use App\Models\SupplyPurchase;
use App\Models\UploadedImage;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandMediaPurchasePromoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_product_upload_is_served_without_a_local_storage_file(): void
    {
        Storage::fake('public');
        $file = new UploadedFile(public_path('images/catbrews-logo.png'), 'drink.png', 'image/png', null, true);
        $path = UploadedImage::storeUpload($file);
        Storage::disk('public')->assertMissing($path);
        $response = $this->get(route('product-images.show', ['filename' => basename($path)]));
        $response->assertOk()->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame($file->getContent(), $response->getContent());
        $this->get(route('product-images.show', ['filename' => basename($path)]), ['If-None-Match' => $response->headers->get('ETag')])->assertStatus(304);
    }

    public function test_legacy_public_upload_and_missing_image_are_handled(): void
    {
        $this->assertDatabaseCount('uploaded_images', 0);
        Storage::fake('public');
        Storage::disk('public')->put('products/legacy.png', 'legacy-image-bytes');
        $this->get(route('product-images.show', ['filename' => 'legacy.png']))->assertOk()->assertStreamedContent('legacy-image-bytes');
        $this->get(route('product-images.show', ['filename' => 'missing.png']))->assertNotFound();
    }

    public function test_duplicate_reference_is_rejected_without_changing_stock_or_expenses(): void
    {
        $item = InventoryItem::create(['name' => 'Milk', 'type' => 'ingredient', 'unit' => 'ml', 'current_quantity' => 0, 'reorder_level' => 0]);
        $this->actingAs(User::factory()->create());
        $payload = ['invoice_number' => ' inv-101 ', 'purchase_date' => '2026-10-10', 'payment_method' => 'Cash',
            'items' => [['inventory_item_id' => $item->id, 'quantity' => 10, 'unit_cost' => 2]]];
        $this->post(route('admin.supply-purchases.store'), $payload)->assertSessionHasNoErrors();
        $this->post(route('admin.supply-purchases.store'), array_replace($payload, ['invoice_number' => 'INV-101']))->assertSessionHasErrors('invoice_number');
        $this->assertDatabaseCount('supply_purchases', 1);
        $this->assertDatabaseCount('expenses', 1);
        $this->assertSame('10.00', $item->fresh()->current_quantity);
        $this->assertDatabaseHas('supply_purchases', ['invoice_number' => 'INV-101']);
    }

    public function test_database_also_enforces_reference_uniqueness(): void
    {
        $data = ['invoice_number' => 'INV-101', 'purchase_date' => '2026-10-10', 'payment_method' => 'Cash', 'total_amount' => 0];
        SupplyPurchase::create($data);
        $this->expectException(UniqueConstraintViolationException::class);
        SupplyPurchase::create($data);
    }

    public function test_blank_purchase_references_can_be_used_more_than_once(): void
    {
        $item = InventoryItem::create(['name' => 'Milk', 'type' => 'ingredient', 'unit' => 'ml', 'current_quantity' => 0, 'reorder_level' => 0]);
        $this->actingAs(User::factory()->create());
        $payload = ['invoice_number' => '  ', 'purchase_date' => '2026-10-10', 'payment_method' => 'Cash',
            'items' => [['inventory_item_id' => $item->id, 'quantity' => 1, 'unit_cost' => 2]]];
        $this->post(route('admin.supply-purchases.store'), $payload)->assertSessionHasNoErrors();
        $this->post(route('admin.supply-purchases.store'), $payload)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('supply_purchases', 2);
    }

    public function test_promo_preview_requires_a_valid_pos_session(): void
    {
        $this->getJson(route('pos.promos.preview', ['promo_code' => 'SAVE', 'subtotal' => 100]))->assertUnauthorized();
        $this->withSession(['pos_credential_id' => 999])->getJson(route('pos.promos.preview', ['promo_code' => 'SAVE', 'subtotal' => 100]))->assertUnauthorized();
    }

    public function test_preview_validates_promo_before_sale_and_does_not_create_records(): void
    {
        $credential = Credential::create(['first_name' => 'Cashier', 'last_name' => 'Test', 'role' => 'assistant', 'passcode' => '1234']);
        Promo::create(['code' => 'SAVE', 'type' => 'percent', 'value' => 10, 'active' => true]);
        Promo::create(['code' => 'FIXED', 'type' => 'fixed', 'value' => 200, 'active' => true]);
        Promo::create(['code' => 'OFF', 'type' => 'fixed', 'value' => 10, 'active' => false]);
        Promo::create(['code' => 'OLD', 'type' => 'fixed', 'value' => 10, 'active' => true, 'expires_at' => '2020-01-01']);
        $this->withSession(['pos_credential_id' => $credential->id]);
        $this->getJson(route('pos.promos.preview', ['promo_code' => ' save ', 'subtotal' => 100]))->assertOk()->assertJson(['code' => 'SAVE', 'type' => 'percent', 'discount' => 10]);
        $this->getJson(route('pos.promos.preview', ['promo_code' => 'FIXED', 'subtotal' => 100]))->assertOk()->assertJson(['discount' => 100]);
        foreach (['OFF', 'OLD', 'UNKNOWN'] as $code) {
            $this->getJson(route('pos.promos.preview', ['promo_code' => $code, 'subtotal' => 100]))->assertUnprocessable();
        }
        $this->getJson(route('pos.promos.preview', ['promo_code' => 'SAVE', 'subtotal' => -1]))->assertUnprocessable();
        $this->assertDatabaseCount('sales_transactions', 0);
        $this->assertDatabaseCount('inventory_transactions', 0);
        $this->assertFalse((new Promo(['type' => 'invalid', 'value' => 10, 'active' => true]))->isValid());
        $this->assertFalse((new Promo(['type' => 'percent', 'value' => 101, 'active' => true]))->isValid());
    }
}
