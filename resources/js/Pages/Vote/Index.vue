<template>
  <AuthenticatedLayout title="LIVE BALLOTS">

    <div v-if="!activeElection" class="max-w-2xl mx-auto mt-8 md:mt-12">
      <div class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 border-neo border-neo-black mx-auto mb-4 md:mb-6 flex items-center justify-center">
          <span class="material-symbols-outlined text-3xl md:text-4xl text-neo-grey">ballot</span>
        </div>
        <h2 class="font-heading font-black text-h2 uppercase mb-2">ARENA BELUM DIBUKA</h2>
        <p class="font-body text-sm md:text-body-md text-neo-grey">Tunggu hingga admin membuka arena pemilihan.</p>
      </div>
    </div>

    <div v-else>
      <!-- Header Section -->
      <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
        <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div>
            <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 md:mb-2 flex items-center gap-2 md:gap-3">
              <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">how_to_vote</span>
              PILIH KANDIDATMU
            </h1>
            <p class="font-body text-xs md:text-body-md text-neo-grey">Klik pada kartu kandidat untuk memilih, lalu tekan tombol konfirmasi.</p>
          </div>
          <div v-if="user?.has_voted" class="neo-badge bg-gray-200 text-neo-black">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            SUDAH MEMILIH
          </div>
          <div v-else class="neo-badge-live neo-pulse">
            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
            VOTING AKTIF
          </div>
        </div>
      </div>

      <form @submit.prevent="submitVote">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
          <label v-for="candidate in candidates" :key="candidate.id" class="cursor-pointer block" :class="{ 'pointer-events-none': user?.has_voted }">
            <input type="radio" v-model="form.candidate_id" :value="candidate.id" class="peer sr-only" :disabled="user?.has_voted">

            <div :class="[
              'neo-card overflow-hidden transition-all duration-150 h-full flex flex-col relative',
              form.candidate_id === candidate.id
                ? 'border-neo-blue shadow-neo-blue -translate-x-[2px] -translate-y-[2px]'
                : 'hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px]'
            ]">
              <!-- Photo -->
              <div class="aspect-[4/3] bg-gray-100 border-b-neo border-neo-black relative overflow-hidden">
                <img v-if="candidate.photo" :src="`/storage/${candidate.photo}`" class="w-full h-full object-cover">
                <div v-else class="w-full h-full flex items-center justify-center">
                  <span class="material-symbols-outlined text-5xl md:text-6xl text-gray-300">person</span>
                </div>
                <div class="absolute top-3 left-3 bg-neo-yellow border-2 border-neo-black px-2.5 py-0.5 md:px-3 md:py-1">
                  <span class="font-heading text-xs md:text-sm font-black">#{{ candidate.order_number }}</span>
                </div>
                <!-- Selected Indicator -->
                <div v-if="form.candidate_id === candidate.id" class="absolute top-3 right-3 bg-neo-blue border-2 border-neo-black p-1">
                  <span class="material-symbols-outlined text-white text-lg">check</span>
                </div>
              </div>

              <!-- Info -->
              <div class="p-4 md:p-5 flex flex-col flex-1">
                <h3 class="font-heading font-black text-base md:text-lg uppercase mb-1 md:mb-2">{{ candidate.name }}</h3>
                <p class="font-body text-xs md:text-sm text-neo-grey mb-3 line-clamp-2 flex-1">{{ candidate.vision }}</p>

                <div class="font-heading text-[10px] md:text-xs font-bold uppercase tracking-wider text-center py-2 border-neo transition-colors"
                  :class="form.candidate_id === candidate.id ? 'bg-neo-blue text-white border-neo-blue' : 'bg-gray-50 text-neo-grey border-gray-200'">
                  {{ form.candidate_id === candidate.id ? '✓ TERPILIH' : 'PILIH KANDIDAT' }}
                </div>
              </div>
            </div>
          </label>
        </div>

        <!-- Submit Button -->
        <div v-if="!user?.has_voted" class="mt-10 mb-4 pt-6 border-t-2 border-dashed border-gray-200 dark:border-gray-700 flex justify-center">
          <!-- UI-04 FIX: Tambahkan loading indicator saat form.processing -->
          <button type="submit" :disabled="!form.candidate_id || form.processing"
            class="neo-btn bg-neo-yellow text-neo-black py-3 md:py-4 px-8 md:px-12 text-sm md:text-lg shadow-neo disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-x-0 disabled:translate-y-0">
            <span v-if="form.processing" class="material-symbols-outlined text-xl md:text-2xl animate-spin">autorenew</span>
            <span v-else class="material-symbols-outlined text-xl md:text-2xl">verified</span>
            {{ form.processing ? 'MENGIRIM SUARA...' : 'KIRIM SUARA \u2192' }}
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  user: Object,
  activeElection: Object,
  candidates: Array,
});

const form = useForm({ candidate_id: null });

const submitVote = () => {
  if (!form.candidate_id) return;
  const chosen = props.candidates.find(c => c.id === form.candidate_id);

  Swal.fire({
    title: 'KONFIRMASI PILIHANMU',
    html: `<p style="font-family:'Space Grotesk';font-weight:800;font-size:18px;text-transform:uppercase">${chosen?.name || 'Kandidat'}</p><p style="font-family:'Work Sans';margin-top:8px;color:#555">Pilihan ini tidak dapat diubah!</p>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#0048FF',
    cancelButtonColor: '#FF3C3C',
    confirmButtonText: 'YA, KIRIM!',
    cancelButtonText: 'BATAL',
    background: '#FFFFFF',
    customClass: { popup: 'border-[3px] border-black shadow-[4px_4px_0px_#000]', confirmButton: 'font-bold uppercase tracking-wider', cancelButton: 'font-bold uppercase tracking-wider' },
  }).then((result) => {
    if (result.isConfirmed) {
      form.post(route('vote.store'));
    }
  });
};
</script>
