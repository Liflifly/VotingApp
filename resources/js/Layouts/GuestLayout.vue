<template>
  <div :class="['min-h-screen flex flex-col lg:flex-row dark:bg-neo-dark-bg', themeClass]">
    <div class="hidden lg:flex flex-col justify-between w-[480px] shrink-0 bg-neo-blue border-r-neo border-neo-black p-10 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-40 h-40 bg-neo-yellow border-l-neo border-b-neo border-neo-black"></div>
      <div class="absolute bottom-0 left-0 w-32 h-32 bg-neo-red border-r-neo border-t-neo border-neo-black"></div>
      <div class="absolute inset-0 neo-scanline pointer-events-none"></div>
      <div class="absolute inset-0 opacity-5" style="background-image: repeating-linear-gradient(-45deg, transparent, transparent 20px, #fff 20px, #fff 22px)"></div>
      <div class="relative z-10">
        <div class="font-heading font-black text-3xl text-white tracking-tight">VUWOTING<span class="text-neo-yellow">&#8482;</span></div>
        <div class="font-heading text-xs font-bold text-blue-200 uppercase tracking-[0.15em] mt-1">EVENT VOTING PLATFORM V2.0</div>
      </div>
      <div class="relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border-2 border-white/30 text-white text-xs font-heading font-bold px-3 py-1.5 uppercase tracking-wider mb-5">
          <div class="w-2 h-2 bg-neo-yellow animate-pulse"></div>
          SECURE · TRANSPARENT · SCALABLE
        </div>
        <h1 class="font-heading font-black text-[44px] text-white leading-[1.05] uppercase">
          Your Vote.<br>Your Event.<br>Your Decision.
        </h1>
        <p class="font-body text-sm text-blue-100 mt-4 leading-relaxed max-w-[360px]">
          Create and manage any voting event. From company elections to student councils — Vuwoting handles it all.
        </p>
      </div>
      <div class="relative z-10 flex items-center gap-4">
        <div class="font-heading text-xs font-bold text-neo-white uppercase tracking-[0.2em]">#VUWOTING</div>
        <div class="flex-1 h-[2px] bg-white/20"></div>
      </div>
    </div>

    <div class="flex-1 flex flex-col bg-neo-surface neo-crosshatch relative">
      <div class="absolute top-0 right-0 w-20 h-20 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20 pointer-events-none hidden md:block"></div>
      <div class="absolute bottom-0 left-0 w-16 h-16 bg-neo-blue/10 border-r-2 border-t-2 border-neo-blue/20 pointer-events-none hidden md:block"></div>
      <div class="flex-1 flex flex-col justify-center px-6 py-10 md:px-12 max-w-[480px] w-full mx-auto relative z-10">
        <div class="lg:hidden mb-8 border-neo border-neo-black bg-neo-blue p-4 shadow-neo relative overflow-hidden">
          <div class="absolute top-0 right-0 w-16 h-16 bg-neo-yellow border-l-neo border-b-neo border-neo-black"></div>
          <div class="absolute inset-0 neo-scanline pointer-events-none"></div>
          <div class="relative z-10">
            <div class="font-heading font-black text-2xl text-white tracking-tight">VUWOTING<span class="text-neo-yellow">&#8482;</span></div>
            <div class="font-heading text-[10px] font-bold text-blue-200 uppercase tracking-[0.15em]">EVENT VOTING PLATFORM V2.0</div>
          </div>
        </div>
        <div class="mb-5 flex">
          <Link :href="route('dashboard')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-neo-dark-card border-2 border-neo-black dark:border-white font-heading text-[10px] md:text-xs font-black uppercase tracking-wider text-neo-black dark:text-white shadow-[3px_3px_0px_#000] hover:bg-neo-yellow hover:translate-x-[1.5px] hover:translate-y-[1.5px] hover:shadow-[1.5px_1.5px_0px_#000] active:shadow-none transition-all duration-100 group focus:outline-none">
            <span class="material-symbols-outlined text-base font-bold group-hover:-translate-x-1 transition-transform text-neo-blue">arrow_back</span>
            BACK TO DASHBOARD
          </Link>
        </div>
        <h2 class="font-heading font-black text-h2 uppercase text-neo-black dark:text-white mb-1">{{ title }}</h2>
        <p class="font-body text-sm text-neo-grey mb-8">{{ subtitle }}</p>
        <slot />
      </div>
      <div class="neo-stripe-thin mt-auto"></div>
    </div>
    <NeoToast />
  </div>
</template>

<script setup>
import NeoToast from '@/Components/NeoToast.vue';
import { useDarkMode } from '@/Composables/useDarkMode.js';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({ title: String, subtitle: String });

useDarkMode();

const page = usePage();
const currentEvent = computed(() => page.props.currentEvent);

const themeClass = computed(() => {
  if (currentEvent.value && currentEvent.value.theme) {
    return `theme-${currentEvent.value.theme}`;
  }
  return 'theme-neo-brutalism';
});
</script>
