<template>
  <AuthenticatedLayout title="EDIT ELECTION">
    <div class="max-w-2xl">
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
        <div class="relative z-10 flex items-center gap-3">
          <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">edit</span>
          <h1 class="font-heading font-black text-xl uppercase dark:text-white">
            EDIT ELECTION
          </h1>
        </div>
      </div>

      <form @submit.prevent="submit" class="neo-card p-5 md:p-6 space-y-4 md:space-y-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>
        
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">ELECTION NAME</label>
          <input v-model="form.name" type="text" class="neo-input" required />
          <div v-if="form.errors.name" class="font-body text-xs text-neo-red mt-1.5">{{ form.errors.name }}</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">START DATE</label>
            <input v-model="form.starts_at" type="datetime-local" class="neo-input" required />
          </div>
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">END DATE</label>
            <input v-model="form.ends_at" type="datetime-local" class="neo-input" required />
          </div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">NOTES</label>
          <textarea v-model="form.notes" class="neo-input h-24 resize-none"></textarea>
        </div>
        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-3 md:py-4 text-sm md:text-base">UPDATE ELECTION →</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ election: Object, event: Object });

// Format date strings to YYYY-MM-DDTHH:mm for datetime-local input parsing
const formatForDateTimeLocal = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  if (isNaN(date.getTime())) {
    return dateStr.replace(' ', 'T').substring(0, 16);
  }
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const form = useForm({
  name: props.election.name,
  starts_at: formatForDateTimeLocal(props.election.starts_at),
  ends_at: formatForDateTimeLocal(props.election.ends_at),
  notes: props.election.notes
});

const submit = () => form.put(route('events.admin.elections.update', { event: props.event.slug, election: props.election.id }));
</script>
