<template>
    <AdminLayout>
        <Head title="Pengumuman" />

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pengumuman</h1>
            <button @click="openModal()" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                <i class="bi bi-plus-lg mr-2"></i>Tambah Pengumuman
            </button>
        </div>

        <!-- Announcements List -->
        <div class="space-y-4">
            <div v-for="announcement in announcements" :key="announcement.id" 
                 class="bg-white rounded-xl shadow-sm p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <span :class="typeClass(announcement.type)" 
                                  class="px-2 py-1 text-xs rounded font-medium">
                                {{ typeLabel(announcement.type) }}
                            </span>
                            <span :class="announcement.is_active ? 'text-emerald-600' : 'text-gray-400'" 
                                  class="text-xs">
                                {{ announcement.is_active ? '● Aktif' : '○ Nonaktif' }}
                            </span>
                        </div>
                        <h3 class="font-medium text-lg">{{ announcement.title }}</h3>
                        <p class="text-gray-600 mt-1">{{ announcement.content }}</p>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ formatDate(announcement.created_at) }}
                            <span v-if="announcement.expires_at">
                                · Berakhir: {{ formatDate(announcement.expires_at) }}
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="toggleActive(announcement)" 
                                :class="announcement.is_active ? 'text-gray-600' : 'text-emerald-600'"
                                class="p-2 hover:bg-gray-50 rounded">
                            <i :class="announcement.is_active ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                        </button>
                        <button @click="openModal(announcement)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button @click="confirmDelete(announcement)" class="p-2 text-red-600 hover:bg-red-50 rounded">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="announcements.length === 0" class="bg-white rounded-xl p-12 text-center">
                <i class="bi bi-megaphone text-5xl text-gray-300"></i>
                <p class="text-gray-500 mt-4">Belum ada pengumuman</p>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="closeModal">
            <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-auto">
                <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white">
                    <h3 class="font-semibold text-lg">{{ editMode ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form @submit.prevent="submit" class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul *</label>
                        <input v-model="form.title" type="text" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                               :class="{ 'border-red-500': form.errors.title }">
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konten *</label>
                        <textarea v-model="form.content" rows="4" required
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                  :class="{ 'border-red-500': form.errors.content }"></textarea>
                        <p v-if="form.errors.content" class="mt-1 text-sm text-red-500">{{ form.errors.content }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                        <select v-model="form.type" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                            <option value="info">Info</option>
                            <option value="warning">Peringatan</option>
                            <option value="success">Sukses</option>
                            <option value="danger">Penting</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir (opsional)</label>
                        <input v-model="form.expires_at" type="datetime-local"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="flex items-center space-x-3">
                            <input v-model="form.is_active" type="checkbox" 
                                   class="w-5 h-5 text-emerald-500 rounded focus:ring-emerald-500">
                            <span>Pengumuman Aktif</span>
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
                <h3 class="font-semibold text-lg mb-2">Hapus Pengumuman?</h3>
                <p class="text-gray-500 mb-6">"{{ announcementToDelete?.title }}" akan dihapus permanen.</p>
                <div class="flex justify-center space-x-2">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="deleteAnnouncement" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
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
    announcements: Array,
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const editMode = ref(false);
const editingAnnouncement = ref(null);
const announcementToDelete = ref(null);

const form = useForm({
    title: '',
    content: '',
    type: 'info',
    expires_at: '',
    is_active: true,
});

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const typeLabel = (type) => {
    const labels = {
        info: 'Info',
        warning: 'Peringatan',
        success: 'Sukses',
        danger: 'Penting'
    };
    return labels[type] || type;
};

const typeClass = (type) => {
    const classes = {
        info: 'bg-blue-100 text-blue-800',
        warning: 'bg-yellow-100 text-yellow-800',
        success: 'bg-emerald-100 text-emerald-800',
        danger: 'bg-red-100 text-red-800'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

const openModal = (announcement = null) => {
    if (announcement) {
        editMode.value = true;
        editingAnnouncement.value = announcement;
        form.title = announcement.title;
        form.content = announcement.content;
        form.type = announcement.type;
        form.expires_at = announcement.expires_at ? announcement.expires_at.slice(0, 16) : '';
        form.is_active = announcement.is_active;
    } else {
        editMode.value = false;
        editingAnnouncement.value = null;
        form.reset();
        form.type = 'info';
        form.is_active = true;
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editMode.value) {
        form.put(route('admin.announcements.update', editingAnnouncement.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.announcements.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const toggleActive = (announcement) => {
    router.put(route('admin.announcements.toggle', announcement.id));
};

const confirmDelete = (announcement) => {
    announcementToDelete.value = announcement;
    showDeleteModal.value = true;
};

const deleteAnnouncement = () => {
    router.delete(route('admin.announcements.destroy', announcementToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            announcementToDelete.value = null;
        },
    });
};
</script>
