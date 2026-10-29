<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Search, Edit2, Trash2, Package, CheckCircle2, AlertCircle } from '@lucide/vue';

const props = defineProps({
    tenant: Object,
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const handleSearch = () => {
    router.get(route('admin.products.index'), { search: search.value || undefined }, { preserveState: true, replace: true });
};

const deleteProduct = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        router.delete(route('admin.products.destroy', id));
    }
};

const formatCurrency = (val) => {
    return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
};
</script>

<template>
    <AdminLayout>
        <Head title="Katalog Produk - Merchant Admin" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                        <Package class="w-6 h-6 text-indigo-400" />
                        Katalog Produk &amp; Inventaris
                    </h1>
                    <p class="text-sm text-slate-400 mt-1">
                        Kelola katalog produk toko Anda (mendukung 50 hingga 200+ varian produk) untuk toko online {{ tenant?.name }}.
                    </p>
                </div>

                <Link
                    :href="route('admin.products.create')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition"
                >
                    <Plus class="w-4 h-4" />
                    Tambah Produk
                </Link>
            </div>

            <!-- Search & Filters -->
            <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800 flex items-center justify-between gap-4">
                <form @submit.prevent="handleSearch" class="relative flex-1 max-w-md">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama produk atau SKU..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                    />
                    <Search class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" />
                </form>
            </div>

            <!-- Table -->
            <div class="bg-slate-950/60 rounded-2xl border border-slate-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-900/80 text-xs uppercase font-semibold text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="p-4">Produk</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Harga</th>
                                <th class="p-4">Stok</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-slate-900/40 transition">
                                <td class="p-4 flex items-center gap-3">
                                    <img
                                        :src="product.thumbnail || '/images/solkit-clean.png'"
                                        class="w-10 h-10 object-contain rounded-lg bg-slate-900 p-1 border border-slate-800 shrink-0"
                                    />
                                    <div>
                                        <p class="font-bold text-slate-200">{{ product.name }}</p>
                                        <p v-if="product.sku" class="text-xs font-mono text-slate-500">{{ product.sku }}</p>
                                    </div>
                                </td>
                                <td class="p-4 text-slate-400">
                                    {{ product.category?.name || '-' }}
                                </td>
                                <td class="p-4 font-semibold text-slate-200">
                                    {{ formatCurrency(product.price) }}
                                </td>
                                <td class="p-4">
                                    <span :class="['font-semibold', product.stock > 10 ? 'text-emerald-400' : 'text-amber-400']">
                                        {{ product.stock }} unit
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold',
                                            product.status === 'published' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400'
                                        ]"
                                    >
                                        {{ product.status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <Link
                                        :href="route('admin.products.edit', product.id)"
                                        class="p-2 inline-flex text-indigo-400 hover:bg-slate-800 rounded-lg transition"
                                        title="Edit"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </Link>
                                    <button
                                        @click="deleteProduct(product.id)"
                                        class="p-2 inline-flex text-rose-400 hover:bg-slate-800 rounded-lg transition"
                                        title="Hapus"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!products.data || products.data.length === 0">
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    Belum ada produk yang ditambahkan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products.links && products.links.length > 3" class="p-4 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Menampilkan {{ products.from || 0 }} - {{ products.to || 0 }} dari {{ products.total || 0 }} produk
                    </span>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, i) in products.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 text-xs rounded-lg transition',
                                link.active ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800',
                                !link.url && 'opacity-40 pointer-events-none'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
