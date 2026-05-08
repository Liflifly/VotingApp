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
        <p class="font-body text-xs md:text-sm text-neo-grey">Real-time election tracking, results, and AI-powered analysis.</p>
      </div>
    </div>

    <div v-if="!selectedElection" class="neo-card p-8 text-center mb-6">
      <span class="material-symbols-outlined text-4xl text-neo-grey mb-3 block">event_busy</span>
      <h3 class="font-heading font-black text-lg uppercase dark:text-white">NO ELECTION DATA</h3>
      <p class="font-body text-sm text-neo-grey">Create and activate an election period first.</p>
    </div>

    <div v-else class="space-y-6 md:space-y-8">
      <!-- Election Selector -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-heading font-black text-base uppercase dark:text-white">
          Results for: <span class="text-neo-blue">{{ selectedElection.name }}</span>
        </h3>
        <select
          v-if="elections.length > 1"
          @change="switchElection"
          class="neo-input w-full sm:w-60 text-sm"
        >
          <option v-for="el in elections" :key="el.id" :value="el.id" :selected="el.id === selectedElection.id">
            {{ el.name }} ({{ el.status }})
          </option>
        </select>
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

      <!-- ─── AI ANALYSIS PANEL ─────────────────────────────────────── -->
      <div class="neo-card overflow-hidden">
        <div class="flex items-center justify-between p-4 md:p-5 border-b-2 border-neo-black dark:border-white">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-neo-black dark:bg-white flex items-center justify-center border-neo border-neo-black shrink-0">
              <span class="material-symbols-outlined text-white dark:text-neo-black text-lg">psychology</span>
            </div>
            <div>
              <div class="font-heading font-black text-sm uppercase dark:text-white">AI RESULTS ANALYSIS</div>
              <div class="font-body text-[10px] text-neo-grey">Powered by Ollama · Results are cached</div>
            </div>
          </div>
          <button
            @click="requestAiAnalysis"
            :disabled="aiLoading || totalVotes === 0"
            class="neo-btn-primary text-xs px-4 py-2 flex items-center gap-1.5"
            :class="{ 'opacity-50 cursor-not-allowed': aiLoading || totalVotes === 0 }"
          >
            <span v-if="aiLoading" class="material-symbols-outlined text-sm animate-spin">autorenew</span>
            <span v-else class="material-symbols-outlined text-sm">auto_awesome</span>
            {{ aiLoading ? 'ANALYSING...' : (aiAnalysis ? 'RE-ANALYSE' : 'ANALYSE WITH AI') }}
          </button>
        </div>

        <!-- AI Response area -->
        <div class="p-4 md:p-5">
          <div v-if="totalVotes === 0" class="text-center py-4 text-neo-grey font-body text-sm">
            No votes yet — AI analysis will be available once voting has started.
          </div>
          <div v-else-if="!aiAnalysis && !aiLoading && !aiError" class="text-center py-4 text-neo-grey font-body text-sm">
            Click <strong>Analyse with AI</strong> to generate a summary of the current results.
          </div>
          <div v-else-if="aiLoading" class="flex items-center gap-3 py-4">
            <span class="material-symbols-outlined text-neo-blue animate-spin">autorenew</span>
            <p class="font-body text-sm text-neo-grey">AI is analysing the results, please wait...</p>
          </div>
          <div v-else-if="aiError" class="neo-card p-3 bg-neo-red/10 border-2 border-neo-red">
            <p class="font-body text-sm text-neo-red">{{ aiError }}</p>
            <button @click="requestAiAnalysis" class="neo-btn-sm-secondary mt-2 text-xs">RETRY</button>
          </div>
          <div v-else-if="aiAnalysis" class="space-y-2">
            <div class="flex items-center gap-2 mb-3">
              <div class="w-1 h-5 bg-neo-blue"></div>
              <span class="font-heading text-[9px] font-bold uppercase text-neo-grey tracking-wider">
                AI ANALYSIS · {{ aiCached ? 'CACHED' : 'FRESH' }} · MODEL: {{ aiModel }}
              </span>
            </div>
            <div class="font-body text-sm dark:text-white leading-relaxed whitespace-pre-wrap prose-neo">
              {{ aiAnalysis }}
            </div>
          </div>
        </div>
      </div>

      <!-- Vote Distribution Chart -->
      <div class="neo-card p-4 md:p-6 bg-white dark:bg-neo-dark-card">
        <h3 class="font-heading font-black text-sm md:text-base uppercase mb-4 md:mb-6 border-b-2 border-neo-black dark:border-white pb-2 dark:text-white">
          VOTE DISTRIBUTION
        </h3>
        <div class="h-[250px] md:h-[300px]">
          <Bar v-if="chartData" :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Leaderboard List -->
      <div class="space-y-3">
        <h3 class="font-heading font-black text-sm md:text-base uppercase mb-2 dark:text-white">LEADERBOARD</h3>
        <div
          v-for="(candidate, index) in results" :key="candidate.id"
          class="neo-card p-3 md:p-4 flex items-center gap-4 bg-white dark:bg-neo-dark-card relative overflow-hidden"
          :class="index === 0 ? 'border-neo-yellow ring-2 ring-neo-yellow/30' : ''"
        >
          <div v-if="index === 0" class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow flex items-center justify-center border-l-2 border-b-2 border-neo-black dark:border-white">
            <span class="material-symbols-outlined text-neo-black text-sm">emoji_events</span>
          </div>

          <div class="w-8 md:w-10 text-center font-heading font-black text-xl md:text-2xl" :class="index === 0 ? 'text-neo-yellow' : 'text-neo-grey'">
            #{{ index + 1 }}
          </div>

          <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-200 border-2 border-neo-black dark:border-white shrink-0 overflow-hidden">
            <img v-if="candidate.photo_url" :src="candidate.photo_url" class="w-full h-full object-cover" />
            <span v-else class="material-symbols-outlined w-full h-full flex items-center justify-center text-gray-400">person</span>
          </div>

          <div class="flex-1 min-w-0">
            <h4 class="font-heading font-black text-sm md:text-base uppercase truncate dark:text-white">
              {{ getCandidateName(candidate) }}
            </h4>
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
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { useDarkMode } from '@/Composables/useDarkMode.js';
import axios from 'axios';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
  event:             Object,
  results:           Array,
  totalVoters:       Number,
  totalVotes:        Number,
  selectedElection:  Object,
  elections:         Array,
  candidateFields:   Array,
});

const { isDark } = useDarkMode();
const page = usePage();

// ─── AI Analysis ──────────────────────────────────────────────────────────────
const aiAnalysis = ref('');
const aiLoading  = ref(false);
const aiError    = ref('');
const aiCached   = ref(false);
const aiModel    = ref('');

const requestAiAnalysis = async () => {
  if (!props.selectedElection || props.totalVotes === 0) return;
  aiLoading.value  = true;
  aiError.value    = '';
  aiAnalysis.value = '';

  try {
    const slug = page.props.currentEvent?.slug || props.event?.slug;
    const resp = await axios.post(`/e/${slug}/admin/results/ai-analysis`, {
      election_id: props.selectedElection.id,
    });
    aiAnalysis.value = resp.data.analysis;
    aiCached.value   = resp.data.cached;
    aiModel.value    = resp.data.model;
  } catch (err) {
    aiError.value = err.response?.data?.error || 'Failed to connect to AI service. Is Ollama running?';
  } finally {
    aiLoading.value = false;
  }
};

// ─── Election Switcher ────────────────────────────────────────────────────────
const switchElection = (e) => {
  const slug = page.props.currentEvent?.slug || props.event?.slug;
  router.visit(`/e/${slug}/admin/results?election_id=${e.target.value}`);
};

// ─── Helpers ──────────────────────────────────────────────────────────────────
const getCandidateName = (candidate) => {
  if (!candidate.fields) return `Candidate #${candidate.order_number}`;
  const data = typeof candidate.fields === 'string' ? JSON.parse(candidate.fields) : candidate.fields;
  return Object.values(data)[0] || `Candidate #${candidate.order_number}`;
};

const participationRate = computed(() => {
  if (!props.totalVoters || props.totalVoters === 0) return 0;
  return ((props.totalVotes / props.totalVoters) * 100).toFixed(1);
});

const getPercentage = (votes) => {
  if (!props.totalVotes || props.totalVotes === 0) return 0;
  return ((votes / props.totalVotes) * 100).toFixed(1);
};

// ─── Chart ────────────────────────────────────────────────────────────────────
const chartData = computed(() => {
  if (!props.results?.length) return null;
  return {
    labels: props.results.map(c => getCandidateName(c)),
    datasets: [{
      label: 'Total Votes',
      data: props.results.map(c => c.votes_count),
      backgroundColor: '#0048FF',
      borderColor: '#000000',
      borderWidth: 2,
    }],
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
      borderWidth: 2, padding: 10, cornerRadius: 0, displayColors: false,
      titleFont: { family: "'Space Grotesk', sans-serif", size: 14, weight: 'bold' },
      bodyFont: { family: "'Work Sans', sans-serif", size: 13 },
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { stepSize: 1, font: { family: "'Work Sans', sans-serif", weight: 'bold' }, color: isDark.value ? '#9CA3AF' : '#4B5563' },
      grid: { color: isDark.value ? '#374151' : '#E5E7EB' },
      border: { color: isDark.value ? '#FFFFFF' : '#000000', width: 2 },
    },
    x: {
      ticks: { font: { family: "'Space Grotesk', sans-serif", weight: 'bold', size: 10 }, color: isDark.value ? '#9CA3AF' : '#4B5563' },
      grid: { display: false },
      border: { color: isDark.value ? '#FFFFFF' : '#000000', width: 2 },
    },
  },
}));
</script>
