<template>
    <MainLayout>
        <Head title="Semua Produk" />

        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-gray-800">Semua Produk</h1>
                
                <!-- Sort -->
                <select v-model="sortOption" @change="handleSort"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="latest">Terbaru</option>
                    <option value="price_low">Harga Terendah</option>
                    <option value="price_high">Harga Tertinggi</option>
                    <option value="popular">Terpopuler</option>
                </select>
            </div>

            <!-- Products Grid -->
            <div v-if="products.data.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <i class="bi bi-box text-5xl text-gray-300"></i>
                <p class="text-gray-500 mt-4">Belum ada produk</p>
            </div>

            <!-- Pagination -->
            <div v-if="products.last_page > 1" class="mt-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <Link v-if="products.prev_page_url" :href="products.prev_page_url"
                          class="px-3 py-2 border rounded-lg hover:bg-gray-50">
                        <i class="bi bi-chevron-left"></i>
                    </Link>
                    
                    <template v-for="page in visiblePages" :key="page">
                        <Link v-if="page !== '...'" 
                              :href="`?page=${page}&sort=${sortOption}`"
                              :class="[products.current_page === page ? 'bg-emerald-500 text-white' : 'hover:bg-gray-50',
                                       'px-4 py-2 border rounded-lg']">
                            {{ page }}
                        </Link>
                        <span v-else class="px-2">...</span>
                    </template>

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
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    products: Object,
    sort: {
        type: String,
        default: 'latest'
    }
});

const sortOption = ref(props.sort);

const handleSort = () => {
    router.get(route('products.index'), { sort: sortOption.value }, { preserveState: true });
};

const visiblePages = computed(() => {
    const current = props.products.current_page;
    const last = props.products.last_page;
    const pages = [];
    
    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        if (current <= 3) {
            pages.push(1, 2, 3, 4, '...', last);
        } else if (current >= last - 2) {
            pages.push(1, '...', last - 3, last - 2, last - 1, last);
        } else {
            pages.push(1, '...', current - 1, current, current + 1, '...', last);
        }
    }
    
    return pages;
});
</script>
