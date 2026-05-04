<template>
  <AuthenticatedLayout title="JADWAL PEMILIHAN">
    <div class="max-w-2xl">
      <h1 class="font-heading font-black text-h1 uppercase mb-6">JADWAL PEMILIHAN</h1>
      <form @submit.prevent="submit" class="neo-card p-6 space-y-5">
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NAMA</label>
          <input v-model="form.name" type="text" class="neo-input" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">MULAI</label>
            <input v-model="form.starts_at" type="datetime-local" class="neo-input" />
          </div>
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">SELESAI</label>
            <input v-model="form.ends_at" type="datetime-local" class="neo-input" />
          </div>
        </div>
        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-4 text-base">SIMPAN →</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
const props = defineProps({ election: Object });
const form = useForm({ name: props.election?.name || '', starts_at: props.election?.starts_at || '', ends_at: props.election?.ends_at || '' });
const submit = () => form.put(route('admin.election.update'));
</script>
