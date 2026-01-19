<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', 
                        'fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transition-transform duration-300 ease-in-out lg:translate-x-0']">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 bg-gray-800">
                <Link :href="route('admin.dashboard')" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                        <i class="bi bi-shop text-white"></i>
                    </div>
                    <span class="text-white font-bold">Admin Panel</span>
                </Link>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-4 px-2 space-y-1">
                <Link :href="route('admin.dashboard')" 
                      :class="[isActive('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                               'flex items-center px-4 py-3 rounded-lg transition-colors']">
                    <i class="bi bi-speedometer2 mr-3"></i>
                    Dashboard
                </Link>

                <Link :href="route('admin.products.index')" 
                      :class="[isActive('admin.products') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                               'flex items-center px-4 py-3 rounded-lg transition-colors']">
                    <i class="bi bi-box mr-3"></i>
                    Produk
                </Link>

                <Link :href="route('admin.orders.index')" 
                      :class="[isActive('admin.orders') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                               'flex items-center px-4 py-3 rounded-lg transition-colors']">
                    <i class="bi bi-bag mr-3"></i>
                    Pesanan
                    <span v-if="pendingOrdersCount > 0" 
                          class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                        {{ pendingOrdersCount }}
                    </span>
                </Link>

                <Link :href="route('admin.categories.index')" 
                      :class="[isActive('admin.categories') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                               'flex items-center px-4 py-3 rounded-lg transition-colors']">
                    <i class="bi bi-tags mr-3"></i>
                    Kategori
                </Link>

                <Link :href="route('admin.banners.index')" 
                      :class="[isActive('admin.banners') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                               'flex items-center px-4 py-3 rounded-lg transition-colors']">
                    <i class="bi bi-images mr-3"></i>
                    Banner
                </Link>

                <Link :href="route('admin.announcements.index')" 
                      :class="[isActive('admin.announcements') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                               'flex items-center px-4 py-3 rounded-lg transition-colors']">
                    <i class="bi bi-megaphone mr-3"></i>
                    Pengumuman
                </Link>

                <!-- Super Admin Only -->
                <template v-if="isSuperAdmin">
                    <div class="pt-4 pb-2 px-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Super Admin</p>
                    </div>
                    <Link :href="route('admin.users.index')" 
                          :class="[isActive('admin.users') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                                   'flex items-center px-4 py-3 rounded-lg transition-colors']">
                        <i class="bi bi-people mr-3"></i>
                        Manajemen User
                    </Link>
                </template>

                <div class="pt-4 pb-2 px-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lainnya</p>
                </div>
                <Link :href="route('home')" 
                      class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                    <i class="bi bi-house mr-3"></i>
                    Lihat Toko
                </Link>
            </nav>
        </aside>

        <!-- Overlay -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false" 
             class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Header -->
            <header class="sticky top-0 z-30 bg-white shadow-sm">
                <div class="flex items-center justify-between px-4 py-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-600">
                        <i class="bi bi-list text-2xl"></i>
                    </button>

                    <div class="flex-1 lg:flex-none"></div>

                    <!-- User Info -->
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button @click="showUserMenu = !showUserMenu" class="flex items-center space-x-2">
                                <img :src="$page.props.auth.user.avatar_url" 
                                     :alt="$page.props.auth.user.name"
                                     class="w-8 h-8 rounded-full object-cover border-2 border-emerald-500">
                                <span class="hidden md:inline text-sm font-medium text-gray-700">
                                    {{ $page.props.auth.user.name }}
                                </span>
                                <i class="bi bi-chevron-down text-gray-500"></i>
                            </button>

                            <!-- Dropdown -->
                            <transition enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95">
                                <div v-if="showUserMenu" 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                    <div class="px-4 py-2 border-b">
                                        <p class="font-medium text-gray-800">{{ $page.props.auth.user.name }}</p>
                                        <p class="text-xs text-gray-500 capitalize">{{ $page.props.auth.user.role.replace('_', ' ') }}</p>
                                    </div>
                                    <Link :href="route('profile.show')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="bi bi-person mr-2"></i> Profil
                                    </Link>
                                    <hr class="my-2">
                                    <button @click="logout" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                        <i class="bi bi-box-arrow-right mr-2"></i> Logout
                                    </button>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 md:p-6">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" 
                     class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="bi bi-check-circle mr-2"></i>
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" 
                     class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="bi bi-x-circle mr-2"></i>
                    {{ $page.props.flash.error }}
                </div>

                <slot />
            </main>
        </div>

        <!-- Overlay for User Menu -->
        <div v-if="showUserMenu" @click="showUserMenu = false" class="fixed inset-0 z-40"></div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    pendingOrdersCount: {
        type: Number,
        default: 0
    }
});

const page = usePage();
const sidebarOpen = ref(false);
const showUserMenu = ref(false);

const isSuperAdmin = computed(() => page.props.auth?.user?.role === 'super_admin');

const isActive = (name) => {
    const currentRoute = route().current();
    if (name === 'admin.dashboard') return currentRoute === 'admin.dashboard';
    return currentRoute?.startsWith(name);
};

const logout = () => {
    router.post(route('logout'));
};
</script>
