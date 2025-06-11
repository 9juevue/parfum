<?php

namespace Database\Seeders;

use App\Enums\User\RoleEnum;
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
        $user = User::factory()->create([
            'name' => 'Администратор',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        $adminRole = Role::query()->firstOrCreate([
            'title' => RoleEnum::ADMIN,
        ]);

        $userRole = Role::query()->firstOrCreate([
            'title' => RoleEnum::USER,
        ]);

        $user->roles()->sync([$adminRole->id]);
    }
}
