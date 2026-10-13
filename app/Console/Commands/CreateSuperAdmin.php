<?php

namespace App\Console\Commands;

use App\Models\PlatformActivity;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateSuperAdmin extends Command
{
    protected $signature = 'saas:superadmin {email : Email for a new platform administrator} {--name= : Unique username}';

    protected $description = 'Create a separate superadmin account using a hidden password prompt';

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Run this command interactively to set the password securely.');

            return self::FAILURE;
        }
        $name = $this->option('name') ?: $this->ask('Superadmin username');
        $password = $this->secret('Password (12+ characters, uppercase, lowercase, and a number)');
        $confirmation = $this->secret('Confirm password');
        $validator = Validator::make(['name' => $name, 'email' => $this->argument('email'), 'password' => $password, 'password_confirmation' => $confirmation], ['name' => ['required', 'string', 'max:255', 'unique:users,name'], 'email' => ['required', 'email', 'unique:users,email'], 'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()]]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }
        DB::transaction(function () use ($name, $password): void {
            $user = User::create(['name' => $name, 'email' => $this->argument('email'), 'password' => $password, 'role' => 'superadmin', 'shop_id' => null, 'is_active' => true]);
            PlatformActivity::create(['user_id' => $user->id, 'action' => 'superadmin.created', 'description' => 'Platform administrator created using the secure setup command.']);
        });
        $this->info('Superadmin created. Sign in through the existing admin login page.');

        return self::SUCCESS;
    }
}
