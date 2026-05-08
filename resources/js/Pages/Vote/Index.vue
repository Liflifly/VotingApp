<template>
  <AuthenticatedLayout title="LIVE BALLOTS">

    <div v-if="!activeElection" class="max-w-2xl mx-auto mt-8 md:mt-12">
      <div class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 border-neo border-neo-black mx-auto mb-4 md:mb-6 flex items-center justify-center">
          <span class="material-symbols-outlined text-3xl md:text-4xl text-neo-grey">ballot</span>
        </div>
        <h2 class="font-heading font-black text-h2 uppercase mb-2">VOTING NOT OPEN</h2>
        <p class="font-body text-sm md:text-body-md text-neo-grey">Voting has not started yet. Please wait for an administrator to open the election period.</p>
      </div>
    </div>

    <div v-else>
      <!-- Header Section (Fully aligned size and styles with other pages) -->
      <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
        <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">how_to_vote</span>
            <div>
              <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 flex items-center gap-2 md:gap-3">
                CAST YOUR VOTE
              </h1>
              <p class="font-body text-xs md:text-sm text-neo-grey">Click "READ DETAILS" on each candidate card, then cast your vote after reviewing all candidates.</p>
            </div>
          </div>
          <div v-if="user?.has_voted" class="neo-badge bg-gray-200 text-neo-black shrink-0">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            ALREADY VOTED
          </div>
          <div v-else class="neo-badge-live neo-pulse shrink-0">
            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
            VOTING ACTIVE
          </div>
        </div>
      </div>

      <!-- Election Notes (If any) -->
      <div v-if="activeElection.notes" class="neo-card bg-neo-yellow/10 border-2 border-neo-black p-4 md:p-5 mb-6 md:mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow/20 border-l border-b border-neo-black"></div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10 w-full">
          <div class="flex items-start gap-3 flex-1">
            <span class="material-symbols-outlined text-neo-yellow text-2xl font-bold bg-neo-black p-1.5 shrink-0 shadow-[2px_2px_0px_#000]">event_note</span>
            <div>
              <h4 class="font-heading font-black text-xs md:text-sm uppercase tracking-wider text-neo-black mb-1">ELECTION NOTES</h4>
              <p class="font-body text-xs md:text-sm text-neo-black leading-relaxed whitespace-pre-line">{{ activeElection.notes }}</p>
            </div>
          </div>
          
          <!-- Scroll Button -->
          <button 
            @click="scrollToVoteForm" 
            class="neo-btn bg-white hover:bg-neo-blue hover:text-white text-neo-black py-2 px-3 shadow-[2px_2px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all duration-100 shrink-0 self-stretch sm:self-auto flex items-center justify-center gap-2 text-xs font-heading font-black focus:outline-none"
          >
            VOTE NOW
            <span class="material-symbols-outlined text-base animate-bounce">arrow_downward</span>
          </button>
        </div>
      </div>

      <!-- Candidate Grid -->
      <div id="candidates-section" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5 mb-6 md:mb-8">
        <Link v-for="candidate in candidates" :key="candidate.id" :href="route('events.candidates.show', { event: event.slug, candidate: candidate.id })" class="block">
          <div :class="[
            'neo-card overflow-hidden transition-all duration-150 h-full flex flex-col relative group',
            hasRead(candidate.id) ? 'bg-gray-50/60' : 'bg-white',
            'hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px]'
          ]">
            <!-- Photo (Height restricted for a tighter, premium feel) -->
            <div class="h-36 sm:h-40 w-full bg-gray-100 border-b-neo border-neo-black relative overflow-hidden shrink-0">
              <img v-if="candidate.photo" :src="`/storage/${candidate.photo}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-gray-300">person</span>
              </div>
              <div class="absolute top-2.5 left-2.5 bg-neo-yellow border-2 border-neo-black px-2 py-0.5">
                <span class="font-heading text-[10px] md:text-xs font-black">#{{ candidate.order_number }}</span>
              </div>
              
              <div v-if="hasRead(candidate.id)" class="absolute top-2.5 right-2.5 bg-green-500 border-2 border-neo-black p-0.5 flex items-center justify-center rounded-none">
                <span class="material-symbols-outlined text-white text-base">check_circle</span>
              </div>
            </div>

            <!-- Info -->
            <div class="p-3 md:p-4 flex flex-col flex-1">
              <h3 class="font-heading font-black text-sm md:text-base uppercase mb-1 group-hover:text-neo-blue transition-colors truncate">{{ candidate.fields?.name || 'Candidate' }}</h3>
              <p class="font-body text-[11px] md:text-xs text-neo-grey mb-4 line-clamp-2 flex-1">{{ candidate.fields?.vision || '' }}</p>

              <div class="w-full mt-auto"
                :class="hasRead(candidate.id) ? 'neo-btn-sm-secondary' : 'neo-btn-sm-primary'">
                {{ hasRead(candidate.id) ? 'READ AGAIN' : 'READ DETAILS & VOTE →' }}
              </div>
            </div>
          </div>
        </Link>
      </div>

      <!-- Action Footer -->
      <div id="vote-form-section" v-if="!user?.has_voted" class="mt-10 mb-4 pt-6 border-t-2 border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center gap-4 w-full">
        <div class="text-center flex flex-col items-center gap-3">
          <p class="font-heading font-bold text-xs uppercase text-neo-grey">You must read every candidate's profile before casting your vote.</p>
          
          <div class="flex flex-wrap items-center justify-center gap-4">
            <div class="flex items-center gap-2">
              <span :class="['w-3 h-3 border-2 border-neo-black', allRead ? 'bg-green-500 animate-pulse' : 'bg-neo-red']"></span>
              <span class="font-heading text-sm font-black uppercase">{{ readCandidates.length }} / {{ candidates.length }} CANDIDATES READ</span>
            </div>
            
            <!-- Bypass Button -->
            <button 
              v-if="!allRead"
              @click="markAllAsRead"
              class="neo-btn-sm bg-neo-yellow hover:bg-neo-blue hover:text-white text-neo-black"
            >
              <span class="material-symbols-outlined text-sm font-bold">done_all</span>
              I'VE ALREADY READ ALL CANDIDATE PROFILES
            </button>
          </div>
        </div>

        <!-- Form Pemilihan (Set to w-full to align side borders exactly with candidates grid) -->
        <form v-if="allRead" @submit.prevent="submitVote" class="w-full bg-white border-2 border-neo-black p-5 sm:p-6 shadow-neo flex flex-col gap-4">
          <div>
            <label class="block font-heading text-xs font-black uppercase text-neo-black mb-3">SELECT YOUR CANDIDATE:</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
              <label v-for="candidate in candidates" :key="candidate.id" 
                :class="[
                  'flex items-center gap-3 p-2.5 border-2 border-neo-black dark:border-white cursor-pointer transition-all shadow-neo-sm dark:shadow-[2px_2px_0px_#fff]',
                  form.candidate_id === candidate.id ? 'bg-neo-blue text-white translate-x-[2px] translate-y-[2px] shadow-none' : 'bg-white dark:bg-neo-dark-card hover:bg-gray-50 dark:hover:bg-neo-dark-surface dark:text-white'
                ]"
              >
                <input type="radio" v-model="form.candidate_id" :value="candidate.id" class="sr-only">
                <span class="font-heading font-black text-sm" :class="form.candidate_id === candidate.id ? 'text-neo-yellow' : 'text-neo-black dark:text-white'">#{{ candidate.order_number }}</span>
                <span class="font-heading font-bold text-sm uppercase flex-1 truncate">{{ candidate.fields?.name || 'Candidate' }}</span>
                <span class="material-symbols-outlined text-lg" v-if="form.candidate_id === candidate.id">check_circle</span>
              </label>
            </div>
          </div>

          <button type="submit" :disabled="!form.candidate_id || form.processing" 
            class="neo-btn bg-neo-yellow text-neo-black w-full py-3 text-sm md:text-base shadow-neo disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-x-0 disabled:translate-y-0"
          >
            <span v-if="form.processing" class="material-symbols-outlined text-xl animate-spin">autorenew</span>
            <span v-else class="material-symbols-outlined text-xl">how_to_vote</span>
            {{ form.processing ? 'SUBMITTING VOTE...' : 'SUBMIT VOTE \u2192' }}
          </button>
        </form>
        
        <!-- Locked Form Overlay Container -->
        <div v-else class="w-full bg-gray-50 border-2 border-dashed border-gray-300 p-6 text-center">
          <p class="font-heading font-bold text-xs uppercase text-neo-grey">
            Voting form is locked. Please read all candidate profiles above to unlock it.
          </p>
        </div>

        <!-- Scroll to Top Button -->
        <!-- Scroll to Top Button -->
        <button @click="scrollToCandidates" class="neo-btn-sm-secondary mt-2">
          <span class="material-symbols-outlined text-sm sm:text-base">arrow_upward</span>
          CANDIDATE PROFILES
        </button>
      </div>
    </div>

    <!-- ═══ NEO VOTE CONFIRMATION MODAL ═══ -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-4"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-4"
      >
        <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-neo-black/60 backdrop-blur-[2px]" @click="showConfirmModal = false" />

          <!-- Modal Card -->
          <div class="relative w-full max-w-sm border-[3px] border-neo-black dark:border-white bg-white dark:bg-neo-dark-card shadow-[8px_8px_0px_#000] dark:shadow-[8px_8px_0px_rgba(255,255,255,0.8)] overflow-hidden">
            <div class="h-2 bg-neo-blue w-full" />
            <div class="absolute top-2 right-0 w-12 h-12 bg-neo-black dark:bg-white border-l-[3px] border-b-[3px] border-neo-black dark:border-white" />

            <!-- Body -->
            <div class="p-8 text-center">
              <!-- Icon box -->
              <div class="mx-auto mb-5 w-20 h-20 bg-neo-yellow border-[3px] border-neo-black dark:border-white shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] flex items-center justify-center">
                <span class="material-symbols-outlined text-neo-black text-4xl" style="font-variation-settings: 'FILL' 1;">how_to_vote</span>
              </div>

              <!-- Label chip -->
              <div class="inline-block mb-3 px-3 py-0.5 bg-neo-black dark:bg-white text-white dark:text-neo-black font-heading font-black text-[10px] uppercase tracking-[0.25em]">
                CONFIRM YOUR CHOICE
              </div>

              <!-- Title -->
              <h3 class="font-heading font-black text-xl uppercase leading-tight mb-2 dark:text-white">
                {{ selectedCandidateName }}
              </h3>


              <!-- Divider -->
              <div class="mx-auto mb-4 w-12 h-[3px] bg-neo-blue" />

              <!-- Body text -->
              <p class="font-body text-sm text-neo-grey dark:text-gray-400 mb-7 leading-relaxed font-bold">
                This choice cannot be changed after submission!
              </p>

              <!-- CTA Buttons -->
              <div class="flex gap-3">
                <button
                  @click="showConfirmModal = false"
                  class="flex-1 py-3 px-2 bg-white dark:bg-neo-dark-surface text-neo-black dark:text-white border-[3px] border-neo-black dark:border-white font-heading font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#000] dark:hover:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-100 flex items-center justify-center gap-1"
                >
                  CANCEL
                </button>
                <button
                  @click="executeVote"
                  class="flex-1 py-3 px-2 bg-neo-blue text-white border-[3px] border-neo-black dark:border-white font-heading font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#000] dark:hover:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-100 flex items-center justify-center gap-1"
                >
                  YES, SUBMIT!
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  user: Object,
  event: Object,
  activeElection: Object,
  candidates: Array,
});

const readCandidates = ref([]);
const showConfirmModal = ref(false);
const form = useForm({ candidate_id: null });

onMounted(() => {
  if (props.activeElection) {
    const stored = localStorage.getItem('read_candidates_' + props.activeElection.id);
    if (stored) {
      try {
        readCandidates.value = JSON.parse(stored);
      } catch(e) {}
    }
  }

  // Handle auto-scroll if requested
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('scroll') === 'vote-form') {
    setTimeout(() => {
      document.getElementById('vote-form-section')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 400);
  }
});

const hasRead = (id) => readCandidates.value.includes(id);
const allRead = computed(() => props.candidates?.every(c => hasRead(c.id)));

const selectedCandidateName = computed(() => {
  const chosen = props.candidates?.find(c => c.id === form.candidate_id);
  return chosen ? (chosen.fields?.name || 'Candidate') : 'Candidate';
});

const submitVote = () => {
  if (allRead.value && form.candidate_id) {
    showConfirmModal.value = true;
  }
};

const executeVote = () => {
  showConfirmModal.value = false;
  form.post(route('events.vote.store', props.event.slug));
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const scrollToCandidates = () => {
  document.getElementById('candidates-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

const scrollToVoteForm = () => {
  document.getElementById('vote-form-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

const markAllAsRead = () => {
  if (props.candidates && props.activeElection) {
    const ids = props.candidates.map(c => c.id);
    readCandidates.value = ids;
    localStorage.setItem('read_candidates_' + props.activeElection.id, JSON.stringify(ids));
  }
};
</script>
