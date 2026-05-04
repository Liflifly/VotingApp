<template>
  <AuthenticatedLayout title="TAMBAH KANDIDAT">
    <div class="max-w-2xl">
      <!-- Header -->
      <div class="neo-page-header bg-white shadow-neo mb-6">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex items-center gap-3">
          <Link :href="route('admin.candidates.index', election.id)" class="neo-btn-secondary text-[10px] py-1.5 px-3 shadow-neo-sm">← KEMBALI</Link>
          <h1 class="font-heading font-black text-lg md:text-h1 uppercase">TAMBAH KANDIDAT</h1>
        </div>
      </div>

      <form @submit.prevent="submit" class="neo-card p-5 md:p-6 space-y-4 md:space-y-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>
        
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NAMA KANDIDAT</label>
          <input v-model="form.name" type="text" class="neo-input" placeholder="Nama lengkap" required />
          <div v-if="form.errors.name" class="font-body text-xs text-neo-red mt-1.5">{{ form.errors.name }}</div>
        </div>
        <div class="grid grid-cols-2 gap-3 md:gap-4">
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NOMOR URUT</label>
            <input v-model="form.order_number" type="number" class="neo-input" placeholder="Auto" min="1" />
          </div>
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">KELAS</label>
            <input v-model="form.class" type="text" class="neo-input" placeholder="XII RPL 1" required />
          </div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">VISI</label>
          <textarea v-model="form.vision" class="neo-input h-20 resize-none" placeholder="Visi kandidat..." required></textarea>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">MISI</label>
          <textarea v-model="form.mission" class="neo-input h-20 resize-none" placeholder="Misi kandidat..." required></textarea>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">PROGRAM KERJA</label>
          <textarea v-model="form.program" class="neo-input h-20 resize-none" placeholder="Opsional..."></textarea>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">FOTO</label>
          <div
            class="border-neo border-dashed border-neo-black p-4 md:p-6 text-center bg-gray-50 cursor-pointer hover:bg-neo-yellow/10 transition-colors relative overflow-hidden"
            @click="$refs.photoInput.click()"
          >
            <div class="absolute top-0 right-0 w-8 h-8 bg-neo-blue/10 border-l border-b border-neo-blue/20"></div>

            <!-- Preview after crop -->
            <template v-if="photoPreview">
              <img :src="photoPreview" class="w-24 h-24 mx-auto mb-2 border-2 border-neo-black object-cover" />
              <p class="font-heading text-[10px] md:text-xs font-bold uppercase text-green-600">✓ Foto sudah di-crop</p>
            </template>
            <template v-else>
              <span class="material-symbols-outlined text-2xl md:text-3xl text-neo-grey mb-2">upload_file</span>
              <p class="font-heading text-[10px] md:text-xs font-bold uppercase text-neo-grey">Klik untuk upload foto</p>
            </template>

            <input ref="photoInput" type="file" class="hidden" accept="image/*" @change="onPhotoSelect" />
          </div>
          <div v-if="form.errors.photo" class="font-body text-xs text-neo-red mt-1.5">{{ form.errors.photo }}</div>
        </div>
        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-3 md:py-4 text-sm md:text-base">SIMPAN KANDIDAT →</button>
      </form>
    </div>

    <!-- Crop Modal -->
    <NeoCropper
      v-model:show="showCropper"
      :image-src="cropImageSrc"
      crop-shape="square"
      @crop="onCropped"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NeoCropper from '@/Components/NeoCropper.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ election: Object });
const form = useForm({ name: '', order_number: '', class: '', vision: '', mission: '', program: '', photo: null });

const showCropper   = ref(false);
const cropImageSrc  = ref(null);
const photoPreview  = ref(null);

const onPhotoSelect = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;
  e.target.value = '';

  const reader = new FileReader();
  reader.onload = (ev) => {
    cropImageSrc.value = ev.target.result;
    showCropper.value  = true;
  };
  reader.readAsDataURL(file);
};

const onCropped = (file) => {
  form.photo = file;
  // Create preview URL
  photoPreview.value = URL.createObjectURL(file);
};

const submit = () => form.post(route('admin.candidates.store', props.election.id));
</script>
