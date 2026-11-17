<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Enums\ConsentStatus;
use App\Enums\RoleName;
use App\Models\ParentalConsent;
use App\Models\Role;
use App\Models\User;
use App\Models\UserStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Creates a complete account: user, profile, statistics row and the student
 * role, matching what registration produces.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => Str::lower(fake()->unique()->bothify('user_????##')),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'date_of_birth' => now()->subYears(25)->toDateString(),
            'account_type' => AccountType::Self,
            'status' => AccountStatus::Active,
            'timezone' => 'Africa/Harare',
            'remember_token' => Str::random(10),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            if ($user->profile()->doesntExist()) {
                $user->profile()->create(['display_name' => $user->name]);
            }

            if (! UserStatistic::query()->whereKey($user->id)->exists()) {
                (new UserStatistic)->forceFill(['user_id' => $user->id])->save();
            }

            $student = Role::query()->firstOrCreate(
                ['name' => RoleName::Student->value],
                ['label' => RoleName::Student->label()],
            );
            $user->roles()->syncWithoutDetaching([$student->id => ['created_at' => now()]]);
            $user->flushPermissionCache();
        });
    }

    public function withRole(RoleName $role): static
    {
        return $this->afterCreating(function (User $user) use ($role) {
            $model = Role::query()->firstOrCreate(['name' => $role->value], ['label' => $role->label()]);
            $user->roles()->syncWithoutDetaching([$model->id => ['created_at' => now()]]);
            $user->flushPermissionCache();
        });
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function minor(int $age = 12): static
    {
        return $this->state(fn () => ['date_of_birth' => now()->subYears($age)->subMonth()->toDateString()]);
    }

    public function teacherRequested(): static
    {
        return $this->state(fn () => ['teacher_requested_at' => now()]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => AccountStatus::Suspended]);
    }

    public function schoolManaged(): static
    {
        return $this->state(fn () => [
            'account_type' => AccountType::SchoolManaged,
            'email' => null,
            'email_verified_at' => null,
        ]);
    }

    public function pendingConsent(string $guardianEmail = 'guardian@example.com'): static
    {
        return $this->minor()
            ->state(fn () => ['status' => AccountStatus::PendingConsent])
            ->afterCreating(function (User $user) use ($guardianEmail) {
                ParentalConsent::create([
                    'user_id' => $user->id,
                    'guardian_email' => $guardianEmail,
                    'token_hash' => ParentalConsent::hashToken(Str::random(64)),
                    'status' => ConsentStatus::Pending,
                    'expires_at' => now()->addDays(14),
                ]);
            });
    }

    public function withTwoFactor(): static
    {
        return $this->state(fn () => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
