<template>
  <AuthenticatedLayout title="CREATE ELECTION">
    <div class="max-w-2xl">


      <!-- Header -->
      <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 shadow-neo dark:shadow-neo-white relative overflow-hidden p-6 border-3 border-neo-black dark:border-white">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">add_circle</span>
            <h1 class="font-heading font-black text-xl uppercase dark:text-white">
              CREATE ELECTION
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
          <input v-model="form.name" type="text" class="neo-input" placeholder="e.g. General Election 2026" required />
          <div v-if="form.errors.name" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.name }}</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">START DATE</label>
            <input v-model="form.starts_at" type="datetime-local" class="neo-input" required />
            <div v-if="form.errors.starts_at" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.starts_at }}</div>
          </div>
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">END DATE</label>
            <input v-model="form.ends_at" type="datetime-local" class="neo-input" required />
            <div v-if="form.errors.ends_at" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.ends_at }}</div>
          </div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">NOTES</label>
          <textarea v-model="form.notes" class="neo-input h-24 resize-none" placeholder="Optional notes..."></textarea>
        </div>
        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-3 md:py-4 text-sm md:text-base">SAVE ELECTION →</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ event: Object });

const form = useForm({ name: '', starts_at: '', ends_at: '', notes: '' });
const submit = () => form.post(route('events.admin.elections.store', props.event.slug));
</script>
