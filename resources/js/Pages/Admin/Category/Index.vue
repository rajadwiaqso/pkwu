<template>
    <AdminLayout>
        <Head title="Kelola Kategori" />

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Kategori</h1>
            <button @click="openModal()" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                <i class="bi bi-plus-lg mr-2"></i>Tambah Kategori
            </button>
        </div>

        <!-- Categories Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="category in categories" :key="category.id" 
                 class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div v-if="category.icon" class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i :class="`bi bi-${category.icon} text-xl text-emerald-600`"></i>
                    </div>
                    <div v-else class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="bi bi-folder text-xl text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium">{{ category.name }}</h3>
                        <p class="text-sm text-gray-500">{{ category.products_count || 0 }} produk</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="openModal(category)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="confirmDelete(category)" class="p-2 text-red-600 hover:bg-red-50 rounded"
                            :disabled="category.products_count > 0">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <div v-if="categories.length === 0" class="col-span-full text-center py-12">
                <i class="bi bi-folder-x text-5xl text-gray-300"></i>
                <p class="text-gray-500 mt-4">Belum ada kategori</p>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="closeModal">
            <div class="bg-white rounded-xl max-w-md w-full">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-lg">{{ editMode ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form @submit.prevent="submit" class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori *</label>
                        <input v-model="form.name" type="text" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                               :class="{ 'border-red-500': form.errors.name }">
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea v-model="form.description" rows="3"
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon (Bootstrap Icon)</label>
                        <div class="flex space-x-2">
                            <input v-model="form.icon" type="text" placeholder="contoh: box, bag, cart"
                                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                            <div v-if="form.icon" class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i :class="`bi bi-${form.icon} text-emerald-600`"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <a href="https://icons.getbootstrap.com/" target="_blank" class="text-emerald-600">
                                Lihat daftar icon
                            </a>
                        </p>
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
                <h3 class="font-semibold text-lg mb-2">Hapus Kategori?</h3>
                <p class="text-gray-500 mb-6">Kategori "{{ categoryToDelete?.name }}" akan dihapus permanen.</p>
                <div class="flex justify-center space-x-2">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="deleteCategory" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
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
    categories: Array,
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const editMode = ref(false);
const categoryToDelete = ref(null);
const editingId = ref(null);

const form = useForm({
    name: '',
    description: '',
    icon: '',
});

const openModal = (category = null) => {
    if (category) {
        editMode.value = true;
        editingId.value = category.id;
        form.name = category.name;
        form.description = category.description || '';
        form.icon = category.icon || '';
    } else {
        editMode.value = false;
        editingId.value = null;
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editMode.value) {
        form.put(route('admin.categories.update', editingId.value), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (category) => {
    categoryToDelete.value = category;
    showDeleteModal.value = true;
};

const deleteCategory = () => {
    router.delete(route('admin.categories.destroy', categoryToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            categoryToDelete.value = null;
        },
    });
};
</script>
