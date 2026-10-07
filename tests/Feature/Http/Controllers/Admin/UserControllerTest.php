<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_management_shows_the_admin_username_but_never_the_password_hash(): void
    {
        $admin = User::factory()->create(['name' => 'owner']);

        $response = $this->actingAs($admin)->get(route('admin.users'));

        $response
            ->assertOk()
            ->assertSee('owner')
            ->assertDontSee($admin->password, false);
    }

    public function test_user_management_masks_staff_pins_and_passwords_behind_an_eye_button(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.users'));

        $response
            ->assertSee('<input type="password" name="passcode"', false)
            ->assertSee('<input type="password" name="current_password"', false)
            ->assertSee('<input type="password" name="password"', false)
            ->assertSee('data-eye', false);
    }
}
