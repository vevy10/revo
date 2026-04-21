<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (UserRole::seedData() as $role) {
            Role::query()->updateOrCreate(
                ['code' => $role['code']],
                ['name' => $role['name']],
            );
        }

        $adminRole = Role::query()->where('code', UserRole::Admin->value)->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => env('DEFAULT_ADMIN_EMAIL', 'admin@example.com')],
            [
                'role_id' => $adminRole->id,
                'first_name' => 'Default',
                'last_name' => 'Admin',
                'password' => Hash::make(env('DEFAULT_ADMIN_PASSWORD', 'Password1234')),
                'email_verified_at' => now(),
                'activated_at' => now(),
            ],
        );
    }
}
