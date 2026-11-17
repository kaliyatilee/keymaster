<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Cached totals for dashboards. Written only by domain listeners and
 * rebuildable from typing_attempts and xp_transactions.
 *
 * @property int $user_id
 * @property int $xp_total
 * @property int|null $level_id
 * @property string $best_net_wpm
 * @property int $attempts_count
 * @property string $sum_net_wpm
 * @property string $sum_accuracy
 * @property int $total_practice_ms
 * @property int $lessons_completed
 * @property-read Level|null $level
 */
class UserStatistic extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    /** Nothing is mass-assignable: totals change only through domain code. */
    protected $guarded = ['*'];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Level, $this> */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function averageNetWpm(): ?float
    {
        return $this->attempts_count > 0
            ? round((float) $this->sum_net_wpm / $this->attempts_count, 2)
            : null;
    }

    public function averageAccuracy(): ?float
    {
        return $this->attempts_count > 0
            ? round((float) $this->sum_accuracy / $this->attempts_count, 2)
            : null;
    }
}
