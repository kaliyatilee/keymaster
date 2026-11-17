<?php

namespace Tests\Unit\Identity;

use App\Domain\Admin\DatabaseGrantPlanner;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DatabaseGrantPlannerTest extends TestCase
{
    private function plan(array $tables, string $database = 'keymaster'): array
    {
        return (new DatabaseGrantPlanner)->plan(
            database: $database,
            username: 'keymaster_app',
            host: '%',
            password: 's3cret',
            tables: $tables,
            appendOnly: ['xp_transactions', 'audit_logs'],
            excluded: ['migrations'],
            quote: fn (string $v) => "'".addslashes($v)."'",
        );
    }

    public function test_append_only_tables_get_select_and_insert_only()
    {
        $sql = $this->plan(['users', 'xp_transactions', 'audit_logs', 'migrations']);

        $this->assertContains("GRANT SELECT, INSERT ON `keymaster`.`audit_logs` TO 'keymaster_app'@'%'", $sql);
        $this->assertContains("GRANT SELECT, INSERT ON `keymaster`.`xp_transactions` TO 'keymaster_app'@'%'", $sql);
        $this->assertContains("GRANT SELECT, INSERT, UPDATE, DELETE ON `keymaster`.`users` TO 'keymaster_app'@'%'", $sql);
        $this->assertEmpty(array_filter($sql, fn ($s) => str_contains($s, '`migrations`')));
    }

    public function test_existing_privileges_are_revoked_before_granting()
    {
        $sql = $this->plan(['users']);

        $this->assertStringStartsWith('CREATE USER IF NOT EXISTS', $sql[0]);
        $this->assertSame("REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'keymaster_app'@'%'", $sql[2]);
    }

    public function test_unsafe_identifiers_are_refused()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->plan(['users`; DROP TABLE users; --']);
    }

    public function test_passwords_are_masked_when_printed()
    {
        $planner = new DatabaseGrantPlanner;

        $this->assertSame("IDENTIFIED BY '********'", $planner->mask("IDENTIFIED BY 's3cret'", 's3cret'));
    }
}
