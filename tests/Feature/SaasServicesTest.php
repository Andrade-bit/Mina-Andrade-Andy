<?php

namespace Tests\Feature;

use App\Models\Credential;
use App\Models\EmailMessage;
use App\Models\InventoryItem;
use App\Models\Plan;
use App\Models\Shop;
use App\Models\ShopActivity;
use App\Models\UploadedImage;
use App\Models\User;
use App\Notifications\ServiceEmail;
use App\Support\AccountInvitations;
use App\Support\ServiceMail;
use App\Support\ShopContext;
use App\Support\SubscriptionReminders;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SaasServicesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_activity_details_use_plain_labels_and_private_inventory_names(): void
    {
        $item = $this->stock();
        ShopActivity::create(['action' => 'created', 'subject_type' => 'InventoryTransaction', 'subject_id' => '123', 'changes' => ['before' => [], 'after' => ['inventory_item_id' => $item->id, 'quantity' => 1, 'transaction_type' => 'Sales']]]);
        $shop = Shop::create(['name' => 'Other', 'slug' => 'display-other', 'plan_id' => 1, 'status' => 'active', 'subscription_status' => 'active']);
        app(ShopContext::class)->set($shop->id);
        $foreignItem = InventoryItem::create(['name' => 'PRIVATE FOREIGN STOCK', 'type' => 'ingredient', 'unit' => 'g']);
        app(ShopContext::class)->set(1);
        ShopActivity::create(['action' => 'created', 'subject_type' => 'InventoryTransaction', 'subject_id' => '124', 'changes' => ['before' => [], 'after' => ['inventory_item_id' => $foreignItem->id, 'quantity' => 1]]]);
        $item->update(['current_quantity' => 50]);
        $this->actingAs($this->owner())->get(route('admin.shop-activity'))->assertOk()->assertSee('Stock movement')->assertSee('Movement type')->assertSee('Coffee')->assertSee('Previous')->assertSee('New')->assertDontSee('PRIVATE FOREIGN STOCK')->assertDontSee('<pre', false)->assertDontSee('&quot;after&quot;', false);
    }

    private function owner(): User
    {
        return User::factory()->create();
    }

    private function stock(): InventoryItem
    {
        app(ShopContext::class)->set(1);

        return InventoryItem::create(['name' => 'Coffee', 'type' => 'ingredient', 'unit' => 'g', 'current_quantity' => 100, 'reorder_level' => 10]);
    }

    public function test_recovery_is_private_expiring_and_single_use(): void
    {
        $owner = $this->owner();
        config(['app.url' => 'https://trusted.example']);
        $this->post(route('account.forgot.store'), ['email' => $owner->email])->assertSessionHas('status');
        $message = EmailMessage::where('kind', 'recovery')->firstOrFail();
        $this->assertStringStartsWith('https://trusted.example/account/reset/', $message->payload['url']);
        $this->assertStringNotContainsString('https://trusted.example', DB::table('email_messages')->value('payload'));
        $token = basename(parse_url($message->payload['url'], PHP_URL_PATH));
        $credentials = ['email' => $owner->email, 'token' => $token, 'password' => 'NewSecurePass123', 'password_confirmation' => 'NewSecurePass123'];
        $this->post(route('account.reset.store'), $credentials)->assertRedirect(route('admin.login'));
        $this->assertTrue(Hash::check('NewSecurePass123', $owner->fresh()->password));
        $this->assertNotNull($owner->fresh()->email_verified_at);
        $this->post(route('account.reset.store'), $credentials)->assertSessionHasErrors('email');
    }

    public function test_expired_recovery_and_unknown_accounts_do_not_grant_access(): void
    {
        $owner = $this->owner();
        $this->post(route('account.forgot.store'), ['email' => 'unknown@example.test'])->assertSessionHas('status');
        $this->assertSame(0, EmailMessage::count());
        $this->post(route('account.forgot.store'), ['email' => $owner->email]);
        $token = basename(parse_url(EmailMessage::firstOrFail()->payload['url'], PHP_URL_PATH));
        $this->travel(61)->minutes();
        $this->post(route('account.reset.store'), ['email' => $owner->email, 'token' => $token, 'password' => 'NewSecurePass123', 'password_confirmation' => 'NewSecurePass123'])->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('password', $owner->fresh()->password));
    }

    public function test_invitation_is_rotated_and_recipient_sets_the_password(): void
    {
        $owner = $this->owner();
        app(AccountInvitations::class)->send($owner);
        $first = basename(parse_url(EmailMessage::firstOrFail()->payload['url'], PHP_URL_PATH));
        app(AccountInvitations::class)->send($owner);
        $second = basename(parse_url(EmailMessage::latest('id')->firstOrFail()->payload['url'], PHP_URL_PATH));
        $this->get(route('account.invitation', $first))->assertNotFound();
        $this->post(route('admin.login.store'), ['username' => $owner->email, 'password' => 'password'])->assertSessionHasErrors('username');
        $this->get(route('account.invitation', $second))->assertOk();
        $this->post(route('account.invitation.accept', $second), ['password' => 'OwnerSecurePass123', 'password_confirmation' => 'OwnerSecurePass123'])->assertRedirect(route('admin.login'));
        $this->get(route('account.invitation', $second))->assertNotFound();
        $this->assertNull($owner->fresh()->invitation_token);
        $this->assertNotNull($owner->fresh()->email_verified_at);
        $this->assertTrue(Hash::check('OwnerSecurePass123', $owner->fresh()->password));
    }

    public function test_private_inventory_photos_persist_without_public_files(): void
    {
        Storage::fake('public');
        $item = $this->stock();
        $owner = $this->owner();
        $file = UploadedFile::fake()->createWithContent('coffee.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
        $this->actingAs($owner)->put(route('admin.inventory-items.update', $item), ['name' => 'Coffee', 'type' => 'ingredient', 'unit' => 'g', 'reorder_level' => 10, 'image' => $file])->assertSessionHasNoErrors();
        $this->assertSame(1, UploadedImage::count());
        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->get(route('admin.inventory-items.photo', $item))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $shop = Shop::create(['name' => 'Other', 'slug' => 'other', 'plan_id' => 1, 'status' => 'active', 'subscription_status' => 'active']);
        $this->actingAs(User::factory()->create(['shop_id' => $shop->id]))->get(route('admin.inventory-items.photo', $item))->assertNotFound();
        $this->actingAs(User::factory()->create(['role' => 'superadmin', 'shop_id' => null]))->get(route('admin.inventory-items.photo', $item))->assertForbidden();
    }

    public function test_disabled_features_block_reads_writes_and_do_not_block_recovery(): void
    {
        Plan::findOrFail(1)->update(['enabled_features' => []]);
        $this->actingAs($this->owner());
        $this->get(route('admin.inventory'))->assertForbidden();
        $this->post(route('admin.promos.store'), ['code' => 'TEST'])->assertForbidden();
        $this->get(route('admin.reports.export'))->assertForbidden();
        $this->get(route('pos.login'))->assertForbidden();
        $this->get(route('account.forgot'))->assertOk();
    }

    public function test_shop_history_is_scoped_and_never_contains_staff_pins(): void
    {
        $item = $this->stock();
        $item->update(['current_quantity' => 50]);
        $owner = $this->owner();
        $this->actingAs($owner)->get(route('admin.shop-activity'))->assertOk()->assertSee('InventoryItem');
        $this->actingAs(User::factory()->create(['role' => 'manager']))->get(route('admin.shop-activity'))->assertForbidden();
        $shop = Shop::create(['name' => 'Other', 'slug' => 'other', 'plan_id' => 1, 'status' => 'active', 'subscription_status' => 'active']);
        $this->actingAs(User::factory()->create(['shop_id' => $shop->id]))->get(route('admin.shop-activity'))->assertOk()->assertViewHas('activities', fn ($activities) => $activities->total() === 0);
        app(ShopContext::class)->set(1);
        Credential::create(['first_name' => 'Cashier', 'last_name' => 'Example', 'passcode' => '123456', 'role' => 'cashier']);
        $this->assertStringNotContainsString('123456', ShopActivity::all()->toJson());
    }

    public function test_mail_is_deduplicated_cleared_after_delivery_and_disabled_by_default(): void
    {
        Notification::fake();
        $owner = $this->owner();
        $mail = app(ServiceMail::class);
        $message = $mail->queue($owner, 'test', ['subject' => 'Test', 'lines' => ['Test email']], 'unique');
        $this->assertSame($message->id, $mail->queue($owner, 'test', ['subject' => 'Test', 'lines' => ['Test email']], 'unique')->id);
        $this->assertFalse($mail->deliver($message->id));
        Notification::assertNothingSent();
        config(['saas.email_enabled' => true, 'mail.default' => 'smtp']);
        $this->assertTrue($mail->deliver($message->id));
        $this->assertFalse($mail->deliver($message->id));
        $this->assertNull($message->fresh()->payload);
        Notification::assertSentTo($owner, ServiceEmail::class);
    }

    public function test_subscription_reminders_require_verified_owners_and_cron_secret(): void
    {
        $owner = $this->owner();
        Shop::findOrFail(1)->update(['subscription_status' => 'trial', 'trial_ends_at' => now()->addDays(3)]);
        User::factory()->create(['role' => 'manager']);
        User::factory()->unverified()->create();
        $reminders = app(SubscriptionReminders::class);
        $reminders->collect();
        $reminders->collect();
        $this->assertSame(1, EmailMessage::where('kind', 'subscription')->count());
        $this->assertSame($owner->id, EmailMessage::where('kind', 'subscription')->value('user_id'));
        $this->get(route('internal.notifications'))->assertUnauthorized();
        config(['saas.cron_secret' => str_repeat('x', 32)]);
        $this->withHeader('Authorization', 'Bearer '.str_repeat('x', 32))->get(route('internal.notifications'))->assertOk();
    }

    public function test_password_changes_rotate_remembered_access_and_queue_account_notice(): void
    {
        $owner = $this->owner();
        $oldToken = $owner->remember_token;
        $owner->update(['password' => 'ChangedSecurePass123']);
        $this->assertNotSame($oldToken, $owner->fresh()->remember_token);
        $notice = EmailMessage::where('kind', 'account_change')->firstOrFail();
        $this->assertSame($owner->id, $notice->user_id);
        $this->assertStringNotContainsString('ChangedSecurePass123', json_encode($notice->payload));
    }

    public function test_failed_messages_are_retained_for_retry_and_expired_links_are_erased(): void
    {
        $owner = $this->owner();
        config(['saas.email_enabled' => true, 'mail.default' => 'smtp']);
        Notification::shouldReceive('sendNow')->once()->andThrow(new \RuntimeException('Private provider error'));
        $mail = app(ServiceMail::class);
        $message = $mail->queue($owner, 'recovery', ['subject' => 'Test', 'lines' => ['Test'], 'url' => 'https://example.test/private-token'], 'failed', now()->addMinutes(10));
        $this->assertFalse($mail->deliver($message->id));
        $this->assertSame('failed', $message->fresh()->status);
        $this->assertNotNull($message->fresh()->payload);
        $this->travel(11)->minutes();
        $this->assertFalse($mail->deliver($message->id));
        $this->assertSame('expired', $message->fresh()->status);
        $this->assertNull($message->fresh()->payload);
    }
}
