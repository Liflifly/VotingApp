<template>
  <AuthenticatedLayout title="EDIT ELECTION">
    <div class="max-w-2xl">


      <!-- Header -->
      <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 shadow-neo dark:shadow-neo-white relative overflow-hidden p-6 border-3 border-neo-black dark:border-white">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">edit</span>
            <h1 class="font-heading font-black text-xl uppercase dark:text-white">
              EDIT ELECTION
            </h1>
          </div>
          <div>
            <Link 
              :href="route('events.admin.elections.index', event.slug)" 
              class="neo-btn-secondary text-xs py-2 px-4 flex items-center justify-center gap-2"
            >
              <span class="material-symbols-outlined text-sm">arrow_back</span>
              BACK TO ELECTIONS
            </Link>
          </div>
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
