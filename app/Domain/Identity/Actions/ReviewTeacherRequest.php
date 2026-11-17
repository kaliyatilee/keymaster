<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Admin\AuditLogger;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewTeacherRequest
{
    public function __construct(
        private readonly AssignRole $assignRole,
        private readonly AuditLogger $audit,
    ) {}

    public function approve(User $applicant, User $admin): void
    {
        DB::transaction(function () use ($applicant, $admin) {
            $this->assignRole->handle($applicant, RoleName::Teacher, $admin);
            $this->audit->record('teacher.approved', $applicant, actor: $admin);
        });
    }

    public function reject(User $applicant, User $admin): void
    {
        DB::transaction(function () use ($applicant, $admin) {
            $applicant->forceFill(['teacher_requested_at' => null])->save();
            $this->audit->record('teacher.rejected', $applicant, actor: $admin);
        });
    }
}
