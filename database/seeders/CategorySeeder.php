<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'description' => 'Produk elektronik seperti gadget, aksesoris, dan perangkat digital',
                'icon' => 'cpu',
            ],
            [
                'name' => 'Fashion',
                'description' => 'Pakaian, sepatu, dan aksesoris fashion',
                'icon' => 'bag',
            ],
            [
                'name' => 'Makanan & Minuman',
                'description' => 'Produk makanan dan minuman',
                'icon' => 'cup-hot',
            ],
            [
                'name' => 'Kesehatan',
                'description' => 'Produk kesehatan dan kecantikan',
                'icon' => 'heart-pulse',
            ],
            [
                'name' => 'Rumah Tangga',
                'description' => 'Peralatan dan perlengkapan rumah tangga',
                'icon' => 'house',
            ],
            [
                'name' => 'Olahraga',
                'description' => 'Perlengkapan dan peralatan olahraga',
                'icon' => 'bicycle',
            ],
            [
                'name' => 'Buku & Alat Tulis',
                'description' => 'Buku, alat tulis, dan perlengkapan sekolah',
                'icon' => 'book',
            ],
            [
                'name' => 'Hobi & Mainan',
                'description' => 'Mainan dan perlengkapan hobi',
                'icon' => 'controller',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
                'is_active' => true,
            ]);
        }
    }
}
