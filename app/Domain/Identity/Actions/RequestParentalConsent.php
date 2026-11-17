<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Notifications\ParentalConsentRequested;
use App\Enums\ConsentStatus;
use App\Models\ParentalConsent;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class RequestParentalConsent
{
    public function handle(User $child, string $guardianEmail): ParentalConsent
    {
        // Close any earlier open request so only the newest link works.
        $child->parentalConsents()
            ->where('status', ConsentStatus::Pending->value)
            ->update(['status' => ConsentStatus::Revoked->value, 'updated_at' => now()]);

        $token = Str::random(64);

        $consent = ParentalConsent::create([
            'user_id' => $child->getKey(),
            'guardian_email' => $guardianEmail,
            'token_hash' => ParentalConsent::hashToken($token),
            'status' => ConsentStatus::Pending,
            'expires_at' => now()->addDays((int) config('keymaster.consent_link_expires_days', 14)),
        ]);

        Notification::route('mail', $guardianEmail)
            ->notify(new ParentalConsentRequested($child, $consent, $token));

        return $consent;
    }
}
