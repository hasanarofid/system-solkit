<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    protected function getActiveTenant(): Tenant
    {
        if (auth()->check() && auth()->user()->tenant_id) {
            return auth()->user()->tenant;
        }

        return Tenant::firstOrCreate(
            ['slug' => 'solkit-store'],
            [
                'name' => 'Solkit Smart Merchant',
                'phone_whatsapp' => '6281316053870',
                'email' => 'sales@solkit.tech',
                'address' => 'Jakarta, Indonesia',
                'is_active' => true,
            ]
        );
    }

    /**
     * Display the merchant store settings page.
     */
    public function index(): Response
    {
        $tenant = $this->getActiveTenant();
        $setting = StoreSetting::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'store_name' => $tenant->name,
                'tagline' => 'Katalog Cerdas Instan & Checkout WhatsApp Terpadu',
                'about' => 'Solkit Smart Merchant menyediakan produk kurasi terbaik dengan kemudahan order langsung ke WhatsApp dan dukungan konsultasi AI Sales 24/7.',
                'wa_checkout_template' => "Halo Solkit Store, saya ingin memesan barang berikut:\n{items}\nTotal: {total}",
                'llm_system_prompt' => "Anda adalah Asisten Penjualan AI pintar dan ramah dari Solkit Smart Merchant. Bantu calon pelanggan memahami spesifikasi, ketersediaan, dan rekomendasi produk terbaik dari katalog kami.",
                'is_ai_sales_enabled' => true,
            ]
        );

        return Inertia::render('Admin/Settings', [
            'tenant' => $tenant,
            'setting' => $setting,
        ]);
    }

    /**
     * Update the tenant store settings and sync dynamically with storefront.
     */
    public function update(Request $request): RedirectResponse
    {
        $tenant = $this->getActiveTenant();
        $setting = StoreSetting::firstOrCreate(['tenant_id' => $tenant->id]);

        $validated = $request->validate([
            // Tenant Identity
            'name' => 'required|string|max:150',
            'phone_whatsapp' => 'required|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',

            // Storefront & Brand Display
            'store_name' => 'required|string|max:150',
            'tagline' => 'nullable|string|max:255',
            'about' => 'nullable|string|max:1000',
            'wa_checkout_template' => 'nullable|string|max:1000',
            'llm_system_prompt' => 'nullable|string|max:2000',
            'is_ai_sales_enabled' => 'boolean',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('tenants', 'public');
            $tenant->logo_url = '/storage/' . $path;
        }

        $tenant->update([
            'name' => $validated['name'],
            'phone_whatsapp' => $validated['phone_whatsapp'],
            'email' => $validated['email'] ?? $tenant->email,
            'address' => $validated['address'] ?? $tenant->address,
        ]);

        $setting->update([
            'store_name' => $validated['store_name'],
            'tagline' => $validated['tagline'] ?? '',
            'about' => $validated['about'] ?? '',
            'wa_checkout_template' => $validated['wa_checkout_template'] ?? "Halo Solkit Store, saya ingin memesan barang berikut:\n{items}\nTotal: {total}",
            'llm_system_prompt' => $validated['llm_system_prompt'] ?? '',
            'is_ai_sales_enabled' => $request->boolean('is_ai_sales_enabled', true),
            'meta_title' => $validated['meta_title'] ?? $validated['store_name'],
            'meta_description' => $validated['meta_description'] ?? $validated['about'],
        ]);

        return redirect()->back()->with('success', 'Pengaturan toko berhasil diperbarui dan disinkronkan ke storefront.');
    }
}
