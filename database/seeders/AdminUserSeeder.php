<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@bmn.local'],
            ['name' => 'Admin BMN', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );

        User::firstOrCreate(
            ['email' => 'pengelola@bmn.local'],
            ['name' => 'Pengelola', 'password' => Hash::make('password'), 'role' => User::ROLE_PENGELOLA]
        );
    }
}
