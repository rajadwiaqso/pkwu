<template>
    <MainLayout>
        <Head title="Edit Profil" />

        <div class="max-w-2xl mx-auto px-4 py-6">
            <Link :href="route('profile.show')" class="text-gray-600 hover:text-gray-800 mb-4 inline-block">
                <i class="bi bi-arrow-left mr-2"></i>Kembali
            </Link>

            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Profil</h1>

            <!-- Avatar Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-800 mb-4">Foto Profil</h2>
                
                <div class="flex items-center space-x-4">
                    <img :src="user.avatar_url" :alt="user.name" 
                         class="w-20 h-20 rounded-full object-cover border-4 border-emerald-100">
                    <div>
                        <label class="block">
                            <span class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-emerald-600">
                                Ganti Foto
                            </span>
                            <input type="file" accept="image/*" @change="uploadAvatar" class="hidden">
                        </label>
                        <p class="text-xs text-gray-500 mt-2">JPG, PNG. Maksimal 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <form @submit.prevent="updateProfile" class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-800 mb-4">Informasi Profil</h2>

                <div class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input v-model="profileForm.name" type="text" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               :class="{ 'border-red-500': profileForm.errors.name }">
                        <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-500">{{ profileForm.errors.name }}</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp</label>
                        <input v-model="profileForm.phone" type="tel" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="08xxxxxxxxxx"
                               :class="{ 'border-red-500': profileForm.errors.phone }">
                        <p v-if="profileForm.errors.phone" class="mt-1 text-sm text-red-500">{{ profileForm.errors.phone }}</p>
                    </div>

                    <button type="submit" 
                            :disabled="profileForm.processing"
                            class="w-full bg-emerald-500 text-white font-semibold py-3 rounded-xl hover:bg-emerald-600 transition-colors disabled:opacity-50">
                        <span v-if="profileForm.processing">Menyimpan...</span>
                        <span v-else>Simpan Perubahan</span>
                    </button>
                </div>
            </form>

            <!-- Password Form -->
            <form @submit.prevent="updatePassword" class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Ubah Password</h2>

                <div class="space-y-5">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                        <input v-model="passwordForm.current_password" type="password" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               :class="{ 'border-red-500': passwordForm.errors.current_password }">
                        <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-500">{{ passwordForm.errors.current_password }}</p>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input v-model="passwordForm.password" type="password" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="Minimal 8 karakter"
                               :class="{ 'border-red-500': passwordForm.errors.password }">
                        <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-500">{{ passwordForm.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <input v-model="passwordForm.password_confirmation" type="password" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <button type="submit" 
                            :disabled="passwordForm.processing"
                            class="w-full bg-gray-800 text-white font-semibold py-3 rounded-xl hover:bg-gray-900 transition-colors disabled:opacity-50">
                        <span v-if="passwordForm.processing">Menyimpan...</span>
                        <span v-else>Ubah Password</span>
                    </button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    user: Object,
});

const profileForm = useForm({
    name: props.user.name,
    phone: props.user.phone || '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updateProfile = () => {
    profileForm.put(route('profile.update'));
};

const updatePassword = () => {
    passwordForm.put(route('profile.password'), {
        onSuccess: () => {
            passwordForm.reset();
        }
    });
};

const uploadAvatar = (event) => {
    const file = event.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('avatar', file);
        
        router.post(route('profile.avatar'), formData);
    }
};
</script>
