<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\AgeGate;
use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Enums\RoleName;
use App\Models\Level;
use App\Models\User;
use App\Models\UserStatistic;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Creates a self-registered account with its profile, statistics row and
 * default role in one transaction. Under-age users start in the
 * pending_consent state and a consent request is sent to their guardian.
 */
class RegisterUser
{
    public function __construct(
        private readonly AgeGate $ageGate,
        private readonly AssignRole $assignRole,
        private readonly RequestParentalConsent $requestConsent,
    ) {}

    /**
     * @param  array{name: string, username: string, email: string, password: string, date_of_birth: string, timezone?: string|null, guardian_email?: string|null, wants_teacher_access?: bool}  $data
     */
    public function handle(array $data): User
    {
        $dateOfBirth = Carbon::parse($data['date_of_birth'])->startOfDay();
        $needsConsent = $this->ageGate->needsParentalConsent($dateOfBirth);
        $wantsTeacher = ! $needsConsent && (bool) ($data['wants_teacher_access'] ?? false);

        return DB::transaction(function () use ($data, $dateOfBirth, $needsConsent, $wantsTeacher) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'date_of_birth' => $dateOfBirth,
                'account_type' => AccountType::Self,
                'status' => $needsConsent ? AccountStatus::PendingConsent : AccountStatus::Active,
                'timezone' => $data['timezone'] ?? config('keymaster.default_timezone'),
                'teacher_requested_at' => $wantsTeacher ? now() : null,
            ]);

            $user->profile()->create([
                'display_name' => $data['name'],
                'daily_goal_minutes' => (int) config('keymaster.default_daily_goal_minutes', 10),
                // Minors are hidden from public boards by default.
                'show_on_leaderboards' => ! $needsConsent && $this->ageGate->ageOn($dateOfBirth) >= 18,
            ]);

            $statistics = new UserStatistic;
            $statistics->forceFill([
                'user_id' => $user->getKey(),
                'level_id' => Level::forXp(0)?->getKey(),
            ])->save();

            $this->assignRole->handle($user, RoleName::Student);

            if ($needsConsent) {
                $this->requestConsent->handle($user, (string) $data['guardian_email']);
            }

            return $user;
        });
    }
}
