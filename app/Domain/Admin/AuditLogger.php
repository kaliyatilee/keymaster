<?php

namespace App\Domain\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Writes append-only audit entries for administrative and security-relevant
 * actions. Never pass secrets in $old or $new.
 */
class AuditLogger
{
    public function __construct(private readonly ?Request $request = null) {}

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     */
    public function record(
        string $action,
        ?Model $subject = null,
        array $old = [],
        array $new = [],
        ?User $actor = null,
    ): AuditLog {
        $actor ??= $this->request?->user();

        $log = new AuditLog;
        $log->forceFill([
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'auditable_type' => $subject?->getMorphClass(),
            'auditable_id' => $subject?->getKey(),
            'old_values' => $old === [] ? null : $old,
            'new_values' => $new === [] ? null : $new,
            'ip_address' => $this->request?->ip(),
            'user_agent' => $this->request !== null
                ? mb_substr((string) $this->request->userAgent(), 0, 255)
                : null,
        ])->save();

        return $log;
    }
}
