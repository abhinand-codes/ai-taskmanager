<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@taskmanager.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'John Doe',
            'email'    => 'john@taskmanager.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'Jane Smith',
            'email'    => 'jane@taskmanager.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);
    }
}