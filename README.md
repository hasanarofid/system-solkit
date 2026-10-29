# Solkit Smart-Commerce & AI-GEO Hub

Platform B2B Multi-tenant SaaS untuk memfasilitasi UMKM & ritel membuat **"Link Commerce / Toko Online" instan** yang dilengkapi fitur **AI Sales Consultant** dan teroptimasi secara **Generative Engine Optimization (GEO)**.

- **Target Domain:** `system.solkit.tech`
- **Tech Stack:** Laravel 11 Backend API, Vue 3 (Inertia.js) Storefront & Admin, PostgreSQL, Redis, Docker
- **Repository Baru:** [https://github.com/hasanarofid/system-solkit.git](https://github.com/hasanarofid/system-solkit.git)

---

## ⚡ Fitur Utama (Core Features)

### 1. Multi-Tenant Shared Backend System
- **Data Isolation:** Seluruh query produk, order, kategori, dan pengaturan toko terisolasi secara otomatis berdasarkan `tenant_id`.
- **Kapabilitas Dasar:** Multi-location inventory support, Order Management System (OMS), persiapan slot Payment Gateway.
- **Merchant Admin Panel:** Dashboard manajemen inventaris katalog (mendukung 50 hingga 200+ produk), pemrosesan pesanan, dan konfigurasi hotline WhatsApp.

### 2. Smart Link-Commerce (Storefront)
- **Katalog Responsif Instan:** Render halaman toko dengan hero banner, filter kategori, pencarian, dan grid katalog instan.
- **Keranjang Belanja (Cart):** LocalStorage reactive state drawer cart untuk kemudahan multi-item shopping.
- **Automated WhatsApp Checkout:** Memformat nomor pesanan, detail item x qty x harga, total kalkulasi, data pembeli, dan alamat kirim langsung diarahkan (redirect) ke WhatsApp resmi penjual.

### 3. Generative Engine Optimization (GEO) Module
- **Realtime `llms.txt` Generator:** Endpoint `/llms.txt` dan `/shop/{tenant_slug}/llms.txt` yang memaparkan data produk dan profil toko dalam format markdown standar untuk dicerna oleh AI Search Engines (ChatGPT Search, Google Gemini, Perplexity).
- **Auto-injected Schema.org:** Menyisipkan struktur JSON-LD (`Store`, `Product`, `Offer`) pada head dokumen HTML.
- **Dynamic `sitemap.xml` & `robots.txt`:** Crawlability optimal untuk search engine konvensional dan LLM web crawlers.

### 4. AI Sales Agent (RAG-Lite)
- Endpoint `/api/v1/chat` dan `/shop/{tenant_slug}/chat` menginjeksi katalog produk tenant ke dalam context prompt asisten AI.
- Membantu calon pembeli menanyakan ketersediaan stok, harga, dan rekomendasi produk sebelum melakukan checkout WhatsApp.

---

## 🚀 Panduan Menjalankan Sistem

### Opsi 1: Menjalankan dengan Docker Compose (Direkomendasikan)
```bash
# Salin konfigurasi environment
cp .env.example .env

# Jalankan seluruh stack (App, PostgreSQL 16, Redis 7)
docker compose up -d --build

# Jalankan migrasi dan seeder demo
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### Opsi 2: Menjalankan Secara Lokal
```bash
# 1. Install Dependencies
composer install
npm install

# 2. Setup Environment
cp .env.example .env
php artisan key:generate

# 3. Jalankan Migrasi & Seeder
php artisan migrate --seed

# 4. Build Frontend Assets
npm run build
# atau untuk development: npm run dev

# 5. Jalankan Local Server
php artisan serve
```

---

## 🔑 Kredensial Default (Seeder)
- **Superadmin:** `admin@solkit.tech` | Password: `password`
- **Merchant Owner:** `merchant@solkit.tech` | Password: `password`
- **Demo Storefront:** `http://localhost:8000/shop/solkit-store` (atau langsung `http://localhost:8000/`)
- **llms.txt Endpoint:** `http://localhost:8000/llms.txt`
