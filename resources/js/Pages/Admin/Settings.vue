<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Save, Image as ImageIcon, Phone, Bot, Globe, Store, Check, ExternalLink } from '@lucide/vue';

const props = defineProps({
  tenant: Object,
  setting: Object,
});

const currentTab = ref('store'); // 'store', 'whatsapp', 'ai', 'geo'

const form = useForm({
  // Tenant
  name: props.tenant?.name || '',
  phone_whatsapp: props.tenant?.phone_whatsapp || '',
  email: props.tenant?.email || '',
  address: props.tenant?.address || '',
  logo: null,

  // Store Setting
  store_name: props.setting?.store_name || props.tenant?.name || '',
  tagline: props.setting?.tagline || '',
  about: props.setting?.about || '',
  wa_checkout_template: props.setting?.wa_checkout_template || "Halo {store_name}, saya ingin memesan barang berikut:\n{items}\nTotal: {total}",
  llm_system_prompt: props.setting?.llm_system_prompt || '',
  is_ai_sales_enabled: Boolean(props.setting?.is_ai_sales_enabled ?? true),
  meta_title: props.setting?.meta_title || '',
  meta_description: props.setting?.meta_description || '',
});

const logoPreview = ref(props.tenant?.logo_url || null);

const handleLogoChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.logo = file;
    logoPreview.value = URL.createObjectURL(file);
  }
};

const submit = () => {
  form.post(route('admin.settings.update'), {
    forceFormData: true,
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Pengaturan Toko - Solkit Hub" />

  <AdminLayout>
    <div class="space-y-6 max-w-5xl">
      <!-- Title & Action -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Pengaturan Toko &amp; Integrasi</h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Konfigurasi branding, hotline WhatsApp checkout, prompt AI Sales, dan Generative Engine Optimization (GEO).
          </p>
        </div>

        <button
          @click="submit"
          :disabled="form.processing"
          class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold shadow-sm transition disabled:opacity-50 cursor-pointer"
        >
          <Save class="w-4 h-4" />
          <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
        </button>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-px">
        <button 
          @click="currentTab = 'store'"
          :class="[
            currentTab === 'store' 
              ? 'border-blue-600 text-blue-600 bg-white shadow-2xs font-bold' 
              : 'border-transparent text-slate-500 hover:text-slate-800 font-medium',
            'px-4 py-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 whitespace-nowrap transition cursor-pointer rounded-t-xl'
          ]"
        >
          <Store class="w-4 h-4" />
          <span>Profil Toko &amp; Branding</span>
        </button>

        <button 
          @click="currentTab = 'whatsapp'"
          :class="[
            currentTab === 'whatsapp' 
              ? 'border-blue-600 text-blue-600 bg-white shadow-2xs font-bold' 
              : 'border-transparent text-slate-500 hover:text-slate-800 font-medium',
            'px-4 py-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 whitespace-nowrap transition cursor-pointer rounded-t-xl'
          ]"
        >
          <Phone class="w-4 h-4" />
          <span>WhatsApp Checkout Hotline</span>
        </button>

        <button 
          @click="currentTab = 'ai'"
          :class="[
            currentTab === 'ai' 
              ? 'border-blue-600 text-blue-600 bg-white shadow-2xs font-bold' 
              : 'border-transparent text-slate-500 hover:text-slate-800 font-medium',
            'px-4 py-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 whitespace-nowrap transition cursor-pointer rounded-t-xl'
          ]"
        >
          <Bot class="w-4 h-4" />
          <span>AI Sales Consultant (RAG)</span>
        </button>

        <button 
          @click="currentTab = 'geo'"
          :class="[
            currentTab === 'geo' 
              ? 'border-blue-600 text-blue-600 bg-white shadow-2xs font-bold' 
              : 'border-transparent text-slate-500 hover:text-slate-800 font-medium',
            'px-4 py-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 whitespace-nowrap transition cursor-pointer rounded-t-xl'
          ]"
        >
          <Globe class="w-4 h-4" />
          <span>Generative Engine (GEO)</span>
        </button>
      </div>

      <!-- Tab 1: Profil Toko & Branding -->
      <div v-show="currentTab === 'store'" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6 shadow-xs">
        <h3 class="text-base font-bold text-slate-900">Identitas Visual &amp; Informasi Toko</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Toko Online (Storefront) *</label>
            <input 
              v-model="form.store_name"
              type="text" 
              placeholder="Contoh: Solkit Smart Merchant"
              class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
              required
            />
            <p class="text-[11px] text-slate-400 mt-1">Nama ini tampil pada Header navigasi dan Hero halaman depan.</p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Entitas / Legal Merchant *</label>
            <input 
              v-model="form.name"
              type="text" 
              placeholder="Contoh: PT Solusi Kode Kito"
              class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
              required
            />
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tagline Toko</label>
            <input 
              v-model="form.tagline"
              type="text" 
              placeholder="Contoh: Katalog Cerdas Instan & Checkout WhatsApp Terpadu"
              class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
            />
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Singkat / Tentang Toko</label>
            <textarea 
              v-model="form.about"
              rows="3"
              placeholder="Jelaskan mengenai spesialisasi produk Anda..."
              class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
            ></textarea>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Email Toko</label>
            <input 
              v-model="form.email"
              type="email" 
              placeholder="contact@solkit.tech"
              class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Operasional Toko</label>
            <input 
              v-model="form.address"
              type="text" 
              placeholder="Jl. Boulevard Smart Tech No. 88, Jakarta"
              class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
            />
          </div>

          <!-- Logo Upload -->
          <div class="md:col-span-2 pt-2 border-t border-slate-100">
            <label class="block text-xs font-semibold text-slate-700 mb-2">Logo Toko (Tampil di Header &amp; Footer)</label>
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden p-2 shrink-0">
                <img v-if="logoPreview" :src="logoPreview" alt="Logo Preview" class="w-full h-full object-contain" />
                <ImageIcon v-else class="w-6 h-6 text-slate-300" />
              </div>
              <div>
                <input 
                  type="file" 
                  accept="image/*"
                  @change="handleLogoChange" 
                  class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                />
                <p class="text-[11px] text-slate-400 mt-1">Format PNG, JPG, atau WebP (Maks. 2MB).</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 2: WhatsApp Checkout Hotline -->
      <div v-show="currentTab === 'whatsapp'" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6 shadow-xs">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base font-bold text-slate-900">Konfigurasi WhatsApp Direct Checkout</h3>
            <p class="text-xs text-slate-500 mt-0.5">Semua pesanan keranjang belanja dari storefront akan diteruskan ke nomor ini.</p>
          </div>
          <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
            <Phone class="w-5 h-5" />
          </span>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp Admin Toko *</label>
            <input 
              v-model="form.phone_whatsapp"
              type="text" 
              placeholder="Contoh: 6281316053870 (Gunakan awalan 62)"
              class="w-full sm:max-w-md bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 font-mono"
              required
            />
            <p class="text-[11px] text-slate-400 mt-1">Gunakan format internasional tanpa tanda plus atau strip (contoh: 6281316053870).</p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Template Format Pesan WhatsApp Checkout</label>
            <textarea 
              v-model="form.wa_checkout_template"
              rows="6"
              class="w-full bg-white border border-slate-300 rounded-xl p-4 text-xs sm:text-sm font-mono text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 leading-relaxed"
            ></textarea>
            <div class="mt-2 flex flex-wrap gap-2 text-[11px] text-slate-500">
              <span>Tag variabel yang didukung:</span>
              <span class="px-2 py-0.5 bg-slate-100 rounded font-mono text-blue-600">{store_name}</span>
              <span class="px-2 py-0.5 bg-slate-100 rounded font-mono text-blue-600">{items}</span>
              <span class="px-2 py-0.5 bg-slate-100 rounded font-mono text-blue-600">{total}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 3: AI Sales Consultant (RAG) -->
      <div v-show="currentTab === 'ai'" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6 shadow-xs">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base font-bold text-slate-900">AI Sales Consultant System</h3>
            <p class="text-xs text-slate-500 mt-0.5">Asisten kecerdasan buatan interaktif yang terhubung ke inventaris produk toko Anda.</p>
          </div>
          <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
            <Bot class="w-5 h-5" />
          </span>
        </div>

        <div class="space-y-4">
          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
            <div>
              <h4 class="text-xs sm:text-sm font-bold text-slate-800">Aktifkan Tombol "Tanya AI Sales" di Storefront</h4>
              <p class="text-[11px] text-slate-500 mt-0.5">Jika dinonaktifkan, tombol konsultasi AI akan disembunyikan dari pengunjung toko.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="form.is_ai_sales_enabled" class="sr-only peer" />
              <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">System Prompt / Peran Karakter AI</label>
            <textarea 
              v-model="form.llm_system_prompt"
              rows="5"
              placeholder="Anda adalah Asisten Penjualan AI pintar dan ramah dari..."
              class="w-full bg-white border border-slate-300 rounded-xl p-4 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 leading-relaxed font-sans"
            ></textarea>
            <p class="text-[11px] text-slate-400 mt-1">Prompt ini memandu gaya bicara, batasan etika, dan cara AI menjawab ketersediaan produk.</p>
          </div>
        </div>
      </div>

      <!-- Tab 4: Generative Engine Optimization (GEO) -->
      <div v-show="currentTab === 'geo'" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6 shadow-xs">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base font-bold text-slate-900">Generative Engine Optimization (AI Search &amp; SEO)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Memastikan toko Anda terindeks oleh search engine AI (ChatGPT Search, Perplexity, Google Gemini).</p>
          </div>
          <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
            <Globe class="w-5 h-5" />
          </span>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Meta Title Toko</label>
            <input 
              v-model="form.meta_title"
              type="text" 
              placeholder="Contoh: Solkit Smart Merchant - Toko Online Instan & AI Commerce"
              class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Meta Description</label>
            <textarea 
              v-model="form.meta_description"
              rows="3"
              placeholder="Deskripsi ringkas yang tampil pada hasil pencarian..."
              class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
            ></textarea>
          </div>

          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">File Semantik AI Otomatis (Live Endpoints):</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <a 
                :href="`/shop/${tenant?.slug || 'solkit-store'}/llms.txt`" 
                target="_blank"
                class="flex items-center justify-between p-3 rounded-lg bg-white border border-slate-200 hover:border-blue-400 text-xs text-slate-700 transition"
              >
                <span class="font-mono font-bold text-blue-600">llms.txt</span>
                <ExternalLink class="w-3.5 h-3.5 text-slate-400" />
              </a>
              <a 
                :href="`/shop/${tenant?.slug || 'solkit-store'}/sitemap.xml`" 
                target="_blank"
                class="flex items-center justify-between p-3 rounded-lg bg-white border border-slate-200 hover:border-blue-400 text-xs text-slate-700 transition"
              >
                <span class="font-mono font-bold text-blue-600">sitemap.xml</span>
                <ExternalLink class="w-3.5 h-3.5 text-slate-400" />
              </a>
              <a 
                :href="`/shop/${tenant?.slug || 'solkit-store'}/robots.txt`" 
                target="_blank"
                class="flex items-center justify-between p-3 rounded-lg bg-white border border-slate-200 hover:border-blue-400 text-xs text-slate-700 transition"
              >
                <span class="font-mono font-bold text-blue-600">robots.txt</span>
                <ExternalLink class="w-3.5 h-3.5 text-slate-400" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
