<template>
  <div class="min-h-screen bg-neo-surface dark:bg-neo-dark-bg relative">

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
          KOSGORO<span class="text-neo-blue">&#8482;</span>
        </div>
        <div v-else class="font-heading font-black text-base tracking-tight relative z-10 dark:text-white mx-auto">
          <span class="text-neo-blue">K&#8482;</span>
        </div>
      </div>

      <!-- NEW NEO-BRUTALIST SIDEBAR TOGGLE HANDLE -->
      <button
        @click="toggleSidebar()"
        class="hidden lg:flex absolute -right-4 top-24 w-8 h-12 bg-neo-yellow border-neo border-neo-black dark:border-white z-50 items-center justify-center shadow-neo hover:shadow-neo-hover hover:-translate-x-1 transition-all group focus:outline-none"
      >
        <span class="material-symbols-outlined font-black transition-transform duration-300 group-hover:scale-110" :style="{ transform: sidebarCollapsed ? 'rotate(180deg)' : 'rotate(0deg)' }">
          chevron_left
        </span>
      </button>

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
              ]">{{ user.role?.toUpperCase() }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- User avatar (collapsed) -->
      <div v-if="user && sidebarCollapsed" class="mx-2 mt-3 flex justify-center shrink-0">
        <div class="w-9 h-9 bg-neo-blue border-2 border-neo-black dark:border-white flex items-center justify-center overflow-hidden">
          <!-- UI-02 FIX: Gunakan user.avatar langsung (sudah full URL dari HandleInertiaRequests) -->
          <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
          <span v-else class="material-symbols-outlined text-white text-lg">person</span>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-scroll neo-scrollbar px-2 mt-3 space-y-1 pb-4" style="-webkit-overflow-scrolling: touch;">
        <div v-if="!sidebarCollapsed" class="neo-sidebar-section-label dark:text-gray-400 px-1">MENU UTAMA</div>

        <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')" icon="grid_view" label="Dashboard" :collapsed="sidebarCollapsed" />
        <SidebarLink :href="route('vote.index')" :active="route().current('vote.*')" icon="how_to_vote" label="Live Ballots" :collapsed="sidebarCollapsed" />
        <SidebarLink :href="route('results.index')" :active="route().current('results.*')" icon="analytics" label="Voter Analytics" :collapsed="sidebarCollapsed" />
        <SidebarLink :href="route('profile.edit')" :active="route().current('profile.*')" icon="manage_accounts" label="Profil Saya" :collapsed="sidebarCollapsed" />

        <!-- Admin Nav -->
        <template v-if="isAdmin">
          <div class="pt-3 mt-3 border-t-2 border-dashed border-gray-200 dark:border-gray-700">
            <div v-if="!sidebarCollapsed" class="neo-sidebar-section-label flex items-center gap-2 dark:text-gray-400">
              <span class="w-2 h-2 bg-neo-red animate-pulse"></span>
              ADMIN ZONE
            </div>
          </div>
          <SidebarLink :href="route('admin.results.index')" :active="route().current('admin.results.*')" icon="leaderboard" label="Hasil Admin" :collapsed="sidebarCollapsed" />
          <SidebarLink :href="route('admin.elections.index')" :active="route().current('admin.elections.*') || route().current('admin.candidates.*')" icon="event" label="Kelola Periode" :collapsed="sidebarCollapsed" />

          <template v-if="user?.role === 'super_admin'">
            <div class="pt-2 mt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
              <div v-if="!sidebarCollapsed" class="neo-sidebar-section-label flex items-center gap-2 dark:text-gray-400">
                <span class="w-2 h-2 bg-neo-yellow"></span>
                SUPER ADMIN
              </div>
            </div>
            <SidebarLink :href="route('admin.users.index')" :active="route().current('admin.users.*')" icon="admin_panel_settings" label="Kelola Admin" :collapsed="sidebarCollapsed" />
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
            <span v-if="!sidebarCollapsed" class="font-bold tracking-wider">KELUAR</span>
          </Link>
          <div v-if="!sidebarCollapsed" class="text-center mt-2">
            <span class="font-heading text-[8px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-[0.2em]">KOSGORO&#8482; SYSTEM V1.0</span>
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
            <!-- Dark mode toggle -->
            <button @click="toggleDark()"
              class="w-9 h-9 border-2 border-white/40 hover:border-white flex items-center justify-center hover:bg-white/10 transition-colors"
              :title="isDark ? 'Mode Terang' : 'Mode Gelap'">
              <span class="material-symbols-outlined text-base">{{ isDark ? 'light_mode' : 'dark_mode' }}</span>
            </button>
            <div class="flex items-center gap-2">
              <span :class="['w-2 h-2', $page.props.activeElection ? 'bg-neo-yellow animate-pulse' : 'bg-gray-500']"></span>
              <span class="font-heading text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">
                {{ $page.props.activeElection ? 'PEMILIHAN AKTIF' : 'PEMILIHAN TIDAK AKTIF' }}
              </span>
            </div>
            <div class="font-heading text-[10px] font-bold text-gray-500 uppercase tracking-[0.15em]">KOSGORO&#8482;</div>
          </div>
        </div>

        <!-- Mobile Topbar -->
        <div class="lg:hidden flex items-center justify-between px-4 h-14 relative z-10">
          <button @click="sidebarOpen = true" class="w-10 h-10 border-2 border-white flex items-center justify-center hover:bg-white hover:text-neo-black transition-colors">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <div class="font-heading font-black text-base tracking-tight">KOSGORO<span class="text-neo-yellow">&#8482;</span></div>
          <button @click="toggleDark()" class="w-10 h-10 border-2 border-white/40 hover:border-white flex items-center justify-center hover:bg-white/10 transition-colors">
            <span class="material-symbols-outlined text-base">{{ isDark ? 'light_mode' : 'dark_mode' }}</span>
          </button>
        </div>
      </header>

      <!-- Ticker Bar -->
      <div class="neo-ticker-bar block">
        <div class="neo-ticker-content">
          <template v-for="n in 3" :key="n">
            <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>KOSGORO&#8482; VOTING SYSTEM</span>
            <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>PERIODE PEMILIHAN AKTIF</span>
            <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>SUARAMU MENENTUKAN PEMIMPIN</span>
            <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>TRANSPARANSI ABSOLUT</span>
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
        <span class="font-heading text-[10px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-[0.15em]">© 2026 KOSGORO&#8482;</span>
        <span class="font-heading text-[10px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-[0.15em]">POWERED BY NEOBRUTALISM</span>
      </footer>
    </div>

    <!-- Scroll to Top Button -->
    <Transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 translate-y-4 scale-75"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <button 
        v-show="showScrollTop"
        @click="scrollToTop"
        class="fixed z-[9999] bg-neo-yellow text-neo-black border-[3px] border-neo-black dark:border-white shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] hover:bg-neo-blue hover:text-white hover:shadow-[2px_2px_0px_#000] dark:hover:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none transition-all duration-300 flex items-center justify-center focus:outline-none"
        :class="isAtBottom ? 'bottom-28 left-[calc(50%-4rem)] w-32 h-10 rounded-none px-3 gap-1.5' : 'bottom-28 left-[calc(100%-4.5rem)] w-12 h-12 hover:translate-x-[1.5px] hover:translate-y-[1.5px]'"
        aria-label="Scroll to top"
      >
        <span v-if="isAtBottom" class="whitespace-nowrap font-heading font-black tracking-wider text-[11px] select-none">KE ATAS</span>
        <span class="material-symbols-outlined font-bold text-xl">arrow_upward</span>
      </button>
    </Transition>

    <!-- Toasts Layer -->
    <NeoToast />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import SidebarLink from '@/Components/SidebarLink.vue';
import NeoToast from '@/Components/NeoToast.vue';
import { useDarkMode } from '@/Composables/useDarkMode.js';

defineProps({ title: String });

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
try {
  sidebarCollapsed.value = localStorage.getItem('sidebar-collapsed') === '1';
} catch (e) {
  // Ignore
}
const { isDark, toggle: toggleDark } = useDarkMode();

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
  try {
    localStorage.setItem('sidebar-collapsed', sidebarCollapsed.value ? '1' : '0');
  } catch (e) {
    // Ignore
  }
};

const showScrollTop = ref(false);
const isAtBottom = ref(false);
const isScrollingToTop = ref(false);

const handleScroll = () => {
  if (isScrollingToTop.value) return;

  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  showScrollTop.value = scrollTop > 300;

  const scrollHeight = document.documentElement.scrollHeight;
  const clientHeight = window.innerHeight;
  
  // Jika scroll sudah mendekati bawah (misal toleransi 35px)
  isAtBottom.value = (scrollTop + clientHeight) >= (scrollHeight - 45);
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
});

const scrollToTop = () => {
  isScrollingToTop.value = true;
  showScrollTop.value = false;

  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });

  setTimeout(() => {
    isScrollingToTop.value = false;
    isAtBottom.value = false;
  }, 800);
};

router.on('navigate', () => { 
  sidebarOpen.value = false; 
  showScrollTop.value = false; // Reset on navigation
  isAtBottom.value = false;
  isScrollingToTop.value = false;
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => ['admin', 'super_admin'].includes(user.value?.role));
</script>
