# Product Requirements Document (PRD) & Execution Roadmap
**Product Name:** Solkit Smart Merchant (AI-GEO Ready Catalog & WA Checkout)  
**Live Prototype URL:** [https://system.solkit.tech/](https://system.solkit.tech/)  
**Entity:** Solkit.tech  
**Tech Stack:** Laravel (API Backend), Vue 3 / Inertia.js (Storefront & Admin), MySQL / PostgreSQL, Redis, Docker, MinIO/S3 Storage  

---

## 1. Ringkasan Eksekutif & Tujuan Produk
**Solkit Smart Merchant** adalah platform e-commerce / catalog engine B2B Multi-tenant instan berkecepatan tinggi yang dirancang khusus untuk UMKM, brand ritel, dan bisnis lokal.

Platform ini menggabungkan 4 pilar utama:
1. **Tampilan Katalog Modern & Cepat:** Fast loading, responsive grid, pencarian cepat, dan cart drawer reaktif.
2. **Checkout Langsung ke WhatsApp:** Otomatisasi format rincian order ke format pesan WhatsApp siap kirim ke penjual.
3. **AI Sales Consultant Agent:** Asisten cerdas berbasis prompt context katalog merchant (RAG-lite) yang melayani konsultasi pembeli 24/7.
4. **Generative Engine Optimization (GEO):** Pemetaan semantik otomatis (`llms.txt`, `sitemap.xml`, `robots.txt`, Schema.org JSON-LD) agar produk merchant terindeks dan direkomendasikan oleh mesin pencari AI generasi baru (ChatGPT, Google Gemini, Perplexity).

---

## 2. Spesifikasi Fitur Utama (Core Modules)

| Modul | Komponen / Fitur | Deskripsi Teknis |
| :--- | :--- | :--- |
| **Storefront (Front-End)** | • Responsive Product Grid<br>• Dynamic Search & Filter Category<br>• Direct WA Checkout Modal | UI/UX ringan (fast loading), mendukung variasi SKU, harga coret, dan badge ("Unggulan"). Cart state dikelola di client side secara reaktif via LocalStorage. |
| **AI Sales Agent** | • Widget "Tanya AI Sales"<br>• Catalog RAG System | Chatbot berbasis LLM API yang diinjeksi dengan seluruh data produk (SKU, deskripsi, harga, stok) untuk membantu pembeli memilih produk sebelum checkout. Dilengkapi fallback rule-based saat API eksternal offline. |
| **GEO Engine (AI-SEO)** | • Auto Generator `llms.txt`<br>• Auto Schema.org (JSON-LD)<br>• Dynamic `sitemap.xml` & `robots.txt` | Menghasilkan file semantik otomatis untuk setiap tenant agar terindeks sempurna oleh web crawler AI Search. |
| **Multi-Tenant Admin** | • Product & Inventory Management<br>• WhatsApp Message Formatter<br>• Order Management System (OMS)<br>• Tenant Settings | Panel admin untuk merchant menginput produk (50 - 200+ SKU), mengubah nomor WA tujuan, mengelola pesanan masuk, dan upload gambar produk. |

---

## 3. Analisa & Status Roadmap 6 Minggu

```
[Minggu 1: Backend & Multi-Tenant Core] ───► SELESAI (100%)
[Minggu 2: Storage & Storefront Refinement] ─► SELESAI (90% - Tinggal MinIO driver hook)
[Minggu 3: AI Sales Consultant Module] ────► SELESAI (100% - RAG Context Ready)
[Minggu 4: GEO Engine & WA Checkout] ──────► SELESAI (100% - Live Tested)
[Minggu 5: QA, Performance & SSL] ────────► SELESAI (85% - HTTPS Live, Nginx Ready)
[Minggu 6: Deployment & Launch] ──────────► SELESAI (90% - Live di system.solkit.tech)
```

### Detail Sprint:
- **Minggu 1: Fondasi Backend & Arsitektur Multi-Tenant** `[SELESAI]`
  - Schema database berbasis `tenant_id` terisolasi dengan `BelongsToTenant` trait.
  - RESTful API CRUD Produk, Kategori, Order, dan Autentikasi.
- **Minggu 2: Storage Infrastructure & Storefront Rendering** `[SELESAI / AKTIF]`
  - Storefront rendering live di `system.solkit.tech` dengan logo Solkit, catalog grid, pencarian cepat, category pills, dan cart drawer.
  - LocalStorage cart persistence.
- **Minggu 3: AI Sales Consultant Module** `[SELESAI]`
  - Contextual prompt builder dengan real-time inventory injection.
  - Endpoint `/api/v1/chat` & `/shop/{slug}/chat` dengan guardrails katalog merchant.
- **Minggu 4: Engine GEO & Routing WhatsApp Checkout** `[SELESAI]`
  - Endpoint `/llms.txt` & `/shop/{slug}/llms.txt` menghasilkan format standar markdown AI.
  - Schema.org JSON-LD auto-injected pada head HTML.
  - Service `WhatsAppCheckoutService` memformat rincian order dan nomor WA merchant.
- **Minggu 5: QA, Performance Tuning, & Hardening** `[AKTIF]`
  - SSL Let's Encrypt aktif dan terverifikasi di domain `system.solkit.tech`.
  - Nginx FastCGI pass ke PHP 8.5-FPM dengan gzip & static cache.
- **Minggu 6: Peluncuran Resmi & Onboarding** `[AKTIF]`
  - Live deployment v1.0 di VPS `201.18.218.42`.

---

## 4. Rencana Go-To-Market & Komersialisasi

### A. Paket Harga (Pricing Strategy)
- **Paket Starter (Rp 1.500.000 / setup):** Profil Usaha + Katalog Produk (hingga 50 produk) + WA Checkout.
- **Paket E-Commerce Lite + AI (Rp 2.800.000 / setup atau Rp 250.000/bulan):** Katalog Lengkap + Fitur "Tanya AI Sales" + Optimasi GEO (`llms.txt`) + Keranjang Belanja.
- **Paket Custom Enterprise (Mulai Rp 4.500.000+):** Custom Domain, Multi-Location Inventory, dan Integrasi Payment Gateway.

### B. Matriks Promosi
- **Minggu 1-2 (Pre-Launch Outreach):** Cold messaging terarah di Threads & Instagram DM ke UMKM/bisnis lokal yang membutuhkan katalog digital instan.
- **Minggu 3-4 (Content Marketing & AI-GEO Authority):** Video demo pembuatan toko online siap AI dalam 10 menit dan edukasi pentingnya file `llms.txt` untuk indexing ChatGPT/Gemini.
- **Minggu 5-6 (Direct Sales & Merchant Onboarding):** Program *"Free Setup 14 Hari"* untuk 10 merchant pertama di segmen F&B, fashion, dan toko ritel untuk social proof awal.

---

## 5. Indikator Kinerja Utama (KPIs)
- **Technical KPI:** Waktu page load Storefront < 1.5 detik, uptime server 99.9%.
- **Business KPI:** 10 Merchant Aktif dalam 30 hari pertama pasca-launching, membangun Recurring Revenue (MRR) awal dari biaya hosting dan maintenance sistem.
