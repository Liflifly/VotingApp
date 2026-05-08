<template>
  <div class="neo-card p-4 md:p-6">
    <h3 class="font-heading font-black text-base uppercase mb-2 dark:text-white">{{ title }}</h3>
    <p class="font-body text-[10px] text-neo-grey mb-4">{{ description }}</p>
    
    <div class="space-y-3 mb-4">
      <div v-for="(f, i) in modelValue" :key="i" class="p-3 border-2 border-gray-200 dark:border-gray-700 relative group">
        <button @click="removeField(i)" class="absolute top-2 right-2 text-gray-400 hover:text-neo-red">
          <span class="material-symbols-outlined text-sm">close</span>
        </button>
        <div class="grid grid-cols-2 gap-2">
          <input v-model="f.label" type="text" class="neo-input py-1 text-xs" placeholder="Field Label (e.g. Student ID)" required />
          <input v-model="f.key" type="text" class="neo-input py-1 text-xs" placeholder="Database Key (e.g. student_id)" required />
          <select v-model="f.type" class="neo-input py-1 text-xs">
            <option value="text">Text</option>
            <option value="number">Number</option>
            <option value="email">Email</option>
            <option value="textarea">Long Text</option>
            <option value="select">Dropdown</option>
            <option value="image">Image Upload</option>
          </select>
          <label class="flex items-center gap-2 text-xs font-heading uppercase dark:text-white">
            <input type="checkbox" v-model="f.required" class="accent-neo-blue"> Required
          </label>
        </div>
        
        <!-- Options for 'select' type -->
        <div v-if="f.type === 'select'" class="mt-2 pl-1 border-l-2 border-neo-blue">
          <input 
            type="text" 
            :value="f.options ? f.options.join(', ') : ''" 
            @input="updateOptions(f, $event.target.value)"
            class="neo-input py-1 text-xs w-full" 
            placeholder="Comma separated options (e.g. Red, Green, Blue)" 
          />
        </div>
      </div>
    </div>
    
    <div class="flex gap-2">
      <button @click="addField" class="neo-btn-sm-secondary flex-1 text-xs py-2">+ ADD FIELD</button>
      <button @click="$emit('save')" :disabled="processing" class="neo-btn-sm-primary flex-1 text-xs py-2">SAVE FIELDS</button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Array, required: true },
  title: String,
  description: String,
  processing: Boolean
});

const emit = defineEmits(['update:modelValue', 'save']);

const addField = () => {
  const newFields = [...props.modelValue, { key: '', label: '', type: 'text', required: false, options: null }];
  emit('update:modelValue', newFields);
};

const removeField = (index) => {
  const newFields = [...props.modelValue];
  newFields.splice(index, 1);
  emit('update:modelValue', newFields);
};

const updateOptions = (field, value) => {
  field.options = value.split(',').map(s => s.trim()).filter(s => s);
};
</script>
