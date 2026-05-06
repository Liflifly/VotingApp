<template>
  <AuthenticatedLayout title="EDIT KANDIDAT">
    <div class="max-w-2xl">
      <!-- Breadcrumb Navigation -->
      <div class="mb-5 flex">
        <Link 
          :href="route('admin.candidates.index', election.id)" 
          class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-neo-dark-card border-2 border-neo-black dark:border-white font-heading text-[10px] md:text-xs font-black uppercase tracking-wider text-neo-black dark:text-white shadow-[3px_3px_0px_#000] dark:shadow-[3px_3px_0px_rgba(255,255,255,0.8)] hover:bg-neo-yellow dark:hover:bg-neo-yellow hover:text-neo-black dark:hover:text-neo-black hover:translate-x-[1.5px] hover:translate-y-[1.5px] hover:shadow-[1.5px_1.5px_0px_#000] dark:hover:shadow-[1.5px_1.5px_0px_rgba(255,255,255,0.8)] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none transition-all duration-100 group"
        >
          <span class="material-symbols-outlined text-base font-bold group-hover:-translate-x-1 transition-transform text-neo-blue">arrow_back</span>
          KEMBALI KE KELOLA KANDIDAT
        </Link>
      </div>

      <!-- Header -->
      <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 shadow-neo dark:shadow-neo-white relative overflow-hidden p-6 border-3 border-neo-black dark:border-white">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex items-center gap-3">
          <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">edit</span>
          <h1 class="font-heading font-black text-xl uppercase dark:text-white">
            EDIT KANDIDAT
          </h1>
        </div>
      </div>

      <form @submit.prevent="submit" class="neo-card p-5 md:p-6 space-y-4 md:space-y-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>
        
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NAMA KANDIDAT</label>
          <input v-model="form.name" type="text" class="neo-input" required />
        </div>
        <div class="grid grid-cols-2 gap-3 md:gap-4">
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NOMOR URUT</label>
            <input v-model="form.order_number" type="number" class="neo-input" min="1" />
          </div>
          <div>
            <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">KELAS</label>
            <input v-model="form.class" type="text" class="neo-input" required />
          </div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">VISI</label>
          <textarea v-model="form.vision" class="neo-input h-20 resize-none" required></textarea>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">MISI</label>
          <textarea v-model="form.mission" class="neo-input h-20 resize-none" required></textarea>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">PROGRAM KERJA</label>
          <textarea v-model="form.program" class="neo-input h-20 resize-none"></textarea>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">FOTO (opsional)</label>
          <div
            class="border-neo border-dashed border-neo-black p-4 md:p-6 text-center bg-gray-50 cursor-pointer hover:bg-neo-yellow/10 transition-colors relative overflow-hidden"
            @click="$refs.photoInput.click()"
          >
            <div class="absolute top-0 right-0 w-8 h-8 bg-neo-blue/10 border-l border-b border-neo-blue/20"></div>

            <!-- Preview: newly cropped photo -->
            <template v-if="photoPreview">
              <img :src="photoPreview" class="w-24 h-24 mx-auto mb-2 border-2 border-neo-black object-cover" />
              <p class="font-heading text-[10px] md:text-xs font-bold uppercase text-green-600">✓ Foto baru sudah di-crop</p>
            </template>
            <!-- Preview: existing photo from server -->
            <template v-else-if="candidate.photo">
              <img :src="`/storage/${candidate.photo}`" class="w-24 h-24 mx-auto mb-2 border-2 border-neo-black object-cover" />
              <p class="font-heading text-[10px] md:text-xs font-bold uppercase text-neo-grey">Klik untuk ganti foto</p>
            </template>
            <!-- No photo -->
            <template v-else>
              <span class="material-symbols-outlined text-2xl md:text-3xl text-neo-grey mb-2">upload_file</span>
              <p class="font-heading text-[10px] md:text-xs font-bold uppercase text-neo-grey">Ganti foto (opsional)</p>
            </template>

            <input ref="photoInput" type="file" class="hidden" accept="image/*" @change="onPhotoSelect" />
          </div>
        </div>
        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-3 md:py-4 text-sm md:text-base">UPDATE KANDIDAT →</button>
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

const props = defineProps({ election: Object, candidate: Object });
const form = useForm({
  name: props.candidate.name,
  order_number: props.candidate.order_number,
  class: props.candidate.class,
  vision: props.candidate.vision,
  mission: props.candidate.mission,
  program: props.candidate.program,
  photo: null,
  _method: 'put',
});

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
  photoPreview.value = URL.createObjectURL(file);
};

const submit = () => form.post(route('admin.candidates.update', { election: props.election.id, candidate: props.candidate.id }));
</script>
