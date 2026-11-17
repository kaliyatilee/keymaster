<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $level_number
 * @property string $name
 * @property int $xp_required
 * @property string|null $badge_path
 */
#[Fillable(['level_number', 'name', 'xp_required', 'badge_path'])]
class Level extends Model
{
    public static function forXp(int $xp): ?self
    {
        return static::query()
            ->where('xp_required', '<=', $xp)
            ->orderByDesc('xp_required')
            ->first();
    }
}
