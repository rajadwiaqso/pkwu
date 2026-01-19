<template>
    <MainLayout>
        <Head :title="'Daftar'" />

        <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-shop text-white text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h1>
                    <p class="text-gray-500 mt-2">Daftar untuk mendapat harga khusus member</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <i class="bi bi-person absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input v-model="form.name" type="text" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Nama lengkap"
                                   :class="{ 'border-red-500': form.errors.name }">
                        </div>
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <i class="bi bi-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input v-model="form.email" type="email" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="email@example.com"
                                   :class="{ 'border-red-500': form.errors.email }">
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp (Opsional)</label>
                        <div class="relative">
                            <i class="bi bi-whatsapp absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input v-model="form.phone" type="tel" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="08xxxxxxxxxx"
                                   :class="{ 'border-red-500': form.errors.phone }">
                        </div>
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" 
                                   class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Minimal 8 karakter"
                                   :class="{ 'border-red-500': form.errors.password }">
                            <button type="button" @click="showPassword = !showPassword" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input v-model="form.password_confirmation" :type="showPassword ? 'text' : 'password'" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Ulangi password">
                        </div>
                    </div>

                    <!-- Benefits Info -->
                    <div class="bg-emerald-50 rounded-xl p-4">
                        <h4 class="font-semibold text-emerald-800 mb-2">
                            <i class="bi bi-patch-check-fill mr-2"></i>Keuntungan Member
                        </h4>
                        <ul class="text-sm text-emerald-700 space-y-1">
                            <li><i class="bi bi-check2 mr-2"></i>Harga khusus lebih murah</li>
                            <li><i class="bi bi-check2 mr-2"></i>Riwayat pesanan tersimpan</li>
                            <li><i class="bi bi-check2 mr-2"></i>Promo eksklusif member</li>
                        </ul>
                    </div>

                    <!-- Submit -->
                    <button type="submit" 
                            :disabled="form.processing"
                            class="w-full bg-emerald-500 text-white font-semibold py-3 rounded-xl hover:bg-emerald-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="form.processing">
                            <i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...
                        </span>
                        <span v-else>Daftar</span>
                    </button>
                </form>

                <!-- Login Link -->
                <p class="text-center mt-6 text-gray-600">
                    Sudah punya akun?
                    <Link :href="route('login')" class="text-emerald-500 font-medium hover:underline">
                        Masuk
                    </Link>
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const showPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register.post'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
