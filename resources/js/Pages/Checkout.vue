<template>
    <MainLayout>
        <Head :title="`Checkout - ${product.name}`" />

        <div class="max-w-2xl mx-auto px-4 py-6">
            <!-- Back Button -->
            <button @click="$inertia.visit(route('products.show', product.slug))" class="text-gray-600 hover:text-gray-800 mb-4">
                <i class="bi bi-arrow-left mr-2"></i>Kembali
            </button>

            <h1 class="text-2xl font-bold text-gray-800 mb-6">Checkout</h1>

            <!-- Product Summary -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <div class="flex space-x-4">
                    <img :src="product.main_image_url" :alt="product.name" 
                         class="w-24 h-24 rounded-lg object-cover">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">{{ product.name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ product.category?.name }}</p>
                        <div class="mt-2">
                            <span class="font-bold text-emerald-600">{{ formatPrice(currentPrice) }}</span>
                            <span v-if="isVerified && product.discount_percentage > 0" class="ml-2 text-sm text-gray-400 line-through">
                                {{ formatPrice(product.price) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout Form -->
            <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm p-6 space-y-5">
                <h2 class="font-semibold text-gray-800">Data Pembeli</h2>

                <!-- Customer Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                          <input v-model="form.customer_name" type="text" 
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Nama lengkap"
                              :class="{ 'border-red-500': getError('customer_name','Nama Lengkap') }">
                          <p v-if="getError('customer_name','Nama Lengkap')" class="mt-1 text-sm text-red-500">{{ getError('customer_name','Nama Lengkap') }}</p>
                </div>

                <!-- Customer Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp *</label>
                    <div class="relative">
                        <i class="bi bi-whatsapp absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                           <input v-model="form.customer_phone" type="tel" 
                               class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="08xxxxxxxxxx"
                               :class="{ 'border-red-500': getError('customer_phone','Nomor WhatsApp') }">
                    </div>
                          <p v-if="getError('customer_phone','Nomor WhatsApp')" class="mt-1 text-sm text-red-500">{{ getError('customer_phone','Nomor WhatsApp') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Untuk komunikasi proses pembelian</p>
                </div>

                <!-- Customer Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email (Opsional)</label>
                          <input v-model="form.customer_email" type="email" 
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="email@example.com"
                              :class="{ 'border-red-500': getError('customer_email','Email') }">
                          <p v-if="getError('customer_email','Email')" class="mt-1 text-sm text-red-500">{{ getError('customer_email','Email') }}</p>
                </div>

                <!-- Address (optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat (Opsional)</label>
                    <textarea v-model="form.address" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="(Opsional) Contoh: Jl. Contoh No. 123, Kota, Kode Pos"
                              :class="{ 'border-red-500': getError('address','Alamat') }"></textarea>
                    <p v-if="getError('address','Alamat')" class="mt-1 text-sm text-red-500">{{ getError('address','Alamat') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Isi jika ingin alamat dikirimkan; kosongkan untuk pengambilan/penjemputan.</p>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                    <div class="flex items-center space-x-4">
                        <button type="button" @click="decreaseQty" 
                                :disabled="form.quantity <= 1"
                                class="w-10 h-10 border rounded-lg flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input v-model.number="form.quantity" type="number" min="1" :max="product.stock"
                               class="w-20 text-center py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <button type="button" @click="increaseQty" 
                                :disabled="form.quantity >= product.stock"
                                class="w-10 h-10 border rounded-lg flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-plus"></i>
                        </button>
                        <span class="text-sm text-gray-500">Stok: {{ product.stock }}</span>
                    </div>
                    <p v-if="getError('quantity','Jumlah')" class="mt-1 text-sm text-red-500">{{ getError('quantity','Jumlah') }}</p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea v-model="form.notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Catatan tambahan untuk penjual..."></textarea>
                </div>

                <!-- Price Summary -->
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal ({{ form.quantity }} item)</span>
                        <span>{{ formatPrice(currentPrice * form.quantity) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg text-gray-800">
                        <span>Total</span>
                        <span class="text-emerald-600">{{ formatPrice(currentPrice * form.quantity) }}</span>
                    </div>
                </div>

                <!-- Member Price Info -->
                <div v-if="!isVerified && product.discount_percentage > 0" class="bg-emerald-50 rounded-lg p-4">
                    <p class="text-sm text-emerald-700">
                        <i class="bi bi-info-circle mr-1"></i>
                        <Link :href="route('register')" class="underline">Daftar sebagai member</Link>
                        untuk hemat {{ formatPrice((product.price - product.verified_price) * form.quantity) }}
                    </p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-2">
                        <i class="bi bi-info-circle mr-2"></i>Cara Pembelian
                    </h4>
                    <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                        <li>Klik tombol "Beli via WhatsApp" di bawah</li>
                        <li>Anda akan diarahkan ke WhatsApp dengan pesan otomatis</li>
                        <li>Lakukan pembayaran sesuai instruksi admin</li>
                        <li>Produk akan dikirim setelah pembayaran dikonfirmasi</li>
                    </ol>
                </div>

                <!-- Submit -->
                <button type="submit" 
                        :disabled="form.processing || product.stock === 0"
                        class="w-full bg-emerald-500 text-white font-semibold py-4 rounded-xl hover:bg-emerald-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span v-if="form.processing">
                        <i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...
                    </span>
                    <span v-else>
                        <i class="bi bi-whatsapp mr-2"></i>Beli via WhatsApp
                    </span>
                </button>
            </form>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    product: Object,
    user: Object,
});

const page = usePage();

const isVerified = computed(() => {
    return page.props.auth?.user?.email_verified_at;
});

const currentPrice = computed(() => {
    return isVerified.value ? props.product.verified_price : props.product.price;
});

const form = useForm({
    product_id: props.product.id,
    customer_name: props.user?.name || '',
    customer_phone: props.user?.phone || '',
    customer_email: props.user?.email || '',
    address: '',
    quantity: 1,
    notes: '',
});

const increaseQty = () => {
    if (form.quantity < props.product.stock) {
        form.quantity++;
    }
};

const decreaseQty = () => {
    if (form.quantity > 1) {
        form.quantity--;
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

/**
 * Return a friendly validation message for a given field.
 * Falls back to the original message if it isn't a known validation key.
 */
const getError = (field, label) => {
    const err = form.errors[field];
    if (!err) return '';
    if (typeof err !== 'string') return err;

    if (err.includes('validation.required')) return `${label} wajib diisi.`;
    if (err.includes('validation.email')) return `${label} tidak valid.`;
    if (err.includes('validation.regex') || err.includes('validation.phone')) return `${label} tidak valid.`;
    if (err.includes('validation.min')) return `${label} terlalu pendek atau nilainya kurang.`;
    if (err.includes('validation.max')) return `${label} terlalu besar atau melebihi batas.`;

    // If backend already returned a human message, use it.
    return err;
};

const submit = () => {
    form.post(route('checkout.store'));
};
</script>
