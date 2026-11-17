<?php

namespace App\Console\Commands;

use App\Domain\Admin\AuditLogger;
use App\Domain\Identity\Actions\AssignRole;
use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Enums\RoleName;
use App\Models\User;
use App\Models\UserStatistic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;

/**
 * Creates the first administrator in a new environment. There is no
 * self-service path to the admin role.
 */
class CreateAdminCommand extends Command
{
    protected $signature = 'keymaster:create-admin {email} {username} {--name=Administrator}';

    protected $description = 'Create an administrator account';

    public function handle(AssignRole $assignRole, AuditLogger $audit): int
    {
        $data = [
            'email' => strtolower((string) $this->argument('email')),
            'username' => strtolower((string) $this->argument('username')),
            'name' => (string) $this->option('name'),
        ];

        $validator = Validator::make($data, [
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'regex:/^[a-z0-9_]{3,30}$/', 'unique:users,username'],
            'name' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $secret = password(
            label: 'Password for the new administrator',
            required: true,
            validate: fn (string $value) => Validator::make(
                ['password' => $value],
                ['password' => [Password::min(12)->mixedCase()->numbers()->symbols()]],
            )->errors()->first('password') ?: null,
        );

        DB::transaction(function () use ($data, $secret, $assignRole, $audit) {
            $user = User::create([
                ...$data,
                'password' => $secret,
                'account_type' => AccountType::Self,
                'status' => AccountStatus::Active,
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->profile()->create(['display_name' => $data['name']]);
            (new UserStatistic)->forceFill(['user_id' => $user->id])->save();

            $assignRole->handle($user, RoleName::Admin);
            $audit->record('admin.created_from_cli', $user);
        });

        $this->components->info("Administrator {$data['username']} created.");

        return self::SUCCESS;
    }
}
