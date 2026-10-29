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
        <div class="space-y-8">
            <!-- Header Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-slate-800 shadow-xl">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        ⚡ Solkit Multi-tenant SaaS Engine
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mt-2">
                        {{ tenant?.name || 'Solkit Smart Merchant' }}
                    </h2>
                    <p class="text-sm text-slate-300 mt-1">
                        Pusat kontrol inventaris, Order Management System (OMS), dan Generative Engine Optimization (GEO).
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        :href="`/shop/${tenant?.slug || 'solkit-store'}`"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-lg shadow-blue-600/30 transition"
                    >
                        <span>Buka Toko Online</span>
                        <ExternalLink class="w-4 h-4" />
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Products Count -->
                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-6 relative overflow-hidden shadow-xl group">
                    <div class="flex items-center justify-between">
                        <div class="space-y-2">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Produk</span>
                            <h3 class="text-3xl font-black text-white">{{ stats.products_count }}</h3>
                            <p class="text-xs text-indigo-400 font-medium">Katalog Aktif</p>
                        </div>
                        <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl border border-indigo-500/20">
                            <ShoppingBag class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-6 relative overflow-hidden shadow-xl group">
                    <div class="flex items-center justify-between">
                        <div class="space-y-2">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pesanan</span>
                            <h3 class="text-3xl font-black text-white">{{ stats.orders_count }}</h3>
                            <p class="text-xs text-emerald-400 font-medium">WhatsApp Checkout</p>
                        </div>
                        <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20">
                            <ShoppingCart class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-6 relative overflow-hidden shadow-xl group">
                    <div class="flex items-center justify-between">
                        <div class="space-y-2">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Konfirmasi</span>
                            <h3 class="text-3xl font-black text-white">{{ stats.pending_orders_count }}</h3>
                            <p class="text-xs text-amber-400 font-medium">Perlu Tindakan</p>
                        </div>
                        <div class="p-3 bg-amber-500/10 text-amber-400 rounded-xl border border-amber-500/20">
                            <Clock class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-6 relative overflow-hidden shadow-xl group">
                    <div class="flex items-center justify-between">
                        <div class="space-y-2">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Penjualan</span>
                            <h3 class="text-2xl font-black text-white">{{ formatCurrency(stats.total_revenue) }}</h3>
                            <p class="text-xs text-blue-400 font-medium">Transaksi Sukses</p>
                        </div>
                        <div class="p-3 bg-blue-500/10 text-blue-400 rounded-xl border border-blue-500/20">
                            <DollarSign class="w-6 h-6" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- GEO & AI Status Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl flex items-start gap-4">
                    <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl border border-indigo-500/20 shrink-0">
                        <Globe class="w-6 h-6" />
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-bold text-white text-base">Generative Engine Optimization (GEO)</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            File <code>llms.txt</code>, <code>sitemap.xml</code>, dan Schema.org JSON-LD ter-generate otomatis secara real-time untuk mempermudah indeks AI Search Engine seperti ChatGPT Search, Perplexity, dan Google Gemini.
                        </p>
                        <div class="pt-2 flex items-center gap-3 text-xs font-mono text-indigo-400">
                            <a :href="`/shop/${tenant?.slug}/llms.txt`" target="_blank" class="hover:underline">View llms.txt &rarr;</a>
                            <a :href="`/shop/${tenant?.slug}/sitemap.xml`" target="_blank" class="hover:underline">View sitemap.xml &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl flex items-start gap-4">
                    <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20 shrink-0">
                        <Bot class="w-6 h-6" />
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-bold text-white text-base">AI Sales Consultant Ready</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Sistem AI Consultant storefront otomatis membaca seluruh katalog dan stok produk toko Anda untuk menjawab pertanyaan calon pembeli sebelum melakukan checkout WhatsApp.
                        </p>
                        <div class="pt-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                RAG Pipeline Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="bg-slate-950 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white">Pesanan Masuk Terbaru (OMS)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi checkout WhatsApp yang masuk.</p>
                    </div>
                    <Link :href="route('admin.orders.index')" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center gap-1 font-semibold">
                        Lihat Semua Pesanan
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div class="divide-y divide-slate-800/60">
                    <div
                        v-for="order in recent_orders"
                        :key="order.id"
                        class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-900/30 transition"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-indigo-400">{{ order.order_number }}</span>
                                <span class="text-xs text-slate-500">&bull; {{ formatDate(order.created_at) }}</span>
                            </div>
                            <h4 class="font-bold text-slate-200 text-sm">{{ order.customer_name }}</h4>
                            <p class="text-xs text-slate-400">
                                {{ order.items?.map((i) => `${i.product_name} (${i.quantity}x)`).join(', ') }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-4">
                            <div class="text-right">
                                <p class="text-sm font-bold text-emerald-400">{{ formatCurrency(order.total_amount) }}</p>
                                <span class="text-xs font-semibold uppercase text-slate-500">{{ order.payment_method }}</span>
                            </div>
                            <span
                                :class="[
                                    'px-2.5 py-1 rounded-full text-xs font-semibold',
                                    order.status === 'completed' ? 'bg-emerald-500/10 text-emerald-400' :
                                    order.status === 'confirmed' ? 'bg-blue-500/10 text-blue-400' :
                                    'bg-amber-500/10 text-amber-400'
                                ]"
                            >
                                {{ order.status }}
                            </span>
                        </div>
                    </div>

                    <div v-if="!recent_orders || recent_orders.length === 0" class="p-8 text-center text-slate-500 text-sm">
                        Belum ada pesanan terbaru.
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
