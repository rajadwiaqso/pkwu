<template>
    <MainLayout>
        <Head title="Pesanan Saya" />

        <div class="max-w-4xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Pesanan Saya</h1>

            <!-- Orders List -->
            <div v-if="orders.data.length > 0" class="space-y-4">
                <div v-for="order in orders.data" :key="order.id" 
                     class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="px-4 py-3 bg-gray-50 border-b flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</span>
                            <p class="font-medium">{{ order.order_number }}</p>
                        </div>
                        <span :class="[
                            'px-3 py-1 rounded-full text-sm font-medium',
                            order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            order.status === 'processing' ? 'bg-blue-100 text-blue-800' :
                            order.status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                            'bg-red-100 text-red-800'
                        ]">
                            {{ order.status_label }}
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <div class="flex space-x-4">
                            <img :src="order.product.main_image_url" :alt="order.product.name" 
                                 class="w-20 h-20 rounded-lg object-cover">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800">{{ order.product.name }}</h3>
                                <p class="text-sm text-gray-500">{{ order.quantity }} item × {{ formatPrice(order.unit_price) }}</p>
                                <p class="font-bold text-emerald-600 mt-1">{{ formatPrice(order.total_price) }}</p>
                            </div>
                        </div>

                        <!-- Member Badge -->
                        <div v-if="order.is_verified_price" class="mt-3">
                            <span class="text-xs text-emerald-600">
                                <i class="bi bi-patch-check-fill mr-1"></i>Harga member
                            </span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-4 py-3 border-t flex justify-end space-x-3">
                        <Link :href="route('orders.show', order.order_number)"
                              class="text-emerald-500 text-sm font-medium hover:underline">
                            Lihat Detail
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <i class="bi bi-bag text-5xl text-gray-300"></i>
                <p class="text-gray-500 mt-4">Belum ada pesanan</p>
                <Link :href="route('products.index')" class="mt-4 inline-block text-emerald-500 hover:underline">
                    Mulai belanja
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="orders.last_page > 1" class="mt-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <Link v-if="orders.prev_page_url" :href="orders.prev_page_url"
                          class="px-3 py-2 border rounded-lg hover:bg-gray-50">
                        <i class="bi bi-chevron-left"></i>
                    </Link>
                    
                    <span class="px-4 py-2 text-gray-600">
                        {{ orders.current_page }} / {{ orders.last_page }}
                    </span>

                    <Link v-if="orders.next_page_url" :href="orders.next_page_url"
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

defineProps({
    orders: Object,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>
