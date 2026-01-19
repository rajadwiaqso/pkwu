<template>
  <AdminLayout>
    <div class="p-6">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Produk</h3>
          <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Pesanan</h3>
          <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Kategori</h3>
          <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Pengguna</h3>
          <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>
