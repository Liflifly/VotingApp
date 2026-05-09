<template>
  <div class="space-y-4">
    <template v-for="field in fields" :key="field.key">
      <!-- Skip rendering 'name' if omitName is true (usually handled statically outside) -->
      <div v-if="!(omitName && field.key === 'name')">
        <!-- Field Banner/Image -->
        <div v-if="field.banner" class="mb-3">
          <img :src="field.banner" class="w-full max-h-[300px] object-cover border-2 border-neo-black dark:border-white shadow-neo-sm" />
        </div>

        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">
          {{ field.label }} <span v-if="field.required" class="text-neo-red">*</span>
        </label>
        
        <textarea
          v-if="field.type === 'textarea'"
          v-model="modelValue[field.key]"
          class="neo-input resize-none h-20"
          :required="field.required"
        ></textarea>
        
        <select
          v-else-if="field.type === 'select'"
          v-model="modelValue[field.key]"
          class="neo-input"
          :required="field.required"
        >
          <option value="">— Select —</option>
          <option v-for="opt in (field.options || [])" :key="opt" :value="opt">{{ opt }}</option>
        </select>
        
        <div v-else-if="field.type === 'image'">
          <div
            class="border-neo border-dashed border-neo-black dark:border-white p-4 text-center bg-gray-50 dark:bg-neo-dark-surface cursor-pointer hover:bg-neo-yellow/10 transition-colors relative overflow-hidden"
            @click="triggerFileInput(field.key)"
          >
            <div class="absolute top-0 right-0 w-8 h-8 bg-neo-blue/10 border-l border-b border-neo-blue/20"></div>
            <template v-if="modelValue[field.key]">
              <img v-if="getImagePreview(modelValue[field.key])" :src="getImagePreview(modelValue[field.key])" class="w-20 h-20 mx-auto mb-2 border-2 border-neo-black object-cover" />
              <span v-else class="material-symbols-outlined text-2xl text-green-600 mb-2">check_circle</span>
              <p class="font-heading text-[10px] md:text-xs font-bold uppercase text-green-600 truncate px-2">
                {{ typeof modelValue[field.key] === 'string' ? 'Current Image' : (modelValue[field.key].name || 'New Image Selected') }}
              </p>
            </template>
            <template v-else>
              <span class="material-symbols-outlined text-2xl text-neo-grey mb-2">upload_file</span>
              <p class="font-heading text-[10px] md:text-xs font-bold uppercase text-neo-grey">Click to upload {{ field.label }}</p>
            </template>
            <input
              :ref="el => setFileInputRef(el, field.key)"
              type="file"
              class="hidden"
              accept="image/*"
              @change="handleImageChange(field.key, $event)"
            />
          </div>
        </div>
        
        <input
          v-else-if="field.type === 'number'"
          type="number"
          v-model="modelValue[field.key]"
          class="neo-input"
          :required="field.required"
          step="any"
        />

        <input
          v-else
          :type="field.type"
          v-model="modelValue[field.key]"
          class="neo-input"
          :required="field.required"
        />
        
        <div v-if="getFieldError(field.key)" class="font-body text-xs text-neo-red mt-1.5 font-semibold">
          {{ getFieldError(field.key) }}
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  fields: { type: Array, required: true },
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) },
  omitName: { type: Boolean, default: false },
  errorPrefix: { type: String, default: 'fields.' }
});

const emit = defineEmits(['update:modelValue', 'image-select']);

const getFieldError = (key) => {
  return props.errors[props.errorPrefix + key] || props.errors[key];
};

const fileInputRefs = ref({});
const setFileInputRef = (el, key) => {
  if (el) fileInputRefs.value[key] = el;
};

const triggerFileInput = (key) => {
  fileInputRefs.value[key]?.click();
};

const handleImageChange = (key, event) => {
  const file = event.target.files?.[0];
  if (file) {
    props.modelValue[key] = file;
    emit('image-select', { key, file, event });
  }
};

const objectUrls = {};
const getImagePreview = (val) => {
  if (typeof val === 'string') return val; // Existing URL from DB
  if (val instanceof File || val instanceof Blob) {
    // Generate object URL once per file object to avoid memory leaks/flickering
    if (!objectUrls[val]) {
      objectUrls[val] = URL.createObjectURL(val);
    }
    return objectUrls[val];
  }
  return null;
};
</script>