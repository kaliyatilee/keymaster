<?php

namespace App\Domain\Identity\Listeners;

use App\Domain\Admin\AuditLogger;
use App\Domain\Identity\Actions\AssignRole;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Str;

/**
 * A teacher request from a verified address on an approved school domain
 * is granted without waiting for an administrator.
 */
class AutoApproveTeacherByDomain
{
    public function __construct(
        private readonly AssignRole $assignRole,
        private readonly AuditLogger $audit,
    ) {}

    public function handle(Verified $event): void
    {
        $user = $event->user;

        if (! $user instanceof User || ! $user->isAwaitingTeacherApproval() || $user->email === null) {
            return;
        }

        $domain = Str::lower(Str::after($user->email, '@'));
        $allowed = array_map(Str::lower(...), config('keymaster.teacher_auto_approve_domains', []));

        if (! in_array($domain, $allowed, true)) {
            return;
        }

        $this->assignRole->handle($user, RoleName::Teacher);
        $this->audit->record('teacher.auto_approved', $user, new: ['domain' => $domain], actor: $user);
    }
}
