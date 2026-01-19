<template>
    <MainLayout>
        <Head :title="`Pesanan ${order.order_number}`" />

        <div class="max-w-2xl mx-auto px-4 py-6">
            <!-- Back Button -->
            <Link :href="route('orders.history')" class="text-gray-600 hover:text-gray-800 mb-4 inline-block">
                <i class="bi bi-arrow-left mr-2"></i>Kembali
            </Link>

            <!-- Order Header -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Order ID</p>
                        <p class="font-bold text-lg">{{ order.order_number }}</p>
                    </div>
                    <span :class="[
                        'px-4 py-2 rounded-full text-sm font-medium',
                        order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                        order.status === 'processing' ? 'bg-blue-100 text-blue-800' :
                        order.status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                        'bg-red-100 text-red-800'
                    ]">
                        {{ order.status_label }}
                    </span>
                </div>

                <div class="text-sm text-gray-500">
                    <p>Tanggal: {{ formatDate(order.created_at) }}</p>
                    <p v-if="order.completed_at">Selesai: {{ formatDate(order.completed_at) }}</p>
                </div>
            </div>

            <!-- Product Info -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-800 mb-4">Detail Produk</h2>
                
                <div class="flex space-x-4">
                    <img :src="order.product.main_image_url" :alt="order.product.name" 
                         class="w-24 h-24 rounded-lg object-cover">
                    <div class="flex-1">
                        <Link :href="route('products.show', order.product.slug)" 
                              class="font-medium text-gray-800 hover:text-emerald-500">
                            {{ order.product.name }}
                        </Link>
                        <p class="text-sm text-gray-500 mt-1">{{ order.quantity }} item</p>
                        <p class="font-medium mt-2">{{ formatPrice(order.unit_price) }} / item</p>
                    </div>
                </div>

                <!-- Member Badge -->
                <div v-if="order.is_verified_price" class="mt-4 bg-emerald-50 rounded-lg p-3">
                    <p class="text-sm text-emerald-700">
                        <i class="bi bi-patch-check-fill mr-1"></i>
                        Pesanan ini menggunakan harga khusus member
                    </p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-800 mb-4">Informasi Pembeli</h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama</span>
                        <span>{{ order.customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">WhatsApp</span>
                        <span>{{ order.customer_phone }}</span>
                    </div>
                    <div v-if="order.customer_email" class="flex justify-between">
                        <span class="text-gray-500">Email</span>
                        <span>{{ order.customer_email }}</span>
                    </div>
                    <div v-if="order.notes" class="pt-3 border-t">
                        <p class="text-gray-500 mb-1">Catatan:</p>
                        <p>{{ order.notes }}</p>
                    </div>
                </div>
            </div>

            <!-- Price Summary -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-800 mb-4">Ringkasan Harga</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal ({{ order.quantity }} item)</span>
                        <span>{{ formatPrice(order.total_price) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-3 border-t">
                        <span>Total</span>
                        <span class="text-emerald-600">{{ formatPrice(order.total_price) }}</span>
                    </div>
                </div>
            </div>

            <!-- Proof of Delivery -->
            <div v-if="order.proof_of_delivery_url" class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-800 mb-4">Bukti Pengiriman</h2>
                <img :src="order.proof_of_delivery_url" alt="Bukti Pengiriman" 
                     class="w-full rounded-lg">
            </div>

            <!-- Status Info -->
            <div :class="[
                'rounded-xl p-4',
                order.status === 'pending' ? 'bg-yellow-50' :
                order.status === 'processing' ? 'bg-blue-50' :
                order.status === 'completed' ? 'bg-emerald-50' :
                'bg-red-50'
            ]">
                <div :class="[
                    'flex items-center',
                    order.status === 'pending' ? 'text-yellow-800' :
                    order.status === 'processing' ? 'text-blue-800' :
                    order.status === 'completed' ? 'text-emerald-800' :
                    'text-red-800'
                ]">
                    <i :class="[
                        'text-xl mr-3',
                        order.status === 'pending' ? 'bi-clock' :
                        order.status === 'processing' ? 'bi-arrow-repeat' :
                        order.status === 'completed' ? 'bi-check-circle' :
                        'bi-x-circle'
                    ]"></i>
                    <div>
                        <p class="font-semibold">
                            {{ order.status === 'pending' ? 'Menunggu Konfirmasi' :
                               order.status === 'processing' ? 'Sedang Diproses' :
                               order.status === 'completed' ? 'Pesanan Selesai' :
                               'Pesanan Dibatalkan' }}
                        </p>
                        <p class="text-sm opacity-80">
                            {{ order.status === 'pending' ? 'Silakan hubungi admin via WhatsApp untuk konfirmasi pembayaran' :
                               order.status === 'processing' ? 'Pesanan Anda sedang diproses oleh admin' :
                               order.status === 'completed' ? 'Terima kasih telah berbelanja!' :
                               'Pesanan Anda telah dibatalkan' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    order: Object,
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
