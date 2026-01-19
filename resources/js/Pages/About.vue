<template>
  <MainLayout>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white py-12">
      <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">Tentang Toko Digital Raja</h1>
          <p class="text-lg text-gray-600">Belanja online terpercaya dengan harga terbaik</p>
        </div>

        <!-- About Content -->
        <div class="grid md:grid-cols-2 gap-8 mb-12">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Siapa Kami?</h2>
            <p class="text-gray-700 mb-4">
              Toko Digital Raja adalah platform e-commerce modern yang menyediakan berbagai produk berkualitas
              dengan harga kompetitif. Kami berkomitmen memberikan pengalaman berbelanja terbaik.
            </p>
            <p class="text-gray-700">
              Dengan sistem pembayaran yang aman dan layanan pelanggan yang responsif, kami siap melayani kebutuhan
              belanja online Anda.
            </p>
          </div>

          <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Nilai Kami</h2>
            <ul class="space-y-3 text-gray-700">
              <li class="flex items-start">
                <span class="text-blue-600 mr-3">✓</span>
                <span><strong>Terpercaya:</strong> Transparansi dalam setiap transaksi</span>
              </li>
              <li class="flex items-start">
                <span class="text-blue-600 mr-3">✓</span>
                <span><strong>Berkualitas:</strong> Produk pilihan dari penjual terpercaya</span>
              </li>
              <li class="flex items-start">
                <span class="text-blue-600 mr-3">✓</span>
                <span><strong>Cepat:</strong> Pengiriman dan layanan customer support yang responsif</span>
              </li>
              <li class="flex items-start">
                <span class="text-blue-600 mr-3">✓</span>
                <span><strong>Aman:</strong> Sistem pembayaran terenkripsi dan terjamin</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Features -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Keunggulan Kami</h2>
          <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="text-4xl mb-3">🛍️</div>
              <h3 class="font-bold text-gray-900 mb-2">Katalog Lengkap</h3>
              <p class="text-gray-600 text-sm">Ribuan produk dari berbagai kategori</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">💰</div>
              <h3 class="font-bold text-gray-900 mb-2">Harga Terjangkau</h3>
              <p class="text-gray-600 text-sm">Harga spesial untuk member terverifikasi</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🚚</div>
              <h3 class="font-bold text-gray-900 mb-2">Pengiriman Cepat</h3>
              <p class="text-gray-600 text-sm">Tracking pesanan real-time</p>
            </div>
          </div>
        </div>

        <!-- Contact Section -->
        <div class="bg-blue-50 rounded-lg p-8 text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Hubungi Kami</h2>
          <p class="text-gray-700 mb-4">Ada pertanyaan? Kami siap membantu Anda.</p>
          <p class="text-gray-600">
            Hubungi customer service kami melalui WhatsApp atau email untuk informasi lebih lanjut.
          </p>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
</script>
