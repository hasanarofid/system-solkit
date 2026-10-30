<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderManagerController;
use App\Http\Controllers\Admin\ProductManagerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Generative Engine Optimization (GEO) Dynamic Endpoints
|--------------------------------------------------------------------------
*/
Route::get('/llms.txt', [StorefrontController::class, 'llmsTxt'])->name('geo.llmstxt');
Route::get('/sitemap.xml', [StorefrontController::class, 'sitemapXml'])->name('geo.sitemap');
Route::get('/robots.txt', [StorefrontController::class, 'robotsTxt'])->name('geo.robots');

Route::get('/shop/{tenant_slug}/llms.txt', [StorefrontController::class, 'llmsTxt']);
Route::get('/shop/{tenant_slug}/sitemap.xml', [StorefrontController::class, 'sitemapXml']);
Route::get('/shop/{tenant_slug}/robots.txt', [StorefrontController::class, 'robotsTxt']);

/*
|--------------------------------------------------------------------------
| Smart Link-Commerce (Storefront & Catalog)
|--------------------------------------------------------------------------
*/
// Root Storefront (default tenant / resolved from subdomain)
Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::post('/checkout', [StorefrontController::class, 'checkout'])->name('storefront.checkout.root');
Route::post('/chat', [StorefrontController::class, 'chat'])->name('storefront.chat.root');

// Multi-tenant Storefront with explicit slug
Route::prefix('shop/{tenant_slug}')->name('shop.')->group(function () {
    Route::get('/', [StorefrontController::class, 'index'])->name('index');
    Route::get('/products/{product_slug}', [StorefrontController::class, 'showProduct'])->name('product.show');
    Route::post('/checkout', [StorefrontController::class, 'checkout'])->name('checkout');
    Route::post('/chat', [StorefrontController::class, 'chat'])->name('chat');
});

/*
|--------------------------------------------------------------------------
| Merchant & Tenant Admin Panel (OMS, Inventory, Settings)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('admin');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products Management (50 - 200+ items catalog)
    Route::resource('products', ProductManagerController::class);

    // Order Management System (OMS)
    Route::get('/orders', [OrderManagerController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderManagerController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderManagerController::class, 'updateStatus'])->name('orders.update-status');

    // Store Settings (Branding, WhatsApp Hotline, AI Consultant Prompt)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
