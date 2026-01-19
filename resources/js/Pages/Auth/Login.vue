<template>
    <MainLayout>
        <Head :title="'Login'" />

        <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-shop text-white text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Masuk ke Akun</h1>
                    <p class="text-gray-500 mt-2">Selamat datang kembali di Toko Digital Raja</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
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

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" 
                                   class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="••••••••"
                                   :class="{ 'border-red-500': form.errors.password }">
                            <button type="button" @click="showPassword = !showPassword" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input v-model="form.remember" type="checkbox" 
                                   class="w-4 h-4 text-emerald-500 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" 
                            :disabled="form.processing"
                            class="w-full bg-emerald-500 text-white font-semibold py-3 rounded-xl hover:bg-emerald-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="form.processing">
                            <i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...
                        </span>
                        <span v-else>Masuk</span>
                    </button>
                </form>

                <!-- Register Link -->
                <p class="text-center mt-6 text-gray-600">
                    Belum punya akun?
                    <Link :href="route('register')" class="text-emerald-500 font-medium hover:underline">
                        Daftar sekarang
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
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login.post'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
