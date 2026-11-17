<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Admin\AuditLogger;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Removes personal data and soft-deletes the account. Typing attempts and
 * the XP ledger are append-only, so they are kept but no longer linked to
 * anything that identifies the person.
 */
class DeleteAccount
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function handle(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->audit->record('account.deleted', $user, actor: $user);

            $user->profile?->delete();
            $user->roles()->detach();
            $user->parentalConsents()->delete();

            $user->forceFill([
                'name' => 'Deleted user',
                'username' => 'deleted_'.$user->getKey().'_'.Str::lower(Str::random(6)),
                'email' => null,
                'email_verified_at' => null,
                'date_of_birth' => null,
                'password' => Str::random(64),
                'remember_token' => null,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();

            $user->delete();
        });
    }
}
