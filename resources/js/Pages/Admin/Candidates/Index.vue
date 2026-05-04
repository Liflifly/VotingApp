<template>
  <AuthenticatedLayout title="KELOLA KANDIDAT">
    <!-- Header -->
    <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>
      
      <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-1">
            <Link :href="route('admin.elections.index')" class="neo-btn-secondary text-[9px] md:text-[10px] py-1.5 px-3 shadow-neo-sm">← PERIODE</Link>
            <h1 class="font-heading font-black text-lg md:text-h1 uppercase">KELOLA KANDIDAT</h1>
          </div>
          <p class="font-body text-xs md:text-sm text-neo-grey">{{ election.name }}</p>
        </div>
        <Link :href="route('admin.candidates.create', election.id)" class="neo-btn-primary text-[10px] md:text-xs py-2 md:py-2.5 px-4 md:px-6">
          <span class="material-symbols-outlined text-base">person_add</span>
          TAMBAH KANDIDAT
        </Link>
      </div>
    </div>

    <div v-if="!candidates.length" class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
      <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
      <h3 class="font-heading font-black text-h2 uppercase mb-2">BELUM ADA KANDIDAT</h3>
      <p class="font-body text-sm md:text-body-md text-neo-grey">Tambahkan kandidat untuk periode ini.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      <div v-for="candidate in candidates" :key="candidate.id" class="neo-card overflow-hidden group hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all relative">
        <div class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow/10 border-l border-b border-neo-yellow/20 z-20"></div>
        
        <div class="aspect-square bg-gray-100 border-b-neo border-neo-black relative overflow-hidden">
          <img v-if="candidate.photo" :src="`/storage/${candidate.photo}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          <div v-else class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-5xl md:text-6xl text-gray-300">person</span></div>
          <div class="absolute top-3 left-3 bg-neo-yellow border-2 border-neo-black px-2.5 py-0.5 md:px-3 md:py-1"><span class="font-heading text-xs md:text-sm font-black">#{{ candidate.order_number }}</span></div>
        </div>
        <div class="p-4 md:p-5">
          <h3 class="font-heading font-black text-base md:text-lg uppercase mb-1">{{ candidate.name }}</h3>
          <p class="font-body text-[10px] md:text-xs text-neo-grey mb-3">{{ candidate.class }}</p>
          <div class="flex gap-1.5 md:gap-2">
            <Link :href="route('admin.candidates.edit', { election: election.id, candidate: candidate.id })" class="neo-btn-secondary text-[9px] md:text-[10px] py-1.5 md:py-2 px-3 md:px-4 shadow-neo-sm flex-1 justify-center">EDIT</Link>
            <button @click="confirmDelete(candidate)" class="neo-btn-danger text-[9px] md:text-[10px] py-1.5 md:py-2 px-3 md:px-4 shadow-neo-sm">HAPUS</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({ election: Object, candidates: Array });

const confirmDelete = (candidate) => {
  Swal.fire({
    title: 'HAPUS KANDIDAT?',
    html: `<strong>${candidate.name}</strong> akan dihapus permanen.`,
    icon: 'warning', showCancelButton: true,
    confirmButtonColor: '#FF3C3C', cancelButtonColor: '#000',
    confirmButtonText: 'YA, HAPUS!', cancelButtonText: 'BATAL',
  }).then((r) => { if (r.isConfirmed) router.delete(route('admin.candidates.destroy', { election: props.election.id, candidate: candidate.id })); });
};
</script>
