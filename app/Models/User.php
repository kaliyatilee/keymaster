<?php

namespace App\Models;

use App\Casts\EncryptedDate;
use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Enums\RoleName;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string|null $email
 * @property CarbonInterface|null $email_verified_at
 * @property string $password
 * @property CarbonInterface|null $date_of_birth
 * @property AccountType $account_type
 * @property AccountStatus $status
 * @property string $timezone
 * @property CarbonInterface|null $timezone_changed_at
 * @property CarbonInterface|null $teacher_requested_at
 * @property CarbonInterface|null $last_login_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property CarbonInterface|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 * @property CarbonInterface|null $deleted_at
 * @property-read Profile|null $profile
 * @property-read UserStatistic|null $statistics
 * @property-read Collection<int, Role> $roles
 */
#[Fillable(['name', 'username', 'email', 'password', 'date_of_birth', 'account_type', 'status', 'timezone', 'teacher_requested_at'])]
#[Hidden(['password', 'date_of_birth', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, SoftDeletes, TwoFactorAuthenticatable;

    /** @var array<string, bool>|null */
    private ?array $permissionCache = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => EncryptedDate::class,
            'account_type' => AccountType::class,
            'status' => AccountStatus::class,
            'timezone_changed_at' => 'datetime',
            'teacher_requested_at' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /** @return HasOne<Profile, $this> */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /** @return HasOne<UserStatistic, $this> */
    public function statistics(): HasOne
    {
        return $this->hasOne(UserStatistic::class);
    }

    /** @return HasMany<ParentalConsent, $this> */
    public function parentalConsents(): HasMany
    {
        return $this->hasMany(ParentalConsent::class);
    }

    /** @return BelongsToMany<Role, $this> */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withPivot('granted_by', 'created_at');
    }

    public function hasRole(RoleName|string $role): bool
    {
        $name = $role instanceof RoleName ? $role->value : $role;

        return $this->loadMissing('roles')->roles->contains('name', $name);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->permissionCache === null) {
            $this->permissionCache = $this->roles()
                ->with('permissions:id,name')
                ->get()
                ->flatMap(fn (Role $role) => $role->permissions->pluck('name'))
                ->unique()
                ->mapWithKeys(fn (string $name) => [$name => true])
                ->all();
        }

        return isset($this->permissionCache[$permission]);
    }

    /**
     * @return list<string>
     */
    public function permissionNames(): array
    {
        $this->hasPermission('');

        return array_keys($this->permissionCache ?? []);
    }

    public function flushPermissionCache(): void
    {
        $this->permissionCache = null;
        $this->unsetRelation('roles');
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::Active;
    }

    public function isPendingConsent(): bool
    {
        return $this->status === AccountStatus::PendingConsent;
    }

    public function isSchoolManaged(): bool
    {
        return $this->account_type === AccountType::SchoolManaged;
    }

    public function isAwaitingTeacherApproval(): bool
    {
        return $this->teacher_requested_at !== null && ! $this->hasRole(RoleName::Teacher);
    }

    /**
     * Age in whole years, or null when no date of birth is stored.
     */
    public function age(?CarbonInterface $at = null): ?int
    {
        if ($this->date_of_birth === null) {
            return null;
        }

        return (int) $this->date_of_birth->diffInYears($at ?? now());
    }

    public function isMinor(): bool
    {
        $age = $this->age();

        // School-managed accounts are treated as minors by default.
        return $this->isSchoolManaged() || ($age !== null && $age < 18);
    }

    /**
     * School-managed students have no email address, so verification does
     * not apply to them.
     */
    public function hasVerifiedEmail(): bool
    {
        if ($this->isSchoolManaged()) {
            return true;
        }

        return $this->email_verified_at !== null;
    }

    public function sendEmailVerificationNotification(): void
    {
        if ($this->email !== null) {
            parent::sendEmailVerificationNotification();
        }
    }

    /**
     * The landing page for this user's highest role.
     */
    public function homeRouteName(): string
    {
        return match (true) {
            $this->hasRole(RoleName::Admin) => 'admin.dashboard',
            $this->hasRole(RoleName::Teacher) => 'teacher.dashboard',
            default => 'dashboard',
        };
    }
}
