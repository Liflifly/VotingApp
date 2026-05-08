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
        <p class="font-body text-sm md:text-body-md text-neo-grey">Saat ini tidak ada periode pemilihan aktif. Silakan periksa kembali nanti.</p>
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
            <h2 class="font-heading font-black text-xl sm:text-2xl md:text-h1 uppercase text-white mb-2 truncate">{{ activeElection.name }}</h2>
            <p class="font-body text-xs md:text-sm text-blue-100 max-w-lg">
              Gunakan hak suara Anda untuk menentukan masa depan. Periode pemilihan sedang berlangsung!
            </p>
          </div>

          <!-- Stats -->
          <div class="flex gap-3 md:gap-4 w-full md:w-auto">
            <div class="bg-white border-neo border-neo-black p-3 md:p-4 text-center flex-1 md:flex-none md:min-w-[120px]">
              <div class="font-heading font-black text-stats text-neo-blue">{{ totalVotes }}</div>
              <div class="font-heading text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-neo-grey mt-1">TOTAL SUARA</div>
            </div>
            <div class="bg-white border-neo border-neo-black p-3 md:p-4 text-center flex-1 md:flex-none md:min-w-[120px]">
              <div class="font-heading font-black text-stats text-neo-black">{{ candidates?.length || 0 }}</div>
              <div class="font-heading text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-neo-grey mt-1">KANDIDAT</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Action Bar -->
      <div class="flex flex-col sm:flex-row gap-3 mb-6 md:mb-8">
        <Link :href="route('vote.index')" class="neo-btn-sm-primary flex-1">
          <span class="material-symbols-outlined text-base">groups</span>
          LIHAT KANDIDAT
        </Link>
        <Link :href="route('results.index')" class="neo-btn-sm-secondary flex-1">
          <span class="material-symbols-outlined text-base">analytics</span>
          LIHAT ANALYTICS
        </Link>
      </div>

      <!-- Catatan Periode (Jika ada) -->
      <div v-if="activeElection.notes" class="neo-card bg-neo-yellow/10 border-2 border-neo-black p-4 md:p-5 mb-6 md:mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow/20 border-l border-b border-neo-black"></div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10 w-full">
          <div class="flex items-start gap-3 flex-1">
            <span class="material-symbols-outlined text-neo-yellow text-2xl font-bold bg-neo-black p-1.5 shrink-0 shadow-[2px_2px_0px_#000]">event_note</span>
            <div>
              <h4 class="font-heading font-black text-xs md:text-sm uppercase tracking-wider text-neo-black mb-1">CATATAN PERIODE</h4>
              <p class="font-body text-xs md:text-sm text-neo-black leading-relaxed whitespace-pre-line">{{ activeElection.notes }}</p>
            </div>
          </div>
          
          <!-- Scroll Button -->
          <button 
            @click="scrollToCandidates" 
            class="neo-btn bg-white hover:bg-neo-blue hover:text-white text-neo-black py-2 px-3 shadow-[2px_2px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all duration-100 shrink-0 self-stretch sm:self-auto flex items-center justify-center gap-2 text-xs font-heading font-black focus:outline-none"
          >
            LIHAT KANDIDAT
            <span class="material-symbols-outlined text-base animate-bounce">arrow_downward</span>
          </button>
        </div>
      </div>

      <!-- Candidate Selection (FORMALIZED FROM HERO SELECTION) -->
      <div id="candidates-section" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4 md:mb-6">
        <h3 class="font-heading font-black text-lg md:text-h2 uppercase flex items-center gap-3">
          <span class="material-symbols-outlined text-neo-blue">groups</span>
          DAFTAR KANDIDAT
        </h3>
        <span class="neo-badge bg-neo-yellow text-neo-black">{{ candidates?.length || 0 }} KANDIDAT</span>
      </div>

      <!-- Candidate Grid (Made smaller and more consistent with Vote Index) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
        <div v-for="candidate in candidates" :key="candidate.id"
          class="neo-card overflow-hidden group hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all relative flex flex-col bg-white">
          
          <!-- Photo (Restricted height to look smaller and formal) -->
          <div class="h-36 sm:h-40 w-full bg-gray-100 border-b-neo border-neo-black relative overflow-hidden shrink-0">
            <img v-if="candidate.photo" :src="`/storage/${candidate.photo}`" :alt="candidate.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <div v-else class="w-full h-full flex items-center justify-center">
              <span class="material-symbols-outlined text-4xl text-gray-300">person</span>
            </div>
            <!-- Number Badge -->
            <div class="absolute top-2.5 left-2.5 bg-neo-yellow border-2 border-neo-black px-2 py-0.5">
              <span class="font-heading text-[10px] md:text-xs font-black">#{{ candidate.order_number }}</span>
            </div>
          </div>

          <!-- Info -->
          <div class="p-3 md:p-4 flex flex-col flex-1">
            <h4 class="font-heading font-black text-sm md:text-base uppercase mb-1 group-hover:text-neo-blue transition-colors truncate">
              {{ candidate.name }}
            </h4>
            <p class="font-body text-[11px] md:text-xs text-neo-grey line-clamp-2 mb-3 flex-1">{{ candidate.vision }}</p>
            
            <Link v-if="!user?.has_voted" :href="route('vote.index')" class="neo-btn-sm-primary w-full">
              LIHAT DETAIL KANDIDAT →
            </Link>
            <div v-else class="neo-btn-sm w-full bg-gray-100 text-neo-grey border-gray-300 shadow-none cursor-default pointer-events-none">
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

const scrollToCandidates = () => {
  document.getElementById('candidates-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};
</script>
