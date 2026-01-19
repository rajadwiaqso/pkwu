<template>
    <AdminLayout :pending-orders-count="stats.pendingOrders">
        <Head title="Dashboard Admin" />

        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="bi bi-box text-xl text-emerald-500"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.totalProducts }}</p>
                            <p class="text-sm text-gray-500">Produk</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="bi bi-bag text-xl text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.totalOrders }}</p>
                            <p class="text-sm text-gray-500">Pesanan</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="bi bi-clock text-xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.pendingOrders }}</p>
                            <p class="text-sm text-gray-500">Pending</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="bi bi-currency-dollar text-xl text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-800">{{ formatPrice(stats.totalRevenue) }}</p>
                            <p class="text-sm text-gray-500">Pendapatan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Super Admin Only: Total Users -->
            <div v-if="isSuperAdmin && stats.totalUsers !== null" class="mb-8">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Total Member Terdaftar</p>
                            <p class="text-3xl font-bold mt-1">{{ stats.totalUsers }}</p>
                        </div>
                        <i class="bi bi-people text-5xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b flex items-center justify-between">
                        <h2 class="font-semibold text-gray-800">Pesanan Terbaru</h2>
                        <Link :href="route('admin.orders.index')" class="text-emerald-500 text-sm hover:underline">
                            Lihat Semua
                        </Link>
                    </div>
                    <div class="divide-y">
                        <div v-for="order in recentOrders" :key="order.id" class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">{{ order.order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ order.product?.name }}</p>
                                </div>
                                <span :class="[
                                    'px-2 py-1 rounded text-xs font-medium',
                                    order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    order.status === 'processing' ? 'bg-blue-100 text-blue-800' :
                                    order.status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                                    'bg-red-100 text-red-800'
                                ]">
                                    {{ order.status_label }}
                                </span>
                            </div>
                        </div>
                        <div v-if="recentOrders.length === 0" class="px-6 py-8 text-center text-gray-500">
                            Belum ada pesanan
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Aksi Cepat</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <Link :href="route('admin.products.create')" 
                              class="flex flex-col items-center justify-center p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors">
                            <i class="bi bi-plus-circle text-2xl text-emerald-500 mb-2"></i>
                            <span class="text-sm font-medium text-emerald-700">Tambah Produk</span>
                        </Link>
                        <Link :href="route('admin.orders.index', { status: 'pending' })" 
                              class="flex flex-col items-center justify-center p-4 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition-colors">
                            <i class="bi bi-clock text-2xl text-yellow-500 mb-2"></i>
                            <span class="text-sm font-medium text-yellow-700">Pesanan Pending</span>
                        </Link>
                        <Link :href="route('admin.banners.index')" 
                              class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                            <i class="bi bi-images text-2xl text-blue-500 mb-2"></i>
                            <span class="text-sm font-medium text-blue-700">Kelola Banner</span>
                        </Link>
                        <Link :href="route('admin.categories.index')" 
                              class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                            <i class="bi bi-tags text-2xl text-purple-500 mb-2"></i>
                            <span class="text-sm font-medium text-purple-700">Kelola Kategori</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Order Chart -->
            <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Statistik Pesanan 7 Hari Terakhir</h2>
                <div class="flex items-end space-x-2 h-40">
                    <div v-for="stat in orderStats" :key="stat.date" class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-emerald-100 rounded-t relative" 
                             :style="{ height: getBarHeight(stat.count) + 'px' }">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs text-gray-600">
                                {{ stat.count }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-500 mt-2">{{ stat.date }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    orderStats: Array,
    isSuperAdmin: Boolean,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const maxCount = computed(() => {
    return Math.max(...props.orderStats.map(s => s.count), 1);
});

const getBarHeight = (count) => {
    return (count / maxCount.value) * 100 + 20;
};
</script>
