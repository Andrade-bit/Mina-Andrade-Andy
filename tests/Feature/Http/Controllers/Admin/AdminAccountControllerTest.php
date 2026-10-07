<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_the_admin_login(): void
    {
        $response = $this->put(route('admin.account.update'), [
            'username' => 'boss',
            'current_password' => 'password',
        ]);

        $response->assertRedirectToRoute('admin.login');
    }

    public function test_changes_username_and_password_and_returns_to_user_management(): void
    {
        $admin = User::factory()->create(['name' => 'owner']);

        $response = $this->actingAs($admin)->put(route('admin.account.update'), [
            'username' => 'boss',
            'current_password' => 'password',
            'password' => 'brand-new-pass',
        ]);

        $response
            ->assertRedirectToRoute('admin.users')
            ->assertSessionHas('status', 'Admin login updated.');
        $admin->refresh();
        $this->assertSame('boss', $admin->name);
        $this->assertTrue(Hash::check('brand-new-pass', $admin->password));
    }

    public function test_blank_new_password_keeps_the_existing_password(): void
    {
        $admin = User::factory()->create(['name' => 'owner']);

        $response = $this->actingAs($admin)->put(route('admin.account.update'), [
            'username' => 'boss',
            'current_password' => 'password',
            'password' => '',
        ]);

        $response->assertSessionHasNoErrors();
        $admin->refresh();
        $this->assertSame('boss', $admin->name);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_keeping_the_same_username_is_not_reported_as_taken(): void
    {
        $admin = User::factory()->create(['name' => 'owner']);

        $response = $this->actingAs($admin)->put(route('admin.account.update'), [
            'username' => 'owner',
            'current_password' => 'password',
            'password' => 'brand-new-pass',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('brand-new-pass', $admin->refresh()->password));
    }

    public function test_rejects_a_wrong_current_password_and_changes_nothing(): void
    {
        $admin = User::factory()->create(['name' => 'owner']);

        $response = $this->actingAs($admin)->put(route('admin.account.update'), [
            'username' => 'boss',
            'current_password' => 'not-my-password',
            'password' => 'brand-new-pass',
        ]);

        $response->assertSessionHasErrors(['current_password' => 'Your current password is not correct.']);
        $admin->refresh();
        $this->assertSame('owner', $admin->name);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_requires_the_current_password_and_a_username(): void
    {
        $admin = User::factory()->create(['name' => 'owner']);

        $response = $this->actingAs($admin)->put(route('admin.account.update'), []);

        $response->assertSessionHasErrors([
            'username' => 'The username field is required.',
            'current_password' => 'Enter your current password to save changes.',
        ]);
        $this->assertSame('owner', $admin->refresh()->name);
    }

    public function test_rejects_a_username_already_used_by_another_account(): void
    {
        $admin = User::factory()->create(['name' => 'owner']);
        User::factory()->create(['name' => 'taken']);

        $response = $this->actingAs($admin)->put(route('admin.account.update'), [
            'username' => 'taken',
            'current_password' => 'password',
        ]);

        $response->assertSessionHasErrors(['username' => 'That username is already taken. Choose a different one.']);
        $this->assertSame('owner', $admin->refresh()->name);
    }

    public function test_rejects_a_new_password_shorter_than_eight_characters(): void
    {
        $admin = User::factory()->create(['name' => 'owner']);

        $response = $this->actingAs($admin)->put(route('admin.account.update'), [
            'username' => 'owner',
            'current_password' => 'password',
            'password' => 'short',
        ]);

        $response->assertSessionHasErrors(['password' => 'The new password must be at least 8 characters.']);
        $this->assertTrue(Hash::check('password', $admin->refresh()->password));
    }
}
