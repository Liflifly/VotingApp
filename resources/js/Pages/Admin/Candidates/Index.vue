<template>
  <AuthenticatedLayout title="MANAGE CANDIDATES">
    <!-- Breadcrumb Navigation -->
    <div class="mb-5 flex">
      <Link 
        :href="route('events.admin.elections.index', event.slug)" 
        class="neo-btn-sm-secondary group"
      >
        <span class="material-symbols-outlined text-base font-bold group-hover:-translate-x-1 transition-transform text-neo-blue">arrow_back</span>
        BACK TO ELECTIONS
      </Link>
    </div>

    <!-- Header -->
    <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 md:mb-8 shadow-neo dark:shadow-neo-white relative overflow-hidden p-6 md:p-8 border-3 border-neo-black dark:border-white">
      <!-- Decorative Background Boxes -->
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>

      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <!-- Title & Period Badge -->
        <div class="flex items-center gap-4">
          <!-- Icon Badge -->
          <div class="hidden sm:flex w-12 h-12 bg-neo-blue border-2 border-neo-black dark:border-white shadow-[3px_3px_0px_#000] dark:shadow-[3px_3px_0px_rgba(255,255,255,0.8)] items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-white text-2xl">groups</span>
          </div>
          
          <div class="min-w-0">
            <h1 class="font-heading font-black text-xl md:text-2xl uppercase tracking-tight dark:text-white leading-none">
              MANAGE CANDIDATES
            </h1>
            
            <div class="mt-2.5 flex flex-wrap items-center gap-2">
              <span class="font-heading text-[10px] font-bold uppercase tracking-wider text-neo-grey dark:text-gray-400">
                ELECTION:
              </span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-neo-yellow border-2 border-neo-black font-heading text-[10px] md:text-xs font-black uppercase tracking-wide shadow-[2px_2px_0px_#000] max-w-full break-words text-neo-black">
                <span class="material-symbols-outlined text-sm">calendar_month</span>
                {{ election.name }}
              </span>
            </div>
          </div>
        </div>

        <!-- Add Candidate Action -->
        <div class="shrink-0 w-full md:w-auto">
          <Link 
            :href="route('events.admin.candidates.create', { event: event.slug, election: election.id })" 
            class="neo-btn-sm-primary w-full md:w-auto"
          >
            <span class="material-symbols-outlined text-base">person_add</span>
            ADD CANDIDATE
          </Link>
        </div>
      </div>
    </div>

    <div v-if="!candidates.length" class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
      <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
      <h3 class="font-heading font-black text-h2 uppercase mb-2 dark:text-white">NO CANDIDATES YET</h3>
      <p class="font-body text-sm md:text-body-md text-neo-grey">Add candidates for this election period.</p>
    </div>

    <!-- Candidate Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
      <div v-for="candidate in candidates" :key="candidate.id" class="neo-card overflow-hidden group hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all relative flex flex-col bg-white dark:bg-neo-dark-card">
        <div class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow/10 border-l border-b border-neo-yellow/20 z-20 pointer-events-none"></div>
        
        <!-- Photo -->
        <div class="h-36 sm:h-40 w-full bg-gray-100 dark:bg-neo-dark-surface border-b-neo border-neo-black dark:border-white relative overflow-hidden shrink-0">
          <img v-if="candidate.fields?.photo" :src="candidate.fields.photo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          <div v-else class="w-full h-full flex items-center justify-center">
            <span class="material-symbols-outlined text-4xl text-gray-300">person</span>
          </div>
          <!-- Number Badge -->
          <div class="absolute top-2.5 left-2.5 bg-neo-yellow border-2 border-neo-black px-2 py-0.5">
            <span class="font-heading text-[10px] md:text-xs font-black text-neo-black">#{{ candidate.order_number }}</span>
          </div>
        </div>

        <!-- Info -->
        <div class="p-3 md:p-4 flex flex-col flex-1">
          <h3 class="font-heading font-black text-sm md:text-base uppercase mb-1 truncate dark:text-white">{{ candidate.fields?.name || 'Unnamed Candidate' }}</h3>
          <p class="font-body text-[10px] md:text-xs text-neo-grey dark:text-gray-400 mb-3 flex-1 line-clamp-2">{{ candidate.fields?.vision || '—' }}</p>
          
          <div class="flex gap-1.5 md:gap-2">
            <Link :href="route('events.admin.candidates.edit', { event: event.slug, election: election.id, candidate: candidate.id })" class="neo-btn-sm-secondary flex-1">EDIT</Link>
            <button @click="confirmDelete(candidate)" class="neo-btn-sm-danger flex-1">DELETE</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ election: Object, event: Object, candidates: Array });

const showConfirm = ref(false);
const pendingDelete = ref(null);

const confirmDelete = (candidate) => {
  if (confirm(`Delete candidate "${candidate.fields?.name || 'this candidate'}"? This cannot be undone.`)) {
    router.delete(route('events.admin.candidates.destroy', {
      event: props.event.slug,
      election: props.election.id,
      candidate: candidate.id
    }));
  }
};
</script>
