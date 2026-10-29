<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeft, Save } from '@lucide/vue';

const props = defineProps({
    tenant: Object,
    categories: Array,
    product: Object,
});

const isEditing = !!props.product;

const form = useForm({
    name: props.product?.name || '',
    category_id: props.product?.category_id || '',
    sku: props.product?.sku || '',
    price: props.product?.price || '',
    compare_at_price: props.product?.compare_at_price || '',
    stock: props.product?.stock ?? 10,
    weight_grams: props.product?.weight_grams || 250,
    short_description: props.product?.short_description || '',
    description: props.product?.description || '',
    thumbnail: props.product?.thumbnail || '/images/solkit-clean.png',
    is_featured: props.product?.is_featured || false,
    status: props.product?.status || 'published',
});

const submit = () => {
    if (isEditing) {
        form.put(route('admin.products.update', props.product.id));
    } else {
        form.post(route('admin.products.store'));
    }
};
</script>

<template>
    <AdminLayout>
        <Head :title="isEditing ? 'Edit Produk' : 'Tambah Produk'" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.products.index')"
                        class="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition"
                    >
                        <ArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold text-white">
                            {{ isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}
                        </h1>
                        <p class="text-xs text-slate-400">
                            {{ isEditing ? `Perbarui informasi ${product.name}` : 'Masukkan rincian produk ke katalog merchant.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="bg-slate-950/60 p-6 rounded-2xl border border-slate-800 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                Nama Produk *
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="Contoh: Wireless Fast Charger 15W"
                                class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                            />
                            <p v-if="form.errors.name" class="text-xs text-rose-400 mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                Kategori
                            </label>
                            <select
                                v-model="form.category_id"
                                class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                            >
                                <option value="">Pilih Kategori</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                SKU / Kode Barang
                            </label>
                            <input
                                v-model="form.sku"
                                type="text"
                                placeholder="SLK-001"
                                class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                    Harga Jual (Rp) *
                                </label>
                                <input
                                    v-model="form.price"
                                    type="number"
                                    required
                                    min="0"
                                    placeholder="249000"
                                    class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                    Harga Coret (Rp)
                                </label>
                                <input
                                    v-model="form.compare_at_price"
                                    type="number"
                                    min="0"
                                    placeholder="299000"
                                    class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                    Stok Tersedia *
                                </label>
                                <input
                                    v-model="form.stock"
                                    type="number"
                                    required
                                    min="0"
                                    class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                    Berat (Gram)
                                </label>
                                <input
                                    v-model="form.weight_grams"
                                    type="number"
                                    min="0"
                                    class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                URL Thumbnail Foto Produk
                            </label>
                            <input
                                v-model="form.thumbnail"
                                type="text"
                                placeholder="/images/solkit-clean.png"
                                class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                Ringkasan Singkat (Untuk AI GEO &amp; WhatsApp)
                            </label>
                            <textarea
                                v-model="form.short_description"
                                rows="2"
                                placeholder="Deskripsi 1-2 kalimat untuk preview dan asisten AI..."
                                class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                Deskripsi Lengkap
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                placeholder="Penjelasan lengkap spesifikasi dan manfaat produk..."
                                class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                    Status Publikasi
                                </label>
                                <select
                                    v-model="form.status"
                                    class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                >
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="flex items-center pt-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        v-model="form.is_featured"
                                        type="checkbox"
                                        class="rounded bg-slate-900 border-slate-800 text-indigo-600 focus:ring-0"
                                    />
                                    <span class="text-xs font-semibold text-slate-300">Produk Unggulan</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <Link
                        :href="route('admin.products.index')"
                        class="px-5 py-2.5 rounded-xl border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-900 text-sm font-semibold transition"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold flex items-center gap-2 shadow-lg shadow-indigo-600/30 transition disabled:opacity-50"
                    >
                        <Save class="w-4 h-4" />
                        <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Produk' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
