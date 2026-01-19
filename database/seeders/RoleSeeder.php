<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $roles = [
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Basic operational access for day-to-day tasks',
                'level' => Role::LEVEL_STAFF,
                'permissions' => Role::getDefaultPermissions(Role::LEVEL_STAFF),
                'color_theme' => '#2563EB',
                'is_active' => true,
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Management access with team oversight capabilities',
                'level' => Role::LEVEL_MANAGER,
                'permissions' => Role::getDefaultPermissions(Role::LEVEL_MANAGER),
                'color_theme' => '#0D9488',
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system administration capabilities',
                'level' => Role::LEVEL_ADMIN,
                'permissions' => Role::getDefaultPermissions(Role::LEVEL_ADMIN),
                'color_theme' => '#EA580C',
                'is_active' => true,
            ],
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Complete system control with multi-store management',
                'level' => Role::LEVEL_SUPER_ADMIN,
                'permissions' => Role::getDefaultPermissions(Role::LEVEL_SUPER_ADMIN),
                'color_theme' => '#DC2626',
                'is_active' => true,
            ],
            [
                'name' => 'owner',
                'display_name' => 'Owner/Developer',
                'description' => 'Ultimate system access with developer tools',
                'level' => Role::LEVEL_OWNER,
                'permissions' => Role::getDefaultPermissions(Role::LEVEL_OWNER),
                'color_theme' => '#7C3AED',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        // Create default admin user if doesn't exist
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole && !User::where('email', 'admin@marketplace.com')->exists()) {
            User::create([
                'name' => 'System Administrator',
                'username' => 'admin',
                'email' => 'admin@marketplace.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'role_id' => $adminRole->id,
                'verification_code' => '',
                'is_verified' => true,
                'is_seller' => false,
                'is_active' => true,
                'profile_picture' => 'default.jpg',
                'phone' => '',
                'timezone' => 'Asia/Jakarta',
                'language' => 'id',
                'theme_preference' => 'light',
            ]);
        }

        // Create test users for each role
        $testUsers = [
            [
                'name' => 'Staff User',
                'username' => 'staff',
                'email' => 'staff@marketplace.com',
                'role' => 'staff',
                'password' => 'staff123',
            ],
            [
                'name' => 'Manager User',
                'username' => 'manager',
                'email' => 'manager@marketplace.com',
                'role' => 'manager',
                'password' => 'manager123',
            ],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@marketplace.com',
                'role' => 'super_admin',
                'password' => 'super123',
            ],
            [
                'name' => 'Owner',
                'username' => 'owner',
                'email' => 'owner@marketplace.com',
                'role' => 'owner',
                'password' => 'owner123',
            ],
        ];

        foreach ($testUsers as $userData) {
            $role = Role::where('name', $userData['role'])->first();
            
            if ($role && !User::where('email', $userData['email'])->exists()) {
                User::create([
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                    'role_id' => $role->id,
                    'verification_code' => '',
                    'is_verified' => true,
                    'is_seller' => false,
                    'is_active' => true,
                    'profile_picture' => 'default.jpg',
                    'phone' => '',
                    'timezone' => 'Asia/Jakarta',
                    'language' => 'id',
                    'theme_preference' => 'light',
                ]);
            }
        }

        $this->command->info('Roles and default users created successfully!');
        $this->command->info('Admin credentials: admin@marketplace.com / admin123');
        $this->command->info('Test users created for each role level.');
    }
}
