<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  Settings as SettingsIcon, 
  ShoppingBag, 
  ShoppingCart,
  ExternalLink,
  Menu, 
  X, 
  LogOut, 
  ChevronDown,
  Search,
  ChevronLeft,
  ChevronRight
} from '@lucide/vue';

const page = usePage();
const user = page.props.auth.user;

const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);
const isUserMenuOpen = ref(false);

const navigation = [
  { name: 'Dashboard', href: route('admin.dashboard'), icon: LayoutDashboard, current: route().current('admin.dashboard') },
  { name: 'Katalog Produk', href: route('admin.products.index'), icon: ShoppingBag, current: route().current('admin.products.*') },
  { name: 'Pesanan Masuk (OMS)', href: route('admin.orders.index'), icon: ShoppingCart, current: route().current('admin.orders.*') },
  { name: 'Pengaturan Toko', href: route('admin.settings.index'), icon: SettingsIcon, current: route().current('admin.settings.*') },
];

const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 font-sans antialiased relative selection:bg-blue-600 selection:text-white">
    <!-- Mobile Sidebar Backdrop -->
    <div 
      v-if="isSidebarOpen" 
      @click="isSidebarOpen = false" 
      class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden transition-opacity"
    ></div>

    <!-- Sidebar for Mobile and Desktop -->
    <aside 
      :class="[
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        isSidebarCollapsed ? 'lg:w-20' : 'lg:w-72',
        'fixed top-0 bottom-0 left-0 z-50 w-72 bg-white border-r border-slate-200/80 transition-all duration-300 ease-in-out lg:fixed flex flex-col justify-between shadow-xs'
      ]"
    >
      <div>
        <!-- Sidebar Header / Logo -->
        <div :class="[isSidebarCollapsed ? 'lg:px-0 lg:justify-center' : 'px-6 justify-between', 'flex items-center h-16 border-b border-slate-200/80']">
          <Link :href="route('admin.dashboard')" class="flex items-center gap-3 min-w-0">
            <div class="p-1 bg-white rounded-lg border border-slate-200 shadow-2xs shrink-0">
              <img src="/images/solkit-clean.png" alt="Solkit" class="w-7 h-7 object-contain" />
            </div>
            <div :class="[isSidebarCollapsed ? 'lg:hidden' : 'block', 'transition-opacity duration-300 min-w-0']">
              <h1 class="text-sm font-extrabold tracking-tight text-slate-900 uppercase">
                Solkit <span class="text-blue-600">Hub</span>
              </h1>
              <p class="text-[10px] text-blue-600 font-semibold tracking-wide truncate">Smart-Commerce &amp; GEO</p>
            </div>
          </Link>
          <button 
            @click="isSidebarOpen = false" 
            class="p-2 text-slate-400 rounded-lg hover:bg-slate-100 hover:text-slate-700 lg:hidden"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Navigation Links -->
        <nav class="px-3 py-5 space-y-1 overflow-y-auto">
          <Link 
            v-for="item in navigation" 
            :key="item.name" 
            :href="item.href"
            :class="[
              item.current 
                ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-bold' 
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-r-4 border-transparent font-medium',
              isSidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-4',
              'group flex items-center py-3 text-sm rounded-l-xl transition-all duration-200'
            ]"
            :title="isSidebarCollapsed ? item.name : ''"
          >
            <component 
              :is="item.icon" 
              :class="[
                item.current ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600',
                isSidebarCollapsed ? 'lg:mr-0' : 'mr-3',
                'h-5 w-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105'
              ]" 
            />
            <span :class="[isSidebarCollapsed ? 'lg:hidden' : 'block', 'whitespace-nowrap']">{{ item.name }}</span>
          </Link>
        </nav>
      </div>

      <!-- Sidebar Footer / Template Owner & Profile -->
      <div :class="[isSidebarCollapsed ? 'lg:p-2' : 'p-4', 'border-t border-slate-200/80 bg-slate-50/60']">
        <!-- Storefront Quick Link -->
        <div v-if="!isSidebarCollapsed" class="mb-3">
          <a
            href="/"
            target="_blank"
            class="w-full flex items-center justify-center gap-2 py-2 px-3 text-xs font-semibold rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/60 transition"
          >
            <ExternalLink class="w-3.5 h-3.5" />
            <span>Kunjungi Storefront</span>
          </a>
        </div>

        <div :class="[isSidebarCollapsed ? 'lg:px-0 lg:justify-center' : 'px-1 justify-between', 'mb-3 flex items-center text-[10px] text-slate-400 font-semibold uppercase tracking-wider']">
          <div v-if="!isSidebarCollapsed" class="flex items-center gap-1">
            <a href="https://solkit.tech" target="_blank" class="text-slate-500 hover:text-blue-600 transition-colors normal-case">
              Powered by Solkit.tech
            </a>
          </div>
          <!-- Collapse Toggle for Desktop -->
          <div class="hidden lg:block">
            <button 
              @click="isSidebarCollapsed = !isSidebarCollapsed"
              class="p-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-md text-slate-400 hover:text-slate-700 transition cursor-pointer"
              title="Toggle Sidebar"
            >
              <ChevronLeft v-if="!isSidebarCollapsed" class="w-3.5 h-3.5" />
              <ChevronRight v-else class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>

        <!-- User Profile -->
        <div :class="[isSidebarCollapsed ? 'lg:px-0 lg:justify-center' : 'px-1 gap-2.5', 'flex items-center pt-2.5 border-t border-slate-200/60']">
          <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 border border-blue-200 shrink-0 text-xs">
            {{ user.name.charAt(0).toUpperCase() }}
          </div>
          <div :class="[isSidebarCollapsed ? 'lg:hidden' : 'block', 'flex-1 min-w-0']">
            <p class="text-xs font-bold text-slate-800 truncate">{{ user.name }}</p>
            <p class="text-[10px] text-slate-500 truncate">{{ user.email }}</p>
          </div>
          <button 
            :class="[isSidebarCollapsed ? 'lg:hidden' : 'block']"
            @click="logout" 
            title="Keluar"
            class="p-1.5 text-slate-400 rounded-lg hover:bg-rose-50 hover:text-rose-600 transition-colors cursor-pointer"
          >
            <LogOut class="w-4 h-4" />
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div 
      :class="[
        isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72',
        'flex flex-col min-h-screen transition-all duration-300 ease-in-out'
      ]"
    >
      <!-- Top header bar (Sticky Clean Glassmorphic) -->
      <header class="flex items-center justify-between h-16 px-4 md:px-8 border-b border-slate-200/80 bg-white/90 backdrop-blur-md sticky top-0 z-30 shadow-2xs">
        <div class="flex items-center gap-4 flex-1 max-w-md">
          <button 
            @click="isSidebarOpen = true" 
            class="p-2 text-slate-500 rounded-lg hover:bg-slate-100 hover:text-slate-800 lg:hidden"
            aria-label="Toggle Menu"
          >
            <Menu class="w-5 h-5" />
          </button>
          
          <div class="hidden sm:flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-400">Panel:</span>
            <span class="text-xs font-bold text-slate-800">{{ user.name }}</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-semibold border border-blue-200/60">
              Verified Merchant
            </span>
          </div>
        </div>

        <!-- Right Header Elements -->
        <div class="flex items-center gap-3 sm:gap-4">
          <!-- Server Status Indicator -->
          <div class="hidden xs:flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-full text-[10px] font-bold uppercase tracking-wider">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            System Live
          </div>

          <!-- Direct Storefront Button -->
          <a
            href="/"
            target="_blank"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-xs"
          >
            <span>Toko Online</span>
            <ExternalLink class="w-3.5 h-3.5" />
          </a>

          <!-- Profile dropdown -->
          <div class="relative">
            <button 
              @click="isUserMenuOpen = !isUserMenuOpen"
              class="flex items-center gap-2 p-1 sm:px-2.5 sm:py-1.5 rounded-xl hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all text-slate-700"
            >
              <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 font-bold flex items-center justify-center text-xs">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <ChevronDown class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': isUserMenuOpen }" />
            </button>
            <div 
              v-if="isUserMenuOpen" 
              @click="isUserMenuOpen = false" 
              class="fixed inset-0 z-10"
            ></div>
            <div 
              v-if="isUserMenuOpen" 
              class="absolute right-0 mt-2 w-48 rounded-2xl bg-white border border-slate-200 shadow-xl py-1.5 z-20 overflow-hidden transform origin-top-right transition-all"
            >
              <Link 
                :href="route('profile.edit')" 
                class="block px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors"
              >
                Pengaturan Akun
              </Link>
              <button 
                @click="logout" 
                class="w-full text-left block px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors border-t border-slate-100"
              >
                Keluar
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Panel Page -->
      <main class="flex-1 p-4 sm:p-6 md:p-8 bg-slate-50">
        <!-- Toast Notification Alert -->
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between shadow-2xs">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="text-xs sm:text-sm font-semibold">{{ $page.props.flash.success }}</span>
          </div>
        </div>
        <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center justify-between shadow-2xs">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            <span class="text-xs sm:text-sm font-semibold">{{ $page.props.flash.error }}</span>
          </div>
        </div>

        <slot />
      </main>
    </div>
  </div>
</template>
