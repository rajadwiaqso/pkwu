<template>
    <AdminLayout>
        <Head title="Kelola Banner" />

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Banner</h1>
            <button @click="openModal()" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                <i class="bi bi-plus-lg mr-2"></i>Tambah Banner
            </button>
        </div>

        <!-- Banners Grid -->
        <div class="grid md:grid-cols-2 gap-4">
            <div v-for="banner in banners" :key="banner.id" 
                 class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="relative aspect-[3/1]">
                    <img :src="banner.image_url" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2 flex space-x-2">
                        <span :class="banner.is_active ? 'bg-emerald-500' : 'bg-gray-500'" 
                              class="px-2 py-1 text-white text-xs rounded">
                            {{ banner.is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-medium">{{ banner.title || 'Tanpa Judul' }}</h3>
                    <p v-if="banner.link" class="text-sm text-gray-500 truncate">{{ banner.link }}</p>
                    <p class="text-sm text-gray-500 mt-1">Urutan: {{ banner.order }}</p>
                    
                    <div class="flex justify-end space-x-2 mt-4 pt-4 border-t">
                        <button @click="toggleActive(banner)" 
                                :class="banner.is_active ? 'text-gray-600' : 'text-emerald-600'"
                                class="p-2 hover:bg-gray-50 rounded">
                            <i :class="banner.is_active ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                        </button>
                        <button @click="openModal(banner)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button @click="confirmDelete(banner)" class="p-2 text-red-600 hover:bg-red-50 rounded">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="banners.length === 0" class="col-span-full text-center py-12">
                <i class="bi bi-image text-5xl text-gray-300"></i>
                <p class="text-gray-500 mt-4">Belum ada banner</p>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="closeModal">
            <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-auto">
                <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white">
                    <h3 class="font-semibold text-lg">{{ editMode ? 'Edit Banner' : 'Tambah Banner' }}</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form @submit.prevent="submit" class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Banner *</label>
                        <div class="flex items-center space-x-4">
                            <div v-if="imagePreview || editingBanner?.image_url" 
                                 class="w-full aspect-[3/1] rounded-lg overflow-hidden bg-gray-100">
                                <img :src="imagePreview || editingBanner?.image_url" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <label class="mt-2 w-full py-3 border-2 border-dashed rounded-lg flex flex-col items-center cursor-pointer hover:border-emerald-500">
                            <i class="bi bi-cloud-upload text-2xl text-gray-400"></i>
                            <span class="text-sm text-gray-500">Upload gambar (3:1 ratio recommended)</span>
                            <input type="file" accept="image/*" @change="handleImage" class="hidden">
                        </label>
                        <p v-if="form.errors.image" class="mt-1 text-sm text-red-500">{{ form.errors.image }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                        <input v-model="form.title" type="text"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link (opsional)</label>
                        <input v-model="form.link" type="url" placeholder="https://..."
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                        <input v-model.number="form.order" type="number" min="0"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="flex items-center space-x-3">
                            <input v-model="form.is_active" type="checkbox" 
                                   class="w-5 h-5 text-emerald-500 rounded focus:ring-emerald-500">
                            <span>Banner Aktif</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing"
                                class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="showDeleteModal = false">
            <div class="bg-white rounded-xl max-w-sm w-full p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h3 class="font-semibold text-lg mb-2">Hapus Banner?</h3>
                <p class="text-gray-500 mb-6">Banner akan dihapus permanen.</p>
                <div class="flex justify-center space-x-2">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="deleteBanner" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    banners: Array,
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const editMode = ref(false);
const editingBanner = ref(null);
const bannerToDelete = ref(null);
const imagePreview = ref(null);

const form = useForm({
    title: '',
    image: null,
    link: '',
    order: 0,
    is_active: true,
});

const openModal = (banner = null) => {
    if (banner) {
        editMode.value = true;
        editingBanner.value = banner;
        form.title = banner.title || '';
        form.link = banner.link || '';
        form.order = banner.order;
        form.is_active = banner.is_active;
        form.image = null;
        imagePreview.value = null;
    } else {
        editMode.value = false;
        editingBanner.value = null;
        form.reset();
        form.is_active = true;
        imagePreview.value = null;
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    imagePreview.value = null;
};

const handleImage = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    if (editMode.value) {
        form.post(route('admin.banners.update', editingBanner.value.id), {
            forceFormData: true,
            _method: 'PUT',
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.banners.store'), {
            forceFormData: true,
            onSuccess: () => closeModal(),
        });
    }
};

const toggleActive = (banner) => {
    router.put(route('admin.banners.toggle', banner.id));
};

const confirmDelete = (banner) => {
    bannerToDelete.value = banner;
    showDeleteModal.value = true;
};

const deleteBanner = () => {
    router.delete(route('admin.banners.destroy', bannerToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            bannerToDelete.value = null;
        },
    });
};
</script>
