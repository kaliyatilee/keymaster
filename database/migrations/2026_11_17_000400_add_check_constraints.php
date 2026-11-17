<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * CHECK constraints are the last line of defence against impossible values.
     * MySQL enforces them from 8.0.16 onwards.
     *
     * @var array<string, array<string, string>>
     */
    private array $checks = [
        'lessons' => [
            'chk_lessons_difficulty' => 'difficulty BETWEEN 1 AND 5',
            'chk_lessons_passing_accuracy' => 'passing_accuracy BETWEEN 0 AND 100',
        ],
        'lesson_progress' => [
            'chk_lesson_progress_accuracy' => 'best_accuracy IS NULL OR best_accuracy BETWEEN 0 AND 100',
            'chk_lesson_progress_wpm' => 'best_net_wpm IS NULL OR best_net_wpm >= 0',
        ],
        'typing_sessions' => [
            'chk_typing_sessions_expiry' => 'expires_at > issued_at',
        ],
        'typing_attempts' => [
            'chk_typing_attempts_elapsed' => 'elapsed_ms > 0',
            'chk_typing_attempts_time_order' => 'ended_at >= started_at',
            'chk_typing_attempts_counts' => 'chars_correct + chars_incorrect = chars_typed',
            'chk_typing_attempts_accuracy' => 'accuracy IS NULL OR accuracy BETWEEN 0 AND 100',
            'chk_typing_attempts_wpm' => 'gross_wpm >= 0 AND net_wpm >= 0 AND net_wpm <= gross_wpm',
        ],
        'xp_transactions' => [
            'chk_xp_transactions_nonzero' => 'amount <> 0',
        ],
        'parental_consents' => [
            'chk_parental_consents_expiry' => 'expires_at > created_at',
        ],
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->checks as $table => $constraints) {
            foreach ($constraints as $name => $expression) {
                DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$name}` CHECK ({$expression})");
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->checks as $table => $constraints) {
            foreach (array_keys($constraints) as $name) {
                DB::statement("ALTER TABLE `{$table}` DROP CHECK `{$name}`");
            }
        }
    }
};
