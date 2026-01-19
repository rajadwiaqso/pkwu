<template>
    <AdminLayout>
        <Head :title="`Pesanan ${order.order_number}`" />

        <div class="max-w-4xl">
            <Link :href="route('admin.orders.index')" class="text-gray-600 hover:text-gray-800 mb-4 inline-block">
                <i class="bi bi-arrow-left mr-2"></i>Kembali
            </Link>

            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ order.order_number }}</h1>
                    <p class="text-gray-500">{{ formatDate(order.created_at) }}</p>
                </div>
                <span :class="statusClass(order.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                    {{ statusLabel(order.status) }}
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Informasi Customer</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama</span>
                            <span class="font-medium">{{ order.customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. HP</span>
                            <a :href="`tel:${order.customer_phone}`" class="font-medium text-emerald-600">
                                {{ order.customer_phone }}
                            </a>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email</span>
                            <span>{{ order.customer_email || '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 block mb-1">Alamat</span>
                            <p class="text-sm">{{ order.address }}</p>
                        </div>
                        <div v-if="order.notes">
                            <span class="text-gray-600 block mb-1">Catatan</span>
                            <p class="text-sm bg-gray-50 p-2 rounded">{{ order.notes }}</p>
                        </div>
                    </div>

                    <a :href="`https://wa.me/${formatWhatsApp(order.customer_phone)}`" target="_blank"
                       class="mt-4 w-full py-2 bg-green-500 text-white rounded-lg flex items-center justify-center space-x-2 hover:bg-green-600">
                        <i class="bi bi-whatsapp"></i>
                        <span>Hubungi via WhatsApp</span>
                    </a>
                </div>

                <!-- Product Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Detail Produk</h2>
                    <div class="flex space-x-4">
                        <img v-if="order.product?.main_image_url" :src="order.product.main_image_url" 
                             class="w-20 h-20 rounded-lg object-cover">
                        <div class="flex-1">
                            <h3 class="font-medium">{{ order.product?.name || 'Produk Dihapus' }}</h3>
                            <p class="text-sm text-gray-500">Quantity: {{ order.quantity }}</p>
                            <p class="text-sm text-gray-500">Harga satuan: Rp {{ formatPrice(order.price) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span>Rp {{ formatPrice(order.price * order.quantity) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ongkir</span>
                            <span>Rp {{ formatPrice(order.shipping_cost || 0) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t">
                            <span>Total</span>
                            <span class="text-emerald-600">Rp {{ formatPrice(order.total_price) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Proof -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Bukti Pembayaran</h2>
                    
                    <div v-if="order.payment_proof_url" class="mb-4">
                        <img :src="order.payment_proof_url" class="w-full rounded-lg cursor-pointer"
                             @click="showProof = true">
                        <p class="text-sm text-gray-500 mt-2">
                            Diupload: {{ formatDate(order.payment_proof_uploaded_at) }}
                        </p>
                    </div>
                    <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
                        <i class="bi bi-receipt text-3xl text-gray-400"></i>
                        <p class="text-gray-500 mt-2">Belum ada bukti pembayaran</p>
                    </div>
                </div>

                <!-- Update Status -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Update Status</h2>
                    
                    <div class="space-y-3">
                        <button v-for="status in availableStatuses" :key="status.value"
                                @click="updateStatus(status.value)"
                                :disabled="order.status === status.value"
                                :class="[
                                    'w-full py-3 rounded-lg border flex items-center justify-center space-x-2',
                                    order.status === status.value 
                                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                        : 'hover:bg-gray-50'
                                ]">
                            <i :class="status.icon"></i>
                            <span>{{ status.label }}</span>
                        </button>
                    </div>

                    <!-- Tracking Number -->
                    <div v-if="order.status === 'processing' || order.status === 'shipped'" class="mt-4 pt-4 border-t">
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Resi Pengiriman</label>
                        <div class="flex space-x-2">
                            <input v-model="trackingNumber" type="text" placeholder="Masukkan no. resi"
                                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                            <button @click="saveTracking" :disabled="!trackingNumber"
                                    class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 disabled:opacity-50">
                                Simpan
                            </button>
                        </div>
                        <p v-if="order.tracking_number" class="text-sm text-gray-500 mt-2">
                            Resi saat ini: {{ order.tracking_number }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                <h2 class="font-semibold text-gray-800 mb-4">Riwayat Pesanan</h2>
                <div class="space-y-4">
                    <div v-for="(activity, index) in orderTimeline" :key="index" class="flex space-x-4">
                        <div class="flex flex-col items-center">
                            <div :class="[
                                'w-8 h-8 rounded-full flex items-center justify-center',
                                activity.active ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-500'
                            ]">
                                <i :class="activity.icon"></i>
                            </div>
                            <div v-if="index < orderTimeline.length - 1" class="w-0.5 h-8 bg-gray-200"></div>
                        </div>
                        <div>
                            <p :class="activity.active ? 'font-medium' : 'text-gray-500'">{{ activity.label }}</p>
                            <p v-if="activity.date" class="text-sm text-gray-500">{{ activity.date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proof Modal -->
        <div v-if="showProof" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="showProof = false">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-auto">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-semibold">Bukti Pembayaran</h3>
                    <button @click="showProof = false" class="text-gray-500 hover:text-gray-700">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="p-4">
                    <img :src="order.payment_proof_url" class="w-full">
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    order: Object,
});

const showProof = ref(false);
const trackingNumber = ref(props.order.tracking_number || '');

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatWhatsApp = (phone) => {
    let formatted = phone.replace(/\D/g, '');
    if (formatted.startsWith('0')) {
        formatted = '62' + formatted.slice(1);
    }
    return formatted;
};

const statusLabel = (status) => {
    const labels = {
        pending: 'Menunggu Pembayaran',
        processing: 'Sedang Diproses',
        shipped: 'Dalam Pengiriman',
        completed: 'Selesai',
        cancelled: 'Dibatalkan'
    };
    return labels[status] || status;
};

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-purple-100 text-purple-800',
        completed: 'bg-emerald-100 text-emerald-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const availableStatuses = [
    { value: 'pending', label: 'Menunggu Pembayaran', icon: 'bi bi-clock' },
    { value: 'processing', label: 'Proses Pesanan', icon: 'bi bi-box-seam' },
    { value: 'shipped', label: 'Kirim Pesanan', icon: 'bi bi-truck' },
    { value: 'completed', label: 'Selesai', icon: 'bi bi-check-circle' },
    { value: 'cancelled', label: 'Batalkan', icon: 'bi bi-x-circle' },
];

const orderTimeline = computed(() => {
    const statuses = ['pending', 'processing', 'shipped', 'completed'];
    const currentIndex = statuses.indexOf(props.order.status);
    
    return [
        { 
            label: 'Pesanan Dibuat', 
            icon: 'bi bi-cart-check', 
            active: true,
            date: formatDate(props.order.created_at)
        },
        { 
            label: 'Pembayaran Dikonfirmasi', 
            icon: 'bi bi-credit-card', 
            active: currentIndex >= 1,
            date: currentIndex >= 1 ? formatDate(props.order.paid_at) : null
        },
        { 
            label: 'Pesanan Dikirim', 
            icon: 'bi bi-truck', 
            active: currentIndex >= 2,
            date: currentIndex >= 2 ? formatDate(props.order.shipped_at) : null
        },
        { 
            label: 'Pesanan Selesai', 
            icon: 'bi bi-check-circle', 
            active: currentIndex >= 3,
            date: currentIndex >= 3 ? formatDate(props.order.completed_at) : null
        },
    ];
});

const updateStatus = (status) => {
    if (confirm(`Ubah status pesanan menjadi "${statusLabel(status)}"?`)) {
        router.put(route('admin.orders.update-status', props.order.id), { status });
    }
};

const saveTracking = () => {
    router.put(route('admin.orders.update-tracking', props.order.id), {
        tracking_number: trackingNumber.value
    });
};
</script>
