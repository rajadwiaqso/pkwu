<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@tokodigitalraja.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Admin
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@tokodigitalraja.com',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Verified Buyer
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Unverified Buyer
        User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@example.com',
            'phone' => '081234567893',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'email_verified_at' => null,
            'is_active' => true,
        ]);
    }
}
