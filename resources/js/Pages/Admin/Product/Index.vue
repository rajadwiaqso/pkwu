<template>
    <AdminLayout>
        <Head title="Kelola Produk" />

        <div>
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Kelola Produk</h1>
                <Link :href="route('admin.products.create')" 
                      class="inline-flex items-center justify-center bg-emerald-500 text-white px-4 py-2 rounded-lg hover:bg-emerald-600">
                    <i class="bi bi-plus-lg mr-2"></i>Tambah Produk
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form @submit.prevent="applyFilters" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input v-model="search" type="text" placeholder="Cari produk..."
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <select v-model="categoryFilter" 
                            class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Kategori</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <select v-model="activeFilter" 
                            class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img :src="product.main_image_url" :alt="product.name" 
                                             class="w-12 h-12 rounded-lg object-cover">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ product.name }}</p>
                                            <p class="text-sm text-gray-500">{{ product.sold_count }} terjual</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ product.category?.name }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-800">{{ formatPrice(product.price) }}</p>
                                    <p class="text-xs text-emerald-600">Member: {{ formatPrice(product.verified_price) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="[product.stock > 0 ? 'text-gray-800' : 'text-red-500', 'font-medium']">
                                        {{ product.stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="[product.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800',
                                                   'px-2 py-1 rounded text-xs font-medium']">
                                        {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <Link :href="route('admin.products.edit', product.id)" 
                                              class="text-blue-500 hover:text-blue-700">
                                            <i class="bi bi-pencil"></i>
                                        </Link>
                                        <button @click="confirmDelete(product)" class="text-red-500 hover:text-red-700">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="products.data.length === 0" class="text-center py-12">
                    <i class="bi bi-box text-5xl text-gray-300"></i>
                    <p class="text-gray-500 mt-4">Belum ada produk</p>
                </div>

                <!-- Pagination -->
                <div v-if="products.last_page > 1" class="px-6 py-4 border-t">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Menampilkan {{ products.from }} - {{ products.to }} dari {{ products.total }}
                        </p>
                        <nav class="flex items-center space-x-2">
                            <Link v-if="products.prev_page_url" :href="products.prev_page_url"
                                  class="px-3 py-1 border rounded hover:bg-gray-50">
                                <i class="bi bi-chevron-left"></i>
                            </Link>
                            <Link v-if="products.next_page_url" :href="products.next_page_url"
                                  class="px-3 py-1 border rounded hover:bg-gray-50">
                                <i class="bi bi-chevron-right"></i>
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Hapus Produk?</h3>
                <p class="text-gray-600 mb-6">
                    Apakah Anda yakin ingin menghapus produk "{{ productToDelete?.name }}"? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="deleteProduct" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    isSuperAdmin: Boolean,
});

const search = ref(props.filters?.search || '');
const categoryFilter = ref(props.filters?.category || '');
const activeFilter = ref(props.filters?.active ?? '');

const showDeleteModal = ref(false);
const productToDelete = ref(null);

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (categoryFilter.value) params.category = categoryFilter.value;
    if (activeFilter.value !== '') params.active = activeFilter.value;
    
    router.get(route('admin.products.index'), params, { preserveState: true });
};

const confirmDelete = (product) => {
    productToDelete.value = product;
    showDeleteModal.value = true;
};

const deleteProduct = () => {
    router.delete(route('admin.products.destroy', productToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            productToDelete.value = null;
        }
    });
};
</script>
