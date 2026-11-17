<?php

namespace App\Domain\Admin;

use Closure;
use InvalidArgumentException;

/**
 * Builds the SQL that gives the runtime account exactly the rights it needs.
 *
 * Kept separate from the console command so the plan can be unit-tested
 * without a database.
 */
class DatabaseGrantPlanner
{
    public const FULL = 'SELECT, INSERT, UPDATE, DELETE';

    public const APPEND_ONLY = 'SELECT, INSERT';

    /**
     * @param  list<string>  $tables
     * @param  list<string>  $appendOnly
     * @param  list<string>  $excluded
     * @param  Closure(string): string  $quote
     * @return list<string>
     */
    public function plan(
        string $database,
        string $username,
        string $host,
        string $password,
        array $tables,
        array $appendOnly,
        array $excluded,
        Closure $quote,
    ): array {
        $this->assertIdentifier($database);
        $this->assertAccountPart($username);
        $this->assertAccountPart($host);

        $account = "'{$username}'@'{$host}'";
        $secret = $quote($password);

        $statements = [
            "CREATE USER IF NOT EXISTS {$account} IDENTIFIED BY {$secret}",
            "ALTER USER {$account} IDENTIFIED BY {$secret}",
            "REVOKE ALL PRIVILEGES, GRANT OPTION FROM {$account}",
        ];

        $sorted = $tables;
        sort($sorted);

        foreach ($sorted as $table) {
            if (in_array($table, $excluded, true)) {
                continue;
            }

            $this->assertIdentifier($table);
            $rights = in_array($table, $appendOnly, true) ? self::APPEND_ONLY : self::FULL;
            $statements[] = "GRANT {$rights} ON `{$database}`.`{$table}` TO {$account}";
        }

        return $statements;
    }

    public function mask(string $sql, string $password): string
    {
        if ($password === '') {
            return $sql;
        }

        return str_replace($password, '********', $sql);
    }

    private function assertIdentifier(string $value): void
    {
        if (! preg_match('/^[A-Za-z0-9_]{1,64}$/', $value)) {
            throw new InvalidArgumentException("Unsafe database identifier [{$value}].");
        }
    }

    private function assertAccountPart(string $value): void
    {
        if (! preg_match('/^[A-Za-z0-9_.%\-]{1,64}$/', $value)) {
            throw new InvalidArgumentException("Unsafe account name part [{$value}].");
        }
    }
}
