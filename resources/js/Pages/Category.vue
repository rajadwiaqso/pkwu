<template>
    <MainLayout>
        <Head :title="category.name" />

        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Category Header -->
            <div class="mb-6">
                <Link :href="route('home')" class="text-gray-500 hover:text-gray-700 text-sm">
                    <i class="bi bi-arrow-left mr-1"></i>Kembali
                </Link>
                <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ category.name }}</h1>
                <p v-if="category.description" class="text-gray-500 mt-1">{{ category.description }}</p>
            </div>

            <!-- Categories List -->
            <div class="flex overflow-x-auto space-x-2 pb-4 mb-6 -mx-4 px-4">
                <Link :href="route('products.index')"
                      class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">
                    Semua
                </Link>
                <Link v-for="cat in categories" :key="cat.id"
                      :href="route('category', cat.slug)"
                      :class="[cat.id === category.id ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                               'flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium']">
                    {{ cat.name }}
                </Link>
            </div>

            <!-- Products Grid -->
            <div v-if="products.data.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <i class="bi bi-box text-5xl text-gray-300"></i>
                <p class="text-gray-500 mt-4">Belum ada produk di kategori ini</p>
            </div>

            <!-- Pagination -->
            <div v-if="products.last_page > 1" class="mt-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <Link v-if="products.prev_page_url" :href="products.prev_page_url"
                          class="px-3 py-2 border rounded-lg hover:bg-gray-50">
                        <i class="bi bi-chevron-left"></i>
                    </Link>
                    
                    <span class="px-4 py-2 text-gray-600">
                        {{ products.current_page }} / {{ products.last_page }}
                    </span>

                    <Link v-if="products.next_page_url" :href="products.next_page_url"
                          class="px-3 py-2 border rounded-lg hover:bg-gray-50">
                        <i class="bi bi-chevron-right"></i>
                    </Link>
                </nav>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

defineProps({
    category: Object,
    products: Object,
    categories: Array,
});
</script>
