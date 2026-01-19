<template>
  <MainLayout>
    <div class="min-h-screen bg-white">
      <div class="max-w-2xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Hubungi Kami</h1>
        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Nama</label>
            <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded px-3 py-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Email</label>
            <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded px-3 py-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Pesan</label>
            <textarea v-model="form.message" class="w-full border border-gray-300 rounded px-3 py-2 h-32" required></textarea>
          </div>
          <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-semibold hover:bg-blue-700">
            Kirim Pesan
          </button>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { reactive } from 'vue';

const form = reactive({
  name: '',
  email: '',
  message: '',
});

const submitForm = () => {
  // Placeholder for contact form submission
  alert('Terima kasih atas pesan Anda!');
  form.name = '';
  form.email = '';
  form.message = '';
};
</script>
