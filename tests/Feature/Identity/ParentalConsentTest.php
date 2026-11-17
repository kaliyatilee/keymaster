<?php

namespace Tests\Feature\Identity;

use App\Domain\Identity\Actions\RequestParentalConsent;
use App\Domain\Identity\Notifications\ParentalConsentRequested;
use App\Enums\AccountStatus;
use App\Models\ParentalConsent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ParentalConsentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: string}
     */
    private function childWithToken(): array
    {
        Notification::fake();

        $child = User::factory()->minor(11)->create(['status' => AccountStatus::PendingConsent]);
        app(RequestParentalConsent::class)->handle($child, 'parent@example.com');

        $token = null;
        Notification::assertSentTo(new AnonymousNotifiable, ParentalConsentRequested::class, function ($n) use (&$token) {
            $token = (fn () => $this->token)->call($n);

            return true;
        });

        return [$child, $token];
    }

    public function test_pending_users_are_kept_on_the_waiting_page()
    {
        [$child] = $this->childWithToken();

        $this->actingAs($child)->get(route('dashboard'))->assertRedirect(route('consent.pending'));
        $this->actingAs($child)->get(route('profile.edit'))->assertRedirect(route('consent.pending'));
        $this->actingAs($child)->get(route('consent.pending'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('consent/pending')->where('guardianEmail', 'p*****@example.com'));
    }

    public function test_guardian_can_view_and_approve_with_the_emailed_link()
    {
        [$child, $token] = $this->childWithToken();

        $this->get(route('consent.show', $token))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('consent/show')->where('open', true)->where('child.username', $child->username));

        $this->post(route('consent.respond', $token), ['decision' => 'approve'])
            ->assertRedirect(route('consent.show', $token));

        $this->assertSame(AccountStatus::Active, $child->refresh()->status);
        $this->assertDatabaseHas('parental_consents', ['user_id' => $child->id, 'status' => 'granted']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'consent.granted', 'auditable_id' => $child->id]);

        // Single use.
        $this->post(route('consent.respond', $token), ['decision' => 'decline'])->assertStatus(410);
    }

    public function test_guardian_can_decline()
    {
        [$child, $token] = $this->childWithToken();

        $this->post(route('consent.respond', $token), ['decision' => 'decline']);

        $this->assertSame(AccountStatus::PendingConsent, $child->refresh()->status);
        $this->assertDatabaseHas('parental_consents', ['user_id' => $child->id, 'status' => 'declined']);
    }

    public function test_expired_links_cannot_be_used()
    {
        [$child, $token] = $this->childWithToken();

        $this->travel(15)->days();

        $this->post(route('consent.respond', $token), ['decision' => 'approve'])->assertStatus(410);
        $this->assertSame(AccountStatus::PendingConsent, $child->refresh()->status);
    }

    public function test_unknown_tokens_return_not_found()
    {
        $this->get(route('consent.show', str_repeat('a', 64)))->assertNotFound();
    }

    public function test_resending_replaces_the_old_link()
    {
        [$child, $oldToken] = $this->childWithToken();

        $this->actingAs($child)->post(route('consent.resend'))->assertSessionHas('status', 'consent-link-sent');

        $this->assertSame(1, ParentalConsent::query()->where('user_id', $child->id)->where('status', 'pending')->count());
        $this->post(route('consent.respond', $oldToken), ['decision' => 'approve'])->assertStatus(410);
    }
}
