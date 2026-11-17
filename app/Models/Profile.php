<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property string $display_name
 * @property string|null $avatar_path
 * @property string|null $bio
 * @property string|null $country
 * @property bool $is_public
 * @property bool $show_on_leaderboards
 * @property string $keyboard_layout
 * @property int $daily_goal_minutes
 */
#[Fillable(['display_name', 'avatar_path', 'bio', 'country', 'is_public', 'show_on_leaderboards', 'keyboard_layout', 'daily_goal_minutes'])]
class Profile extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'show_on_leaderboards' => 'boolean',
            'daily_goal_minutes' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
