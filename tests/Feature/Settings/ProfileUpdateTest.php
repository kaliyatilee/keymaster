<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(User $user, array $overrides = []): array
    {
        return [
            'name' => 'Test User',
            'username' => $user->username,
            'email' => $user->email,
            'display_name' => 'Testy',
            'timezone' => $user->timezone,
            'daily_goal_minutes' => 15,
            'is_public' => '0',
            'show_on_leaderboards' => '1',
            ...$overrides,
        ];
    }

    public function test_profile_page_is_displayed()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), $this->payload($user, [
                'email' => 'test@example.com',
                'username' => 'new_name',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('new_name', $user->username);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        $this->assertSame('Testy', $user->profile->display_name);
        $this->assertSame(15, $user->profile->daily_goal_minutes);
    }

    public function test_email_is_stored_in_lowercase_so_the_user_can_still_log_in()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), $this->payload($user, ['email' => 'Test@Example.com']))
            ->assertSessionHasNoErrors();

        $this->assertSame('test@example.com', $user->refresh()->email);

        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'login' => 'Test@Example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_email_must_be_unique_regardless_of_case()
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), $this->payload($user, ['email' => 'Taken@Example.com']))
            ->assertSessionHasErrors('email')
            ->assertRedirect(route('profile.edit'));
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), $this->payload($user))
            ->assertSessionHasNoErrors();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_minors_cannot_make_their_profile_public()
    {
        $user = User::factory()->minor(14)->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), $this->payload($user, ['is_public' => '1']))
            ->assertSessionHasErrors('is_public');

        $this->assertFalse($user->profile->refresh()->is_public);
    }

    public function test_adults_can_make_their_profile_public()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), $this->payload($user, ['is_public' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertTrue($user->profile->refresh()->is_public);
    }

    public function test_timezone_can_only_change_once_a_week()
    {
        $user = User::factory()->create(['timezone' => 'Africa/Harare']);

        $this->actingAs($user)
            ->patch(route('profile.update'), $this->payload($user, ['timezone' => 'Europe/London']))
            ->assertSessionHasNoErrors();

        $this->assertSame('Europe/London', $user->refresh()->timezone);

        $this->actingAs($user)
            ->patch(route('profile.update'), $this->payload($user, ['timezone' => 'Asia/Tokyo']))
            ->assertSessionHasErrors('timezone');

        $this->travel(8)->days();

        $this->actingAs($user)
            ->patch(route('profile.update'), $this->payload($user, ['timezone' => 'Asia/Tokyo']))
            ->assertSessionHasNoErrors();
    }

    public function test_user_can_delete_their_account_and_personal_data_is_removed()
    {
        $user = User::factory()->create(['email' => 'gone@example.com', 'username' => 'gone_user']);

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertSoftDeleted($user);

        $deleted = User::withTrashed()->find($user->id);
        $this->assertNull($deleted->email);
        $this->assertNull($deleted->date_of_birth);
        $this->assertStringStartsWith('deleted_', $deleted->username);
        $this->assertDatabaseMissing('profiles', ['user_id' => $user->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'account.deleted', 'auditable_id' => $user->id]);
    }

    public function test_correct_password_must_be_provided_to_delete_account()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->delete(route('profile.destroy'), ['password' => 'wrong-password'])
            ->assertSessionHasErrors('password')
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->fresh());
    }
}
