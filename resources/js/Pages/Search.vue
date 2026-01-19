<template>
    <MainLayout>
        <Head title="Cari Produk" />

        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Search Form -->
            <form @submit.prevent="handleSearch" class="mb-6">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input v-model="searchQuery" type="text" 
                           class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Cari produk...">
                    <button v-if="searchQuery" type="button" @click="clearSearch"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </form>

            <!-- Filters -->
            <div class="flex flex-wrap gap-3 mb-6">
                <!-- Category Filter -->
                <select v-model="selectedCategory" @change="applyFilters"
                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Kategori</option>
                    <option v-for="category in categories" :key="category.id" :value="category.slug">
                        {{ category.name }}
                    </option>
                </select>

                <!-- Sort -->
                <select v-model="selectedSort" @change="applyFilters"
                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="latest">Terbaru</option>
                    <option value="price_low">Harga Terendah</option>
                    <option value="price_high">Harga Tertinggi</option>
                    <option value="popular">Terpopuler</option>
                </select>
            </div>

            <!-- Active Filters -->
            <div v-if="filters.q || filters.category" class="flex flex-wrap gap-2 mb-4">
                <span v-if="filters.q" 
                      class="inline-flex items-center bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm">
                    "{{ filters.q }}"
                    <button @click="clearSearchTerm" class="ml-2 hover:text-emerald-900">
                        <i class="bi bi-x"></i>
                    </button>
                </span>
                <span v-if="filters.category" 
                      class="inline-flex items-center bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm">
                    {{ getCategoryName(filters.category) }}
                    <button @click="clearCategory" class="ml-2 hover:text-emerald-900">
                        <i class="bi bi-x"></i>
                    </button>
                </span>
            </div>

            <!-- Results Count -->
            <p class="text-sm text-gray-500 mb-4">
                Menampilkan {{ products.data.length }} dari {{ products.total }} produk
            </p>

            <!-- Products Grid -->
            <div v-if="products.data.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <i class="bi bi-search text-5xl text-gray-300"></i>
                <p class="text-gray-500 mt-4">Tidak ada produk ditemukan</p>
                <button @click="clearAllFilters" class="mt-4 text-emerald-500 hover:underline">
                    Hapus semua filter
                </button>
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
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: {
        type: Object,
        default: () => ({})
    }
});

const searchQuery = ref(props.filters.q || '');
const selectedCategory = ref(props.filters.category || '');
const selectedSort = ref(props.filters.sort || 'latest');

const handleSearch = () => {
    applyFilters();
};

const applyFilters = () => {
    const params = {};
    if (searchQuery.value) params.q = searchQuery.value;
    if (selectedCategory.value) params.category = selectedCategory.value;
    if (selectedSort.value !== 'latest') params.sort = selectedSort.value;
    
    router.get(route('search'), params, { preserveState: true });
};

const clearSearch = () => {
    searchQuery.value = '';
    applyFilters();
};

const clearSearchTerm = () => {
    searchQuery.value = '';
    applyFilters();
};

const clearCategory = () => {
    selectedCategory.value = '';
    applyFilters();
};

const clearAllFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedSort.value = 'latest';
    router.get(route('search'));
};

const getCategoryName = (slug) => {
    const category = props.categories.find(c => c.slug === slug);
    return category?.name || slug;
};
</script>
