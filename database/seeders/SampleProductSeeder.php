<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Support\Facades\Hash;

class SampleProductSeeder extends Seeder
{
    public function run()
    {
        // Create sample users and sellers first
        $users = [
            [
                'name' => 'ProGamer Store',
                'email' => 'progamer@example.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Gaming Hub',
                'email' => 'gaminghub@example.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Elite Gaming',
                'email' => 'elite@example.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'is_verified' => true,
                'is_active' => true,
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Create corresponding seller record if it doesn't exist
            Seller::firstOrCreate(
                ['email' => $user->email],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'credits' => 0,
                    'sold_total' => 0,
                    'product_total' => 0,
                ]
            );
        }

        // Get categories
        $categories = Category::all();
        $users = User::where('role', 'seller')->get();

        // Sample products data with gaming theme
        $products = [
            // PUBG Products
            [
                'name' => 'PUBG Mobile Account Level 80 - Conqueror Tier',
                'description' => 'High-level PUBG Mobile account with Conqueror rank, rare skins, and premium items. Perfect for competitive gaming.',
                'price' => 1500000,
                'stock' => 5,
                'category_name' => 'pubg',
                'store_name' => 'ProGamer Store',
                'type' => 'account',
                'product_details' => json_encode([
                    'level' => 80,
                    'tier' => 'Conqueror',
                    'kd_ratio' => '3.2',
                    'server' => 'Asia',
                    'skins' => 'Glacier M416, Dragon AKM'
                ]),
                'images' => json_encode(['/storage/images/pubg-account-1.jpg']),
                'sold' => 15,
                'views' => 250,
            ],
            [
                'name' => 'PUBG UC 8100 + 2040 Bonus',
                'description' => 'Premium UC package for PUBG Mobile with bonus UC. Get the best deals for in-game currency.',
                'price' => 1200000,
                'stock' => 20,
                'category_name' => 'pubg',
                'store_name' => 'Gaming Hub',
                'type' => 'services',
                'product_details' => json_encode([
                    'amount' => '8100 + 2040 bonus',
                    'region' => 'Global',
                    'delivery_time' => '5-15 minutes'
                ]),
                'images' => json_encode(['/storage/images/pubg-uc.jpg']),
                'sold' => 45,
                'views' => 380,
            ],
            
            // Mobile Legends Products
            [
                'name' => 'Mobile Legends Mythic Account - 500+ Skins',
                'description' => 'Premium Mobile Legends account with Mythic rank and 500+ hero skins including limited editions.',
                'price' => 2500000,
                'stock' => 3,
                'category_name' => 'mobile-legends',
                'store_name' => 'Elite Gaming',
                'type' => 'account',
                'product_details' => json_encode([
                    'rank' => 'Mythic Glory',
                    'mmr' => '1200+',
                    'heroes' => 'All heroes unlocked',
                    'skins' => '500+ including limited',
                    'emblems' => 'Max level all emblems'
                ]),
                'images' => json_encode(['/storage/images/ml-account-1.jpg']),
                'sold' => 8,
                'views' => 420,
            ],
            [
                'name' => 'Mobile Legends Diamonds 2180 + Bonus',
                'description' => 'Premium diamond package for Mobile Legends with additional bonus diamonds.',
                'price' => 600000,
                'stock' => 50,
                'category_name' => 'mobile-legends',
                'store_name' => 'ProGamer Store',
                'type' => 'services',
                'product_details' => json_encode([
                    'amount' => '2180 + bonus',
                    'server' => 'Indonesia',
                    'delivery_time' => 'Instant'
                ]),
                'images' => json_encode(['/storage/images/ml-diamonds.jpg']),
                'sold' => 89,
                'views' => 650,
            ],
            
            // Free Fire Products
            [
                'name' => 'Free Fire Account Heroic Rank - Elite Pass',
                'description' => 'High-tier Free Fire account with Heroic rank and multiple Elite Pass collections.',
                'price' => 800000,
                'stock' => 8,
                'category_name' => 'free-fire',
                'store_name' => 'Gaming Hub',
                'type' => 'account',
                'product_details' => json_encode([
                    'rank' => 'Heroic',
                    'level' => '65',
                    'elite_pass' => 'Season 1-40',
                    'pets' => 'All pets unlocked',
                    'characters' => '15+ characters'
                ]),
                'images' => json_encode(['/storage/images/ff-account-1.jpg']),
                'sold' => 25,
                'views' => 330,
            ],
            [
                'name' => 'Free Fire Diamonds 2200',
                'description' => 'Premium diamond package for Free Fire. Fast delivery and secure transaction.',
                'price' => 450000,
                'stock' => 30,
                'category_name' => 'free-fire',
                'store_name' => 'Elite Gaming',
                'type' => 'services',
                'product_details' => json_encode([
                    'amount' => '2200',
                    'region' => 'Indonesia',
                    'delivery_time' => '1-5 minutes'
                ]),
                'images' => json_encode(['/storage/images/ff-diamonds.jpg']),
                'sold' => 67,
                'views' => 520,
            ],
            
            // Roblox Products
            [
                'name' => 'Roblox Account Premium - Rare Items',
                'description' => 'Premium Roblox account with rare limited items and Robux balance.',
                'price' => 1800000,
                'stock' => 4,
                'category_name' => 'roblox',
                'store_name' => 'ProGamer Store',
                'type' => 'account',
                'product_details' => json_encode([
                    'premium' => 'Active',
                    'robux_balance' => '5000+',
                    'limited_items' => '50+ rare limiteds',
                    'account_age' => '5+ years',
                    'games' => '200+ games played'
                ]),
                'images' => json_encode(['/storage/images/roblox-account-1.jpg']),
                'sold' => 12,
                'views' => 290,
            ],
            [
                'name' => 'Robux 4500 Package',
                'description' => 'Premium Robux package for Roblox. Instant delivery to your account.',
                'price' => 900000,
                'stock' => 25,
                'category_name' => 'roblox',
                'store_name' => 'Gaming Hub',
                'type' => 'services',
                'product_details' => json_encode([
                    'amount' => '4500',
                    'delivery_time' => 'Instant',
                    'region' => 'Global'
                ]),
                'images' => json_encode(['/storage/images/robux-package.jpg']),
                'sold' => 34,
                'views' => 410,
            ],
        ];

        foreach ($products as $productData) {
            $category = $categories->where('slug', $productData['category_name'])->first();
            $user = $users->where('name', $productData['store_name'])->first();
            
            if ($category && $user) {
                Product::create([
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'category' => $category->slug,
                    'category_id' => $category->id,
                    'store' => $productData['store_name'],
                    'type' => $productData['type'],
                    'product_details' => $productData['product_details'],
                    'img' => '/storage/images/default-product.jpg',
                    'images' => $productData['images'],
                    'sold' => $productData['sold'],
                    'views' => $productData['views'],
                    'status' => 'active',
                    'average_rating' => rand(40, 50) / 10, // Random rating between 4.0-5.0
                    'reviews_count' => rand(5, 25),
                ]);
            }
        }

        $this->command->info('Sample products created successfully!');
    }
}