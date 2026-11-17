<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

/**
 * Default level curve. Admins can change it later (Phase 4 admin tools).
 * Each level needs a little more XP than the last, so early levels come
 * quickly and later ones mark real effort.
 */
class LevelsSeeder extends Seeder
{
    /** @var list<array{int, string, int}> */
    private const LEVELS = [
        [1, 'Novice', 0],
        [2, 'Home Row Hero', 100],
        [3, 'Key Explorer', 250],
        [4, 'Steady Fingers', 450],
        [5, 'Word Weaver', 700],
        [6, 'Sentence Smith', 1000],
        [7, 'Rhythm Typist', 1400],
        [8, 'Speed Seeker', 1900],
        [9, 'Accuracy Ace', 2500],
        [10, 'Keyboard Captain', 3200],
        [11, 'Flow Master', 4000],
        [12, 'Lightning Keys', 5000],
        [13, 'Typing Virtuoso', 6200],
        [14, 'Grandmaster', 7600],
        [15, 'KeyMaster', 9200],
    ];

    public function run(): void
    {
        foreach (self::LEVELS as [$number, $name, $xp]) {
            Level::query()->updateOrCreate(
                ['level_number' => $number],
                ['name' => $name, 'xp_required' => $xp],
            );
        }
    }
}
