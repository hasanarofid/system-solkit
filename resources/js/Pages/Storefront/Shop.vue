<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    tenant: Object,
    storeSetting: Object,
    products: Object,
    categories: Array,
    schemaJsonLd: Object,
    filters: Object,
});

// State
const searchQuery = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');
const cart = ref([]);
const isCartOpen = ref(false);
const isAiChatOpen = ref(false);
const isCheckingOut = ref(false);

// Customer Checkout Form
const customerForm = ref({
    customer_name: '',
    customer_phone: '',
    customer_address: '',
    notes: '',
});

// AI Chat State
const chatMessages = ref([
    {
        sender: 'ai',
        text: `Halo! Selamat datang di ${props.storeSetting?.store_name || props.tenant?.name}. Saya AI Sales Assistant Anda. Ada produk yang sedang Anda cari atau ingin Anda tanyakan?`,
    },
]);
const inputMessage = ref('');
const isAiTyping = ref(false);

// Currency Formatter
const formatCurrency = (val) => {
    const num = Number(val) || 0;
    return 'Rp ' + num.toLocaleString('id-ID');
};

// Cart Management (LocalStorage Sync)
onMounted(() => {
    const saved = localStorage.getItem(`cart_${props.tenant.slug}`);
    if (saved) {
        try {
            cart.value = JSON.parse(saved);
        } catch (e) {}
    }
});

const saveCart = () => {
    localStorage.setItem(`cart_${props.tenant.slug}`, JSON.stringify(cart.value));
};

const addToCart = (product) => {
    const existing = cart.value.find((item) => item.product_id === product.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.value.push({
            product_id: product.id,
            name: product.name,
            price: Number(product.price),
            thumbnail: product.thumbnail,
            quantity: 1,
            sku: product.sku,
        });
    }
    saveCart();
    isCartOpen.value = true;
};

const updateQuantity = (productId, delta) => {
    const item = cart.value.find((i) => i.product_id === productId);
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) {
        cart.value = cart.value.filter((i) => i.product_id !== productId);
    }
    saveCart();
};

const totalCartItems = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.quantity, 0);
});

const totalCartAmount = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
});

// Filter Handlers
const handleFilter = (categorySlug = null) => {
    selectedCategory.value = categorySlug;
    router.get(
        window.location.pathname,
        {
            category: selectedCategory.value || undefined,
            search: searchQuery.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

// WhatsApp Checkout Execution
const handleCheckout = async () => {
    if (cart.value.length === 0) return;
    if (!customerForm.value.customer_name || !customerForm.value.customer_phone) {
        alert('Mohon lengkapi Nama dan Nomor WhatsApp Anda.');
        return;
    }

    isCheckingOut.value = true;
    try {
        const checkoutUrl = `/shop/${props.tenant.slug}/checkout`;
        const res = await axios.post(checkoutUrl, {
            ...customerForm.value,
            items: cart.value,
        });

        if (res.data.success && res.data.data.whatsapp_url) {
            // Clear cart upon successful order dispatch
            cart.value = [];
            saveCart();
            isCartOpen.value = false;
            // Redirect directly to WhatsApp
            window.location.href = res.data.data.whatsapp_url;
        }
    } catch (err) {
        alert(err.response?.data?.message || 'Terjadi kesalahan saat memproses checkout.');
    } finally {
        isCheckingOut.value = false;
    }
};

// AI Sales Chat Send
const sendAiMessage = async () => {
    const msg = inputMessage.value.trim();
    if (!msg || isAiTyping.value) return;

    chatMessages.value.push({ sender: 'user', text: msg });
    inputMessage.value = '';
    isAiTyping.value = true;

    try {
        const chatUrl = `/shop/${props.tenant.slug}/chat`;
        const res = await axios.post(chatUrl, {
            message: msg,
            history: chatMessages.value.slice(-6),
        });

        if (res.data?.success && res.data?.data?.reply) {
            chatMessages.value.push({ sender: 'ai', text: res.data.data.reply });
        }
    } catch (e) {
        chatMessages.value.push({
            sender: 'ai',
            text: 'Mohon maaf, sistem AI sedang sibuk. Silakan langsung hubungi admin melalui tombol WhatsApp di bawah.',
        });
    } finally {
        isAiTyping.value = false;
    }
};
</script>

<template>
    <Head>
        <title>{{ storeSetting?.meta_title || `${tenant.name} - Official Online Store` }}</title>
        <meta name="description" :content="storeSetting?.meta_description || storeSetting?.about" />
        <!-- Auto-injected Schema.org JSON-LD for AI Search Engines (GEO) -->
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(schemaJsonLd) }}
        </component>
    </Head>

    <div class="min-h-screen bg-slate-50 text-slate-800 font-sans selection:bg-blue-600 selection:text-white pb-24">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <img
                        :src="tenant.logo_url || '/images/solkit-clean.png'"
                        alt="Solkit Logo"
                        class="h-9 w-auto object-contain"
                    />
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 leading-tight">
                            {{ storeSetting?.store_name || tenant.name }}
                        </h1>
                        <p class="text-xs text-blue-600 font-medium flex items-center gap-1">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Verified Merchant &bull; AI-GEO Ready
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- AI Consultant Button -->
                    <button
                        @click="isAiChatOpen = true"
                        class="inline-flex items-center gap-2 px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition border border-indigo-200 shadow-sm"
                    >
                        <svg class="w-4 h-4 text-indigo-600 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="hidden sm:inline">Tanya AI Sales</span>
                    </button>

                    <!-- Cart Trigger -->
                    <button
                        @click="isCartOpen = true"
                        class="relative inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Keranjang</span>
                        <span
                            v-if="totalCartItems > 0"
                            class="absolute -top-2 -right-2 bg-rose-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white"
                        >
                            {{ totalCartItems }}
                        </span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white py-12 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
            <div class="max-w-4xl mx-auto text-center space-y-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    ⚡ Instant WhatsApp Checkout &amp; GEO Hub
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    {{ storeSetting?.tagline || 'Pesan Langsung Tanpa Ribet ke WhatsApp' }}
                </h2>
                <p class="text-slate-300 text-sm sm:text-base max-w-2xl mx-auto">
                    {{ storeSetting?.about || 'Pilih produk favorit Anda, masukkan ke keranjang belanja, dan lakukan checkout instan otomatis terhubung ke WhatsApp resmi kami.' }}
                </p>

                <!-- Search Input -->
                <div class="max-w-xl mx-auto pt-2">
                    <form @submit.prevent="handleFilter(selectedCategory)" class="relative flex items-center">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari produk atau SKU..."
                            class="w-full pl-11 pr-24 py-3 rounded-xl bg-white text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-lg"
                        />
                        <svg class="w-5 h-5 text-slate-400 absolute left-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button
                            type="submit"
                            class="absolute right-1.5 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition"
                        >
                            Cari
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Category Pills -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <button
                    @click="handleFilter(null)"
                    :class="[
                        'px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition border',
                        !selectedCategory
                            ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                            : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
                    ]"
                >
                    Semua Produk
                </button>
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="handleFilter(cat.slug)"
                    :class="[
                        'px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition border',
                        selectedCategory === cat.slug
                            ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                            : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'
                    ]"
                >
                    {{ cat.name }}
                </button>
            </div>
        </div>

        <!-- Product Catalog Grid -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div v-if="products.data && products.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                <div
                    v-for="product in products.data"
                    :key="product.id"
                    class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col group"
                >
                    <!-- Product Image -->
                    <div class="relative aspect-square bg-slate-100 overflow-hidden flex items-center justify-center p-4">
                        <img
                            :src="product.thumbnail || '/images/solkit-clean.png'"
                            :alt="product.name"
                            class="object-contain w-full h-full group-hover:scale-105 transition duration-300"
                        />
                        <span
                            v-if="product.is_featured"
                            class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm"
                        >
                            Unggulan
                        </span>
                        <span
                            v-if="product.stock <= 0"
                            class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px] flex items-center justify-center text-white text-xs font-bold uppercase tracking-wider"
                        >
                            Stok Habis
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <div>
                            <span v-if="product.sku" class="text-[10px] uppercase font-mono text-slate-400">
                                {{ product.sku }}
                            </span>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 line-clamp-2 leading-snug">
                                {{ product.name }}
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 mt-1">
                                {{ product.short_description || 'Produk kualitas terbaik siap dikirim ke seluruh Indonesia.' }}
                            </p>
                        </div>

                        <div>
                            <!-- Price Tag -->
                            <div class="flex items-baseline gap-2">
                                <span class="text-base sm:text-lg font-extrabold text-blue-600">
                                    {{ formatCurrency(product.price) }}
                                </span>
                                <span
                                    v-if="product.compare_at_price && product.compare_at_price > product.price"
                                    class="text-xs text-slate-400 line-through"
                                >
                                    {{ formatCurrency(product.compare_at_price) }}
                                </span>
                            </div>

                            <!-- Action -->
                            <button
                                @click="addToCart(product)"
                                :disabled="product.stock <= 0"
                                class="mt-3 w-full py-2 px-3 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-center gap-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition disabled:opacity-50 disabled:pointer-events-none"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>+ Keranjang</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-white rounded-2xl border border-dashed border-slate-300">
                <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h4 class="text-base font-bold text-slate-700">Tidak ada produk ditemukan</h4>
                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau kategori Anda.</p>
            </div>
        </main>

        <!-- Cart Drawer Modal -->
        <div v-if="isCartOpen" class="fixed inset-0 z-50 flex justify-end bg-slate-900/50 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white h-full shadow-2xl flex flex-col justify-between animate-slide-left">
                <!-- Header -->
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="font-bold text-base text-slate-900">Keranjang Belanja</h3>
                        <span class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full">
                            {{ totalCartItems }}
                        </span>
                    </div>
                    <button @click="isCartOpen = false" class="text-slate-400 hover:text-slate-700 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Items List -->
                <div class="p-4 flex-1 overflow-y-auto space-y-4">
                    <div v-if="cart.length === 0" class="text-center py-12 text-slate-400">
                        <p class="text-sm">Keranjang Anda masih kosong.</p>
                    </div>

                    <div
                        v-for="item in cart"
                        :key="item.product_id"
                        class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200"
                    >
                        <img
                            :src="item.thumbnail || '/images/solkit-clean.png'"
                            class="w-12 h-12 object-contain bg-white rounded-lg p-1 border border-slate-200"
                        />
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">{{ item.name }}</h4>
                            <p class="text-xs text-blue-600 font-semibold">{{ formatCurrency(item.price) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="updateQuantity(item.product_id, -1)"
                                class="w-7 h-7 rounded-lg bg-white border border-slate-300 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-100"
                            >
                                -
                            </button>
                            <span class="text-xs font-bold w-4 text-center">{{ item.quantity }}</span>
                            <button
                                @click="updateQuantity(item.product_id, 1)"
                                class="w-7 h-7 rounded-lg bg-white border border-slate-300 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-100"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Customer Form -->
                    <div v-if="cart.length > 0" class="pt-4 border-t border-slate-200 space-y-3">
                        <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider">Informasi Pengiriman:</h4>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                            <input
                                v-model="customerForm.customer_name"
                                type="text"
                                placeholder="Contoh: Budi Prasetyo"
                                class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Nomor WhatsApp *</label>
                            <input
                                v-model="customerForm.customer_phone"
                                type="tel"
                                placeholder="Contoh: 08123456789"
                                class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Alamat Pengiriman</label>
                            <textarea
                                v-model="customerForm.customer_address"
                                rows="2"
                                placeholder="Alamat lengkap tujuan kirim..."
                                class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer Summary & Checkout Button -->
                <div class="p-4 border-t border-slate-200 bg-slate-50 space-y-3">
                    <div class="flex justify-between items-center text-sm font-bold text-slate-900">
                        <span>Total Pembayaran:</span>
                        <span class="text-lg text-emerald-600">{{ formatCurrency(totalCartAmount) }}</span>
                    </div>

                    <button
                        @click="handleCheckout"
                        :disabled="cart.length === 0 || isCheckingOut"
                        class="w-full py-3 px-4 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30 disabled:opacity-50"
                    >
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086.159.058 1.011.477 1.184.564.173.087.289.13.332.202.043.073.043.42-.101.825z" />
                        </svg>
                        <span>{{ isCheckingOut ? 'Memproses Order...' : 'Checkout via WhatsApp' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- AI Sales Consultant Chat Drawer -->
        <div v-if="isAiChatOpen" class="fixed inset-0 z-50 flex justify-end bg-slate-900/50 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white h-full shadow-2xl flex flex-col justify-between">
                <!-- Chat Header -->
                <div class="p-4 bg-indigo-600 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center font-bold">
                            🤖
                        </div>
                        <div>
                            <h3 class="font-bold text-sm">AI Sales Consultant</h3>
                            <p class="text-[10px] text-indigo-200">Katalog Cerdas RAG Ready</p>
                        </div>
                    </div>
                    <button @click="isAiChatOpen = false" class="text-white/80 hover:text-white p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Chat Messages -->
                <div class="p-4 flex-1 overflow-y-auto space-y-3 bg-slate-50">
                    <div
                        v-for="(msg, idx) in chatMessages"
                        :key="idx"
                        :class="['flex', msg.sender === 'user' ? 'justify-end' : 'justify-start']"
                    >
                        <div
                            :class="[
                                'max-w-[80%] rounded-2xl p-3 text-xs leading-relaxed',
                                msg.sender === 'user'
                                    ? 'bg-blue-600 text-white rounded-br-none shadow-sm'
                                    : 'bg-white text-slate-800 rounded-bl-none border border-slate-200 shadow-sm'
                            ]"
                        >
                            {{ msg.text }}
                        </div>
                    </div>
                    <div v-if="isAiTyping" class="flex justify-start">
                        <div class="bg-white rounded-2xl rounded-bl-none p-3 text-xs border border-slate-200 text-slate-400 flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500 animate-bounce"></span>
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500 animate-bounce delay-100"></span>
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500 animate-bounce delay-200"></span>
                            <span>Mengetik jawaban...</span>
                        </div>
                    </div>
                </div>

                <!-- Chat Input Form -->
                <div class="p-3 border-t border-slate-200 bg-white">
                    <form @submit.prevent="sendAiMessage" class="flex items-center gap-2">
                        <input
                            v-model="inputMessage"
                            type="text"
                            placeholder="Tanyakan produk, stok, atau rekomendasi..."
                            class="flex-1 text-xs p-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                        <button
                            type="submit"
                            :disabled="!inputMessage.trim() || isAiTyping"
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold disabled:opacity-50 transition"
                        >
                            Kirim
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer with Generative Engine Optimization (GEO) Links -->
        <footer class="mt-16 bg-white border-t border-slate-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <img :src="'/images/solkit-clean.png'" alt="Solkit" class="h-6 w-auto opacity-70" />
                    <span>Powered by <strong>Solkit Smart-Commerce</strong> &bull; {{ new Date().getFullYear() }}</span>
                </div>

                <!-- GEO Engine Transparency Links -->
                <div class="flex items-center gap-4">
                    <a :href="`/shop/${tenant.slug}/llms.txt`" target="_blank" class="hover:text-blue-600 underline font-mono">
                        llms.txt (AI GEO)
                    </a>
                    <a :href="`/shop/${tenant.slug}/sitemap.xml`" target="_blank" class="hover:text-blue-600 underline font-mono">
                        sitemap.xml
                    </a>
                    <a :href="`/shop/${tenant.slug}/robots.txt`" target="_blank" class="hover:text-blue-600 underline font-mono">
                        robots.txt
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes slideLeft {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(0);
    }
}
.animate-slide-left {
    animation: slideLeft 0.25s ease-out;
}
</style>
