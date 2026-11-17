<?php

namespace Tests\Feature\Identity;

use App\Domain\Identity\AgeGate;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AgeGateTest extends TestCase
{
    public function test_age_counts_whole_years_and_respects_birthdays()
    {
        $gate = new AgeGate;
        $on = Carbon::parse('2026-11-17');

        $this->assertSame(12, $gate->ageOn(Carbon::parse('2014-11-18'), $on));
        $this->assertSame(13, $gate->ageOn(Carbon::parse('2013-11-17'), $on));
        $this->assertSame(13, $gate->ageOn(Carbon::parse('2012-11-18'), $on));
    }

    public function test_consent_is_needed_below_the_configured_age()
    {
        config(['keymaster.consent_age' => 16]);
        $gate = new AgeGate;
        $on = Carbon::parse('2026-11-17');

        $this->assertTrue($gate->needsParentalConsent(Carbon::parse('2011-01-01'), $on));
        $this->assertFalse($gate->needsParentalConsent(Carbon::parse('2010-11-17'), $on));
    }

    public function test_very_young_or_future_dates_fail_the_minimum_age()
    {
        $gate = new AgeGate;
        $on = Carbon::parse('2026-11-17');

        $this->assertFalse($gate->meetsMinimumAge(Carbon::parse('2024-01-01'), $on));
        $this->assertTrue($gate->meetsMinimumAge(Carbon::parse('2019-01-01'), $on));
    }
}
