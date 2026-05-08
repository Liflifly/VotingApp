<template>
  <AuthenticatedLayout :title="'DETAIL: ' + candidate.name">

    <div class="neo-card bg-white p-6 md:p-8 mb-6 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-20 h-20 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30 pointer-events-none"></div>
      
      <div class="flex flex-col md:flex-row gap-4 md:gap-5">
        <!-- Photo -->
        <div class="w-full md:w-[180px] lg:w-[200px] shrink-0">
          <div class="aspect-[4/3] md:aspect-square bg-gray-100 border-neo border-neo-black relative overflow-hidden shadow-neo-sm">
            <img v-if="candidate.photo" :src="`/storage/${candidate.photo}`" class="w-full h-full object-cover">
            <div v-else class="w-full h-full flex items-center justify-center">
              <span class="material-symbols-outlined text-4xl text-gray-300">person</span>
            </div>
            <div class="absolute top-2.5 left-2.5 bg-neo-yellow border-2 border-neo-black px-2 py-0.5">
              <span class="font-heading text-xs font-black">#{{ candidate.order_number }}</span>
            </div>
          </div>
        </div>

        <!-- Info -->
        <div class="flex-1">
          <div class="flex flex-col sm:flex-row justify-between items-start gap-2.5 mb-3.5">
            <div class="flex-1 min-w-0">
              <h1 class="font-heading font-black text-xl md:text-2xl uppercase mb-1.5 dark:text-white leading-tight">{{ candidate.name }}</h1>
              
              <!-- Candidate Badges: Nomor Urut & Kelas -->
              <div class="flex flex-wrap gap-2">
                <div class="inline-flex items-center gap-1 px-2 py-1 bg-neo-yellow border-2 border-neo-black font-heading text-[10px] sm:text-xs font-black uppercase shadow-[1.5px_1.5px_0px_#000]">
                  <span class="material-symbols-outlined text-xs sm:text-sm font-bold">tag</span>
                  NOMOR URUT: #{{ candidate.order_number }}
                </div>
                <div class="inline-flex items-center gap-1 px-2 py-1 bg-neo-blue text-white border-2 border-neo-black font-heading text-[10px] sm:text-xs font-black uppercase shadow-[1.5px_1.5px_0px_#000]">
                  <span class="material-symbols-outlined text-xs sm:text-sm font-bold">school</span>
                  KELAS: {{ candidate.class }}
                </div>
              </div>
            </div>

            <!-- Scroll Down Button -->
            <button 
              @click="scrollToAction" 
              class="neo-btn-sm-secondary shrink-0 self-stretch sm:self-auto text-[10px] md:text-xs"
            >
              BACA SEKARANG
              <span class="material-symbols-outlined text-sm md:text-base animate-bounce">arrow_downward</span>
            </button>
          </div>

          <div class="space-y-4">
            <div>
              <h3 class="font-heading font-black text-xs md:text-sm uppercase text-neo-blue flex items-center gap-1.5 mb-1">
                <span class="material-symbols-outlined text-sm md:text-base">visibility</span> VISI
              </h3>
              <p class="font-body text-xs md:text-sm text-neo-black dark:text-white leading-relaxed p-2.5 md:p-3 bg-gray-50 dark:bg-neo-dark-bg border-2 border-gray-200 dark:border-neo-black">
                {{ candidate.vision }}
              </p>
            </div>
            
            <div>
              <h3 class="font-heading font-black text-xs md:text-sm uppercase text-neo-red flex items-center gap-1.5 mb-1">
                <span class="material-symbols-outlined text-sm md:text-base">flag</span> MISI
              </h3>
              <p class="font-body text-xs md:text-sm text-neo-black dark:text-white leading-relaxed p-2.5 md:p-3 bg-gray-50 dark:bg-neo-dark-bg border-2 border-gray-200 dark:border-neo-black whitespace-pre-line">
                {{ candidate.mission }}
              </p>
            </div>

            <div v-if="candidate.program">
              <h3 class="font-heading font-black text-xs md:text-sm uppercase text-neo-yellow flex items-center gap-1.5 mb-1">
                <span class="material-symbols-outlined text-sm md:text-base">task_alt</span> PROGRAM KERJA
              </h3>
              <p class="font-body text-xs md:text-sm text-neo-black dark:text-white leading-relaxed p-2.5 md:p-3 bg-gray-50 dark:bg-neo-dark-bg border-2 border-gray-200 dark:border-neo-black whitespace-pre-line">
                {{ candidate.program }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Section Wrapper -->
    <div id="action-section">
      <!-- Action Section -->
      <div v-if="!user?.has_voted" class="neo-card bg-gray-50 p-6 text-center">
        <div v-if="!hasRead" class="flex flex-col items-center gap-2 md:gap-2.5">
          <p class="font-heading font-bold text-[10px] md:text-xs uppercase text-neo-grey">Anda harus membaca detail kandidat ini sebagai syarat pemilihan.</p>
          <button @click="markAsRead" class="neo-btn-sm-primary">
            <span class="material-symbols-outlined text-sm md:text-base">check_circle</span>
            SAYA SUDAH MEMBACA VISI MISI INI
          </button>
        </div>
        
        <div v-else class="flex flex-col items-center gap-2.5 md:gap-3">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 border border-green-500 text-green-700 font-heading font-bold text-[10px] md:text-xs uppercase">
            <span class="material-symbols-outlined text-sm md:text-base">verified</span>
            Telah dibaca
          </div>

          <Link :href="route('vote.index')" class="neo-btn-sm-secondary bg-neo-yellow hover:bg-neo-blue hover:text-white text-neo-black">
            <span class="material-symbols-outlined text-sm md:text-base">arrow_back</span>
            KEMBALI KE LIVE BALLOTS
          </Link>
        </div>
      </div>
      
      <div v-else class="neo-card bg-gray-100 p-6 text-center border-gray-300">
         <span class="font-heading font-bold text-sm text-neo-grey uppercase">Anda sudah memberikan hak suara pada pemilihan ini.</span>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  user: Object,
  candidate: Object,
  activeElection: Object,
  totalCandidates: Number
});

const readCandidates = ref([]);

onMounted(() => {
  if (props.activeElection) {
    const stored = localStorage.getItem('read_candidates_' + props.activeElection.id);
    if (stored) {
      try {
        readCandidates.value = JSON.parse(stored);
      } catch(e) {}
    }
  }
});

const hasRead = computed(() => readCandidates.value.includes(props.candidate.id));

const markAsRead = () => {
  if (!hasRead.value && props.activeElection) {
    readCandidates.value.push(props.candidate.id);
    localStorage.setItem('read_candidates_' + props.activeElection.id, JSON.stringify(readCandidates.value));
    
    // Auto redirect back to index so they can read the next one,
    // unless this was the last one they needed to read.
    if (readCandidates.value.length < props.totalCandidates) {
       router.get(route('vote.index'));
    }
  }
};

const scrollToAction = () => {
  document.getElementById('action-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};
</script>
