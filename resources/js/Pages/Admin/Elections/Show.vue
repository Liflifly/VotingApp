<template>
  <AuthenticatedLayout title="ELECTION DETAIL">
    <div class="max-w-4xl">
      <!-- Breadcrumb Navigation -->
      <div class="mb-5 flex">
        <Link 
          :href="route('events.admin.elections.index', event.slug)" 
          class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-neo-dark-card border-2 border-neo-black dark:border-white font-heading text-[10px] md:text-xs font-black uppercase tracking-wider text-neo-black dark:text-white shadow-[3px_3px_0px_#000] dark:shadow-[3px_3px_0px_rgba(255,255,255,0.8)] hover:bg-neo-yellow dark:hover:bg-neo-yellow hover:text-neo-black dark:hover:text-neo-black hover:translate-x-[1.5px] hover:translate-y-[1.5px] hover:shadow-[1.5px_1.5px_0px_#000] dark:hover:shadow-[1.5px_1.5px_0px_rgba(255,255,255,0.8)] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none transition-all duration-100 group"
        >
          <span class="material-symbols-outlined text-base font-bold group-hover:-translate-x-1 transition-transform text-neo-blue">arrow_back</span>
          BACK TO ELECTIONS
        </Link>
      </div>

      <!-- Header -->
      <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 shadow-neo dark:shadow-neo-white relative overflow-hidden p-6 border-3 border-neo-black dark:border-white">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex items-center gap-4 w-full">
          <!-- Icon -->
          <div class="hidden sm:flex w-12 h-12 bg-neo-blue border-2 border-neo-black dark:border-white shadow-[3px_3px_0px_#000] dark:shadow-[3px_3px_0px_rgba(255,255,255,0.8)] items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-white text-2xl">event_note</span>
          </div>
          
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3">
              <h1 class="font-heading font-black text-xl md:text-2xl uppercase break-all sm:break-words whitespace-normal leading-tight dark:text-white">{{ election.name }}</h1>
              <span :class="statusBadge(election.status)" class="shrink-0">{{ election.status?.toUpperCase() }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-blue">{{ election.candidates?.length || 0 }}</div>
          <div class="neo-stat-label">CANDIDATES</div>
        </div>
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-black">{{ totalVoters }}</div>
          <div class="neo-stat-label">TOTAL VOTERS</div>
        </div>
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-red">{{ totalVotesCast }}</div>
          <div class="neo-stat-label">VOTES CAST</div>
        </div>
      </div>

      <!-- Election Notes (If any) -->
      <div v-if="election.notes" class="neo-card bg-neo-yellow/10 border-2 border-neo-black p-4 md:p-5 mb-6 md:mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow/20 border-l border-b border-neo-black"></div>
        <div class="flex items-start gap-3 relative z-10">
          <span class="material-symbols-outlined text-neo-yellow text-2xl font-bold bg-neo-black p-1.5 shrink-0 shadow-[2px_2px_0px_#000]">event_note</span>
          <div>
            <h4 class="font-heading font-black text-xs md:text-sm uppercase tracking-wider text-neo-black mb-1">ELECTION NOTES</h4>
            <p class="font-body text-xs md:text-sm text-neo-black leading-relaxed whitespace-pre-line">{{ election.notes }}</p>
          </div>
        </div>
      </div>

      <!-- Candidates List -->
      <div v-if="election.candidates?.length">
        <h3 class="font-heading font-black text-lg md:text-h2 uppercase mb-3 md:mb-4 flex items-center gap-2 dark:text-white">
          <span class="material-symbols-outlined text-neo-grey">groups</span>
          CANDIDATE LIST
        </h3>
        <div class="space-y-2 md:space-y-3">
          <div v-for="c in election.candidates" :key="c.id" class="neo-card p-3 md:p-4 flex items-center gap-3 md:gap-4 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-6 h-6 bg-neo-yellow/10 border-l border-b border-neo-yellow/20"></div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-100 dark:bg-neo-dark-surface border-2 border-neo-black dark:border-white shrink-0 overflow-hidden">
              <img v-if="c.fields?.photo" :src="c.fields.photo" class="w-full h-full object-cover">
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined text-xl text-gray-300">person</span>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-heading font-bold text-xs md:text-sm uppercase truncate dark:text-white">{{ c.fields?.name || 'Unnamed Candidate' }}</div>
            </div>
            <div class="font-heading font-black text-base md:text-lg shrink-0 dark:text-white">{{ c.votes?.length || 0 }} <span class="text-[10px] md:text-xs text-neo-grey">votes</span></div>
          </div>
        </div>
      </div>
      <div v-else class="neo-card p-8 text-center">
        <p class="font-body text-sm text-neo-grey">No candidates for this election yet.</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({ election: Object, event: Object, totalVoters: Number });
const totalVotesCast = computed(() => props.election.candidates?.reduce((sum, c) => sum + (c.votes?.length || 0), 0) || 0);
const statusBadge = (s) => s === 'active' ? 'neo-badge bg-neo-blue text-white' : s === 'ended' ? 'neo-badge bg-gray-200' : 'neo-badge bg-neo-yellow';
</script>
