<template>
  <AuthenticatedLayout title="EDIT PERIODE">
    <div class="max-w-2xl">
      <!-- Header -->
      <div class="neo-page-header bg-white shadow-neo mb-6">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex items-center gap-3">
          <Link :href="route('admin.elections.index')" class="neo-btn-secondary text-[10px] py-1.5 px-3 shadow-neo-sm">← KEMBALI</Link>
          <h1 class="font-heading font-black text-lg md:text-h1 uppercase flex items-center gap-2">
            <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">edit</span>
            EDIT PERIODE
          </h1>
        </div>
      </div>

      <form @submit.prevent="submit" class="neo-card p-5 md:p-6 space-y-4 md:space-y-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>
        
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NAMA PERIODE</label>
          <input v-model="form.name" type="text" class="neo-input" required />
          <div v-if="form.errors.name" class="font-body text-xs text-neo-red mt-1.5">{{ form.errors.name }}</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">MULAI</label>
            <input v-model="form.starts_at" type="datetime-local" class="neo-input" required />
          </div>
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">SELESAI</label>
            <input v-model="form.ends_at" type="datetime-local" class="neo-input" required />
          </div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">CATATAN</label>
          <textarea v-model="form.notes" class="neo-input h-24 resize-none"></textarea>
        </div>
        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-3 md:py-4 text-sm md:text-base">UPDATE PERIODE →</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
const props = defineProps({ election: Object });
const form = useForm({ name: props.election.name, starts_at: props.election.starts_at, ends_at: props.election.ends_at, notes: props.election.notes });
const submit = () => form.put(route('admin.elections.update', props.election.id));
</script>
