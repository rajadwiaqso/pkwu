<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TokoProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        $products = [
            // Elektronik
            [
                'category' => 'Elektronik',
                'name' => 'Earbuds Wireless Pro',
                'short_description' => 'Earbuds wireless dengan noise cancelling',
                'description' => "Earbuds wireless dengan teknologi Active Noise Cancelling (ANC) untuk pengalaman audio yang immersive.\n\nFitur:\n- Active Noise Cancelling\n- Battery 24 jam dengan case\n- Touch control\n- IPX4 water resistant\n- Bluetooth 5.2",
                'price' => 350000,
                'verified_price' => 299000,
                'stock' => 50,
                'is_featured' => true,
            ],
            [
                'category' => 'Elektronik',
                'name' => 'Smartwatch Sport Edition',
                'short_description' => 'Smartwatch dengan berbagai fitur kesehatan',
                'description' => "Smartwatch canggih dengan berbagai sensor kesehatan dan fitur olahraga.\n\nFitur:\n- Heart rate monitor\n- SpO2 sensor\n- GPS tracking\n- 100+ mode olahraga\n- Water resistant 50m",
                'price' => 899000,
                'verified_price' => 799000,
                'stock' => 30,
                'is_featured' => true,
            ],
            [
                'category' => 'Elektronik',
                'name' => 'Power Bank 20000mAh',
                'short_description' => 'Power bank kapasitas besar dengan fast charging',
                'description' => "Power bank dengan kapasitas 20000mAh dan teknologi fast charging.\n\nFitur:\n- 20000mAh capacity\n- 22.5W fast charging\n- 3 output ports\n- LED indicator\n- Compact design",
                'price' => 250000,
                'verified_price' => 225000,
                'stock' => 100,
                'is_featured' => false,
            ],
            
            // Fashion
            [
                'category' => 'Fashion',
                'name' => 'Kaos Polos Premium',
                'short_description' => 'Kaos polos dengan bahan cotton combed 30s',
                'description' => "Kaos polos premium dengan bahan cotton combed 30s yang nyaman dipakai.\n\nDetail:\n- Bahan cotton combed 30s\n- Tersedia ukuran S, M, L, XL\n- Berbagai pilihan warna\n- Jahitan rapi\n- Tidak mudah luntur",
                'price' => 85000,
                'verified_price' => 75000,
                'stock' => 200,
                'is_featured' => true,
            ],
            [
                'category' => 'Fashion',
                'name' => 'Topi Baseball Classic',
                'short_description' => 'Topi baseball dengan desain minimalis',
                'description' => "Topi baseball dengan desain minimalis dan bahan berkualitas.\n\nDetail:\n- Bahan kanvas premium\n- Adjustable strap\n- One size fits all\n- Berbagai pilihan warna",
                'price' => 75000,
                'verified_price' => 65000,
                'stock' => 150,
                'is_featured' => false,
            ],
            
            // Makanan & Minuman
            [
                'category' => 'Makanan & Minuman',
                'name' => 'Kopi Arabica Premium 250g',
                'short_description' => 'Biji kopi arabica pilihan dari Aceh Gayo',
                'description' => "Biji kopi arabica premium dari dataran tinggi Aceh Gayo.\n\nDetail:\n- 100% Arabica\n- Medium roast\n- Single origin Aceh Gayo\n- Berat 250g\n- Fresh roasted weekly",
                'price' => 95000,
                'verified_price' => 85000,
                'stock' => 75,
                'is_featured' => true,
            ],
            [
                'category' => 'Makanan & Minuman',
                'name' => 'Snack Box Premium',
                'short_description' => 'Paket snack premium untuk oleh-oleh',
                'description' => "Paket snack premium berisi berbagai cemilan pilihan.\n\nIsi:\n- Keripik singkong\n- Kacang mete\n- Cokelat praline\n- Cookies homemade\n- Kemasan eksklusif",
                'price' => 150000,
                'verified_price' => 135000,
                'stock' => 50,
                'is_featured' => false,
            ],
            
            // Kesehatan
            [
                'category' => 'Kesehatan',
                'name' => 'Hand Sanitizer 500ml',
                'short_description' => 'Hand sanitizer dengan kandungan 70% alkohol',
                'description' => "Hand sanitizer dengan formula efektif membunuh kuman.\n\nDetail:\n- Kandungan 70% alkohol\n- Dilengkapi moisturizer\n- Aroma fresh\n- Cepat kering\n- Isi 500ml",
                'price' => 45000,
                'verified_price' => 39000,
                'stock' => 200,
                'is_featured' => false,
            ],
            [
                'category' => 'Kesehatan',
                'name' => 'Vitamin C 1000mg (30 tablet)',
                'short_description' => 'Suplemen vitamin C untuk daya tahan tubuh',
                'description' => "Suplemen vitamin C dosis tinggi untuk meningkatkan daya tahan tubuh.\n\nDetail:\n- 1000mg per tablet\n- Isi 30 tablet\n- BPOM certified\n- Cocok untuk dewasa",
                'price' => 125000,
                'verified_price' => 110000,
                'stock' => 100,
                'is_featured' => true,
            ],
            
            // Rumah Tangga
            [
                'category' => 'Rumah Tangga',
                'name' => 'Set Wadah Penyimpanan (5 pcs)',
                'short_description' => 'Set wadah penyimpanan makanan anti bocor',
                'description' => "Set wadah penyimpanan dengan berbagai ukuran.\n\nDetail:\n- Isi 5 pieces berbagai ukuran\n- BPA free\n- Anti bocor\n- Microwave safe\n- Stackable design",
                'price' => 185000,
                'verified_price' => 165000,
                'stock' => 60,
                'is_featured' => false,
            ],
            
            // Olahraga
            [
                'category' => 'Olahraga',
                'name' => 'Resistance Band Set',
                'short_description' => 'Set resistance band untuk latihan di rumah',
                'description' => "Set resistance band lengkap untuk home workout.\n\nIsi:\n- 5 resistance band berbagai level\n- 2 handle\n- 2 ankle strap\n- Door anchor\n- Carrying bag",
                'price' => 175000,
                'verified_price' => 155000,
                'stock' => 80,
                'is_featured' => true,
            ],
            [
                'category' => 'Olahraga',
                'name' => 'Matras Yoga Premium 6mm',
                'short_description' => 'Matras yoga anti slip dengan ketebalan 6mm',
                'description' => "Matras yoga premium dengan permukaan anti slip.\n\nDetail:\n- Ketebalan 6mm\n- Anti slip surface\n- Eco-friendly material\n- Ukuran 183 x 61 cm\n- Include carrying strap",
                'price' => 225000,
                'verified_price' => 199000,
                'stock' => 45,
                'is_featured' => false,
            ],
            
            // Buku & Alat Tulis
            [
                'category' => 'Buku & Alat Tulis',
                'name' => 'Notebook Premium A5',
                'short_description' => 'Notebook A5 dengan cover kulit sintetis',
                'description' => "Notebook premium dengan desain elegan.\n\nDetail:\n- Ukuran A5\n- 200 halaman\n- Kertas 100gsm\n- Cover kulit sintetis\n- Bookmark ribbon",
                'price' => 89000,
                'verified_price' => 79000,
                'stock' => 120,
                'is_featured' => false,
            ],
            
            // Hobi & Mainan
            [
                'category' => 'Hobi & Mainan',
                'name' => 'Puzzle 1000 Pieces',
                'short_description' => 'Puzzle landscape dengan 1000 pieces',
                'description' => "Puzzle dengan gambar landscape indah.\n\nDetail:\n- 1000 pieces\n- Ukuran jadi: 70 x 50 cm\n- High quality printing\n- Eco-friendly cardboard",
                'price' => 135000,
                'verified_price' => 120000,
                'stock' => 40,
                'is_featured' => false,
            ],
            [
                'category' => 'Hobi & Mainan',
                'name' => 'Model Kit Robot',
                'short_description' => 'Model kit robot dengan artikulasi lengkap',
                'description' => "Model kit robot yang bisa dirakit sendiri.\n\nDetail:\n- Tinggi sekitar 15cm\n- Full articulation\n- Tidak perlu lem\n- Detail tajam\n- Include stand",
                'price' => 285000,
                'verified_price' => 255000,
                'stock' => 25,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            if ($category) {
                Product::create([
                    'category_id' => $category->id,
                    'admin_id' => $admin->id,
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'short_description' => $productData['short_description'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'verified_price' => $productData['verified_price'],
                    'stock' => $productData['stock'],
                    'is_active' => true,
                    'is_featured' => $productData['is_featured'],
                ]);
            }
        }
    }
}
