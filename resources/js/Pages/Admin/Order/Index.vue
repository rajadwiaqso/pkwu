<template>
    <AdminLayout>
        <Head title="Kelola Pesanan" />

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pesanan</h1>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</div>
                <div class="text-sm text-gray-600">Menunggu</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-blue-600">{{ stats.processing }}</div>
                <div class="text-sm text-gray-600">Diproses</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-purple-600">{{ stats.shipped }}</div>
                <div class="text-sm text-gray-600">Dikirim</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ stats.completed }}</div>
                <div class="text-sm text-gray-600">Selesai</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-red-600">{{ stats.cancelled }}</div>
                <div class="text-sm text-gray-600">Dibatalkan</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <input v-model="filters.search" type="text" placeholder="Cari order ID atau nama..."
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <select v-model="filters.status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Pembayaran</option>
                        <option value="processing">Diproses</option>
                        <option value="shipped">Dikirim</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <input v-model="filters.date" type="date" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <button @click="applyFilters" class="w-full py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Order ID</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Produk</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Total</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="font-mono font-medium text-sm">{{ order.order_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <div class="font-medium">{{ order.customer_name }}</div>
                                    <div class="text-sm text-gray-500">{{ order.customer_phone }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <img v-if="order.product?.main_image_url" :src="order.product.main_image_url" 
                                         class="w-10 h-10 rounded object-cover">
                                    <div>
                                        <div class="text-sm font-medium">{{ order.product?.name || 'Produk Dihapus' }}</div>
                                        <div class="text-xs text-gray-500">x{{ order.quantity }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium">Rp {{ formatPrice(order.total_price) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="statusClass(order.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                    {{ statusLabel(order.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <Link :href="route('admin.orders.show', order.id)" 
                                          class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                                        <i class="bi bi-eye"></i>
                                    </Link>
                                    <button v-if="order.status === 'pending'" 
                                            @click="updateStatus(order.id, 'processing')"
                                            class="p-2 text-emerald-600 hover:bg-emerald-50 rounded" title="Proses">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="orders.data.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada pesanan ditemukan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="orders.last_page > 1" class="px-4 py-3 border-t flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Menampilkan {{ orders.from }} - {{ orders.to }} dari {{ orders.total }} pesanan
                </div>
                <div class="flex space-x-2">
                    <Link v-for="link in orders.links" :key="link.label"
                          :href="link.url || '#'"
                          :class="[
                              'px-3 py-1 rounded text-sm',
                              link.active ? 'bg-emerald-500 text-white' : 'border hover:bg-gray-50',
                              !link.url ? 'opacity-50 cursor-not-allowed' : ''
                          ]"
                          v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    orders: Object,
    stats: Object,
    filters: Object,
});

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    date: props.filters?.date || '',
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const statusLabel = (status) => {
    const labels = {
        pending: 'Menunggu',
        processing: 'Diproses',
        shipped: 'Dikirim',
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

const applyFilters = () => {
    router.get(route('admin.orders.index'), filters, { preserveState: true });
};

const updateStatus = (orderId, status) => {
    if (confirm('Ubah status pesanan?')) {
        router.put(route('admin.orders.update-status', orderId), { status });
    }
};
</script>
