<template>
    <MainLayout>
        <Head title="Beranda" />

        <div class="max-w-7xl mx-auto">
            <!-- Banner Slider -->
            <section v-if="banners.length > 0" class="px-4 pt-4">
                <BannerSlider :banners="banners" />
            </section>

            <!-- Announcements -->
            <section v-if="announcements.length > 0" class="px-4 mt-6">
                <div v-for="announcement in announcements" :key="announcement.id"
                     :class="[
                         'p-4 rounded-lg mb-3 flex items-start space-x-3',
                         announcement.type === 'info' ? 'bg-blue-50 text-blue-800' :
                         announcement.type === 'warning' ? 'bg-yellow-50 text-yellow-800' :
                         announcement.type === 'success' ? 'bg-emerald-50 text-emerald-800' :
                         'bg-red-50 text-red-800'
                     ]">
                    <i :class="[announcement.type_icon, 'text-xl mt-0.5']"></i>
                    <div class="flex-1">
                        <h4 class="font-semibold">{{ announcement.title }}</h4>
                        <p class="text-sm mt-1 opacity-90">{{ announcement.content }}</p>
                    </div>
                </div>
            </section>

            <!-- Categories -->
            <section class="px-4 mt-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Kategori</h2>
                <div class="flex overflow-x-auto space-x-3 pb-2 -mx-4 px-4 scrollbar-hide">
                    <Link v-for="category in categories" :key="category.id"
                          :href="route('category', category.slug)"
                          class="flex-shrink-0 flex flex-col items-center justify-center w-20 h-20 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <i :class="[category.icon || 'bi-tag', 'text-2xl text-emerald-500']"></i>
                        <span class="text-xs text-gray-600 mt-2 text-center line-clamp-1">{{ category.name }}</span>
                    </Link>
                </div>
            </section>

            <!-- Member Benefit Banner -->
            <section v-if="!isVerified" class="px-4 mt-6">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl p-4 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-patch-check text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold">Dapatkan Harga Khusus Member!</h3>
                            <p class="text-sm opacity-90 mt-1">Daftar dan verifikasi email untuk mendapat potongan harga di semua produk.</p>
                        </div>
                    </div>
                    <Link v-if="!$page.props.auth?.user" :href="route('register')" 
                          class="mt-4 block w-full bg-white text-emerald-600 font-semibold text-center py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        Daftar Sekarang
                    </Link>
                </div>
            </section>

            <!-- Featured Products -->
            <section v-if="featuredProducts.length > 0" class="px-4 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Produk Unggulan</h2>
                    <Link :href="route('products.index')" class="text-emerald-500 text-sm font-medium">
                        Lihat Semua <i class="bi bi-chevron-right"></i>
                    </Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
                </div>
            </section>

            <!-- Latest Products -->
            <section class="px-4 mt-8 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Produk Terbaru</h2>
                    <Link :href="route('products.index')" class="text-emerald-500 text-sm font-medium">
                        Lihat Semua <i class="bi bi-chevron-right"></i>
                    </Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <ProductCard v-for="product in latestProducts" :key="product.id" :product="product" />
                </div>
            </section>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import BannerSlider from '@/Components/BannerSlider.vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    banners: Array,
    featuredProducts: Array,
    latestProducts: Array,
    categories: Array,
    announcements: Array,
});

const page = usePage();

const isVerified = computed(() => {
    return page.props.auth?.user?.email_verified_at;
});
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
