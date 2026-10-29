<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard overview for Solkit Smart-Commerce.
     */
    public function index(): Response
    {
        $tenant = auth()->check() && auth()->user()->tenant_id
            ? auth()->user()->tenant
            : Tenant::first();

        $tenantId = $tenant?->id;

        $productsCount = Product::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count();
        $ordersCount = Order::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count();
        $pendingOrders = Order::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->where('status', 'pending')->count();
        $totalRevenue = Order::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_amount');

        $recentOrders = Order::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('items')
            ->latest()
            ->take(6)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'tenant' => $tenant,
            'stats' => [
                'products_count' => $productsCount,
                'orders_count' => $ordersCount,
                'pending_orders_count' => $pendingOrders,
                'total_revenue' => (float) $totalRevenue,
            ],
            'recent_orders' => $recentOrders,
        ]);
    }
}
