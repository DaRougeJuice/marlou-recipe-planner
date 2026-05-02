<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@marlou.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@marlou.com',
                'password' => Hash::make('Admin@1234'),
                'role'     => 'admin',
            ]
        );
    }
}