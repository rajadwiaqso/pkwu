<template>
    <div class="relative">
        <!-- Slides Container -->
        <div class="overflow-hidden rounded-xl">
            <div class="flex transition-transform duration-500 ease-out" 
                 :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                <div v-for="(banner, index) in banners" :key="index" class="w-full flex-shrink-0">
                    <a :href="banner.link || '#'" class="block relative aspect-[16/9] md:aspect-[21/9]">
                        <img :src="banner.image_url" :alt="banner.title" 
                             class="w-full h-full object-cover">
                        
                        <!-- Overlay Content -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end">
                            <div class="p-4 md:p-6 text-white">
                                <h2 class="text-lg md:text-2xl font-bold">{{ banner.title }}</h2>
                                <p v-if="banner.description" class="text-sm md:text-base mt-1 opacity-90 line-clamp-2">
                                    {{ banner.description }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button v-if="banners.length > 1" 
                @click="prev" 
                class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 md:w-10 md:h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-colors">
            <i class="bi bi-chevron-left text-gray-800"></i>
        </button>
        <button v-if="banners.length > 1" 
                @click="next" 
                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 md:w-10 md:h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-colors">
            <i class="bi bi-chevron-right text-gray-800"></i>
        </button>

        <!-- Dots -->
        <div v-if="banners.length > 1" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex space-x-2">
            <button v-for="(_, index) in banners" :key="index"
                    @click="goTo(index)"
                    :class="[currentIndex === index ? 'bg-white' : 'bg-white/50',
                             'w-2 h-2 rounded-full transition-colors']">
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    banners: {
        type: Array,
        default: () => []
    },
    autoPlay: {
        type: Boolean,
        default: true
    },
    interval: {
        type: Number,
        default: 5000
    }
});

const currentIndex = ref(0);
let timer = null;

const next = () => {
    currentIndex.value = (currentIndex.value + 1) % props.banners.length;
};

const prev = () => {
    currentIndex.value = (currentIndex.value - 1 + props.banners.length) % props.banners.length;
};

const goTo = (index) => {
    currentIndex.value = index;
};

const startAutoPlay = () => {
    if (props.autoPlay && props.banners.length > 1) {
        timer = setInterval(next, props.interval);
    }
};

const stopAutoPlay = () => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
};

onMounted(() => {
    startAutoPlay();
});

onUnmounted(() => {
    stopAutoPlay();
});
</script>
