<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased relative overflow-hidden flex flex-col items-center justify-center p-4 sm:p-6 selection:bg-blue-600 selection:text-white">
    <!-- Ambient Background Radial Light -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-gradient-to-b from-blue-100/60 to-transparent blur-3xl pointer-events-none"></div>

    <!-- Login Card Container -->
    <div class="w-full sm:max-w-md bg-white border border-slate-200 shadow-xl shadow-slate-200/60 rounded-2xl p-6 sm:p-8 relative z-10 space-y-6">
      <!-- Solkit Brand Identity -->
      <div class="flex flex-col items-center text-center gap-3">
        <Link href="/" class="group flex items-center justify-center p-2 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition">
          <img
            src="/images/solkit-clean.png"
            alt="Solkit Logo"
            class="h-10 w-auto object-contain transition-transform group-hover:scale-105"
          />
        </Link>
        <div>
          <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
            SOLKIT <span class="text-blue-600">HUB</span>
          </h2>
          <p class="text-xs text-slate-500 font-medium mt-1">
            Smart Merchant Control Panel
          </p>
        </div>
      </div>

      <!-- Form Slot -->
      <div class="space-y-4">
        <slot />
      </div>
    </div>

    <!-- Footer Back Link -->
    <div class="mt-6 text-center text-xs text-slate-500">
      <Link href="/" class="hover:text-blue-600 font-medium transition flex items-center gap-1 justify-center">
        &larr; Kembali ke Katalog Toko
      </Link>
    </div>
  </div>
</template>
