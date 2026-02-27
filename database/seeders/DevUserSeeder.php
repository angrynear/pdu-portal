<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@pdu.test'],
            [
                'name' => 'System Administrator',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'account_status' => 'active',
            ]
        );
    }
}
