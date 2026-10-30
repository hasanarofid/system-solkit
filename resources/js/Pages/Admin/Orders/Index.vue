<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ShoppingCart, Search, CheckCircle, Clock, XCircle, Phone } from '@lucide/vue';

const props = defineProps({
    tenant: Object,
    orders: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');

const handleSearch = () => {
    router.get(
        route('admin.orders.index'),
        {
            search: search.value || undefined,
            status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        },
        { preserveState: true, replace: true }
    );
};

const updateStatus = (orderId, newStatus, paymentStatus) => {
    router.patch(route('admin.orders.update-status', orderId), {
        status: newStatus,
        payment_status: paymentStatus,
    });
};

const formatCurrency = (val) => {
    return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Order Management System (OMS) - Merchant Admin" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                        <ShoppingCart class="w-6 h-6 text-blue-600" />
                        Pesanan Masuk (Order Management System)
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Kelola data pesanan pembeli yang masuk dari Storefront WhatsApp Checkout untuk {{ tenant?.name }}.
                    </p>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                <form @submit.prevent="handleSearch" class="relative w-full sm:max-w-md">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari No. Order, Nama, atau Telepon..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
                    />
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                </form>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select
                        v-model="statusFilter"
                        @change="handleSearch"
                        class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition font-medium"
                    >
                        <option value="all">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="p-4">No. Order</th>
                                <th class="p-4">Pelanggan</th>
                                <th class="p-4">Item Pesanan</th>
                                <th class="p-4">Total</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Waktu</th>
                                <th class="p-4 text-right">Update Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-slate-50/70 transition">
                                <td class="p-4 font-mono font-bold text-blue-600">
                                    {{ order.order_number }}
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-slate-900">{{ order.customer_name }}</p>
                                    <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                        <Phone class="w-3 h-3" />
                                        {{ order.customer_phone }}
                                    </p>
                                </td>
                                <td class="p-4 text-xs">
                                    <ul class="space-y-1">
                                        <li v-for="item in order.items" :key="item.id" class="text-slate-600">
                                            &bull; {{ item.product_name }} ({{ item.quantity }}x)
                                        </li>
                                    </ul>
                                </td>
                                <td class="p-4 font-bold text-emerald-600">
                                    {{ formatCurrency(order.total_amount) }}
                                </td>
                                <td class="p-4">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold',
                                            order.status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                            order.status === 'confirmed' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                                            order.status === 'cancelled' ? 'bg-rose-50 text-rose-700 border border-rose-200' :
                                            'bg-amber-50 text-amber-700 border border-amber-200'
                                        ]"
                                    >
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="p-4 text-xs text-slate-500">
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button
                                            v-if="order.status !== 'confirmed'"
                                            @click="updateStatus(order.id, 'confirmed', 'paid')"
                                            class="px-2.5 py-1 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 hover:border-transparent rounded-lg text-xs font-semibold transition shadow-2xs"
                                            title="Konfirmasi Pesanan"
                                        >
                                            Konfirmasi
                                        </button>
                                        <button
                                            v-if="order.status !== 'completed'"
                                            @click="updateStatus(order.id, 'completed', 'paid')"
                                            class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-transparent rounded-lg text-xs font-semibold transition shadow-2xs"
                                            title="Selesaikan"
                                        >
                                            Selesai
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!orders.data || orders.data.length === 0">
                                <td colspan="7" class="p-8 text-center text-slate-500">
                                    Belum ada pesanan masuk.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="orders.links && orders.links.length > 3" class="p-4 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Menampilkan {{ orders.from || 0 }} - {{ orders.to || 0 }} dari {{ orders.total || 0 }} pesanan
                    </span>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, i) in orders.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 text-xs rounded-lg transition font-medium',
                                link.active ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100',
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
