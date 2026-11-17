<?php

namespace App\Domain\Identity;

use Carbon\CarbonInterface;

/**
 * Age rules for self-registration. The consent age is configurable per
 * deployment (config/keymaster.php) because it differs between countries.
 */
class AgeGate
{
    public function ageOn(CarbonInterface $dateOfBirth, ?CarbonInterface $on = null): int
    {
        return (int) $dateOfBirth->copy()->startOfDay()->diffInYears(($on ?? now())->copy()->startOfDay());
    }

    public function needsParentalConsent(CarbonInterface $dateOfBirth, ?CarbonInterface $on = null): bool
    {
        return $this->ageOn($dateOfBirth, $on) < $this->consentAge();
    }

    public function meetsMinimumAge(CarbonInterface $dateOfBirth, ?CarbonInterface $on = null): bool
    {
        return $this->ageOn($dateOfBirth, $on) >= (int) config('keymaster.minimum_age', 5);
    }

    public function consentAge(): int
    {
        return (int) config('keymaster.consent_age', 13);
    }
}
