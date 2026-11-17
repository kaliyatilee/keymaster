<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $home = $user->homeRouteName();

        if ($home !== 'dashboard') {
            return to_route($home);
        }

        $user->loadMissing('statistics.level', 'profile');
        $stats = $user->statistics;
        $xp = $stats !== null ? $stats->xp_total : 0;
        $level = $stats?->level;
        $next = Level::query()->where('xp_required', '>', $xp)->orderBy('xp_required')->first();

        return Inertia::render('dashboard', [
            'stats' => [
                'level' => $level !== null ? ['number' => $level->level_number, 'name' => $level->name] : null,
                'xp' => $xp,
                'xp_for_next_level' => $next?->xp_required,
                'next_level_name' => $next?->name,
                'best_wpm' => $stats !== null ? (float) $stats->best_net_wpm : 0.0,
                'average_wpm' => $stats?->averageNetWpm(),
                'average_accuracy' => $stats?->averageAccuracy(),
                'attempts' => $stats !== null ? $stats->attempts_count : 0,
                'lessons_completed' => $stats !== null ? $stats->lessons_completed : 0,
                'practice_minutes' => $stats !== null ? intdiv($stats->total_practice_ms, 60000) : 0,
            ],
            'dailyGoalMinutes' => $user->profile !== null ? $user->profile->daily_goal_minutes : 10,
            // Streaks, challenges and leaderboards arrive in Phase 4.
            'streak' => 0,
        ]);
    }
}
