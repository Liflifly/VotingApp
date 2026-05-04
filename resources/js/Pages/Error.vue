<template>
  <div class="min-h-screen bg-neo-surface neo-rich-bg flex flex-col relative overflow-hidden">
    <!-- Decorative corner blocks -->
    <div class="absolute top-0 left-0 w-20 h-20 md:w-32 md:h-32 bg-neo-yellow border-r-neo border-b-neo border-neo-black shadow-neo-sm"></div>
    <div class="absolute top-0 right-0 w-16 h-16 md:w-24 md:h-24 bg-neo-red border-l-neo border-b-neo border-neo-black shadow-neo-sm"></div>
    <div class="absolute bottom-0 left-0 w-16 h-16 md:w-24 md:h-24 bg-neo-blue border-r-neo border-t-neo border-neo-black shadow-neo-sm"></div>
    <div class="absolute bottom-0 right-0 w-12 h-12 md:w-20 md:h-20 bg-neo-yellow border-l-neo border-t-neo border-neo-black shadow-neo-sm"></div>

    <!-- Scanline overlay -->
    <div class="absolute inset-0 neo-scanline pointer-events-none opacity-30"></div>

    <!-- Ticker -->
    <div class="neo-ticker-bar relative z-10">
      <div class="neo-ticker-content">
        <template v-for="n in 4" :key="n">
          <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>ERROR {{ status }}</span>
          <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>CRITICAL SYSTEM ALERT</span>
          <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>{{ title }}</span>
          <span class="neo-ticker-item"><span class="neo-ticker-dot"></span>KOSGORO™ V1.0</span>
        </template>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center p-6 relative z-10">
      <div class="w-full max-w-lg">

        <!-- Error Card -->
        <div class="neo-card bg-white p-6 md:p-10 text-center relative overflow-hidden group">
          <!-- Corner decoration -->
          <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow border-l-2 border-b-2 border-neo-black transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform"></div>
          <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-blue/10 border-r-2 border-t-2 border-neo-blue/20"></div>

          <!-- Error Status Number -->
          <div class="relative mb-6 md:mb-8">
            <!-- Background number (decorative) -->
            <div class="font-heading font-black text-[100px] md:text-[140px] leading-none text-gray-100 select-none absolute inset-0 flex items-center justify-center -rotate-6" aria-hidden="true">
              {{ status }}
            </div>
            <!-- Foreground status badge -->
            <div class="relative z-10 flex justify-center">
              <div class="inline-flex items-center gap-3 px-6 py-3 md:px-8 md:py-4 border-neo border-neo-black shadow-neo transform -rotate-2"
                :class="statusBadgeClasses">
                <span class="material-symbols-outlined text-2xl md:text-3xl">
                  {{ statusIcon }}
                </span>
                <span class="font-heading font-black text-xl md:text-3xl tracking-tighter uppercase">ERR_{{ status }}</span>
              </div>
            </div>
          </div>

          <!-- Title -->
          <h1 class="font-heading font-black text-h2 md:text-h1 uppercase mb-3 mt-12 md:mt-16 leading-none">
            {{ title }}
          </h1>

          <!-- Description -->
          <p class="font-body text-sm md:text-base text-neo-grey mb-8 md:mb-10 max-w-sm mx-auto leading-relaxed">
            {{ description }}
          </p>

          <!-- Stripe divider -->
          <div class="neo-stripe-thin mb-8 md:mb-10 -mx-6 md:-mx-10"></div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link :href="route('dashboard')" class="neo-btn-primary text-xs py-3.5 px-8 shadow-neo">
              <span class="material-symbols-outlined text-base">grid_view</span>
              DASHBOARD
            </Link>
            <button @click="goBack" class="neo-btn-secondary text-xs py-3.5 px-8 shadow-neo">
              <span class="material-symbols-outlined text-base">arrow_back</span>
              KEMBALI
            </button>
          </div>

          <!-- Error code small note -->
          <div class="mt-8 font-heading text-[10px] font-bold text-gray-300 uppercase tracking-[0.25em]">
            IDENTIFIER: {{ errorIdentifier }} • KOSGORO™ OS
          </div>
        </div>

        <!-- Brand -->
        <div class="text-center mt-6 md:mt-8">
          <div class="font-heading font-black text-xl text-neo-black tracking-tight">KOSGORO<span class="text-neo-blue">™</span></div>
          <div class="font-heading text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-0.5">ESTABLISHED 2026 • DIGITAL ARENA</div>
        </div>
      </div>
    </div>

    <!-- Footer stripe -->
    <div class="neo-stripe-divider relative z-10 mt-auto"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  status: Number,
});

const title = computed(() => {
  const titles = {
    503: 'MAINTENANCE MODE',
    500: 'SERVER MELTDOWN',
    419: 'SESSION EXPIRED',
    403: 'AKSES DITOLAK',
    404: 'MISSING IN ACTION',
  };
  return titles[props.status] ?? 'UNKNOWN ERROR';
});

const description = computed(() => {
  const descriptions = {
    503: 'Sistem sedang dalam pemeliharaan rutin. Silakan kembali dalam beberapa menit.',
    500: 'Terjadi kegagalan sistem internal. Teknisi kami sedang melakukan perbaikan darurat.',
    419: 'Sesi anda telah kedaluwarsa demi keamanan. Silakan refresh dan login kembali.',
    403: 'Anda tidak memiliki otorisasi untuk mengakses zona ini. Silakan hubungi Commander.',
    404: 'Halaman yang anda cari tidak ditemukan di server. Periksa kembali koordinat URL anda.',
  };
  return descriptions[props.status] ?? 'Terjadi kesalahan sistem yang tidak terduga. Silakan hubungi tim dukungan kami.';
});

const statusIcon = computed(() => {
  const icons = {
    403: 'lock',
    404: 'search_off',
    500: 'error',
    419: 'schedule',
  };
  return icons[props.status] ?? 'warning';
});

const statusBadgeClasses = computed(() => {
  if (props.status === 403) return 'bg-neo-red text-white';
  if (props.status === 404) return 'bg-neo-yellow text-neo-black';
  if (props.status === 500) return 'bg-neo-black text-white';
  if (props.status === 419) return 'bg-neo-blue text-white';
  return 'bg-neo-blue text-white';
});

const errorIdentifier = computed(() => {
  return Math.random().toString(36).substring(7).toUpperCase();
});

const goBack = () => {
  window.history.back();
};
</script>
