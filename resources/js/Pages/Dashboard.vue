<template>
  <AuthenticatedLayout title="DASHBOARD">

    <!-- No Active Election -->
    <div v-if="!activeElection" class="max-w-2xl mx-auto mt-8 md:mt-12">
      <div class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 border-neo border-neo-black mx-auto mb-4 md:mb-6 flex items-center justify-center">
          <span class="material-symbols-outlined text-3xl md:text-4xl text-neo-grey">event_busy</span>
        </div>
        <h2 class="font-heading font-black text-h2 uppercase mb-2">BELUM ADA PEMILIHAN</h2>
        <p class="font-body text-sm md:text-body-md text-neo-grey">Saat ini tidak ada periode pemilihan aktif. Kembali lagi nanti, warrior.</p>
      </div>
    </div>

    <div v-else>
      <!-- Election Countdown Banner -->
      <div class="neo-card bg-neo-blue p-5 md:p-6 lg:p-8 mb-6 md:mb-8 relative overflow-hidden neo-scanline">
        <div class="absolute top-0 right-0 w-20 md:w-32 h-20 md:h-32 bg-neo-yellow border-l-neo border-b-neo border-neo-black"></div>
        <div class="absolute bottom-0 left-0 w-12 h-12 bg-neo-red/20 border-r-2 border-t-2 border-neo-red/30 hidden md:block"></div>
        
        <!-- Diagonal stripe overlay -->
        <div class="absolute inset-0 opacity-5" style="background-image: repeating-linear-gradient(-45deg, transparent, transparent 20px, #fff 20px, #fff 22px)"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 md:gap-6">
          <div class="flex-1 min-w-0">
            <div class="neo-badge-live shadow-neo-sm neo-pulse mb-3 md:mb-4">
              <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
              LIVE NOW
            </div>
            <h2 class="font-heading font-black text-[28px] sm:text-[36px] md:text-h1 uppercase text-white mb-2 truncate">{{ activeElection.name }}</h2>
            <p class="font-body text-xs md:text-sm text-blue-100 max-w-lg">
              Gunakan hak suaramu untuk menentukan masa depan. Arena pemilihan sedang berlangsung!
            </p>
          </div>

          <!-- Stats -->
          <div class="flex gap-3 md:gap-4 w-full md:w-auto">
            <div class="bg-white border-neo border-neo-black p-3 md:p-4 text-center flex-1 md:flex-none md:min-w-[120px]">
              <div class="font-heading text-[24px] md:text-stats text-neo-blue">{{ totalVotes }}</div>
              <div class="font-heading text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-neo-grey mt-1">TOTAL SUARA</div>
            </div>
            <div class="bg-white border-neo border-neo-black p-3 md:p-4 text-center flex-1 md:flex-none md:min-w-[120px]">
              <div class="font-heading text-[24px] md:text-stats text-neo-black">{{ candidates?.length || 0 }}</div>
              <div class="font-heading text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-neo-grey mt-1">KANDIDAT</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Action Bar -->
      <div class="flex flex-col sm:flex-row gap-3 mb-6 md:mb-8">
        <Link :href="route('vote.index')" class="neo-btn-primary flex-1 text-xs py-3 justify-center">
          <span class="material-symbols-outlined text-base">how_to_vote</span>
          PILIH SEKARANG
        </Link>
        <Link :href="route('results.index')" class="neo-btn-secondary flex-1 text-xs py-3 justify-center">
          <span class="material-symbols-outlined text-base">analytics</span>
          LIHAT ANALYTICS
        </Link>
      </div>

      <!-- Hero Selection -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4 md:mb-6">
        <h3 class="font-heading font-black text-lg md:text-h2 uppercase flex items-center gap-3">
          <span class="material-symbols-outlined text-neo-blue">groups</span>
          HERO SELECTION
        </h3>
        <span class="neo-badge bg-neo-yellow text-neo-black">{{ candidates?.length || 0 }} KANDIDAT</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div v-for="candidate in candidates" :key="candidate.id"
          class="neo-card overflow-hidden group hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all relative">
          
          <!-- Photo -->
          <div class="aspect-square bg-gray-100 border-b-neo border-neo-black relative overflow-hidden">
            <img v-if="candidate.photo" :src="`/storage/${candidate.photo}`" :alt="candidate.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <div v-else class="w-full h-full flex items-center justify-center">
              <span class="material-symbols-outlined text-5xl md:text-6xl text-gray-300">person</span>
            </div>
            <!-- Number Badge -->
            <div class="absolute top-3 left-3 bg-neo-yellow border-2 border-neo-black px-2.5 py-0.5 md:px-3 md:py-1">
              <span class="font-heading text-xs md:text-sm font-black">#{{ candidate.order_number }}</span>
            </div>
          </div>

          <!-- Info -->
          <div class="p-4 md:p-5">
            <h4 class="font-heading font-black text-base md:text-lg uppercase mb-1 group-hover:text-neo-blue transition-colors">
              {{ candidate.name }}
            </h4>
            <p class="font-body text-xs md:text-sm text-neo-grey line-clamp-2 mb-3 md:mb-4">{{ candidate.vision }}</p>
            
            <Link v-if="!user?.has_voted" :href="route('vote.index')" class="neo-btn-primary w-full text-[10px] md:text-xs py-2 md:py-2.5">
              PILIH HERO →
            </Link>
            <div v-else class="neo-btn w-full text-[10px] md:text-xs py-2 md:py-2.5 bg-gray-100 text-neo-grey border-gray-300 shadow-none cursor-default justify-center">
              ✓ SUDAH MEMILIH
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  user: Object,
  activeElection: Object,
  candidates: Array,
  totalVotes: Number,
});
</script>
