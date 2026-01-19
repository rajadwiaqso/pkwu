<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'raja@raja.com',
            'password' => Hash::make('rajaxrizx'),
            'role' => 'admin',
            'is_active' => true,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
