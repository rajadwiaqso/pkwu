<template>
    <AdminLayout>
        <Head title="Tambah Produk" />

        <div class="max-w-4xl">
            <Link :href="route('admin.products.index')" class="text-gray-600 hover:text-gray-800 mb-4 inline-block">
                <i class="bi bi-arrow-left mr-2"></i>Kembali
            </Link>

            <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Produk Baru</h1>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Informasi Dasar</h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk *</label>
                            <input v-model="form.name" type="text" 
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                   :class="{ 'border-red-500': form.errors.name }">
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                            <select v-model="form.category_id" 
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                    :class="{ 'border-red-500': form.errors.category_id }">
                                <option value="">Pilih Kategori</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-500">{{ form.errors.category_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                            <input v-model="form.short_description" type="text" 
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                   placeholder="Deskripsi singkat untuk preview">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Lengkap *</label>
                            <textarea v-model="form.description" rows="5"
                                      class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                      :class="{ 'border-red-500': form.errors.description }"></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Harga & Stok</h2>
                    
                    <div class="grid md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Normal (Guest) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input v-model.number="form.price" type="number" 
                                       class="w-full pl-10 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                       :class="{ 'border-red-500': form.errors.price }">
                            </div>
                            <p v-if="form.errors.price" class="mt-1 text-sm text-red-500">{{ form.errors.price }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Member (Verified) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input v-model.number="form.verified_price" type="number" 
                                       class="w-full pl-10 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                       :class="{ 'border-red-500': form.errors.verified_price }">
                            </div>
                            <p v-if="form.errors.verified_price" class="mt-1 text-sm text-red-500">{{ form.errors.verified_price }}</p>
                            <p v-if="discountPercentage > 0" class="mt-1 text-sm text-emerald-600">
                                Diskon {{ discountPercentage }}%
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stok *</label>
                            <input v-model.number="form.stock" type="number" min="0"
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                   :class="{ 'border-red-500': form.errors.stock }">
                            <p v-if="form.errors.stock" class="mt-1 text-sm text-red-500">{{ form.errors.stock }}</p>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Gambar Produk</h2>
                    
                    <!-- Main Image -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Utama *</label>
                        <div class="flex items-center space-x-4">
                            <div v-if="mainImagePreview" class="w-32 h-32 rounded-lg overflow-hidden">
                                <img :src="mainImagePreview" class="w-full h-full object-cover">
                            </div>
                            <label class="flex flex-col items-center justify-center w-32 h-32 border-2 border-dashed rounded-lg cursor-pointer hover:border-emerald-500">
                                <i class="bi bi-image text-2xl text-gray-400"></i>
                                <span class="text-xs text-gray-500 mt-1">Upload</span>
                                <input type="file" accept="image/*" @change="handleMainImage" class="hidden">
                            </label>
                        </div>
                        <p v-if="form.errors.main_image" class="mt-1 text-sm text-red-500">{{ form.errors.main_image }}</p>
                    </div>

                    <!-- Additional Images -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Tambahan (Max 4)</label>
                        <div class="flex flex-wrap gap-4">
                            <div v-for="(preview, index) in imagePreviews" :key="index" class="relative w-24 h-24">
                                <img :src="preview" class="w-full h-full object-cover rounded-lg">
                                <button type="button" @click="removeImage(index)" 
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <label v-if="imagePreviews.length < 4" 
                                   class="flex flex-col items-center justify-center w-24 h-24 border-2 border-dashed rounded-lg cursor-pointer hover:border-emerald-500">
                                <i class="bi bi-plus text-2xl text-gray-400"></i>
                                <input type="file" accept="image/*" multiple @change="handleImages" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Pengaturan</h2>
                    
                    <div class="space-y-4">
                        <label class="flex items-center space-x-3">
                            <input v-model="form.is_active" type="checkbox" 
                                   class="w-5 h-5 text-emerald-500 rounded focus:ring-emerald-500">
                            <span>Produk Aktif</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input v-model="form.is_featured" type="checkbox" 
                                   class="w-5 h-5 text-emerald-500 rounded focus:ring-emerald-500">
                            <span>Produk Unggulan (Tampil di beranda)</span>
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end space-x-4">
                    <Link :href="route('admin.products.index')" class="px-6 py-3 border rounded-lg hover:bg-gray-50">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                            class="px-6 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 disabled:opacity-50">
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Produk</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    categories: Array,
});

const form = useForm({
    name: '',
    category_id: '',
    short_description: '',
    description: '',
    price: 0,
    verified_price: 0,
    stock: 0,
    main_image: null,
    images: [],
    is_active: true,
    is_featured: false,
});

const mainImagePreview = ref(null);
const imagePreviews = ref([]);

const discountPercentage = computed(() => {
    if (form.price > 0 && form.verified_price < form.price) {
        return Math.round(((form.price - form.verified_price) / form.price) * 100);
    }
    return 0;
});

const handleMainImage = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.main_image = file;
        mainImagePreview.value = URL.createObjectURL(file);
    }
};

const handleImages = (e) => {
    const files = Array.from(e.target.files);
    const remaining = 4 - imagePreviews.value.length;
    const toAdd = files.slice(0, remaining);
    
    toAdd.forEach(file => {
        form.images.push(file);
        imagePreviews.value.push(URL.createObjectURL(file));
    });
};

const removeImage = (index) => {
    form.images.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.products.store'), {
        forceFormData: true,
    });
};
</script>
