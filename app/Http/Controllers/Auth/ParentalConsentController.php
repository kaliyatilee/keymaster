<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Identity\Actions\RequestParentalConsent;
use App\Domain\Identity\Actions\RespondToParentalConsent;
use App\Enums\ConsentStatus;
use App\Http\Controllers\Controller;
use App\Models\ParentalConsent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ParentalConsentController extends Controller
{
    /**
     * The guardian's view of a consent request.
     */
    public function show(string $token): Response
    {
        $consent = $this->findByToken($token);

        return Inertia::render('consent/show', [
            'token' => $token,
            'open' => $consent->isOpen(),
            'status' => $consent->status->value,
            'child' => [
                'name' => $consent->user->name,
                'username' => $consent->user->username,
            ],
            'expiresAt' => $consent->expires_at->toISOString(),
        ]);
    }

    public function respond(Request $request, string $token, RespondToParentalConsent $respond): RedirectResponse
    {
        $data = $request->validate(['decision' => ['required', 'in:approve,decline']]);
        $consent = $this->findByToken($token);

        abort_unless($consent->isOpen(), 410, 'This consent request is no longer open.');

        $respond->handle($consent, $data['decision'] === 'approve', $request->ip());

        return to_route('consent.show', ['token' => $token]);
    }

    /**
     * What a child sees while waiting for approval.
     */
    public function pending(Request $request): Response|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isPendingConsent()) {
            return to_route('dashboard');
        }

        $latest = $user->parentalConsents()->latest('id')->first();

        return Inertia::render('consent/pending', [
            'guardianEmail' => $latest !== null ? $this->mask($latest->guardian_email) : null,
            'expired' => $latest !== null && ! $latest->isOpen() && $latest->status === ConsentStatus::Pending,
            'status' => $request->session()->get('status'),
        ]);
    }

    public function resend(Request $request, RequestParentalConsent $requestConsent): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isPendingConsent(), 403);

        $latest = $user->parentalConsents()->latest('id')->firstOrFail();
        $requestConsent->handle($user, $latest->guardian_email);

        return back()->with('status', 'consent-link-sent');
    }

    private function findByToken(string $token): ParentalConsent
    {
        return ParentalConsent::query()
            ->with('user')
            ->where('token_hash', ParentalConsent::hashToken($token))
            ->firstOrFail();
    }

    private function mask(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return Str::substr($local, 0, 1).str_repeat('*', max(1, Str::length($local) - 1)).'@'.$domain;
    }
}
