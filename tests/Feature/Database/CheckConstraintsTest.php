<?php

namespace Tests\Feature\Database;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CheckConstraintsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('CHECK constraints are created for MySQL only.');
        }
    }

    public function test_zero_xp_transactions_are_rejected()
    {
        $user = User::factory()->create();

        $this->expectException(QueryException::class);

        DB::table('xp_transactions')->insert([
            'user_id' => $user->id,
            'amount' => 0,
            'rule_key' => 'test',
            'idempotency_key' => 'zero',
        ]);
    }

    public function test_duplicate_xp_awards_are_rejected_by_the_idempotency_key()
    {
        $user = User::factory()->create();
        $row = ['user_id' => $user->id, 'amount' => 5, 'rule_key' => 'test', 'idempotency_key' => 'lesson:1:user:1'];

        DB::table('xp_transactions')->insert($row);

        $this->expectException(QueryException::class);
        DB::table('xp_transactions')->insert($row);
    }

    public function test_typing_sessions_cannot_expire_before_they_are_issued()
    {
        $user = User::factory()->create();

        $this->expectException(QueryException::class);

        DB::table('typing_sessions')->insert([
            'id' => '01JZ0000000000000000000000',
            'user_id' => $user->id,
            'context_type' => 'test',
            'content_type' => 'text_passage',
            'content_id' => 1,
            'content_checksum' => str_repeat('a', 64),
            'rules' => '{}',
            'issued_at' => now(),
            'expires_at' => now()->subMinute(),
        ]);
    }

    public function test_lesson_difficulty_must_be_between_one_and_five()
    {
        $courseId = DB::table('courses')->insertGetId([
            'slug' => 'test', 'title' => 'Test', 'level' => 'beginner',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('lessons')->insert([
            'course_id' => $courseId, 'slug' => 'l1', 'title' => 'L1',
            'practice_mode' => 'home_row', 'difficulty' => 9,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
