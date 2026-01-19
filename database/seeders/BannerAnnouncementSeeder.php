<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Announcement;
use Illuminate\Database\Seeder;

class BannerAnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Banners
        Banner::create([
            'title' => 'Grand Opening',
            'image' => 'banners/banner-1.jpg', // placeholder
            'link' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'Promo Member',
            'image' => 'banners/banner-2.jpg',
            'link' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'Free Ongkir',
            'image' => 'banners/banner-3.jpg',
            'link' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        // Announcements
        Announcement::create([
            'title' => 'Selamat Datang di Toko Digital Raja!',
            'content' => 'Terima kasih telah mengunjungi toko kami. Daftar sekarang dan verifikasi email untuk mendapatkan harga spesial member!',
            'type' => 'info',
            'is_active' => true,
            'expires_at' => null,
        ]);

        Announcement::create([
            'title' => 'Promo Akhir Bulan',
            'content' => 'Dapatkan diskon tambahan 10% untuk semua produk elektronik. Promo berlaku sampai akhir bulan ini!',
            'type' => 'success',
            'is_active' => true,
            'expires_at' => now()->endOfMonth(),
        ]);
    }
}
