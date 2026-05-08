<template>
  <AuthenticatedLayout title="ELECTION HISTORY">
    <!-- Header -->
    <div class="neo-page-header bg-white dark:bg-neo-dark-card shadow-neo dark:shadow-neo-white mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="relative z-10">
        <h1 class="font-heading font-black text-lg md:text-h1 uppercase flex items-center gap-2 md:gap-3 dark:text-white">
          <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">history</span>
          ELECTION HISTORY
        </h1>
      </div>
    </div>

    <div v-if="!elections.length" class="neo-card p-8 md:p-12 text-center relative overflow-hidden">
      <div class="absolute top-0 right-0 w-12 h-12 bg-neo-yellow/20 border-l-2 border-b-2 border-neo-yellow/30"></div>
      <p class="font-body text-sm md:text-body-md text-neo-grey">No ended elections yet.</p>
    </div>
    <div v-else class="space-y-3 md:space-y-4">
      <div v-for="e in elections" :key="e.id" class="neo-card p-4 md:p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-8 h-8 bg-gray-100 border-l border-b border-gray-200"></div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
          <div>
            <h3 class="font-heading font-bold text-xs md:text-sm uppercase dark:text-white">{{ e.name }}</h3>
            <div class="font-body text-[10px] md:text-xs text-neo-grey mt-1">{{ formatDate(e.starts_at) }} — {{ formatDate(e.ends_at) }}</div>
          </div>
          <span class="neo-badge bg-gray-200 text-neo-black text-[9px] md:text-xs">ENDED</span>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineProps({ elections: Array, event: Object });
const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
</script>
