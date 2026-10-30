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
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                        <Package class="w-6 h-6 text-blue-600" />
                        Katalog Produk &amp; Inventaris
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Kelola katalog produk toko Anda untuk storefront {{ tenant?.name }}.
                    </p>
                </div>

                <Link
                    :href="route('admin.products.create')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold shadow-sm transition"
                >
                    <Plus class="w-4 h-4" />
                    Tambah Produk Baru
                </Link>
            </div>

            <!-- Search & Filters -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex items-center justify-between gap-4 shadow-xs">
                <form @submit.prevent="handleSearch" class="relative flex-1 max-w-md">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama produk atau SKU..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                    />
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" />
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50/80 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="p-4">Produk</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Harga</th>
                                <th class="p-4">Stok</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-slate-50/70 transition">
                                <td class="p-4 flex items-center gap-3">
                                    <img
                                        :src="product.thumbnail || '/images/solkit-clean.png'"
                                        class="w-10 h-10 object-contain rounded-lg bg-slate-50 p-1 border border-slate-200 shrink-0"
                                    />
                                    <div>
                                        <p class="font-bold text-slate-900">{{ product.name }}</p>
                                        <p v-if="product.sku" class="text-xs font-mono text-slate-400">{{ product.sku }}</p>
                                    </div>
                                </td>
                                <td class="p-4 text-slate-500">
                                    {{ product.category?.name || '-' }}
                                </td>
                                <td class="p-4 font-semibold text-slate-900">
                                    {{ formatCurrency(product.price) }}
                                </td>
                                <td class="p-4">
                                    <span :class="['font-semibold text-xs', product.stock > 5 ? 'text-emerald-700' : 'text-amber-600']">
                                        {{ product.stock }} unit
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border',
                                            product.status === 'published' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200'
                                        ]"
                                    >
                                        {{ product.status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-1">
                                    <Link
                                        :href="route('admin.products.edit', product.id)"
                                        class="p-2 inline-flex text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Edit"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </Link>
                                    <button
                                        @click="deleteProduct(product.id)"
                                        class="p-2 inline-flex text-rose-500 hover:bg-rose-50 rounded-lg transition"
                                        title="Hapus"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!products.data || products.data.length === 0">
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    Belum ada produk yang ditambahkan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products.links && products.links.length > 3" class="p-4 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Menampilkan {{ products.from || 0 }} - {{ products.to || 0 }} dari {{ products.total || 0 }} produk
                    </span>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, i) in products.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1 text-xs rounded-lg transition border',
                                link.active ? 'bg-blue-600 text-white border-blue-600' : 'text-slate-600 hover:bg-slate-100 border-slate-200',
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
