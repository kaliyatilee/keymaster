<?php

namespace Tests\Feature\Auth;

use App\Domain\Identity\Notifications\ParentalConsentRequested;
use App\Enums\AccountStatus;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Test User',
            'username' => 'Test_User',
            'email' => 'test@example.com',
            'date_of_birth' => now()->subYears(30)->toDateString(),
            'timezone' => 'Africa/Harare',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
            ...$overrides,
        ];
    }

    public function test_registration_screen_can_be_rendered()
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('auth/register')->where('consentAge', 13));
    }

    public function test_adults_can_register_and_get_a_complete_account()
    {
        $response = $this->post(route('register.store'), $this->payload());

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertSame('test_user', $user->username);
        $this->assertSame(AccountStatus::Active, $user->status);
        $this->assertSame('Africa/Harare', $user->timezone);
        $this->assertTrue($user->hasRole(RoleName::Student));
        $this->assertFalse($user->hasRole(RoleName::Teacher));
        $this->assertNotNull($user->profile);
        $this->assertFalse($user->profile->is_public);
        $this->assertNotNull($user->statistics);
        $this->assertSame(0, $user->statistics->xp_total);
        $this->assertSame(now()->subYears(30)->toDateString(), $user->date_of_birth->toDateString());
    }

    public function test_date_of_birth_is_encrypted_at_rest()
    {
        $this->post(route('register.store'), $this->payload());

        $raw = DB::table('users')->where('email', 'test@example.com')->value('date_of_birth');

        $this->assertIsString($raw);
        $this->assertStringNotContainsString(now()->subYears(30)->format('Y-m-d'), $raw);
    }

    public function test_under_age_users_must_give_a_guardian_email()
    {
        $this->post(route('register.store'), $this->payload([
            'date_of_birth' => now()->subYears(11)->toDateString(),
        ]))->assertSessionHasErrors('guardian_email');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_under_age_registration_is_pending_until_a_guardian_consents()
    {
        Notification::fake();

        $this->post(route('register.store'), $this->payload([
            'date_of_birth' => now()->subYears(11)->toDateString(),
            'guardian_email' => 'parent@example.com',
        ]));

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertSame(AccountStatus::PendingConsent, $user->status);
        $this->assertFalse($user->profile->show_on_leaderboards);
        $this->assertDatabaseHas('parental_consents', [
            'user_id' => $user->id,
            'guardian_email' => 'parent@example.com',
            'status' => 'pending',
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            ParentalConsentRequested::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'parent@example.com',
        );
    }

    public function test_guardian_email_must_differ_from_the_users_email()
    {
        $this->post(route('register.store'), $this->payload([
            'date_of_birth' => now()->subYears(11)->toDateString(),
            'guardian_email' => 'test@example.com',
        ]))->assertSessionHasErrors('guardian_email');
    }

    public function test_teacher_access_is_requested_not_granted()
    {
        $this->post(route('register.store'), $this->payload(['wants_teacher_access' => '1']));

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertNotNull($user->teacher_requested_at);
        $this->assertFalse($user->hasRole(RoleName::Teacher));
        $this->assertTrue($user->isAwaitingTeacherApproval());
    }

    public function test_children_cannot_request_teacher_access()
    {
        Notification::fake();

        $this->post(route('register.store'), $this->payload([
            'date_of_birth' => now()->subYears(10)->toDateString(),
            'guardian_email' => 'parent@example.com',
            'wants_teacher_access' => '1',
        ]));

        $this->assertNull(User::query()->firstOrFail()->teacher_requested_at);
    }

    public function test_reserved_and_malformed_usernames_are_rejected()
    {
        foreach (['admin', 'ab', 'has space', '_edge', 'bad-dash'] as $username) {
            $this->post(route('register.store'), $this->payload(['username' => $username]))
                ->assertSessionHasErrors('username');
        }

        $this->assertDatabaseCount('users', 0);
    }

    public function test_terms_must_be_accepted()
    {
        $payload = $this->payload();
        unset($payload['terms']);

        $this->post(route('register.store'), $payload)->assertSessionHasErrors('terms');
    }

    public function test_implausible_dates_of_birth_are_rejected()
    {
        foreach ([now()->addDay()->toDateString(), now()->subYears(2)->toDateString(), 'not-a-date'] as $dob) {
            $this->post(route('register.store'), $this->payload(['date_of_birth' => $dob]))
                ->assertSessionHasErrors('date_of_birth');
        }
    }
}
