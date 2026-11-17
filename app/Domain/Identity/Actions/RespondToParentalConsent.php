<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Admin\AuditLogger;
use App\Enums\AccountStatus;
use App\Enums\ConsentStatus;
use App\Models\ParentalConsent;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RespondToParentalConsent
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function handle(ParentalConsent $consent, bool $granted, ?string $ip): void
    {
        if (! $consent->isOpen()) {
            throw new InvalidArgumentException('This consent request is no longer open.');
        }

        DB::transaction(function () use ($consent, $granted, $ip) {
            $consent->forceFill([
                'status' => $granted ? ConsentStatus::Granted : ConsentStatus::Declined,
                'responded_at' => now(),
                'responder_ip' => $ip,
            ])->save();

            if ($granted) {
                $consent->user->forceFill(['status' => AccountStatus::Active])->save();
            }

            $this->audit->record(
                $granted ? 'consent.granted' : 'consent.declined',
                $consent->user,
                new: ['consent_id' => $consent->getKey()],
            );
        });
    }
}
