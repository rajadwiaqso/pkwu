<template>
  <MainLayout>
    <div class="min-h-screen bg-white">
      <div class="max-w-4xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Pengumuman</h1>
        <div v-if="announcements.length" class="space-y-4">
          <div v-for="announcement in announcements" :key="announcement.id" class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
            <h3 class="font-bold text-gray-900">{{ announcement.title }}</h3>
            <p class="text-gray-700 mt-2">{{ announcement.content }}</p>
          </div>
        </div>
        <div v-else class="text-center text-gray-500 py-12">
          <p>Tidak ada pengumuman saat ini</p>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { defineProps } from 'vue';

defineProps({
  announcements: Array,
});
</script>
