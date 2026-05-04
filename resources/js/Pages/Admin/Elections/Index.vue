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
        <Link :href="route('admin.elections.create')" class="neo-btn-primary text-[10px] md:text-xs py-2 md:py-2.5 px-4 md:px-6">
          <span class="material-symbols-outlined text-base">add</span>
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
        <div class="absolute top-0 right-0 w-8 h-8 border-l border-b" :class="election.status === 'active' ? 'bg-neo-blue/10 border-neo-blue/20' : election.status === 'ended' ? 'bg-gray-100 border-gray-200' : 'bg-neo-yellow/10 border-neo-yellow/20'"></div>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 md:gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-1 md:mb-2">
              <h3 class="font-heading font-black text-base md:text-lg uppercase truncate">{{ election.name }}</h3>
              <span :class="statusBadgeClass(election.status)">{{ election.status?.toUpperCase() }}</span>
            </div>
            <div class="flex flex-wrap gap-3 md:gap-4 font-body text-[10px] md:text-xs text-neo-grey">
              <span><strong class="text-neo-black">Mulai:</strong> {{ formatDate(election.starts_at) }}</span>
              <span><strong class="text-neo-black">Selesai:</strong> {{ formatDate(election.ends_at) }}</span>
            </div>
          </div>
          <div class="flex flex-wrap gap-1.5 md:gap-2">
            <Link :href="route('admin.elections.show', election.id)" class="neo-btn-secondary text-[9px] md:text-[10px] py-1.5 md:py-2 px-3 md:px-4 shadow-neo-sm">DETAIL</Link>
            <Link v-if="election.status === 'draft'" :href="route('admin.elections.edit', election.id)" class="neo-btn-secondary text-[9px] md:text-[10px] py-1.5 md:py-2 px-3 md:px-4 shadow-neo-sm">EDIT</Link>
            <Link :href="route('admin.candidates.index', election.id)" class="neo-btn-secondary text-[9px] md:text-[10px] py-1.5 md:py-2 px-3 md:px-4 shadow-neo-sm">KANDIDAT</Link>
            <Link v-if="election.status === 'draft'" :href="route('admin.elections.activate', election.id)" method="post" as="button" class="neo-btn-primary text-[9px] md:text-[10px] py-1.5 md:py-2 px-3 md:px-4 shadow-neo-sm">AKTIFKAN</Link>
            <Link v-if="election.status === 'active'" :href="route('admin.elections.end', election.id)" method="post" as="button" class="neo-btn-danger text-[9px] md:text-[10px] py-1.5 md:py-2 px-3 md:px-4 shadow-neo-sm">AKHIRI</Link>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({ elections: Array });

const statusBadgeClass = (status) => {
  const base = 'neo-badge';
  if (status === 'active') return `${base} bg-neo-blue text-white`;
  if (status === 'ended') return `${base} bg-gray-200 text-neo-black`;
  return `${base} bg-neo-yellow text-neo-black`; // draft
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>
