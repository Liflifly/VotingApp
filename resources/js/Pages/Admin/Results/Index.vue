<template>
  <AuthenticatedLayout title="HASIL ADMIN">
    <!-- Header -->
    <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>
      
      <div class="relative z-10">
        <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 md:mb-2 flex items-center gap-2 md:gap-3">
          <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">leaderboard</span>
          ADMIN RESULTS
        </h1>
        <p class="font-body text-xs md:text-sm text-neo-grey">
          {{ selectedElection ? selectedElection.name : 'Belum ada data' }}
        </p>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">
      <div class="neo-stat-card">
        <div class="neo-stat-value text-neo-blue">{{ totalVotes }}</div>
        <div class="neo-stat-label">SUARA MASUK</div>
      </div>
      <div class="neo-stat-card">
        <div class="neo-stat-value text-neo-black">{{ totalVoters }}</div>
        <div class="neo-stat-label">TOTAL PEMILIH</div>
      </div>
      <div class="neo-stat-card">
        <div class="neo-stat-value" :class="turnoutNumber >= 50 ? 'text-neo-blue' : 'text-neo-red'">{{ turnout }}%</div>
        <div class="neo-stat-label">PARTISIPASI</div>
      </div>
    </div>

    <!-- Results Table -->
    <div v-if="results.length">
      <h3 class="font-heading font-black text-lg md:text-h2 uppercase mb-3 md:mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-neo-grey">format_list_numbered</span>
        RANKING KANDIDAT
      </h3>
      <div class="space-y-3 md:space-y-4">
        <div v-for="(r, idx) in sortedResults" :key="r.id" class="neo-card p-4 md:p-5 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-8 h-8 border-l border-b" :class="idx === 0 ? 'bg-neo-yellow/20 border-neo-yellow/30' : 'bg-gray-100 border-gray-200'"></div>
          
          <div class="flex items-center gap-3 md:gap-4 mb-2 md:mb-3">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-100 border-2 border-neo-black flex items-center justify-center shrink-0 font-heading font-black text-sm md:text-base">
              {{ idx === 0 ? '🏆' : `#${idx + 1}` }}
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-100 border-2 border-neo-black shrink-0 overflow-hidden">
              <img v-if="r.photo" :src="`/storage/${r.photo}`" class="w-full h-full object-cover">
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined text-xl text-gray-300">person</span>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="font-heading font-bold text-xs md:text-sm uppercase truncate">{{ r.name }}</h3>
              <div class="font-body text-[10px] md:text-xs text-neo-grey">{{ r.votes_count }} suara</div>
            </div>
            <div class="font-heading font-black text-base md:text-xl shrink-0">{{ getPercentage(r.votes_count) }}%</div>
          </div>
          <div class="neo-health-bar h-5 md:h-6">
            <div class="neo-health-fill" :class="idx === 0 ? 'bg-neo-blue' : idx === 1 ? 'bg-neo-yellow' : 'bg-gray-300'" :style="{ width: `${getPercentage(r.votes_count)}%` }"></div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
      <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
      <p class="font-body text-sm md:text-body-md text-neo-grey">Belum ada hasil.</p>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';
const props = defineProps({ results: Array, totalVoters: Number, totalVotes: Number, elections: Array, selectedElection: Object });
const sortedResults = computed(() => [...(props.results || [])].sort((a, b) => b.votes_count - a.votes_count));
const turnoutNumber = computed(() => props.totalVoters ? (props.totalVotes / props.totalVoters) * 100 : 0);
const turnout = computed(() => turnoutNumber.value.toFixed(1));
const getPercentage = (v) => props.totalVotes ? ((v / props.totalVotes) * 100).toFixed(1) : 0;
</script>
