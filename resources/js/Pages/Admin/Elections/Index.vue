<template>
  <AuthenticatedLayout title="KELOLA PERIODE">
    <!-- Header -->
    <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>
      
      <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 class="font-heading font-black text-lg md:text-h1 uppercase flex items-center gap-2 md:gap-3">
            <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">event</span>
            KELOLA PERIODE
          </h1>
          <p class="font-body text-xs md:text-sm text-neo-grey mt-1">Manajemen siklus pemilihan Kosgoro</p>
        </div>
        <Link :href="route('admin.elections.create')" class="neo-btn-sm-primary">
          <span class="material-symbols-outlined text-sm font-bold">add</span>
          BUAT PERIODE BARU
        </Link>
      </div>
    </div>

    <div v-if="elections.length === 0" class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
      <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
      <h3 class="font-heading font-black text-h2 uppercase mb-2">BELUM ADA PERIODE</h3>
      <p class="font-body text-sm md:text-body-md text-neo-grey">Buat periode pemilihan pertama untuk memulai.</p>
    </div>

    <div v-else class="space-y-3 md:space-y-4">
      <div v-for="election in elections" :key="election.id" class="neo-card p-4 md:p-5 hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all relative overflow-hidden">
        <div class="absolute top-0 right-0 w-8 h-8 border-l border-b" :class="getEffectiveStatus(election) === 'active' ? 'bg-neo-blue/10 border-neo-blue/20' : (getEffectiveStatus(election) === 'expired' || getEffectiveStatus(election) === 'ended') ? 'bg-gray-100 border-gray-200' : 'bg-neo-yellow/10 border-neo-yellow/20'"></div>
        
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-3 md:gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-start md:items-center gap-2 md:gap-3 mb-1 md:mb-2">
              <h3 class="font-heading font-black text-base md:text-lg uppercase break-words whitespace-normal leading-tight flex-1 min-w-0">{{ election.name }}</h3>
              <span :class="statusBadgeClass(getEffectiveStatus(election))" class="shrink-0 mt-0.5 md:mt-0">{{ getEffectiveStatus(election) === 'expired' ? 'KADALUARSA' : election.status?.toUpperCase() }}</span>
            </div>
            <div class="flex flex-wrap gap-3 md:gap-4 font-body text-[10px] md:text-xs text-neo-grey">
              <span><strong class="text-neo-black">Mulai:</strong> {{ formatDate(election.starts_at) }}</span>
              <span><strong class="text-neo-black">Selesai:</strong> {{ formatDate(election.ends_at) }}</span>
            </div>
            
            <!-- Notes Preview -->
            <div v-if="election.notes" class="mt-2.5 p-2 bg-gray-50 dark:bg-neo-dark-surface border-l-4 border-neo-yellow font-body text-[10px] md:text-[11px] text-neo-black dark:text-white max-h-[85px] overflow-y-auto max-w-2xl whitespace-pre-line scrollbar-thin scrollbar-thumb-neo-black scrollbar-track-transparent">
              <strong class="font-heading text-[9px] uppercase text-neo-grey dark:text-gray-400 block mb-0.5">Catatan:</strong>
              {{ election.notes }}
            </div>
          </div>
          <div class="flex flex-wrap gap-1.5 md:gap-2 md:shrink-0">
            <Link :href="route('admin.elections.show', election.id)" class="neo-btn-sm-secondary">DETAIL</Link>
            <Link v-if="election.status === 'draft' || election.status === 'active'" :href="route('admin.elections.edit', election.id)" class="neo-btn-sm-secondary">EDIT</Link>
            <Link :href="route('admin.candidates.index', election.id)" class="neo-btn-sm-secondary">KANDIDAT</Link>
            <button 
              v-if="election.status === 'draft'" 
              @click="handleActivateClick(election)" 
              class="neo-btn-sm-primary"
            >
              AKTIFKAN
            </button>
            <button v-if="election.status === 'draft'" @click="promptAction(route('admin.elections.destroy', election.id), 'Hapus Draft?', 'Periode ini beserta seluruh kandidat di dalamnya akan dihapus permanen.', 'Ya, Hapus!', 'bg-neo-red', 'delete')" class="neo-btn-sm-danger">HAPUS</button>
            <button v-if="election.status === 'active'" @click="promptAction(route('admin.elections.end', election.id), 'Akhiri Periode?', 'Tindakan ini tidak dapat dibatalkan.', 'Ya, Akhiri!', 'bg-neo-red')" class="neo-btn-sm-danger">AKHIRI</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ NEO ACTION ALERT MODAL ═══ -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-4"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-4"
      >
        <div v-if="showActionAlert" class="fixed inset-0 z-50 flex items-center justify-center p-4">

          <!-- Backdrop -->
          <div
            class="absolute inset-0 bg-neo-black/60 backdrop-blur-[2px]"
            @click="showActionAlert = false"
          />

          <!-- Modal Card -->
          <div class="relative w-full max-w-sm border-[3px] border-neo-black dark:border-white bg-white dark:bg-neo-dark-card shadow-[8px_8px_0px_#000] dark:shadow-[8px_8px_0px_rgba(255,255,255,0.8)] overflow-hidden">

            <!-- Top accent bar -->
            <div :class="['h-2 w-full', actionConfig.colorClass]" />

            <!-- Corner decoration -->
            <div class="absolute top-2 right-0 w-12 h-12 bg-neo-black dark:bg-white border-l-[3px] border-b-[3px] border-neo-black dark:border-white" />

            <!-- Body -->
            <div class="p-8 text-center">

              <!-- Icon box -->
              <div :class="['mx-auto mb-5 w-20 h-20 border-[3px] border-neo-black dark:border-white shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] flex items-center justify-center', actionConfig.colorClass]">
                <span class="material-symbols-outlined text-white text-4xl" style="font-variation-settings: 'FILL' 1;">
                  {{ actionConfig.infoOnly ? 'warning' : (actionConfig.colorClass === 'bg-neo-red' ? 'warning' : 'info') }}
                </span>
              </div>

              <!-- Label chip -->
              <div class="inline-block mb-3 px-3 py-0.5 bg-neo-black dark:bg-white text-white dark:text-neo-black font-heading font-black text-[10px] uppercase tracking-[0.25em]">
                {{ actionConfig.infoOnly ? 'PERINGATAN SISTEM' : 'KONFIRMASI TINDAKAN' }}
              </div>

              <!-- Title -->
              <h3 class="font-heading font-black text-xl uppercase leading-tight mb-2 dark:text-white">
                {{ actionConfig.title }}
              </h3>

              <!-- Divider -->
              <div :class="['mx-auto mb-4 w-12 h-[3px]', actionConfig.colorClass]" />

              <!-- Body text -->
              <p class="font-body text-sm text-neo-grey dark:text-gray-400 mb-7 leading-relaxed">
                {{ actionConfig.text }}
              </p>

              <!-- CTA Buttons -->
              <div class="flex gap-3">
                <template v-if="actionConfig.infoOnly">
                  <button
                    @click="showActionAlert = false"
                    class="flex-1 py-3 px-2 bg-neo-yellow text-neo-black border-[3px] border-neo-black font-heading font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-[4px_4px_0px_#000] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#000] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-100 flex items-center justify-center gap-1"
                  >
                    OK, MENGERTI
                  </button>
                </template>
                <template v-else>
                  <button
                    @click="showActionAlert = false"
                    class="flex-1 py-3 px-2 bg-white dark:bg-neo-dark-surface text-neo-black dark:text-white border-[3px] border-neo-black dark:border-white font-heading font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#000] dark:hover:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-100 flex items-center justify-center gap-1"
                  >
                    BATAL
                  </button>
                  <button
                    @click="confirmActionExecute"
                    :class="['flex-1 py-3 px-2 text-white border-[3px] border-neo-black dark:border-white font-heading font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#000] dark:hover:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-100 flex items-center justify-center gap-1', actionConfig.colorClass]"
                  >
                    {{ actionConfig.confirmText }}
                  </button>
                </template>
              </div>
            </div>

            <!-- Bottom branding bar -->
            <div class="px-5 py-2 bg-gray-50 dark:bg-neo-dark-surface border-t-[3px] border-neo-black dark:border-white flex items-center justify-center gap-2">
              <span class="material-symbols-outlined text-neo-grey dark:text-gray-500 text-sm">event</span>
              <span class="font-heading font-black text-[10px] uppercase tracking-[0.2em] text-neo-grey dark:text-gray-500">KOSGORO™ SYSTEM</span>
            </div>
          </div>

        </div>
      </Transition>
    </Teleport>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({ elections: Array });

const getEffectiveStatus = (election) => {
  if (election.status === 'active' && new Date(election.ends_at) < new Date()) {
    return 'expired';
  }
  return election.status;
};

const showActionAlert = ref(false);
const actionConfig = ref({
  url: '',
  method: 'post',
  title: '',
  text: '',
  confirmText: '',
  colorClass: 'bg-neo-blue',
  infoOnly: false
});

const promptAction = (url, title, text, confirmText, colorClass, method = 'post', infoOnly = false) => {
  actionConfig.value = { url, title, text, confirmText, colorClass, method, infoOnly };
  showActionAlert.value = true;
};

const handleActivateClick = (election) => {
  const now = new Date();
  const startsAt = new Date(election.starts_at);
  if (startsAt > now) {
    promptAction(
      '', 
      'Belum Waktunya Mulai!', 
      `Periode pemilihan ini baru dijadwalkan mulai pada ${formatDate(election.starts_at)}. Harap tunggu hingga waktu tersebut tiba untuk mengaktifkannya.`, 
      '', 
      'bg-neo-yellow', 
      'post', 
      true
    );
  } else {
    promptAction(
      route('admin.elections.activate', election.id), 
      'Aktifkan Periode?', 
      'Periode ini akan menjadi satu-satunya yang aktif dan pemilih dapat mulai memberikan suara.', 
      'Ya, Aktifkan!', 
      'bg-neo-blue',
      'post',
      false
    );
  }
};

const confirmActionExecute = () => {
  showActionAlert.value = false;
  if (actionConfig.value.method === 'delete') {
    router.delete(actionConfig.value.url, { preserveScroll: true });
  } else {
    router.post(actionConfig.value.url, {}, { preserveScroll: true });
  }
};

const statusBadgeClass = (status) => {
  const base = 'neo-badge';
  if (status === 'active') return `${base} bg-neo-blue text-white`;
  if (status === 'expired' || status === 'ended') return `${base} bg-gray-200 text-neo-black`;
  return `${base} bg-neo-yellow text-neo-black`; // draft
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>
