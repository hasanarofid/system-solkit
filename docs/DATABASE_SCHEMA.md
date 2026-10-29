# Solkit Smart-Commerce — Database Schema & Data Dictionary

Arsitektur database dirancang untuk platform multi-tenant shared database menggunakan PostgreSQL 16.

---

## 1. Relasi Entity-Relationship (ERD Ringkas)

- **`tenants`** (1) ───< **`users`** (N)
- **`tenants`** (1) ─── (1) **`store_settings`**
- **`tenants`** (1) ───< **`categories`** (N)
- **`tenants`** (1) ───< **`products`** (N)
- **`tenants`** (1) ───< **`orders`** (N)
- **`orders`** (1) ───< **`order_items`** (N)

---

## 2. Definisi Tabel

### A. `tenants`
| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT PK | Primary key |
| `name` | VARCHAR | Nama bisnis/toko |
| `slug` | VARCHAR UNIQUE | Identifier sub-path (`/shop/{slug}`) |
| `custom_domain`| VARCHAR UNIQUE | Domain kustom tenant (opsional) |
| `phone_whatsapp`| VARCHAR | Nomor WhatsApp resmi untuk checkout |
| `email` | VARCHAR | Email kontak |
| `address` | TEXT | Alamat operasional toko |
| `logo_url` | VARCHAR | Path logo toko |
| `is_active` | BOOLEAN | Status aktif tenant |
| `business_profile` | JSON | Metadata jam kerja & kategori UMKM |

### B. `store_settings`
| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT PK | Primary key |
| `tenant_id` | BIGINT FK | Foreign key ke `tenants.id` (UNIQUE) |
| `store_name` | VARCHAR | Display name toko |
| `tagline` | VARCHAR | Tagline banner toko |
| `about` | TEXT | Deskripsi profil toko |
| `primary_color` | VARCHAR | Warna aksen utama (HEX) |
| `currency` | VARCHAR | Kode mata uang (default: `IDR`) |
| `currency_symbol` | VARCHAR | Simbol mata uang (default: `Rp`) |
| `wa_checkout_template` | TEXT | Format template pesan WhatsApp |
| `llm_system_prompt` | TEXT | Custom prompt untuk AI Sales Consultant |
| `is_ai_sales_enabled` | BOOLEAN | Flag status aktif AI Assistant |

### C. `products`
| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT PK | Primary key |
| `tenant_id` | BIGINT FK | Foreign key ke `tenants.id` |
| `category_id` | BIGINT FK | Kategori produk |
| `name` | VARCHAR | Nama produk |
| `slug` | VARCHAR | URL slug produk |
| `sku` | VARCHAR | Stock Keeping Unit |
| `price` | DECIMAL(14,2) | Harga jual |
| `compare_at_price` | DECIMAL(14,2) | Harga coret / diskon |
| `stock` | INT | Jumlah stok barang |
| `weight_grams` | INT | Berat produk (gram) |
| `short_description` | VARCHAR | Ringkasan untuk preview & AI GEO |
| `description` | TEXT | Deskripsi lengkap produk |
| `thumbnail` | VARCHAR | Path gambar utama |
| `is_featured` | BOOLEAN | Penanda produk unggulan |
| `status` | VARCHAR | `published`, `draft`, `archived` |

### D. `orders` & `order_items`
- **`orders`**: Menyimpan order number, customer name, phone, address, total amount, status (`pending`, `confirmed`, `completed`, `cancelled`), payment method (`whatsapp_checkout`).
- **`order_items`**: Menyimpan detail produk, price, quantity, dan subtotal per item.
