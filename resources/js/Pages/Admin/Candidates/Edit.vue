<template>
  <AuthenticatedLayout title="EDIT CANDIDATE">
    <div class="max-w-2xl">
      <div class="mb-5 flex">
        <Link :href="route('events.admin.candidates.index', { event: event.slug, election: election.id })" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-neo-dark-card border-2 border-neo-black dark:border-white font-heading text-[10px] md:text-xs font-black uppercase tracking-wider text-neo-black dark:text-white shadow-[3px_3px_0px_#000] hover:bg-neo-yellow hover:text-neo-black hover:translate-x-[1.5px] hover:translate-y-[1.5px] hover:shadow-[1.5px_1.5px_0px_#000] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none transition-all duration-100 group">
          <span class="material-symbols-outlined text-base font-bold group-hover:-translate-x-1 transition-transform text-neo-blue">arrow_back</span>
          BACK TO CANDIDATES
        </Link>
      </div>

      <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 shadow-neo dark:shadow-neo-white relative overflow-hidden p-6 border-3 border-neo-black dark:border-white">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex items-center gap-3">
          <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">edit</span>
          <h1 class="font-heading font-black text-xl uppercase dark:text-white">EDIT CANDIDATE</h1>
        </div>
      </div>

      <form @submit.prevent="submit" class="neo-card p-5 md:p-6 space-y-4 md:space-y-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>

        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">CANDIDATE NUMBER</label>
          <input v-model="form.order_number" type="number" class="neo-input" min="1" />
        </div>

        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">FULL NAME <span class="text-neo-red">*</span></label>
          <input v-model="form.fields.name" type="text" class="neo-input" required />
        </div>

        <DynamicFieldRenderer
          :fields="candidateFields"
          v-model="form.fields"
          :errors="form.errors"
          :omit-name="true"
          @image-select="onImageSelect"
        />

        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-3 md:py-4 text-sm md:text-base">UPDATE CANDIDATE →</button>
      </form>
    </div>

    <NeoCropper v-model:show="showCropper" :image-src="cropImageSrc" crop-shape="square" @crop="onCropped" />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NeoCropper from '@/Components/NeoCropper.vue';
import DynamicFieldRenderer from '@/Components/DynamicFieldRenderer.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ election: Object, event: Object, candidate: Object, candidateFields: Array });

const initialFields = {};
(props.candidateFields || []).forEach(f => { initialFields[f.key] = props.candidate.fields?.[f.key] ?? ''; });
if (!initialFields.name) initialFields.name = props.candidate.fields?.name ?? '';

const form = useForm({ order_number: props.candidate.order_number, fields: { ...initialFields }, _method: 'put' });
const showCropper = ref(false);
const cropImageSrc = ref(null);
const currentCropKey = ref(null);

const onImageSelect = ({ key, file }) => {
  currentCropKey.value = key;
  const reader = new FileReader();
  reader.onload = (ev) => { cropImageSrc.value = ev.target.result; showCropper.value = true; };
  reader.readAsDataURL(file);
};

const onCropped = (file) => {
  if (currentCropKey.value) {
    form.fields[currentCropKey.value] = file;
    Object.defineProperty(file, 'name', { value: 'Cropped_Image.png', writable: false });
  }
};
const submit = () => form.post(route('events.admin.candidates.update', { event: props.event.slug, election: props.election.id, candidate: props.candidate.id }));
</script>
