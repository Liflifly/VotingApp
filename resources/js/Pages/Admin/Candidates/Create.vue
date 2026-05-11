<template>
  <AuthenticatedLayout title="ADD CANDIDATE">
    <div class="max-w-2xl">


      <!-- Header -->
      <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 shadow-neo dark:shadow-neo-white relative overflow-hidden p-6 border-3 border-neo-black dark:border-white">
        <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">person_add</span>
            <h1 class="font-heading font-black text-xl uppercase dark:text-white">
              ADD CANDIDATE
            </h1>
          </div>
          <div>
            <Link 
              :href="route('events.admin.candidates.index', { event: event.slug, election: election.id })" 
              class="neo-btn-secondary text-xs py-2 px-4 flex items-center justify-center gap-2"
            >
              <span class="material-symbols-outlined text-sm">arrow_back</span>
              BACK TO CANDIDATES
            </Link>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="neo-card p-5 md:p-6 space-y-4 md:space-y-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>
        


        <!-- Dynamic fields from event field definitions -->
        <DynamicFieldRenderer
          :fields="candidateFields"
          v-model="form.fields"
          :errors="form.errors"
          @image-select="onImageSelect"
        />

        <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-3 md:py-4 text-sm md:text-base">SAVE CANDIDATE →</button>
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
import DynamicFieldRenderer from '@/Components/DynamicFieldRenderer.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ election: Object, event: Object, candidateFields: Array });

// Build initial fields object from definitions
const initialFields = {};
(props.candidateFields || []).forEach(f => { initialFields[f.key] = ''; });
if (!initialFields.name) initialFields.name = '';

const form = useForm({ fields: { ...initialFields } });

const showCropper  = ref(false);
const cropImageSrc = ref(null);
const currentCropKey = ref(null);

const onImageSelect = ({ key, file }) => {
  currentCropKey.value = key;
  const reader = new FileReader();
  reader.onload = (ev) => {
    cropImageSrc.value = ev.target.result;
    showCropper.value  = true;
  };
  reader.readAsDataURL(file);
};

const onCropped = (file) => {
  if (currentCropKey.value) {
    form.fields[currentCropKey.value] = file;
    // Set a fake file name to show in the renderer
    Object.defineProperty(file, 'name', { value: 'Cropped_Image.png', writable: false });
  }
};

const submit = () => form.post(route('events.admin.candidates.store', {
  event: props.event.slug,
  election: props.election.id
}));
</script>
