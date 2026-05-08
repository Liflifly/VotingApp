<template>
  <AuthenticatedLayout title="VOTER ANALYTICS">

    <div v-if="!election" class="max-w-2xl mx-auto mt-8 md:mt-12">
      <div class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
        <h2 class="font-heading font-black text-h2 uppercase mb-2">DATA KOSONG</h2>
        <p class="font-body text-sm md:text-body-md text-neo-grey">Belum ada hasil pemilihan yang tersedia.</p>
      </div>
    </div>

    <div v-else>
      <!-- Header -->
      <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
        <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>
        
        <div class="relative z-10">
          <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 md:mb-2 flex items-center gap-2 md:gap-3">
            <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">leaderboard</span>
            ELECTION RESULTS
          </h1>
          <p class="font-body text-xs md:text-body-md text-neo-grey uppercase font-bold tracking-wider">{{ election.name }}</p>
        </div>
      </div>

      <!-- Catatan Periode (Jika ada) -->
      <div v-if="election.notes" class="neo-card bg-neo-yellow/10 border-2 border-neo-black p-4 md:p-5 mb-6 md:mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow/20 border-l border-b border-neo-black"></div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10 w-full">
          <div class="flex items-start gap-3 flex-1">
            <span class="material-symbols-outlined text-neo-yellow text-2xl font-bold bg-neo-black p-1.5 shrink-0 shadow-[2px_2px_0px_#000]">event_note</span>
            <div>
              <h4 class="font-heading font-black text-xs md:text-sm uppercase tracking-wider text-neo-black mb-1">CATATAN PERIODE</h4>
              <p class="font-body text-xs md:text-sm text-neo-black leading-relaxed whitespace-pre-line uppercase font-bold">{{ election.notes }}</p>
            </div>
          </div>
          
          <!-- Scroll Button -->
          <button 
            @click="scrollToCandidates" 
            class="neo-btn-sm-secondary shrink-0 self-stretch sm:self-auto"
          >
            LIHAT KANDIDAT
            <span class="material-symbols-outlined text-base animate-bounce">arrow_downward</span>
          </button>
        </div>
      </div>

      <!-- Stats Row -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-blue">{{ totalVotes.toLocaleString() }}</div>
          <div class="neo-stat-label">TOTAL BALLOTS CAST</div>
        </div>
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-black">{{ candidates.length }}</div>
          <div class="neo-stat-label">CANDIDATES</div>
        </div>
        <div class="neo-stat-card">
          <span class="neo-badge" :class="election.status === 'active' ? 'bg-neo-blue text-white' : election.status === 'ended' ? 'bg-gray-200' : 'bg-neo-yellow'">
            {{ election.status?.toUpperCase() || 'N/A' }}
          </span>
          <div class="neo-stat-label mt-2">ELECTION STATUS</div>
        </div>
      </div>

      <!-- CHAMPION (Winner) -->
      <div v-if="sortedCandidates.length > 0" class="neo-card p-4 sm:p-5 md:p-6 mb-5 md:mb-6 border-neo-yellow bg-neo-yellow/5 relative overflow-hidden">
        <div class="absolute top-0 right-0 px-2.5 py-1 bg-neo-yellow border-l-2 border-b-2 border-neo-black">
          <span class="font-heading text-[10px] sm:text-xs font-black uppercase">🏆 CHAMPION</span>
        </div>
        <div class="absolute bottom-0 left-0 w-12 h-12 bg-neo-yellow/20 border-r-2 border-t-2 border-neo-yellow/30 hidden md:block"></div>

        <div class="flex flex-col md:flex-row items-center gap-3 sm:gap-4 md:gap-5 mt-4 md:mt-0">
          <div class="w-16 h-16 sm:w-18 sm:h-18 md:w-20 md:h-20 bg-gray-100 border-neo border-neo-black shrink-0 overflow-hidden">
            <img v-if="sortedCandidates[0].photo" :src="`/storage/${sortedCandidates[0].photo}`" class="w-full h-full object-cover">
            <div v-else class="w-full h-full flex items-center justify-center">
              <span class="material-symbols-outlined text-2xl sm:text-3xl md:text-4xl text-gray-300">person</span>
            </div>
          </div>
          <div class="flex-1 text-center md:text-left">
            <h3 class="font-heading font-black text-base sm:text-lg md:text-xl uppercase text-neo-black">{{ sortedCandidates[0].name }}</h3>
            <div class="font-heading text-sm sm:text-base md:text-lg font-bold text-neo-blue mt-1">{{ sortedCandidates[0].votes_count.toLocaleString() }} <span class="text-xs md:text-sm text-neo-grey">VOTES</span></div>
          </div>
          <div class="text-center">
            <div class="font-heading text-2xl sm:text-3xl md:text-4xl font-black text-neo-blue">{{ getPercentage(sortedCandidates[0].votes_count) }}%</div>
          </div>
        </div>

        <!-- Health Bar -->
        <div class="mt-3 md:mt-4 neo-health-bar">
          <div class="neo-health-fill bg-neo-blue" :style="{ width: `${getPercentage(sortedCandidates[0].votes_count)}%` }"></div>
        </div>
      </div>

      <h3 id="ranking-section" class="font-heading font-black text-lg md:text-h2 uppercase mb-3 md:mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-neo-grey">format_list_numbered</span>
        CHALLENGERS RANKING
      </h3>
      <div class="space-y-3 md:space-y-4">
        <div v-for="(candidate, index) in sortedCandidates.slice(1)" :key="candidate.id" class="neo-card p-4 md:p-5 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-8 h-8 border-l border-b" :class="index === 0 ? 'bg-neo-yellow/10 border-neo-yellow/20' : 'bg-gray-100 border-gray-200'"></div>
          
          <div class="flex items-center gap-3 md:gap-5">
            <!-- Rank -->
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-100 border-neo border-neo-black flex items-center justify-center shrink-0">
              <span class="font-heading font-black text-sm md:text-lg">#{{ index + 2 }}</span>
            </div>

            <!-- Photo -->
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-100 border-2 border-neo-black shrink-0 overflow-hidden">
              <img v-if="candidate.photo" :src="`/storage/${candidate.photo}`" class="w-full h-full object-cover">
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h4 class="font-heading font-bold text-xs md:text-sm uppercase truncate">{{ candidate.name }}</h4>
              <div class="font-body text-[10px] md:text-xs text-neo-grey">{{ candidate.votes_count.toLocaleString() }} votes</div>
            </div>

            <!-- Percentage -->
            <div class="font-heading font-black text-base md:text-lg text-neo-black shrink-0">
              {{ getPercentage(candidate.votes_count) }}%
            </div>
          </div>

          <!-- Health Bar -->
          <div class="mt-2 md:mt-3 neo-health-bar h-4 md:h-5">
            <div class="neo-health-fill" :class="index === 0 ? 'bg-neo-yellow' : 'bg-gray-300'" :style="{ width: `${getPercentage(candidate.votes_count)}%` }"></div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
  election: Object,
  candidates: Array,
  totalVotes: Number,
});

const sortedCandidates = computed(() => {
  if (!props.candidates) return [];
  return [...props.candidates].sort((a, b) => b.votes_count - a.votes_count);
});

const getPercentage = (votes) => {
  if (!props.totalVotes || props.totalVotes === 0) return 0;
  return ((votes / props.totalVotes) * 100).toFixed(1);
};

const scrollToCandidates = () => {
  document.getElementById('ranking-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};
</script>
