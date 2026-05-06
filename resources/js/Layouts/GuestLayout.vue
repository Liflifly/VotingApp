<template>
  <div class="min-h-screen flex flex-col lg:flex-row dark:bg-neo-dark-bg">

    <!-- LEFT PANEL: Branding -->
    <div class="hidden lg:flex flex-col justify-between w-[480px] shrink-0 bg-neo-blue border-r-neo border-neo-black p-10 relative overflow-hidden">
      <!-- Decorative blocks -->
      <div class="absolute top-0 right-0 w-40 h-40 bg-neo-yellow border-l-neo border-b-neo border-neo-black"></div>
      <div class="absolute bottom-0 left-0 w-32 h-32 bg-neo-red border-r-neo border-t-neo border-neo-black"></div>
      <div class="absolute top-40 left-0 w-16 h-16 bg-white/10 border-r-2 border-b-2 border-white/20"></div>
      <div class="absolute bottom-40 right-0 w-24 h-24 bg-neo-yellow/20 border-l-2 border-t-2 border-neo-yellow/30"></div>
      
      <!-- Scanline overlay -->
      <div class="absolute inset-0 neo-scanline pointer-events-none"></div>
      
      <!-- Diagonal stripe background -->
      <div class="absolute inset-0 opacity-5" style="background-image: repeating-linear-gradient(-45deg, transparent, transparent 20px, #fff 20px, #fff 22px)"></div>

      <!-- Logo -->
      <div class="relative z-10">
        <div class="font-heading font-black text-3xl text-white tracking-tight">
          KOSGORO<span class="text-neo-yellow">™</span>
        </div>
        <div class="font-heading text-xs font-bold text-blue-200 uppercase tracking-[0.15em] mt-1">
          VOTING SYSTEM V1.0
        </div>
      </div>

      <!-- Tagline -->
      <div class="relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border-2 border-white/30 text-white text-xs font-heading font-bold px-3 py-1.5 uppercase tracking-wider mb-5">
          <div class="w-2 h-2 bg-neo-yellow animate-pulse"></div>
          PEMILIHAN BERLANGSUNG
        </div>
        <h1 class="font-heading font-black text-[44px] text-white leading-[1.05] uppercase">
          Suaramu<br>Menentukan<br>Pemimpin Kita
        </h1>
        <p class="font-body text-sm text-blue-100 mt-4 leading-relaxed max-w-[360px]">
          Aplikasi voting resmi Kosgoro. Gunakan hakmu untuk memilih dengan mudah, cepat, dan aman.
        </p>
      </div>

      <!-- Footer tag -->
      <div class="relative z-10 flex items-center gap-4">
        <div class="font-heading text-xs font-bold text-neo-white uppercase tracking-[0.2em]">
          #KOSGOROMEMILIH
        </div>
        <div class="flex-1 h-[2px] bg-white/20"></div>
      </div>
    </div>

    <!-- RIGHT PANEL: Form Area -->
    <div class="flex-1 flex flex-col bg-neo-surface neo-crosshatch relative">
      <!-- Decorative corner blocks -->
      <div class="absolute top-0 right-0 w-20 h-20 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20 pointer-events-none hidden md:block"></div>
      <div class="absolute bottom-0 left-0 w-16 h-16 bg-neo-blue/10 border-r-2 border-t-2 border-neo-blue/20 pointer-events-none hidden md:block"></div>
      
      <div class="flex-1 flex flex-col justify-center px-6 py-10 md:px-12 max-w-[480px] w-full mx-auto relative z-10">

        <!-- Mobile Logo Banner -->
        <div class="lg:hidden mb-8 border-neo border-neo-black bg-neo-blue p-4 shadow-neo relative overflow-hidden">
          <div class="absolute top-0 right-0 w-16 h-16 bg-neo-yellow border-l-neo border-b-neo border-neo-black"></div>
          <div class="absolute bottom-0 left-0 w-8 h-8 bg-neo-red/30 border-r-2 border-t-2 border-neo-red/40"></div>
          <div class="absolute inset-0 neo-scanline pointer-events-none"></div>
          <div class="relative z-10">
            <div class="font-heading font-black text-2xl text-white tracking-tight">
              KOSGORO<span class="text-neo-yellow">™</span>
            </div>
            <div class="font-heading text-[10px] font-bold text-blue-200 uppercase tracking-[0.15em]">
              VOTING SYSTEM V1.0
            </div>
            <p class="font-body text-xs text-blue-100 mt-2 max-w-[280px]">
              Gunakan hakmu untuk memilih pemimpin masa depan.
            </p>
          </div>
        </div>

        <!-- Back Link -->
        <div class="mb-5 flex">
          <Link 
            :href="route('welcome')" 
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-neo-dark-card border-2 border-neo-black dark:border-white font-heading text-[10px] md:text-xs font-black uppercase tracking-wider text-neo-black dark:text-white shadow-[3px_3px_0px_#000] dark:shadow-[3px_3px_0px_rgba(255,255,255,0.8)] hover:bg-neo-yellow dark:hover:bg-neo-yellow hover:text-neo-black dark:hover:text-neo-black hover:translate-x-[1.5px] hover:translate-y-[1.5px] hover:shadow-[1.5px_1.5px_0px_#000] dark:hover:shadow-[1.5px_1.5px_0px_rgba(255,255,255,0.8)] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none transition-all duration-100 group focus:outline-none"
          >
            <span class="material-symbols-outlined text-base font-bold group-hover:-translate-x-1 transition-transform text-neo-blue">arrow_back</span>
            KEMBALI KE HALAMAN UTAMA
          </Link>
        </div>

        <!-- Title -->
        <h2 class="font-heading font-black text-h2 uppercase text-neo-black mb-1">
          {{ title }}
        </h2>
        <p class="font-body text-sm text-neo-grey mb-8">
          {{ subtitle }}
        </p>

        <slot />

      </div>
      
      <!-- Bottom stripe on right panel -->
      <div class="neo-stripe-thin mt-auto"></div>
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
        :class="isAtBottom ? 'bottom-6 left-[calc(50%-4rem)] w-32 h-10 rounded-none px-3 gap-1.5' : 'bottom-6 left-[calc(100%-4.5rem)] w-12 h-12 hover:translate-x-[1.5px] hover:translate-y-[1.5px]'"
        aria-label="Scroll to top"
      >
        <span v-if="isAtBottom" class="whitespace-nowrap font-heading font-black tracking-wider text-[11px] select-none">KE ATAS</span>
        <span class="material-symbols-outlined font-bold text-xl">arrow_upward</span>
      </button>
    </Transition>

    <NeoToast />
  </div>
</template>

<script setup>
import NeoToast from '@/Components/NeoToast.vue';
import { useDarkMode } from '@/Composables/useDarkMode.js';
import { Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';

defineProps({
  title: String,
  subtitle: String,
});

// Initialize dark mode (no-op if already initialized, just keeps reactive)
useDarkMode();

const showScrollTop = ref(false);
const isAtBottom = ref(false);
const isScrollingToTop = ref(false);

const handleScroll = () => {
  if (isScrollingToTop.value) return;

  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  showScrollTop.value = scrollTop > 300;

  const scrollHeight = document.documentElement.scrollHeight;
  const clientHeight = window.innerHeight;
  
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
  showScrollTop.value = false;
  isAtBottom.value = false;
  isScrollingToTop.value = false;
});
</script>
