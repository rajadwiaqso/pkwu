<template>
  <MainLayout>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center py-12 px-4">
      <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
            <i class="bi bi-envelope-check text-blue-600 text-2xl"></i>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi Email</h1>
          <p class="text-gray-600">Silakan cek email Anda untuk menyelesaikan verifikasi</p>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <p class="text-sm text-gray-700">
            Kami telah mengirimkan link verifikasi ke email Anda. Silakan klik link tersebut untuk mengaktifkan akun Anda.
          </p>
        </div>

        <!-- Status Messages -->
        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
          <p class="text-green-700 text-sm">{{ $page.props.flash.success }}</p>
        </div>

        <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-700 text-sm">{{ $page.props.flash.error }}</p>
        </div>

        <div v-if="$page.props.flash?.info" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <p class="text-blue-700 text-sm">{{ $page.props.flash.info }}</p>
        </div>

        <!-- Instructions -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <h3 class="font-semibold text-gray-900 mb-3 text-sm">Langkah-langkah:</h3>
          <ol class="space-y-2 text-sm text-gray-600">
            <li class="flex items-start">
              <span class="flex items-center justify-center h-5 w-5 rounded-full bg-blue-600 text-white text-xs mr-3 mt-0.5">1</span>
              <span>Cek email Anda (juga periksa folder Spam)</span>
            </li>
            <li class="flex items-start">
              <span class="flex items-center justify-center h-5 w-5 rounded-full bg-blue-600 text-white text-xs mr-3 mt-0.5">2</span>
              <span>Klik link "Verifikasi Email" dalam email</span>
            </li>
            <li class="flex items-start">
              <span class="flex items-center justify-center h-5 w-5 rounded-full bg-blue-600 text-white text-xs mr-3 mt-0.5">3</span>
              <span>Nikmati harga khusus member terverifikasi</span>
            </li>
          </ol>
        </div>

        <!-- Resend Button -->
        <div class="space-y-3">
          <form @submit.prevent="resendEmail">
            <button
              type="submit"
              :disabled="isResending || resendCountdown > 0"
              class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
              <span v-if="resendCountdown > 0">
                Kirim ulang dalam {{ resendCountdown }}s
              </span>
              <span v-else-if="isResending">
                <i class="bi bi-hourglass-split animate-spin mr-2"></i>Mengirim...
              </span>
              <span v-else>
                Kirim Email Verifikasi Ulang
              </span>
            </button>
          </form>

          <Link
            href="/"
            class="block text-center text-gray-600 hover:text-gray-900 py-2 font-medium transition"
          >
            Kembali ke Beranda
          </Link>
        </div>

        <!-- Help Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <p class="text-xs text-gray-500 mb-3">
            <strong>Tidak menerima email?</strong>
          </p>
          <ul class="text-xs text-gray-600 space-y-1">
            <li>• Periksa folder "Spam" atau "Promosi"</li>
            <li>• Email mungkin menggunakan nama pengirim berbeda</li>
            <li>• Tunggu beberapa menit untuk email tiba</li>
            <li>• Coba kirim email verifikasi ulang</li>
          </ul>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const isResending = ref(false);
const resendCountdown = ref(0);
const page = usePage();

const resendEmail = async () => {
  isResending.value = true;

  try {
    const response = await fetch(route('email.resend'), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      },
    });

    if (response.ok) {
      resendCountdown.value = 60;
      const timer = setInterval(() => {
        resendCountdown.value--;
        if (resendCountdown.value <= 0) {
          clearInterval(timer);
        }
      }, 1000);
    }
  } catch (error) {
    console.error('Error resending email:', error);
  } finally {
    isResending.value = false;
  }
};

onMounted(() => {
  // Optional: Auto-focus for accessibility
  document.querySelector('button')?.focus();
});
</script>
