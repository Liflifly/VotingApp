<template>
  <div class="neo-card p-4 md:p-6">
    <h3 class="font-heading font-black text-base uppercase mb-2 dark:text-white">{{ title }}</h3>
    <p class="font-body text-[10px] text-neo-grey mb-4">{{ description }}</p>
    
    <div class="space-y-3 mb-4">
      <div v-for="(f, i) in modelValue" :key="i" class="p-3 border-2 border-neo-black dark:border-white relative group bg-white dark:bg-neo-dark-card shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] mb-4">
        <div class="flex flex-col md:flex-row gap-2 items-start md:items-center">
          <input v-model="f.label" @input="updateLabel(f, i)" type="text" class="neo-input py-1 text-xs flex-1 w-full" placeholder="Field Label (e.g. Student ID)" required />
          <div class="flex items-center gap-2 w-full md:w-auto">
            <select v-model="f.type" class="neo-input py-1 text-xs flex-1 md:w-32">
              <option value="text">Text</option>
              <option value="number">Number</option>
              <option value="email">Email</option>
              <option value="textarea">Long Text</option>
              <option value="select">Dropdown</option>
              <option value="image">Image Upload</option>
            </select>
            <label class="flex items-center gap-2 text-[10px] font-heading font-bold uppercase dark:text-white shrink-0">
              <input type="checkbox" v-model="f.required" class="accent-neo-blue"> Required
            </label>
            <button 
              @click="removeField(i)" 
              class="w-8 h-8 flex items-center justify-center border-2 border-neo-black bg-white hover:bg-neo-red hover:text-white transition-colors shrink-0"
              title="Remove Field"
            >
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
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

const slugify = (text) => {
  return text.toString().toLowerCase()
    .replace(/\s+/g, '_')
    .replace(/[^\w\-]+/g, '')
    .replace(/\-\-+/g, '_')
    .replace(/^-+/, '')
    .replace(/-+$/, '');
};

const updateLabel = (field, index) => {
  if (!field.label) {
    field.key = '';
    return;
  }
  
  let baseKey = slugify(field.label) || 'field';
  let finalKey = baseKey;
  let counter = 1;
  
  // Check for duplicates
  while (props.modelValue.some((f, i) => f.key === finalKey && i !== index)) {
    finalKey = `${baseKey}_${counter}`;
    counter++;
  }
  
  field.key = finalKey;
};
</script>
