<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for Solkit Smart-Commerce & AI-GEO Hub.
     */
    public function run(): void
    {
        // 1. Seed Roles & Permissions if exists
        if (class_exists(RoleAndPermissionSeeder::class)) {
            $this->call(RoleAndPermissionSeeder::class);
        }

        // 2. Create Default Tenant: Solkit Smart-Commerce Demo
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'solkit-store'],
            [
                'name' => 'Solkit Smart Merchant',
                'custom_domain' => null,
                'phone_whatsapp' => '6281316053870',
                'email' => 'contact@solkit.tech',
                'address' => 'Jl. Boulevard Smart Tech No. 88, Jakarta Selatan, DKI Jakarta 12950',
                'logo_url' => '/images/solkit-clean.png',
                'is_active' => true,
                'business_profile' => [
                    'category' => 'Retail & E-Commerce',
                    'operating_hours' => 'Senin - Sabtu: 08:00 - 21:00 WIB',
                    'tagline' => 'Belanja Cepat, Mudah, & Terverifikasi AI-GEO',
                ],
            ]
        );

        // 3. Create Store Settings for Tenant
        StoreSetting::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'store_name' => 'Solkit Smart Merchant',
                'tagline' => 'Katalog Cerdas Instan & Checkout WhatsApp Terpadu',
                'about' => 'Solkit Smart Merchant menyediakan produk kurasi terbaik dengan kemudahan order langsung ke WhatsApp dan dukungan konsultasi AI Sales 24/7.',
                'primary_color' => '#2563EB',
                'secondary_color' => '#0F172A',
                'currency' => 'IDR',
                'currency_symbol' => 'Rp',
                'wa_checkout_template' => "Halo Solkit Store, saya ingin memesan barang berikut:\n{items}\nTotal: {total}",
                'llm_system_prompt' => "Anda adalah Asisten Penjualan AI pintar dan ramah dari Solkit Smart Merchant. Bantu calon pelanggan memahami spesifikasi, ketersediaan, dan rekomendasi produk terbaik dari katalog kami.",
                'banner_url' => '/images/solkit.jpeg',
                'meta_title' => 'Solkit Smart Merchant - Toko Online Instan & AI Commerce',
                'meta_description' => 'Jelajahi produk berkualitas tinggi dengan checkout WhatsApp otomatis dan asisten AI pintar.',
                'meta_keywords' => 'solkit, link-commerce, toko online, whatsapp checkout, umkm',
                'is_ai_sales_enabled' => true,
            ]
        );

        // 4. Create Users (Superadmin & Tenant Admin)
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@solkit.tech'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Solkit Administrator',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'phone' => '6281316053870',
            ]
        );
        if (method_exists($superAdmin, 'assignRole')) {
            $superAdmin->assignRole('admin');
        }

        $merchantOwner = User::updateOrCreate(
            ['email' => 'merchant@solkit.tech'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Budi Santoso (Merchant Owner)',
                'password' => Hash::make('password'),
                'role' => 'tenant_admin',
                'phone' => '6281234567890',
            ]
        );
        if (method_exists($merchantOwner, 'assignRole')) {
            $merchantOwner->assignRole('client');
        }

        // 5. Seed Product Categories
        $categoriesData = [
            ['name' => 'Gadget & Smart Devices', 'slug' => 'gadget-smart-devices'],
            ['name' => 'Fashion & Daily Wear', 'slug' => 'fashion-daily-wear'],
            ['name' => 'Specialty Coffee & Beverages', 'slug' => 'coffee-beverages'],
            ['name' => 'Office & Lifestyle', 'slug' => 'office-lifestyle'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $cat['name'],
                    'type' => 'product',
                    'is_active' => true,
                ]
            );
        }

        // 6. Seed Products
        $productsData = [
            [
                'category_slug' => 'gadget-smart-devices',
                'name' => 'Smart Desk Wireless Fast Charger 15W',
                'sku' => 'SLK-WLC-01',
                'price' => 249000,
                'compare_at_price' => 320000,
                'stock' => 85,
                'short_description' => 'Pad charger nirkabel ultra-slim dengan proteksi suhu dan pengisian daya kilat Qi Certified.',
                'description' => '<p>Nikmati kemudahan mengisi daya smartphone tanpa repot kabel ruwet. Dilengkapi chipset cerdas anti-overheat dan LED indikator elegan.</p>',
                'thumbnail' => '/images/solkit-clean.png',
                'is_featured' => true,
            ],
            [
                'category_slug' => 'gadget-smart-devices',
                'name' => 'Noise-Cancelling True Wireless Earbuds Pro',
                'sku' => 'SLK-TWS-PRO',
                'price' => 599000,
                'compare_at_price' => 799000,
                'stock' => 40,
                'short_description' => 'Earbuds Bluetooth 5.3 dengan Active Noise Cancellation (ANC), bass bertenaga, dan ketahanan baterai 32 jam.',
                'description' => '<p>Kualitas audio studio di telinga Anda. Nyaman digunakan sepanjang hari untuk panggilan kerja maupun menikmati playlist musik favorit.</p>',
                'thumbnail' => '/images/solkit-clean.png',
                'is_featured' => true,
            ],
            [
                'category_slug' => 'coffee-beverages',
                'name' => 'Solkit Signature Gayo Arabica Roast Beans 250g',
                'sku' => 'SLK-KOP-250',
                'price' => 85000,
                'compare_at_price' => 99000,
                'stock' => 120,
                'short_description' => 'Biji kopi sangrai 100% Arabika dataran tinggi Gayo Aceh dengan notes cokelat manis, karamel, dan body lembut.',
                'description' => '<p>Dipanen dari perkebunan organik berkelanjutan, di-roast secara presisi untuk menghasilkan ekstraksi espresso atau manual brew yang sempurna.</p>',
                'thumbnail' => '/images/solkit-clean.png',
                'is_featured' => true,
            ],
            [
                'category_slug' => 'coffee-beverages',
                'name' => 'Cold Brew Concentrate Bottle 500ml',
                'sku' => 'SLK-CB-500',
                'price' => 65000,
                'compare_at_price' => 75000,
                'stock' => 50,
                'short_description' => 'Konsentrat kopi seduh dingin selama 18 jam, rendah asam, siap saji dengan susu atau air es.',
                'description' => '<p>Praktis untuk teman produktif harian Anda di meja kerja. Tahan disimpan dalam chiller hingga 2 minggu.</p>',
                'thumbnail' => '/images/solkit-clean.png',
                'is_featured' => false,
            ],
            [
                'category_slug' => 'fashion-daily-wear',
                'name' => 'Solkit Heavyweight Minimalist Oversized Tee',
                'sku' => 'SLK-TEE-BLK',
                'price' => 149000,
                'compare_at_price' => 199000,
                'stock' => 95,
                'short_description' => 'Kaos polos katun combed 24s gramasi tebal yang adem, jahitan rantai rapi, potongan drop shoulder modern.',
                'description' => '<p>Esensial harian pria dan wanita. Bahan tidak menyusut dan tetap kokoh meski telah dicuci berulang kali.</p>',
                'thumbnail' => '/images/solkit-clean.png',
                'is_featured' => true,
            ],
            [
                'category_slug' => 'office-lifestyle',
                'name' => 'Ergonomic Memory Foam Lumbar Support Cushion',
                'sku' => 'SLK-LMB-01',
                'price' => 189000,
                'compare_at_price' => 245000,
                'stock' => 60,
                'short_description' => 'Bantal sandaran punggung busa memori ergonomis pereda pegal duduk seharian di kursi kerja.',
                'description' => '<p>Dirancang sesuai kontur tulang belakang untuk memperbaiki postur duduk dan mencegah sakit pinggang saat bekerja lama.</p>',
                'thumbnail' => '/images/solkit-clean.png',
                'is_featured' => false,
            ],
        ];

        foreach ($productsData as $prod) {
            $catId = $categories[$prod['category_slug']]->id ?? null;
            $slug = Str::slug($prod['name']);

            Product::updateOrCreate(
                ['slug' => $slug, 'tenant_id' => $tenant->id],
                [
                    'category_id' => $catId,
                    'name' => $prod['name'],
                    'sku' => $prod['sku'],
                    'price' => $prod['price'],
                    'compare_at_price' => $prod['compare_at_price'],
                    'stock' => $prod['stock'],
                    'short_description' => $prod['short_description'],
                    'description' => $prod['description'],
                    'thumbnail' => $prod['thumbnail'],
                    'is_featured' => $prod['is_featured'],
                    'status' => 'published',
                ]
            );
        }

        // 7. Seed Initial Demo Order for OMS testing
        $sampleProduct = Product::where('tenant_id', $tenant->id)->first();
        if ($sampleProduct) {
            $order = Order::updateOrCreate(
                ['order_number' => 'ORD-DEMO-001'],
                [
                    'tenant_id' => $tenant->id,
                    'customer_name' => 'Ahmad Fauzi',
                    'customer_phone' => '081299887766',
                    'customer_email' => 'fauzi@example.com',
                    'customer_address' => 'Jl. Tebet Timur Dalam No. 12, Tebet, Jakarta Selatan',
                    'total_amount' => $sampleProduct->price * 2,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => 'whatsapp_checkout',
                    'notes' => 'Tolong kirim sebelum jam 3 sore.',
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order->id, 'product_id' => $sampleProduct->id],
                [
                    'product_name' => $sampleProduct->name,
                    'sku' => $sampleProduct->sku,
                    'price' => $sampleProduct->price,
                    'quantity' => 2,
                    'subtotal' => $sampleProduct->price * 2,
                ]
            );
        }
    }
}
