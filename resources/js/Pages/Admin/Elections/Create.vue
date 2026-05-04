<template>
  <AuthenticatedLayout title="BUAT PERIODE BARU">
    <div class="max-w-2xl">
      <!-- Header -->
      <div class="neo-page-header bg-white shadow-neo mb-6">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex items-center gap-3">
          <Link :href="route('admin.elections.index')" class="neo-btn-secondary text-[10px] py-1.5 px-3 shadow-neo-sm">← KEMBALI</Link>
          <h1 class="font-heading font-black text-lg md:text-h1 uppercase flex items-center gap-2">
            <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">add_circle</span>
            BUAT PERIODE
          </h1>
        </div>
      </div>

      <form @submit.prevent="submit" class="neo-card p-5 md:p-6 space-y-4 md:space-y-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>
        
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NAMA PERIODE</label>
          <input v-model="form.name" type="text" class="neo-input" placeholder="Pemilihan Ketua Umum 2026" required />
          <div v-if="form.errors.name" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.name }}</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">MULAI</label>
            <input v-model="form.starts_at" type="datetime-local" class="neo-input" required />
            <div v-if="form.errors.starts_at" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.starts_at }}</div>
          </div>
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">SELESAI</label>
            <input v-model="form.ends_at" type="datetime-local" class="neo-input" required />
            <div v-if="form.errors.ends_at" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.ends_at }}</div>
          </div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">CATATAN</label>
          <textarea v-model="form.notes" class="neo-input h-24 resize-none" placeholder="Opsional..."></textarea>
        </div>
        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-3 md:py-4 text-sm md:text-base">SIMPAN PERIODE →</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({ name: '', starts_at: '', ends_at: '', notes: '' });
const submit = () => form.post(route('admin.elections.store'));
</script>
