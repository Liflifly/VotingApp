<template>
  <AuthenticatedLayout title="DETAIL PERIODE">
    <div class="max-w-4xl">
      <!-- Header -->
      <div class="neo-page-header bg-white shadow-neo mb-6">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-3">
          <Link :href="route('admin.elections.index')" class="neo-btn-secondary text-[10px] py-1.5 px-3 shadow-neo-sm">← KEMBALI</Link>
          <h1 class="font-heading font-black text-lg md:text-h1 uppercase truncate">{{ election.name }}</h1>
          <span :class="statusBadge(election.status)">{{ election.status?.toUpperCase() }}</span>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-blue">{{ election.candidates?.length || 0 }}</div>
          <div class="neo-stat-label">KANDIDAT</div>
        </div>
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-black">{{ totalVoters }}</div>
          <div class="neo-stat-label">TOTAL PEMILIH</div>
        </div>
        <div class="neo-stat-card">
          <div class="neo-stat-value text-neo-red">{{ totalVotesCast }}</div>
          <div class="neo-stat-label">SUARA MASUK</div>
        </div>
      </div>

      <!-- Candidates List -->
      <div v-if="election.candidates?.length">
        <h3 class="font-heading font-black text-lg md:text-h2 uppercase mb-3 md:mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-neo-grey">groups</span>
          DAFTAR KANDIDAT
        </h3>
        <div class="space-y-2 md:space-y-3">
          <div v-for="c in election.candidates" :key="c.id" class="neo-card p-3 md:p-4 flex items-center gap-3 md:gap-4 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-6 h-6 bg-neo-yellow/10 border-l border-b border-neo-yellow/20"></div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-100 border-2 border-neo-black shrink-0 overflow-hidden">
              <img v-if="c.photo" :src="`/storage/${c.photo}`" class="w-full h-full object-cover">
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined text-xl text-gray-300">person</span>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-heading font-bold text-xs md:text-sm uppercase truncate">{{ c.name }}</div>
            </div>
            <div class="font-heading font-black text-base md:text-lg shrink-0">{{ c.votes?.length || 0 }} <span class="text-[10px] md:text-xs text-neo-grey">votes</span></div>
          </div>
        </div>
      </div>
      <div v-else class="neo-card p-8 text-center">
        <p class="font-body text-sm text-neo-grey">Belum ada kandidat untuk periode ini.</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({ election: Object, totalVoters: Number });
const totalVotesCast = computed(() => props.election.candidates?.reduce((sum, c) => sum + (c.votes?.length || 0), 0) || 0);
const statusBadge = (s) => s === 'active' ? 'neo-badge bg-neo-blue text-white' : s === 'ended' ? 'neo-badge bg-gray-200' : 'neo-badge bg-neo-yellow';
</script>
