<template>
    <MainLayout>
        <Head title="Profil Saya" />

        <div class="max-w-2xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Profil Saya</h1>

            <!-- Profile Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <img :src="user.avatar_url" :alt="user.name" 
                         class="w-20 h-20 rounded-full object-cover border-4 border-emerald-100">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ user.name }}</h2>
                        <p class="text-gray-500">{{ user.email }}</p>
                        <div class="mt-2">
                            <span v-if="user.email_verified_at" 
                                  class="inline-flex items-center text-sm text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                                <i class="bi bi-patch-check-fill mr-1"></i>Member Terverifikasi
                            </span>
                            <span v-else 
                                  class="inline-flex items-center text-sm text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">
                                <i class="bi bi-exclamation-circle mr-1"></i>Belum Terverifikasi
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Notice -->
            <div v-if="!user.email_verified_at" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-yellow-800 mb-2">
                    <i class="bi bi-exclamation-triangle mr-2"></i>Verifikasi Email Anda
                </h3>
                <p class="text-sm text-yellow-700 mb-3">
                    Verifikasi email untuk mendapatkan harga khusus member di semua produk!
                </p>
                <button @click="resendVerification" 
                        :disabled="resending"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700 disabled:opacity-50">
                    <span v-if="resending">Mengirim...</span>
                    <span v-else>Kirim Ulang Email Verifikasi</span>
                </button>
            </div>

            <!-- Member Benefits -->
            <div v-if="user.email_verified_at" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-emerald-800 mb-2">
                    <i class="bi bi-star-fill mr-2"></i>Keuntungan Member
                </h3>
                <ul class="text-sm text-emerald-700 space-y-1">
                    <li><i class="bi bi-check2 mr-2"></i>Harga khusus lebih murah untuk semua produk</li>
                    <li><i class="bi bi-check2 mr-2"></i>Riwayat pesanan tersimpan</li>
                    <li><i class="bi bi-check2 mr-2"></i>Promo eksklusif member</li>
                </ul>
            </div>

            <!-- Info List -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Informasi Akun</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b">
                        <div>
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-medium">{{ user.name }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b">
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ user.email }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b">
                        <div>
                            <p class="text-sm text-gray-500">Nomor WhatsApp</p>
                            <p class="font-medium">{{ user.phone || '-' }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <div>
                            <p class="text-sm text-gray-500">Bergabung Sejak</p>
                            <p class="font-medium">{{ formatDate(user.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col space-y-3">
                <Link :href="route('profile.edit')" 
                      class="block bg-emerald-500 text-white text-center font-semibold py-3 rounded-xl hover:bg-emerald-600 transition-colors">
                    <i class="bi bi-pencil mr-2"></i>Edit Profil
                </Link>
                <Link :href="route('orders.history')" 
                      class="block bg-gray-100 text-gray-700 text-center font-semibold py-3 rounded-xl hover:bg-gray-200 transition-colors">
                    <i class="bi bi-bag mr-2"></i>Lihat Pesanan Saya
                </Link>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    user: Object,
});

const resending = ref(false);

const resendVerification = () => {
    resending.value = true;
    router.post(route('verification.resend'), {}, {
        onFinish: () => {
            resending.value = false;
        }
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>
