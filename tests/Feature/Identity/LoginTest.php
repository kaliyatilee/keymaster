<?php

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_log_in_with_their_username_in_any_case()
    {
        $user = User::factory()->create(['username' => 'fast_fingers']);

        $this->post(route('login.store'), ['login' => 'Fast_Fingers', 'password' => 'password']);

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->refresh()->last_login_at);
    }

    public function test_school_managed_students_log_in_with_a_username_and_need_no_email_verification()
    {
        $user = User::factory()->schoolManaged()->create(['username' => 'class7_amara']);

        $this->post(route('login.store'), ['login' => 'class7_amara', 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }

    public function test_suspended_users_cannot_log_in()
    {
        $user = User::factory()->suspended()->create();

        $this->post(route('login.store'), ['login' => $user->username, 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_users_suspended_mid_session_are_signed_out()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();

        $user->forceFill(['status' => 'suspended'])->save();

        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
