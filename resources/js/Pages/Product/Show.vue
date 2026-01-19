<template>
    <MainLayout>
        <Head :title="product.name" />

        <div class="max-w-7xl mx-auto pb-24 md:pb-8">
            <!-- Back Button -->
            <div class="px-4 py-3">
                <button @click="$inertia.visit(route('home'))" class="text-gray-600 hover:text-gray-800">
                    <i class="bi bi-arrow-left mr-2"></i>Kembali
                </button>
            </div>

            <div class="md:flex md:gap-8 md:px-4">
                <!-- Product Images -->
                <div class="md:w-1/2">
                    <!-- Main Image -->
                    <div class="relative aspect-square bg-gray-100">
                        <img :src="currentImage" :alt="product.name" 
                             class="w-full h-full object-cover">
                        
                        <!-- Discount Badge -->
                        <div v-if="product.discount_percentage > 0" 
                             class="absolute top-4 left-4 bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-lg">
                            -{{ product.discount_percentage }}%
                        </div>

                        <!-- Out of Stock -->
                        <div v-if="product.stock === 0" 
                             class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                            <span class="bg-gray-800 text-white text-lg px-6 py-2 rounded-lg">Stok Habis</span>
                        </div>
                    </div>

                    <!-- Thumbnails -->
                    <div class="flex space-x-2 p-4 overflow-x-auto">
                        <button @click="currentImage = product.main_image_url"
                                :class="[currentImage === product.main_image_url ? 'ring-2 ring-emerald-500' : '',
                                         'w-16 h-16 rounded-lg overflow-hidden flex-shrink-0']">
                            <img :src="product.main_image_url" :alt="product.name" class="w-full h-full object-cover">
                        </button>
                        <button v-for="image in product.images" :key="image.id"
                                @click="currentImage = image.image_url"
                                :class="[currentImage === image.image_url ? 'ring-2 ring-emerald-500' : '',
                                         'w-16 h-16 rounded-lg overflow-hidden flex-shrink-0']">
                            <img :src="image.image_url" :alt="product.name" class="w-full h-full object-cover">
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="md:w-1/2 px-4 md:px-0">
                    <!-- Category -->
                    <Link :href="route('category', product.category.slug)" 
                          class="text-sm text-emerald-600 font-medium hover:underline">
                        {{ product.category.name }}
                    </Link>

                    <!-- Title -->
                    <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ product.name }}</h1>

                    <!-- Stats -->
                    <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                        <span><i class="bi bi-eye mr-1"></i>{{ product.views }} dilihat</span>
                        <span><i class="bi bi-bag mr-1"></i>{{ product.sold_count }} terjual</span>
                        <span v-if="product.stock > 0"><i class="bi bi-box mr-1"></i>Stok: {{ product.stock }}</span>
                    </div>

                    <!-- Prices -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                        <div v-if="isVerified">
                            <!-- Verified User Price -->
                            <div class="flex items-baseline space-x-3">
                                <span class="text-3xl font-bold text-emerald-600">{{ formatPrice(product.verified_price) }}</span>
                                <span v-if="product.discount_percentage > 0" class="text-lg text-gray-400 line-through">
                                    {{ formatPrice(product.price) }}
                                </span>
                            </div>
                            <p class="text-sm text-emerald-600 mt-1">
                                <i class="bi bi-patch-check-fill mr-1"></i>Harga khusus member
                            </p>
                        </div>
                        <div v-else>
                            <!-- Guest Price -->
                            <span class="text-3xl font-bold text-gray-800">{{ formatPrice(product.price) }}</span>
                            <div v-if="product.discount_percentage > 0" class="mt-2 p-3 bg-emerald-50 rounded-lg">
                                <p class="text-sm text-emerald-700">
                                    <i class="bi bi-patch-check mr-1"></i>
                                    Harga member: <strong>{{ formatPrice(product.verified_price) }}</strong>
                                    <span class="text-emerald-600"> (hemat {{ product.discount_percentage }}%)</span>
                                </p>
                                <Link :href="route('register')" class="text-sm text-emerald-600 underline">
                                    Daftar untuk mendapat harga member
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Deskripsi</h3>
                        <div class="text-gray-600 whitespace-pre-line">{{ product.description }}</div>
                    </div>

                    <!-- Buy Button (Desktop) -->
                    <div class="hidden md:block mt-8">
                        <Link v-if="product.stock > 0" :href="route('checkout', product.slug)"
                              class="block w-full bg-emerald-500 text-white text-center font-semibold py-4 rounded-xl hover:bg-emerald-600 transition-colors">
                            <i class="bi bi-whatsapp mr-2"></i>Beli Sekarang
                        </Link>
                        <button v-else disabled 
                                class="w-full bg-gray-300 text-gray-500 font-semibold py-4 rounded-xl cursor-not-allowed">
                            Stok Habis
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <section v-if="relatedProducts.length > 0" class="px-4 mt-8">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Produk Terkait</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <ProductCard v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" :product="relatedProduct" />
                </div>
            </section>

            <!-- Mobile Fixed Buy Button -->
            <div class="md:hidden fixed bottom-16 left-0 right-0 bg-white border-t p-4 z-30">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Harga</p>
                        <p class="text-lg font-bold text-emerald-600">
                            {{ formatPrice(isVerified ? product.verified_price : product.price) }}
                        </p>
                    </div>
                    <Link v-if="product.stock > 0" :href="route('checkout', product.slug)"
                          class="flex-1 bg-emerald-500 text-white text-center font-semibold py-3 rounded-xl hover:bg-emerald-600 transition-colors">
                        <i class="bi bi-whatsapp mr-2"></i>Beli
                    </Link>
                    <button v-else disabled 
                            class="flex-1 bg-gray-300 text-gray-500 font-semibold py-3 rounded-xl cursor-not-allowed">
                        Habis
                    </button>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    product: Object,
    relatedProducts: Array,
});

const page = usePage();
const currentImage = ref(props.product.main_image_url);

const isVerified = computed(() => {
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
