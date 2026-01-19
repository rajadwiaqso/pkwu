<template>
    <AdminLayout>
        <Head title="Kelola User" />

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola User</h1>
            <button @click="openModal()" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                <i class="bi bi-plus-lg mr-2"></i>Tambah User
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-gray-800">{{ stats.total }}</div>
                <div class="text-sm text-gray-600">Total User</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-blue-600">{{ stats.buyers }}</div>
                <div class="text-sm text-gray-600">Buyer</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-purple-600">{{ stats.admins }}</div>
                <div class="text-sm text-gray-600">Admin</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ stats.verified }}</div>
                <div class="text-sm text-gray-600">Terverifikasi</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <input v-model="filters.search" type="text" placeholder="Cari nama atau email..."
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <select v-model="filters.role" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Role</option>
                        <option value="buyer">Buyer</option>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div>
                    <select v-model="filters.status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                        <option value="verified">Terverifikasi</option>
                        <option value="unverified">Belum Verifikasi</option>
                    </select>
                </div>
                <div>
                    <button @click="applyFilters" class="w-full py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">User</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Role</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Bergabung</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <img v-if="user.avatar_url" :src="user.avatar_url" 
                                         class="w-10 h-10 rounded-full object-cover">
                                    <div v-else class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <span class="text-emerald-600 font-medium">{{ user.name.charAt(0) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ user.name }}</div>
                                        <div class="text-sm text-gray-500">{{ user.phone || '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <span>{{ user.email }}</span>
                                    <i v-if="user.email_verified_at" class="bi bi-patch-check-fill text-blue-500" 
                                       title="Email terverifikasi"></i>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="roleClass(user.role)" class="px-2 py-1 rounded-full text-xs font-medium">
                                    {{ roleLabel(user.role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="user.is_active ? 'text-emerald-600' : 'text-red-600'" class="text-sm">
                                    {{ user.is_active ? '● Aktif' : '○ Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <button @click="openModal(user)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="toggleActive(user)" 
                                            :class="user.is_active ? 'text-yellow-600' : 'text-emerald-600'"
                                            class="p-2 hover:bg-gray-50 rounded" :title="user.is_active ? 'Nonaktifkan' : 'Aktifkan'">
                                        <i :class="user.is_active ? 'bi bi-pause-circle' : 'bi bi-play-circle'"></i>
                                    </button>
                                    <button v-if="!user.email_verified_at" @click="verifyEmail(user)"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded" title="Verifikasi Email">
                                        <i class="bi bi-patch-check"></i>
                                    </button>
                                    <button v-if="user.role !== 'super_admin'" @click="confirmDelete(user)" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada user ditemukan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="px-4 py-3 border-t flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Menampilkan {{ users.from }} - {{ users.to }} dari {{ users.total }} user
                </div>
                <div class="flex space-x-2">
                    <Link v-for="link in users.links" :key="link.label"
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

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="closeModal">
            <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-auto">
                <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white">
                    <h3 class="font-semibold text-lg">{{ editMode ? 'Edit User' : 'Tambah User' }}</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form @submit.prevent="submit" class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama *</label>
                        <input v-model="form.name" type="text" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                               :class="{ 'border-red-500': form.errors.name }">
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input v-model="form.email" type="email" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                               :class="{ 'border-red-500': form.errors.email }">
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                        <input v-model="form.phone" type="text"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password {{ editMode ? '(kosongkan jika tidak diubah)' : '*' }}
                        </label>
                        <input v-model="form.password" type="password" :required="!editMode"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                               :class="{ 'border-red-500': form.errors.password }">
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select v-model="form.role" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                            <option value="buyer">Buyer</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center space-x-3">
                            <input v-model="form.is_active" type="checkbox" 
                                   class="w-5 h-5 text-emerald-500 rounded focus:ring-emerald-500">
                            <span>Akun Aktif</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input v-model="form.email_verified" type="checkbox" 
                                   class="w-5 h-5 text-emerald-500 rounded focus:ring-emerald-500">
                            <span>Email Terverifikasi</span>
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
                <h3 class="font-semibold text-lg mb-2">Hapus User?</h3>
                <p class="text-gray-500 mb-6">"{{ userToDelete?.name }}" akan dihapus permanen.</p>
                <div class="flex justify-center space-x-2">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="deleteUser" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    users: Object,
    stats: Object,
    filters: Object,
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const editMode = ref(false);
const editingUser = ref(null);
const userToDelete = ref(null);

const filters = reactive({
    search: props.filters?.search || '',
    role: props.filters?.role || '',
    status: props.filters?.status || '',
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'buyer',
    is_active: true,
    email_verified: false,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const roleLabel = (role) => {
    const labels = {
        buyer: 'Buyer',
        admin: 'Admin',
        super_admin: 'Super Admin'
    };
    return labels[role] || role;
};

const roleClass = (role) => {
    const classes = {
        buyer: 'bg-blue-100 text-blue-800',
        admin: 'bg-purple-100 text-purple-800',
        super_admin: 'bg-red-100 text-red-800'
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

const applyFilters = () => {
    router.get(route('admin.users.index'), filters, { preserveState: true });
};

const openModal = (user = null) => {
    if (user) {
        editMode.value = true;
        editingUser.value = user;
        form.name = user.name;
        form.email = user.email;
        form.phone = user.phone || '';
        form.password = '';
        form.role = user.role;
        form.is_active = user.is_active;
        form.email_verified = !!user.email_verified_at;
    } else {
        editMode.value = false;
        editingUser.value = null;
        form.reset();
        form.role = 'buyer';
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
        form.put(route('admin.users.update', editingUser.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const toggleActive = (user) => {
    router.put(route('admin.users.toggle-active', user.id));
};

const verifyEmail = (user) => {
    if (confirm('Verifikasi email user ini?')) {
        router.put(route('admin.users.verify-email', user.id));
    }
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    router.delete(route('admin.users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        },
    });
};
</script>
