<template>
  <AuthenticatedLayout title="VOTER ANALYTICS">
    <!-- Header -->
    <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>
      
      <div class="relative z-10">
        <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 md:mb-2 flex items-center gap-2 md:gap-3">
          <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">analytics</span>
          VOTER ANALYTICS
        </h1>
        <p class="font-body text-xs md:text-sm text-neo-grey">Real-time election tracking and analytics</p>
      </div>
    </div>

    <div v-if="!selectedElection" class="neo-card p-8 text-center mb-6">
      <span class="material-symbols-outlined text-4xl text-neo-grey mb-3">event_busy</span>
      <h3 class="font-heading font-black text-lg uppercase dark:text-white">NO ELECTION DATA</h3>
      <p class="font-body text-sm text-neo-grey">Create and activate an election period first.</p>
    </div>

    <div v-else class="space-y-6 md:space-y-8">
      <!-- Election Selector -->
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-heading font-black text-base uppercase dark:text-white">Viewing Results for: <span class="text-neo-blue">{{ selectedElection.name }}</span></h3>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-blue">{{ totalVotes }}</div>
          <div class="neo-stat-label">TOTAL VOTES</div>
        </div>
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-black">{{ totalVoters }}</div>
          <div class="neo-stat-label">ELIGIBLE VOTERS</div>
        </div>
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-yellow">{{ participationRate }}%</div>
          <div class="neo-stat-label">PARTICIPATION RATE</div>
        </div>
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-red">{{ results.length }}</div>
          <div class="neo-stat-label">CANDIDATES</div>
        </div>
      </div>

      <!-- Leaderboard Chart -->
      <div class="neo-card p-4 md:p-6 bg-white dark:bg-neo-dark-card">
        <h3 class="font-heading font-black text-sm md:text-base uppercase mb-4 md:mb-6 border-b-2 border-neo-black dark:border-white pb-2 dark:text-white">VOTE DISTRIBUTION</h3>
        <div class="h-[250px] md:h-[300px]">
          <Bar v-if="chartData" :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Leaderboard List -->
      <div class="space-y-3">
        <h3 class="font-heading font-black text-sm md:text-base uppercase mb-2 dark:text-white">LEADERBOARD</h3>
        <div v-for="(candidate, index) in results" :key="candidate.id" 
             class="neo-card p-3 md:p-4 flex items-center gap-4 bg-white dark:bg-neo-dark-card relative overflow-hidden"
             :class="index === 0 ? 'border-neo-yellow ring-2 ring-neo-yellow/30' : ''">
          
          <div v-if="index === 0" class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow flex items-center justify-center border-l-2 border-b-2 border-neo-black dark:border-white">
            <span class="material-symbols-outlined text-neo-black text-sm">emoji_events</span>
          </div>

          <div class="w-8 md:w-10 text-center font-heading font-black text-xl md:text-2xl" :class="index === 0 ? 'text-neo-yellow' : 'text-neo-grey'">
            #{{ index + 1 }}
          </div>
          
          <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-200 border-2 border-neo-black dark:border-white shrink-0 overflow-hidden">
            <img v-if="candidate.photo_url" :src="candidate.photo_url" class="w-full h-full object-cover">
            <span v-else class="material-symbols-outlined w-full h-full flex items-center justify-center text-gray-400">person</span>
          </div>
          
          <div class="flex-1 min-w-0">
            <h4 class="font-heading font-black text-sm md:text-base uppercase truncate dark:text-white">{{ candidate.fields?.name || 'Unnamed' }}</h4>
            <div class="w-full h-2 md:h-3 bg-gray-100 dark:bg-gray-700 border border-neo-black dark:border-white mt-1 relative overflow-hidden">
              <div class="absolute top-0 left-0 h-full bg-neo-blue transition-all duration-1000" :style="{ width: `${getPercentage(candidate.votes_count)}%` }"></div>
            </div>
          </div>
          
          <div class="text-right shrink-0">
            <div class="font-heading font-black text-lg md:text-xl text-neo-blue">{{ candidate.votes_count }}</div>
            <div class="font-body text-[10px] md:text-xs text-neo-grey">{{ getPercentage(candidate.votes_count) }}%</div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { useDarkMode } from '@/Composables/useDarkMode.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
  event: Object,
  results: Array,
  totalVoters: Number,
  totalVotes: Number,
  selectedElection: Object,
  elections: Array,
  candidateFields: Array,
});

const { isDark } = useDarkMode();

const participationRate = computed(() => {
  if (!props.totalVoters || props.totalVoters === 0) return 0;
  return ((props.totalVotes / props.totalVoters) * 100).toFixed(1);
});

const getPercentage = (votes) => {
  if (props.totalVotes === 0) return 0;
  return ((votes / props.totalVotes) * 100).toFixed(1);
};

const chartData = computed(() => {
  if (!props.results?.length) return null;
  return {
    labels: props.results.map(c => c.fields?.name || `Candidate #${c.order_number}`),
    datasets: [{
      label: 'Total Votes',
      data: props.results.map(c => c.votes_count),
      backgroundColor: '#0048FF',
      borderColor: '#000000',
      borderWidth: 2,
    }]
  };
});

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: isDark.value ? '#1F2937' : '#FFFFFF',
      titleColor: isDark.value ? '#FFFFFF' : '#000000',
      bodyColor: isDark.value ? '#9CA3AF' : '#4B5563',
      borderColor: isDark.value ? '#FFFFFF' : '#000000',
      borderWidth: 2,
      padding: 10,
      cornerRadius: 0,
      titleFont: { family: "'Space Grotesk', sans-serif", size: 14, weight: 'bold' },
      bodyFont: { family: "'Work Sans', sans-serif", size: 13 },
      displayColors: false,
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        stepSize: 1,
        font: { family: "'Work Sans', sans-serif", weight: 'bold' },
        color: isDark.value ? '#9CA3AF' : '#4B5563',
      },
      grid: { color: isDark.value ? '#374151' : '#E5E7EB' },
      border: { color: isDark.value ? '#FFFFFF' : '#000000', width: 2 }
    },
    x: {
      ticks: {
        font: { family: "'Space Grotesk', sans-serif", weight: 'bold', size: 10 },
        color: isDark.value ? '#9CA3AF' : '#4B5563',
      },
      grid: { display: false },
      border: { color: isDark.value ? '#FFFFFF' : '#000000', width: 2 }
    }
  }
}));
</script>
