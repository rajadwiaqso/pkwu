<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories first
        $categories = [
            'Mobile Legends',
            'PUBG Mobile', 
            'Free Fire',
            'Genshin Impact',
            'Valorant',
            'Call of Duty Mobile',
            'Apex Legends Mobile',
            'Roblox'
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName]);
        }

        // Sample gaming products
        $products = [
            [
                'produk_name' => 'Akun Mobile Legends Epic Rank',
                'price' => 150000,
                'stock' => 5,
                'category' => 'Mobile Legends',
                'description' => 'Akun Mobile Legends dengan rank Epic, hero lengkap, skin rare',
                'image' => 'placeholder.jpg',
                'store_name' => 'gamingstore1',
                'sold' => 15
            ],
            [
                'produk_name' => 'Akun PUBG Mobile Conqueror',
                'price' => 300000,
                'stock' => 3,
                'category' => 'PUBG Mobile',
                'description' => 'Akun PUBG Mobile rank Conqueror, skin legendary, weapon upgrade',
                'image' => 'placeholder.jpg',
                'store_name' => 'pubgpro',
                'sold' => 8
            ],
            [
                'produk_name' => 'Free Fire Diamond 1000',
                'price' => 50000,
                'stock' => 20,
                'category' => 'Free Fire',
                'description' => '1000 Diamond Free Fire, instant delivery, aman terpercaya',
                'image' => 'placeholder.jpg',
                'store_name' => 'diamondstore',
                'sold' => 45
            ],
            [
                'produk_name' => 'Genshin Impact Account AR55',
                'price' => 500000,
                'stock' => 2,
                'category' => 'Genshin Impact',
                'description' => 'Akun Genshin Impact AR55, 5 star characters, weapon gacha',
                'image' => 'placeholder.jpg',
                'store_name' => 'genshinpro',
                'sold' => 3
            ],
            [
                'produk_name' => 'Valorant Account Immortal',
                'price' => 800000,
                'stock' => 1,
                'category' => 'Valorant',
                'description' => 'Akun Valorant rank Immortal, skin premium, battle pass complete',
                'image' => 'placeholder.jpg',
                'store_name' => 'valorantking',
                'sold' => 2
            ],
            [
                'produk_name' => 'Call of Duty Mobile Legendary',
                'price' => 200000,
                'stock' => 7,
                'category' => 'Call of Duty Mobile',
                'description' => 'Akun COD Mobile dengan skin legendary, weapon epic',
                'image' => 'placeholder.jpg',
                'store_name' => 'codstore',
                'sold' => 12
            ],
            [
                'produk_name' => 'Akun Mobile Legends Mythic Glory',
                'price' => 1000000,
                'stock' => 1,
                'category' => 'Mobile Legends',
                'description' => 'Akun Mobile Legends Mythic Glory, semua hero, skin limited',
                'image' => 'placeholder.jpg',
                'store_name' => 'mlpro',
                'sold' => 1
            ],
            [
                'produk_name' => 'PUBG Mobile UC 3000',
                'price' => 120000,
                'stock' => 15,
                'category' => 'PUBG Mobile',
                'description' => '3000 UC PUBG Mobile, bisa untuk royal pass dan skin',
                'image' => 'placeholder.jpg',
                'store_name' => 'ucstore',
                'sold' => 25
            ],
            [
                'produk_name' => 'Roblox Robux 10000',
                'price' => 80000,
                'stock' => 30,
                'category' => 'Roblox',
                'description' => '10000 Robux untuk Roblox, instant delivery, murah meriah',
                'image' => 'placeholder.jpg',
                'store_name' => 'robuxstore',
                'sold' => 67
            ],
            [
                'produk_name' => 'Genshin Impact Primogem 8000',
                'price' => 250000,
                'stock' => 10,
                'category' => 'Genshin Impact',
                'description' => '8000 Primogem Genshin Impact untuk gacha character',
                'image' => 'placeholder.jpg',
                'store_name' => 'primostore',
                'sold' => 18
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
