<?php

namespace Tests\Feature\Database;

use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Proves the MySQL replacement for row-level security: the runtime account
 * can read and add to append-only tables but can never change or remove
 * what is there, and cannot change the schema.
 *
 * Needs DB_APP_PASSWORD and a migrator account allowed to create users and
 * grant privileges (CI uses root). Skipped otherwise.
 */
class AppAccountGrantsTest extends TestCase
{
    /*
     * GRANT and CREATE USER commit implicitly, so this test cannot run inside
     * RefreshDatabase's transaction. It rebuilds the schema itself before
     * and after, leaving the same clean state other tests expect.
     */

    private const APP_CONNECTION = 'mysql_app_test';

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() !== 'mysql' || blank(config('keymaster.database.app_password'))) {
            $this->markTestSkipped('Set DB_APP_PASSWORD and use MySQL to run the grants test.');
        }

        $this->rebuildDatabase();
        $this->beforeApplicationDestroyed(fn () => $this->rebuildDatabase());

        $this->assertSame(0, Artisan::call('keymaster:db-grants'));

        config(['database.connections.'.self::APP_CONNECTION => array_merge(
            config('database.connections.mysql'),
            [
                'username' => config('keymaster.database.app_username'),
                'password' => config('keymaster.database.app_password'),
            ],
        )]);
        DB::purge(self::APP_CONNECTION);
    }

    private function rebuildDatabase(): void
    {
        $this->artisan('migrate:fresh', ['--seed' => true, '--seeder' => ReferenceDataSeeder::class])->assertSuccessful();
    }

    private function appDb(): Connection
    {
        return DB::connection(self::APP_CONNECTION);
    }

    private function assertDenied(callable $query): void
    {
        try {
            $query();
        } catch (QueryException $e) {
            // 1142: command denied to user for table.
            $this->assertSame(1142, $e->errorInfo[1] ?? null, $e->getMessage());

            return;
        }

        $this->fail('The runtime account was allowed to run a statement it must not run.');
    }

    public function test_append_only_tables_accept_inserts_and_reads_but_not_changes()
    {
        $user = User::factory()->create();

        $this->appDb()->table('xp_transactions')->insert([
            'user_id' => $user->id,
            'amount' => 10,
            'rule_key' => 'test',
            'idempotency_key' => 'grants-test-1',
        ]);

        $this->assertSame(1, $this->appDb()->table('xp_transactions')->where('user_id', $user->id)->count());

        $this->assertDenied(fn () => $this->appDb()->table('xp_transactions')->where('user_id', $user->id)->update(['amount' => 1000]));
        $this->assertDenied(fn () => $this->appDb()->table('xp_transactions')->where('user_id', $user->id)->delete());

        $this->appDb()->table('audit_logs')->insert(['action' => 'grants.test']);
        $this->assertDenied(fn () => $this->appDb()->table('audit_logs')->update(['action' => 'tampered']));
        $this->assertDenied(fn () => $this->appDb()->table('audit_logs')->delete());

        $this->assertDenied(fn () => $this->appDb()->table('typing_attempts')->update(['net_wpm' => 999]));

        $this->assertSame(10, (int) DB::table('xp_transactions')->where('user_id', $user->id)->value('amount'));
    }

    public function test_ordinary_tables_allow_normal_writes()
    {
        $user = User::factory()->create();

        $this->appDb()->table('profiles')->where('user_id', $user->id)->update(['display_name' => 'Changed']);

        $this->assertSame('Changed', DB::table('profiles')->where('user_id', $user->id)->value('display_name'));
    }

    public function test_runtime_account_cannot_change_the_schema()
    {
        $this->expectException(QueryException::class);

        $this->appDb()->statement('DROP TABLE xp_transactions');
    }
}
