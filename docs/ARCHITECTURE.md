# Solkit Smart-Commerce & AI-GEO Hub — System Architecture & Business Flow

Dokumen ini mendefinisikan arsitektur sistem, struktur multi-tenant, dan alur bisnis proses untuk **Solkit Smart-Commerce & AI-GEO Hub** (`system.solkit.tech`).

---

## 1. Profil & Model Bisnis
- **Nama Platform:** Solkit Smart-Commerce & AI-GEO Hub
- **Target Domain:** `system.solkit.tech`
- **Model Bisnis:** B2B Multi-tenant SaaS untuk memfasilitasi UMKM, toko ritel, dan brand lokal membuat Toko Online Instan berbasis Link-Commerce dengan checkout WhatsApp terpadu dan optimasi Generative Engine Optimization (GEO).

---

## 2. Alur Bisnis Proses (End-to-End Business Flow)

```
[Merchant Onboarding / Admin]
       │
       ▼
1. Registrasi Tenant & Pengaturan Profil Toko (WhatsApp Hotline, Tema, Branding)
       │
       ▼
2. Input Inventaris Produk (Katalog 50 - 200+ varian, SKU, Harga, Stok)
       │
       ├──────────────────────────────────────────────┐
       ▼                                              ▼
[Storefront Link-Commerce]               [GEO Module Worker/API]
       │                                              │
3. Calon Pembeli Mengakses Storefront        3a. Otomatis generate:
   - Pilih Kategori & Search                      - /llms.txt (format AI)
   - Tanya AI Sales Consultant (RAG)              - /sitemap.xml
   - Tambah Produk ke Keranjang (Cart)            - /robots.txt
       │                                          - Schema.org JSON-LD
       ▼                                              │
4. Checkout Instan (Form Nama, HP, Alamat)           ▼
       │                                  Terindeks oleh ChatGPT,
       ▼                                  Gemini, dan Perplexity
5. Simpan Pesanan ke OMS (Status: Pending)
       │
       ▼
6. Redirect Otomatis ke WhatsApp Penjual (Pesan Terformat Rapi)
       │
       ▼
7. Penjual Konfirmasi Pembayaran & Pengiriman di Dashboard Merchant
```

---

## 3. Komponen Arsitektur Utama

### A. Multi-Tenant Shared Database
- Setiap tabel transaksi dan konten (`products`, `orders`, `categories`, `store_settings`) memiliki kolom `tenant_id`.
- `BelongsToTenant` trait dan `TenantScope` memastikan isolasi data secara otomatis.

### B. Generative Engine Optimization (GEO)
- Endpoint `/llms.txt` merender katalog produk dan informasi merchant dalam standar markdown ringkas untuk memudahkan web-crawlers AI menjawab pertanyaan konsumen.
- Schema.org (`Product`, `Store`, `Offer`) disematkan pada setiap halaman produk.

### C. AI Sales Consultant (RAG-Lite)
- Endpoint `/api/v1/chat` mengekstraksi katalog produk tenant secara real-time dan menginjeksinya ke prompt LLM untuk melayani pertanyaan pembeli.

### D. Infrastruktur Container
- `docker-compose.yml` membungkus Laravel, PostgreSQL 16, dan Redis 7 Cache.
