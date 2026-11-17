<?php

namespace App\Console\Commands;

use App\Domain\Admin\DatabaseGrantPlanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Creates (or refreshes) the least-privilege MySQL account the application
 * runs as. Run it with the migrator account after every `migrate`, because
 * MySQL grants are per table and new tables need their grants added.
 *
 *   php artisan migrate --force && php artisan keymaster:db-grants
 */
class DatabaseGrantsCommand extends Command
{
    protected $signature = 'keymaster:db-grants
        {--print : Print the SQL (password masked) instead of running it}';

    protected $description = 'Create or refresh the least-privilege runtime database account';

    public function handle(DatabaseGrantPlanner $planner): int
    {
        if (DB::getDriverName() !== 'mysql') {
            $this->components->warn('Grants are only managed for MySQL. Skipping.');

            return self::SUCCESS;
        }

        $database = (string) DB::connection()->getDatabaseName();
        $config = config('keymaster.database');
        $password = (string) ($config['app_password'] ?? '');

        if ($password === '' && ! $this->option('print')) {
            $this->components->error('Set DB_APP_PASSWORD before creating the runtime account.');

            return self::FAILURE;
        }

        $tables = collect(DB::select(
            'SELECT TABLE_NAME AS name FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = ?',
            [$database, 'BASE TABLE'],
        ))->pluck('name')->map(fn ($name) => (string) $name)->all();

        $statements = $planner->plan(
            database: $database,
            username: (string) $config['app_username'],
            host: (string) $config['app_host'],
            password: $password,
            tables: $tables,
            appendOnly: $config['append_only_tables'],
            excluded: $config['excluded_tables'],
            quote: fn (string $value) => DB::connection()->getPdo()->quote($value),
        );

        if ($this->option('print')) {
            foreach ($statements as $sql) {
                $this->line($planner->mask($sql, $password).';');
            }

            return self::SUCCESS;
        }

        foreach ($statements as $sql) {
            DB::unprepared($sql);
        }

        $this->components->info(sprintf(
            'Granted %s on %d tables (%d append-only).',
            $config['app_username'],
            count(array_diff($tables, $config['excluded_tables'])),
            count(array_intersect($tables, $config['append_only_tables'])),
        ));

        return self::SUCCESS;
    }
}
