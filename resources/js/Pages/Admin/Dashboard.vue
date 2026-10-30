<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShoppingBag, ShoppingCart, DollarSign, Clock, ArrowRight, ExternalLink, Bot, Globe } from '@lucide/vue';

const props = defineProps({
    tenant: Object,
    stats: {
        type: Object,
        required: true,
    },
    recent_orders: {
        type: Array,
        required: true,
    },
});

const formatCurrency = (val) => {
    return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Merchant Dashboard - Solkit Hub" />

    <AdminLayout>
        <div class="space-y-6 sm:space-y-8">
            <!-- Header Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                        ⚡ Solkit Multi-tenant Commerce Engine
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mt-2">
                        {{ tenant?.name || 'Solkit Smart Merchant' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Pusat kontrol inventaris, Order Management System (OMS), dan Generative Engine Optimization (GEO).
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        :href="`/shop/${tenant?.slug || 'solkit-store'}`"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold shadow-sm transition"
                    >
                        <span>Buka Toko Online</span>
                        <ExternalLink class="w-4 h-4" />
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Products Count -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs group hover:border-blue-300 transition">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Produk</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ stats.products_count }}</h3>
                            <p class="text-xs text-blue-600 font-semibold">Katalog Aktif</p>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl border border-blue-100">
                            <ShoppingBag class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs group hover:border-emerald-300 transition">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pesanan</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ stats.orders_count }}</h3>
                            <p class="text-xs text-emerald-600 font-semibold">WhatsApp Checkout</p>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                            <ShoppingCart class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs group hover:border-amber-300 transition">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Konfirmasi</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ stats.pending_orders_count }}</h3>
                            <p class="text-xs text-amber-600 font-semibold">Perlu Tindakan</p>
                        </div>
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl border border-amber-100">
                            <Clock class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs group hover:border-indigo-300 transition">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Penjualan</span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900">{{ formatCurrency(stats.total_revenue) }}</h3>
                            <p class="text-xs text-indigo-600 font-semibold">Transaksi Sukses</p>
                        </div>
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100">
                            <DollarSign class="w-6 h-6" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- GEO & AI Integration Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- GEO Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex items-start gap-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl shrink-0 border border-blue-100">
                        <Globe class="w-6 h-6" />
                    </div>
                    <div class="space-y-2 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 text-sm">Generative Engine Optimization (GEO)</h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                Active &bull; AI Ready
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            File <code class="text-blue-600 font-semibold">llms.txt</code>, <code class="text-blue-600 font-semibold">sitemap.xml</code>, dan Schema.org JSON-LD otomatis digenerate secara real-time agar katalog produk Anda mudah diindeks ChatGPT, Perplexity, & Gemini.
                        </p>
                        <div class="pt-2 flex items-center gap-4 text-xs font-semibold">
                            <a :href="`/shop/${tenant?.slug || 'solkit-store'}/llms.txt`" target="_blank" class="text-blue-600 hover:text-blue-700 underline font-mono">
                                Lihat llms.txt &rarr;
                            </a>
                            <a :href="`/shop/${tenant?.slug || 'solkit-store'}/sitemap.xml`" target="_blank" class="text-blue-600 hover:text-blue-700 underline font-mono">
                                Lihat sitemap.xml &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- AI Sales Consultant Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex items-start gap-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl shrink-0 border border-indigo-100">
                        <Bot class="w-6 h-6" />
                    </div>
                    <div class="space-y-2 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 text-sm">AI Sales Consultant Ready</h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold border border-indigo-200">
                                RAG Pipeline Active
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Sistem AI Sales Consultant di storefront membaca seluruh katalog dan stok produk Anda untuk menjawab pertanyaan calon pembeli 24/7 sebelum checkout WhatsApp.
                        </p>
                        <div class="pt-2">
                            <Link :href="route('admin.settings.index')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 underline">
                                Atur Prompt & Karakter AI Sales &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Pesanan Masuk Terbaru (OMS)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi checkout WhatsApp yang masuk.</p>
                    </div>
                    <Link
                        :href="route('admin.orders.index')"
                        class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1"
                    >
                        <span>Lihat Semua Pesanan</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div v-if="recent_orders && recent_orders.length > 0" class="divide-y divide-slate-100 overflow-x-auto">
                    <div
                        v-for="order in recent_orders"
                        :key="order.id"
                        class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/70 transition"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-800">{{ order.order_number }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-xs text-slate-500">{{ formatDate(order.created_at) }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">{{ order.customer_name }}</h4>
                            <p class="text-xs text-slate-500">
                                {{ order.items ? order.items.map(i => `${i.product_name} (${i.quantity}x)`).join(', ') : '-' }}
                            </p>
                        </div>

                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-1.5 shrink-0">
                            <span class="text-sm sm:text-base font-extrabold text-slate-900">
                                {{ formatCurrency(order.total_amount) }}
                            </span>
                            <span
                                :class="[
                                    'text-[10px] uppercase font-bold px-2 py-0.5 rounded-full border',
                                    order.status === 'confirmed' || order.status === 'completed'
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                        : order.status === 'pending'
                                        ? 'bg-amber-50 text-amber-700 border-amber-200'
                                        : 'bg-slate-50 text-slate-600 border-slate-200'
                                ]"
                            >
                                {{ order.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12 text-slate-400">
                    <p class="text-xs">Belum ada pesanan WhatsApp masuk.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
