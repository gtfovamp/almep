<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Usage:  php artisan admin:create {username} {password}
 * Creates (or updates the password of) an admin user with a bcrypt hash.
 */
class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {username} {password}';
    protected $description = 'Create or update an admin user (bcrypt password)';

    public function handle(): int
    {
        $username = $this->argument('username');
        $password = $this->argument('password');

        $user = AdminUser::updateOrCreate(
            ['username' => $username],
            ['password_hash' => Hash::make($password)]
        );

        $this->info("Admin user '{$user->username}' (id={$user->id}) saved.");
        return self::SUCCESS;
    }
}
