<template>
  <div :class="['min-h-screen bg-neo-surface dark:bg-neo-dark-bg relative', themeClass]">

    <!-- Sidebar Overlay (Mobile) -->
    <Transition
      enter-active-class="transition-opacity duration-300 ease-linear"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-linear"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm"></div>
    </Transition>

    <!-- SIDEBAR -->
    <aside :class="[
      'fixed top-0 left-0 h-[100dvh] bg-white dark:bg-neo-dark-card border-r-neo border-neo-black dark:border-white z-50 flex flex-col transition-all duration-300 ease-in-out shadow-neo dark:shadow-neo-white',
      sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
      sidebarCollapsed ? 'w-[68px]' : 'w-[260px]'
    ]">
      <!-- Logo Section -->
      <div class="p-4 border-b-neo border-neo-black dark:border-white relative overflow-hidden shrink-0 flex items-center justify-between">
        <div v-if="!sidebarCollapsed" class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow border-l-2 border-b-2 border-neo-black dark:border-white"></div>
        <div v-if="!sidebarCollapsed" class="font-heading font-black text-xl tracking-tight relative z-10 dark:text-white">
          VUWOTING<span class="text-neo-blue">&#8482;</span>
        </div>
        <div v-else class="font-heading font-black text-base tracking-tight relative z-10 dark:text-white mx-auto">
          <span class="text-neo-blue">V&#8482;</span>
        </div>
      </div>

      <!-- Sidebar Toggle Handle -->
      <button
        @click="toggleSidebar()"
        class="hidden lg:flex absolute -right-4 top-24 w-8 h-12 bg-neo-yellow border-neo border-neo-black dark:border-white z-50 items-center justify-center shadow-neo hover:shadow-neo-hover hover:-translate-x-1 transition-all group focus:outline-none"
      >
        <span class="material-symbols-outlined font-black transition-transform duration-300 group-hover:scale-110" :style="{ transform: sidebarCollapsed ? 'rotate(180deg)' : 'rotate(0deg)' }">
          chevron_left
        </span>
      </button>

      <!-- Event Context Banner -->
      <div v-if="currentEvent && !sidebarCollapsed" class="mx-3 mt-3 p-2.5 bg-neo-blue/10 dark:bg-neo-blue/20 border-2 border-neo-blue/30 shrink-0">
        <div class="font-heading text-[9px] font-bold text-neo-blue uppercase tracking-wider mb-0.5">CURRENT EVENT</div>
        <div class="font-heading text-xs font-black truncate dark:text-white">{{ currentEvent.name }}</div>
      </div>

      <!-- User Info (expanded) -->
      <div v-if="user && !sidebarCollapsed" class="mx-3 mt-3 p-3 border-neo border-neo-black dark:border-white bg-neo-surface dark:bg-neo-dark-surface relative overflow-hidden shrink-0">
        <div class="absolute bottom-0 right-0 w-6 h-6 bg-neo-blue border-l-2 border-t-2 border-neo-black dark:border-white"></div>
        <div class="flex items-center gap-3 relative z-10">
          <div class="w-9 h-9 bg-neo-blue border-2 border-neo-black dark:border-white flex items-center justify-center shrink-0 overflow-hidden">
            <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
            <span v-else class="material-symbols-outlined text-white text-lg">person</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="font-heading text-xs font-bold truncate uppercase dark:text-white">{{ user.name }}</div>
            <div class="font-body text-[10px] text-neo-grey uppercase tracking-wider">
              <span :class="[
                'inline-block px-1.5 py-0.5 border border-neo-black dark:border-white text-[8px] font-heading font-black',
                user.role === 'super_admin' ? 'bg-neo-red text-white' :
                user.role === 'admin' ? 'bg-neo-blue text-white' :
                'bg-gray-100 dark:bg-gray-700 dark:text-white text-neo-black'
              ]">{{ user.role?.replace('_', ' ').toUpperCase() }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- User avatar (collapsed) -->
      <div v-if="user && sidebarCollapsed" class="mx-2 mt-3 flex justify-center shrink-0">
        <div class="w-9 h-9 bg-neo-blue border-2 border-neo-black dark:border-white flex items-center justify-center overflow-hidden">
          <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
          <span v-else class="material-symbols-outlined text-white text-lg">person</span>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-scroll neo-scrollbar px-2 mt-3 pb-4 space-y-0.5" style="-webkit-overflow-scrolling: touch;">

        <!-- ── MAIN MENU ─────────────────────────────────── -->
        <div v-if="!sidebarCollapsed" class="neo-sidebar-section-label dark:text-gray-400 px-1 mb-1">MAIN MENU</div>

        <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')" icon="home" label="My Dashboard" :collapsed="sidebarCollapsed" />
        <SidebarLink :href="route('events.create')" :active="route().current('events.create')" icon="add_circle" label="Buat Event" :collapsed="sidebarCollapsed" />
        <SidebarLink :href="route('profile.edit')" :active="route().current('profile.*')" icon="manage_accounts" label="Profil Saya" :collapsed="sidebarCollapsed" />

        <!-- ── EVENT PARTICIPANT MENU ─────────────────────── -->
        <template v-if="currentEvent">
          <div class="pt-3 mt-3 border-t-2 border-dashed border-gray-200 dark:border-gray-700 mb-1">
            <div v-if="!sidebarCollapsed" class="neo-sidebar-section-label flex items-center gap-2 dark:text-gray-400">
              <span class="w-2 h-2 bg-neo-blue"></span>
              EVENT SAAT INI
            </div>
          </div>

          <SidebarLink :href="route('events.dashboard', currentEvent.slug)" :active="route().current('events.dashboard')" icon="grid_view" label="Beranda Event" :collapsed="sidebarCollapsed" />
          <SidebarLink :href="route('events.vote.index', currentEvent.slug)" :active="route().current('events.vote.*')" icon="how_to_vote" label="Pilih Kandidat" :collapsed="sidebarCollapsed" />
          <SidebarLink :href="route('events.results', currentEvent.slug)" :active="route().current('events.results')" icon="analytics" label="Hasil Voting" :collapsed="sidebarCollapsed" />
          <SidebarLink :href="route('events.ai.chat', currentEvent.slug)" :active="route().current('events.ai.*')" icon="smart_toy" label="AI Asisten" :collapsed="sidebarCollapsed" />
        </template>

        <!-- ── ADMIN ZONE ─────────────────────────────────── -->
        <template v-if="isAdmin && currentEvent">
          <div class="pt-3 mt-3 border-t-2 border-dashed border-gray-200 dark:border-gray-700 mb-1">
            <div v-if="!sidebarCollapsed" class="neo-sidebar-section-label flex items-center gap-2 dark:text-gray-400">
              <span class="w-2 h-2 bg-neo-red animate-pulse"></span>
              ADMIN ZONE
            </div>
          </div>

          <SidebarLink
            :href="route('events.admin.results', currentEvent.slug)"
            :active="route().current('events.admin.results')"
            icon="leaderboard"
            label="Rekap Hasil"
            :collapsed="sidebarCollapsed"
          />
          <SidebarLink
            :href="route('events.admin.elections.index', currentEvent.slug)"
            :active="route().current('events.admin.elections.*') || route().current('events.admin.candidates.*')"
            icon="event"
            label="Kelola Pemilihan"
            :collapsed="sidebarCollapsed"
          />
          <SidebarLink
            :href="route('events.admin.elections.history', currentEvent.slug)"
            :active="route().current('events.admin.elections.history')"
            icon="history"
            label="Riwayat Pemilihan"
            :collapsed="sidebarCollapsed"
          />
          <SidebarLink
            :href="route('events.admin.users.index', currentEvent.slug)"
            :active="route().current('events.admin.users.*')"
            icon="group"
            label="Anggota Event"
            :collapsed="sidebarCollapsed"
          />

          <!-- ── SUPER ADMIN ONLY ──────────────────────────── -->
          <template v-if="user?.role === 'super_admin'">
            <div class="pt-2 mt-2 border-t border-dashed border-gray-200 dark:border-gray-700 mb-1">
              <div v-if="!sidebarCollapsed" class="neo-sidebar-section-label flex items-center gap-2 dark:text-gray-400">
                <span class="w-2 h-2 bg-neo-yellow"></span>
                SUPER ADMIN
              </div>
            </div>
            <SidebarLink
              :href="route('events.admin.settings', currentEvent.slug)"
              :active="route().current('events.admin.settings')"
              icon="settings"
              label="Pengaturan Event"
              :collapsed="sidebarCollapsed"
            />
          </template>
        </template>
      </nav>

      <!-- Sidebar Footer -->
      <div class="mt-auto shrink-0">
        <div class="neo-stripe-thin"></div>
        <div class="p-3 border-t-neo border-neo-black dark:border-white bg-gray-50 dark:bg-neo-dark-surface">
          <Link :href="route('logout')" method="post" as="button"
            :class="['neo-btn-danger border-neo-black dark:border-white w-full text-xs py-2.5', sidebarCollapsed ? 'px-0 justify-center' : '']">
            <span class="material-symbols-outlined text-base">logout</span>
            <span v-if="!sidebarCollapsed" class="font-bold tracking-wider">SIGN OUT</span>
          </Link>
          <div v-if="!sidebarCollapsed" class="text-center mt-2">
            <span class="font-heading text-[8px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-[0.2em]">VUWOTING&#8482; SYSTEM V2.0</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div :class="[
      'flex flex-col min-h-screen transition-all duration-300',
      sidebarCollapsed ? 'lg:pl-[68px]' : 'lg:pl-[260px]'
    ]">

      <!-- Topbar -->
      <header class="sticky top-0 z-30 bg-neo-black dark:bg-neo-dark-card text-white border-b-neo border-neo-black dark:border-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-full bg-neo-blue/20 hidden lg:block"></div>

        <!-- Desktop Topbar -->
        <div class="hidden lg:flex items-center justify-between px-8 h-14 relative z-10">
          <h1 class="font-heading font-bold text-sm uppercase tracking-wider">{{ title }}</h1>
          <div class="flex items-center gap-4">
            <button @click="toggle()"
              class="w-9 h-9 border-2 border-white/40 hover:border-white flex items-center justify-center hover:bg-white/10 transition-colors"
              :title="isDark ? 'Light Mode' : 'Dark Mode'">
              <span class="material-symbols-outlined text-base">{{ isDark ? 'light_mode' : 'dark_mode' }}</span>
            </button>
            <div class="flex items-center gap-2">
              <span :class="['w-2 h-2', activeElection ? 'bg-neo-yellow animate-pulse' : 'bg-gray-500']"></span>
              <span class="font-heading text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">
                {{ activeElection ? 'ELECTION ACTIVE' : 'NO ACTIVE ELECTION' }}
              </span>
            </div>
            <div class="font-heading text-[10px] font-bold text-gray-500 uppercase tracking-[0.15em]">VUWOTING&#8482;</div>
          </div>
        </div>

        <!-- Mobile Topbar -->
        <div class="lg:hidden flex items-center justify-between px-4 h-14 relative z-10">
          <button @click="sidebarOpen = true" class="w-10 h-10 border-2 border-white flex items-center justify-center hover:bg-white hover:text-neo-black transition-colors">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <div class="font-heading font-black text-base tracking-tight">VUWOTING<span class="text-neo-yellow">&#8482;</span></div>
          <button @click="toggle()" class="w-10 h-10 border-2 border-white/40 hover:border-white flex items-center justify-center hover:bg-white/10 transition-colors">
            <span class="material-symbols-outlined text-base">{{ isDark ? 'light_mode' : 'dark_mode' }}</span>
          </button>
        </div>
      </header>

      <!-- Ticker Bar -->
      <div class="neo-ticker-bar block">
        <div class="neo-ticker-content">
          <template v-for="n in 3" :key="n">
            <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>VUWOTING&#8482; EVENT VOTING PLATFORM</span>
            <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>{{ activeElection ? 'ELECTION ACTIVE' : 'NO ACTIVE ELECTION' }}</span>
            <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>YOUR VOTE. YOUR VOICE.</span>
            <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>ABSOLUTE TRANSPARENCY</span>
          </template>
        </div>
      </div>

      <!-- Page Content -->
      <main class="flex-1 p-4 md:p-6 lg:p-8 neo-rich-bg dark:neo-rich-bg-dark relative overflow-x-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20 pointer-events-none hidden lg:block"></div>
        <div class="absolute bottom-0 left-0 w-12 h-12 bg-neo-blue/10 border-r-2 border-t-2 border-neo-blue/20 pointer-events-none hidden lg:block"></div>
        <div class="relative z-10 max-w-content mx-auto w-full">
          <slot />
        </div>
      </main>

      <!-- Footer -->
      <footer class="border-t-neo border-neo-black dark:border-white bg-white dark:bg-neo-dark-card px-4 md:px-8 py-3 flex items-center justify-between mt-auto">
        <span class="font-heading text-[10px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-[0.15em]">© 2026 VUWOTING&#8482;</span>
        <span class="font-heading text-[10px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-[0.15em]">POWERED BY NEOBRUTALISM</span>
      </footer>
    </div>

    <NeoToast />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import SidebarLink from '@/Components/SidebarLink.vue';
import NeoToast from '@/Components/NeoToast.vue';
import { useDarkMode } from '@/Composables/useDarkMode.js';

defineProps({ title: String });

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
try {
  sidebarCollapsed.value = localStorage.getItem('sidebar-collapsed') === '1';
} catch (e) {}

const { isDark, toggle } = useDarkMode();

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
  try { localStorage.setItem('sidebar-collapsed', sidebarCollapsed.value ? '1' : '0'); } catch (e) {}
};

router.on('navigate', () => { sidebarOpen.value = false; });

const page         = usePage();
const user         = computed(() => page.props.auth?.user);
const currentEvent = computed(() => page.props.currentEvent);
const activeElection = computed(() => page.props.activeElection);
const isAdmin      = computed(() => ['admin', 'super_admin'].includes(user.value?.role));

const themeClass = computed(() => {
  if (currentEvent.value && currentEvent.value.theme) {
    return `theme-${currentEvent.value.theme}`;
  }
  return 'theme-neo-brutalism';
});
</script>
