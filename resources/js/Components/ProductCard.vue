<template>
    <Link :href="route('products.show', product.slug)" 
          class="block bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
        <!-- Image -->
        <div class="relative aspect-square bg-gray-100">
            <img :src="product.main_image_url" :alt="product.name" 
                 class="w-full h-full object-cover">
            
            <!-- Discount Badge -->
            <div v-if="product.discount_percentage > 0" 
                 class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                -{{ product.discount_percentage }}%
            </div>

            <!-- Out of Stock Badge -->
            <div v-if="product.stock === 0" 
                 class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <span class="bg-gray-800 text-white text-sm px-3 py-1 rounded">Habis</span>
            </div>
        </div>

        <!-- Info -->
        <div class="p-3">
            <!-- Category -->
            <p class="text-xs text-emerald-600 font-medium mb-1">{{ product.category?.name }}</p>
            
            <!-- Name -->
            <h3 class="font-medium text-gray-800 text-sm line-clamp-2 mb-2">{{ product.name }}</h3>
            
            <!-- Prices -->
            <div class="space-y-1">
                <!-- Verified Price -->
                <div v-if="showVerifiedPrice" class="flex items-center space-x-2">
                    <span class="text-emerald-600 font-bold">{{ formatPrice(product.verified_price) }}</span>
                    <span v-if="product.discount_percentage > 0" class="text-xs text-gray-400 line-through">
                        {{ formatPrice(product.price) }}
                    </span>
                </div>
                <!-- Normal Price -->
                <div v-else>
                    <span class="text-gray-800 font-bold">{{ formatPrice(product.price) }}</span>
                </div>
                
                <!-- Member Price Hint -->
                <p v-if="!showVerifiedPrice && product.discount_percentage > 0" class="text-xs text-emerald-600">
                    <i class="bi bi-patch-check"></i> Member: {{ formatPrice(product.verified_price) }}
                </p>
            </div>

            <!-- Stats -->
            <div class="flex items-center justify-between mt-3 pt-3 border-t text-xs text-gray-500">
                <span><i class="bi bi-eye mr-1"></i>{{ product.views }}</span>
                <span><i class="bi bi-bag mr-1"></i>{{ product.sold_count }} terjual</span>
            </div>
        </div>
    </Link>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
});

const page = usePage();

const showVerifiedPrice = computed(() => {
    return page.props.auth?.user?.email_verified_at;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};
</script>
