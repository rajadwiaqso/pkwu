<template>
    <MainLayout>
        <Head title="Pesanan Berhasil" />

        <div class="max-w-lg mx-auto px-4 py-12">
            <!-- Success Icon -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-check-lg text-4xl text-emerald-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Pesanan Berhasil Dibuat!</h1>
                <p class="text-gray-500 mt-2">Silakan lanjutkan ke WhatsApp untuk proses pembayaran</p>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-800">Detail Pesanan</h2>
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

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Order ID</span>
                        <span class="font-medium">{{ order.order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Produk</span>
                        <span class="font-medium">{{ order.product.name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jumlah</span>
                        <span class="font-medium">{{ order.quantity }} item</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Harga Satuan</span>
                        <span class="font-medium">{{ formatPrice(order.unit_price) }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-emerald-600">{{ formatPrice(order.total_price) }}</span>
                    </div>
                </div>

                <!-- Member Price Badge -->
                <div v-if="order.is_verified_price" class="mt-4 bg-emerald-50 rounded-lg p-3">
                    <p class="text-sm text-emerald-700">
                        <i class="bi bi-patch-check-fill mr-1"></i>
                        Anda mendapat harga khusus member!
                    </p>
                </div>
            </div>

            <!-- WhatsApp Button -->
            <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer"
               class="block w-full bg-green-500 text-white text-center font-semibold py-4 rounded-xl hover:bg-green-600 transition-colors mb-4">
                <i class="bi bi-whatsapp mr-2"></i>Lanjutkan ke WhatsApp
            </a>

            <!-- Instructions -->
            <div class="bg-blue-50 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-2">
                    <i class="bi bi-info-circle mr-2"></i>Langkah Selanjutnya
                </h3>
                <ol class="text-sm text-blue-700 space-y-2 list-decimal list-inside">
                    <li>Klik tombol "Lanjutkan ke WhatsApp" di atas</li>
                    <li>Kirim pesan otomatis yang sudah disiapkan</li>
                    <li>Tunggu balasan dari admin untuk instruksi pembayaran</li>
                    <li>Lakukan pembayaran sesuai instruksi</li>
                    <li>Konfirmasi pembayaran ke admin</li>
                    <li>Produk akan dikirim setelah pembayaran dikonfirmasi</li>
                </ol>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <Link :href="route('home')" 
                      class="flex-1 bg-gray-100 text-gray-700 text-center font-semibold py-3 rounded-xl hover:bg-gray-200 transition-colors">
                    Kembali ke Beranda
                </Link>
                <Link v-if="$page.props.auth?.user" :href="route('orders.history')" 
                      class="flex-1 bg-emerald-100 text-emerald-700 text-center font-semibold py-3 rounded-xl hover:bg-emerald-200 transition-colors">
                    Lihat Pesanan Saya
                </Link>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    order: Object,
    whatsappUrl: String,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};
</script>
