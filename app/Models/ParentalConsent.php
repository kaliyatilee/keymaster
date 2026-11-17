<?php

namespace App\Models;

use App\Enums\ConsentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\CarbonInterface;

/**
 * @property int $id
 * @property int $user_id
 * @property string $guardian_email
 * @property string $token_hash
 * @property ConsentStatus $status
 * @property CarbonInterface $expires_at
 * @property CarbonInterface|null $responded_at
 * @property string|null $responder_ip
 * @property-read User $user
 */
#[Fillable(['user_id', 'guardian_email', 'token_hash', 'status', 'expires_at'])]
#[Hidden(['token_hash'])]
class ParentalConsent extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ConsentStatus::class,
            'expires_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function isOpen(): bool
    {
        return $this->status === ConsentStatus::Pending && $this->expires_at->isFuture();
    }
}
