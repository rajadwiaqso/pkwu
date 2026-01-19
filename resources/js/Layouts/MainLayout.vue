<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile Header -->
        <header class="sticky top-0 z-50 bg-white shadow-sm">
            <div class="flex items-center justify-between px-4 py-3">
                <!-- Logo -->
                <Link :href="route('home')" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                        <i class="bi bi-shop text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-gray-800 text-sm md:text-base">Toko Digital Raja</span>
                </Link>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <Link :href="route('home')" class="text-gray-600 hover:text-emerald-500">Beranda</Link>
                    <Link :href="route('products.index')" class="text-gray-600 hover:text-emerald-500">Produk</Link>
                    <Link :href="route('search')" class="text-gray-600 hover:text-emerald-500">Cari</Link>
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center space-x-3">
                    <!-- Search Icon (Mobile) -->
                    <Link :href="route('search')" class="md:hidden text-gray-600 hover:text-emerald-500">
                        <i class="bi bi-search text-xl"></i>
                    </Link>

                    <!-- User Menu -->
                    <div v-if="$page.props.auth?.user" class="relative">
                        <!-- Jika belum verified, tampilkan button Verifikasi -->
                        <button v-if="!$page.props.auth.user.email_verified_at" 
                                @click="handleVerification"
                                class="flex items-center space-x-2 bg-yellow-100 text-yellow-800 px-3 py-2 rounded-lg hover:bg-yellow-200 transition">
                            <i class="bi bi-exclamation-triangle"></i>
                            <span class="text-sm">Verifikasi</span>
                        </button>

                        <!-- Jika sudah verified, tampilkan user menu normal -->
                        <button v-else @click="showUserMenu = !showUserMenu" class="flex items-center space-x-2">
                            <img :src="$page.props.auth.user.avatar_url" :alt="$page.props.auth.user.name" 
                                 class="w-8 h-8 rounded-full object-cover border-2 border-emerald-500">
                            <i class="bi bi-chevron-down text-gray-500 hidden md:inline"></i>
                        </button>

                        <!-- Dropdown -->
                        <transition enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                            <div v-if="showUserMenu && $page.props.auth.user.email_verified_at"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <div class="px-4 py-2 border-b">
                                    <p class="font-medium text-gray-800">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
                                    <span v-if="$page.props.auth.user.email_verified_at" 
                                          class="inline-flex items-center text-xs text-emerald-600 mt-1">
                                        <i class="bi bi-patch-check-fill mr-1"></i> Verified
                                    </span>
                                </div>
                                
                                <Link v-if="$page.props.auth.user.role !== 'buyer'" :href="route('admin.dashboard')" 
                                      class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="bi bi-speedometer2 mr-2"></i> Dashboard Admin
                                </Link>
                                <Link :href="route('profile.show')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="bi bi-person mr-2"></i> Profil Saya
                                </Link>
                                <Link :href="route('orders.history')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="bi bi-bag mr-2"></i> Pesanan Saya
                                </Link>
                                <hr class="my-2">
                                <button @click="logout" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                    <i class="bi bi-box-arrow-right mr-2"></i> Logout
                                </button>
                            </div>
                        </transition>
                    </div>

                    <!-- Login/Register -->
                    <div v-else class="flex items-center space-x-2">
                        <Link :href="route('login')" class="text-gray-600 hover:text-emerald-500 text-sm md:text-base">
                            Masuk
                        </Link>
                        <Link :href="route('register')" 
                              class="hidden md:inline-block bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-emerald-600">
                            Daftar
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Search Bar (Mobile) -->
            <div v-if="showMobileSearch" class="px-4 pb-3">
                <form @submit.prevent="handleSearch" class="relative">
                    <input v-model="searchQuery" type="text" placeholder="Cari produk..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </form>
            </div>
        </header>

        <!-- Verification Banner -->
        <div v-if="$page.props.auth?.user && !$page.props.auth.user.email_verified_at" 
             class="bg-yellow-50 border-b border-yellow-200 px-4 py-3">
            <div class="flex items-center justify-between max-w-7xl mx-auto">
                <p class="text-sm text-yellow-800">
                    <i class="bi bi-exclamation-triangle mr-2"></i>
                    Verifikasi email untuk mendapat harga khusus member!
                </p>
                <button @click="resendVerification" 
                        class="text-sm text-yellow-800 underline hover:text-yellow-900">
                    Kirim ulang
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="bg-emerald-50 border-b border-emerald-200 px-4 py-3">
            <div class="flex items-center max-w-7xl mx-auto text-emerald-800">
                <i class="bi bi-check-circle mr-2"></i>
                {{ $page.props.flash.success }}
            </div>
        </div>
        <div v-if="$page.props.flash?.error" class="bg-red-50 border-b border-red-200 px-4 py-3">
            <div class="flex items-center max-w-7xl mx-auto text-red-800">
                <i class="bi bi-x-circle mr-2"></i>
                {{ $page.props.flash.error }}
            </div>
        </div>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Mobile Bottom Navigation -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg z-40">
            <div class="flex items-center justify-around py-2">
                <Link :href="route('home')" class="flex flex-col items-center py-2 px-3" 
                      :class="isActive('home') ? 'text-emerald-500' : 'text-gray-500'">
                    <i class="bi bi-house text-xl"></i>
                    <span class="text-xs mt-1">Beranda</span>
                </Link>
                <Link :href="route('products.index')" class="flex flex-col items-center py-2 px-3"
                      :class="isActive('products') ? 'text-emerald-500' : 'text-gray-500'">
                    <i class="bi bi-grid text-xl"></i>
                    <span class="text-xs mt-1">Produk</span>
                </Link>
                <Link :href="route('search')" class="flex flex-col items-center py-2 px-3"
                      :class="isActive('search') ? 'text-emerald-500' : 'text-gray-500'">
                    <i class="bi bi-search text-xl"></i>
                    <span class="text-xs mt-1">Cari</span>
                </Link>
                <Link v-if="$page.props.auth?.user" :href="route('profile.show')" class="flex flex-col items-center py-2 px-3"
                      :class="isActive('profile') ? 'text-emerald-500' : 'text-gray-500'">
                    <img :src="$page.props.auth.user.avatar_url" :alt="$page.props.auth.user.name" 
                         class="w-6 h-6 rounded-full object-cover border border-emerald-500">
                    <span class="text-xs mt-1">Profil</span>
                </Link>
                <Link v-else :href="route('login')" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <i class="bi bi-person text-xl"></i>
                    <span class="text-xs mt-1">Masuk</span>
                </Link>
            </div>
        </nav>

        <!-- Bottom Spacer for Mobile Nav -->
        <div class="md:hidden h-20"></div>

        <!-- Overlay for User Menu -->
        <div v-if="showUserMenu" @click="showUserMenu = false" class="fixed inset-0 z-40"></div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const showUserMenu = ref(false);
const showMobileSearch = ref(false);
const searchQuery = ref('');

const page = usePage();

const isActive = (name) => {
    const currentRoute = route().current();
    if (name === 'home') return currentRoute === 'home';
    if (name === 'products') return currentRoute?.startsWith('products');
    if (name === 'search') return currentRoute === 'search';
    if (name === 'profile') return currentRoute?.startsWith('profile');
    if (name === 'orders') return currentRoute?.startsWith('orders');
    return false;
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('search'), { q: searchQuery.value });
    }
};

const logout = () => {
    router.post(route('logout'));
};

const resendVerification = () => {
    router.post(route('verification.resend'));
};

const handleVerification = () => {
    router.get(route('email.verify-page'));
};
</script>
