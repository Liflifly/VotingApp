<template>
  <div class="neo-card p-0 overflow-hidden">
    <!-- Header -->
    <div class="px-5 py-4 border-b-2 border-neo-black dark:border-white bg-neo-surface dark:bg-neo-dark-surface">
      <h3 class="font-heading font-black text-sm uppercase dark:text-white flex items-center gap-2">
        <span class="material-symbols-outlined text-neo-blue text-lg">{{ icon }}</span>
        {{ title }}
      </h3>
      <p class="font-body text-[11px] text-neo-grey mt-0.5">{{ description }}</p>
    </div>

    <!-- Empty state -->
    <div v-if="modelValue.length === 0" class="px-5 py-8 text-center">
      <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600 block mb-2">playlist_add</span>
      <p class="font-heading text-xs text-gray-400 dark:text-gray-500 uppercase">Belum ada field. Klik "+ Tambah Field" untuk mulai.</p>
    </div>

    <!-- Field List (Google Form card style) -->
    <div class="px-4 py-3 space-y-3">
      <div
        v-for="(f, i) in modelValue" :key="i"
        class="border-2 border-neo-black dark:border-white bg-white dark:bg-neo-dark-card shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.3)] relative"
      >
        <!-- Field number badge -->
        <div class="absolute -top-3 -left-2 w-6 h-6 bg-neo-blue border-2 border-neo-black dark:border-white flex items-center justify-center z-10">
          <span class="font-heading text-[10px] font-black text-white">{{ i + 1 }}</span>
        </div>

        <!-- Top row: label input + type dropdown -->
        <div class="flex flex-col sm:flex-row gap-0 border-b-2 border-neo-black dark:border-white">
          <div class="flex-1 relative">
            <label class="block font-heading text-[9px] font-bold uppercase text-neo-grey tracking-widest px-3 pt-2.5 pb-0.5">
              Nama Field
            </label>
            <input
              v-model="f.label"
              @input="updateLabel(f, i)"
              type="text"
              class="w-full bg-transparent px-3 pb-2.5 pt-0 font-heading text-sm font-bold dark:text-white outline-none focus:bg-neo-blue/5 transition-colors placeholder:font-body placeholder:font-normal placeholder:text-gray-300 dark:placeholder:text-gray-600"
              placeholder="Contoh: NIS, Nama Lengkap, Foto..."
            />
          </div>
          <div class="border-t-2 sm:border-t-0 sm:border-l-2 border-neo-black dark:border-white bg-gray-50 dark:bg-neo-dark-surface shrink-0">
            <label class="block font-heading text-[9px] font-bold uppercase text-neo-grey tracking-widest px-3 pt-2.5 pb-0.5">
              Tipe Input
            </label>
            <div class="relative px-3 pb-2.5">
              <select
                v-model="f.type"
                class="w-full sm:w-36 bg-transparent font-heading text-sm font-bold dark:text-white outline-none cursor-pointer appearance-none pr-5"
              >
                <option value="text">📝 Teks Singkat</option>
                <option value="textarea">📄 Teks Panjang</option>
                <option value="number">🔢 Angka</option>
                <option value="email">📧 Email</option>
                <option value="select">📋 Pilihan (Dropdown)</option>
                <option value="image">🖼️ Upload Foto</option>
              </select>
              <span class="material-symbols-outlined text-sm text-neo-grey absolute right-3 top-0 pointer-events-none">expand_more</span>
            </div>
          </div>
        </div>

        <!-- Options row (for select type) -->
        <div v-if="f.type === 'select'" class="px-3 py-2.5 border-b-2 border-dashed border-neo-black/30 dark:border-white/20 bg-blue-50 dark:bg-neo-blue/10">
          <label class="block font-heading text-[9px] font-bold uppercase text-neo-blue tracking-widest mb-1">
            Pilihan (pisahkan dengan koma)
          </label>
          <input
            type="text"
            :value="f.options ? f.options.join(', ') : ''"
            @input="updateOptions(f, $event.target.value)"
            class="w-full bg-white dark:bg-neo-dark-bg border-2 border-neo-blue px-3 py-1.5 font-body text-xs outline-none focus:shadow-[2px_2px_0px_#0048FF] transition-shadow"
            placeholder="Contoh: Laki-laki, Perempuan"
          />
        </div>

        <!-- Bottom row: required toggle + delete -->
        <div class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-neo-dark-surface">
          <!-- Required toggle -->
          <button
            type="button"
            @click="f.required = !f.required"
            :class="[
              'flex items-center gap-2 text-[11px] font-heading font-bold uppercase transition-colors focus:outline-none',
              f.required ? 'text-neo-blue' : 'text-gray-400 dark:text-gray-500'
            ]"
          >
            <div :class="[
              'w-9 h-5 rounded-full border-2 border-neo-black dark:border-white relative transition-colors duration-200 flex items-center',
              f.required ? 'bg-neo-blue' : 'bg-gray-200 dark:bg-gray-700'
            ]">
              <div :class="[
                'w-3.5 h-3.5 bg-white border border-neo-black absolute top-0.5 transition-all duration-200',
                f.required ? 'left-[18px]' : 'left-[1px]'
              ]"></div>
            </div>
            {{ f.required ? 'Wajib Diisi' : 'Opsional' }}
          </button>

          <!-- Delete button -->
          <button
            @click="removeField(i)"
            class="flex items-center gap-1 text-[10px] font-heading font-bold uppercase text-neo-grey hover:text-neo-red transition-colors focus:outline-none"
            title="Hapus Field"
          >
            <span class="material-symbols-outlined text-base">delete</span>
            HAPUS
          </button>
        </div>
      </div>
    </div>

    <!-- Footer actions -->
    <div class="px-4 pb-4 pt-1 flex gap-2 border-t-2 border-dashed border-gray-200 dark:border-gray-700 mt-1">
      <button
        @click="addField"
        class="flex-1 flex items-center justify-center gap-1.5 border-2 border-dashed border-neo-black dark:border-white py-2.5 font-heading text-xs font-bold uppercase hover:bg-neo-blue/5 hover:border-neo-blue hover:text-neo-blue transition-all dark:text-white"
      >
        <span class="material-symbols-outlined text-base">add</span>
        Tambah Field
      </button>
      <button
        @click="$emit('save')"
        :disabled="processing"
        class="neo-btn-primary text-xs py-2.5 px-5 shrink-0 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span class="material-symbols-outlined text-base">save</span>
        SIMPAN
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue:  { type: Array, required: true },
  title:       String,
  description: String,
  processing:  Boolean,
  icon:        { type: String, default: 'list_alt' },
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

const slugify = (text) =>
  text.toString().toLowerCase()
    .replace(/\s+/g, '_')
    .replace(/[^\w\-]+/g, '')
    .replace(/\-\-+/g, '_')
    .replace(/^-+/, '')
    .replace(/-+$/, '');

const updateLabel = (field, index) => {
  if (!field.label) { field.key = ''; return; }
  let baseKey = slugify(field.label) || 'field';
  let finalKey = baseKey;
  let counter = 1;
  while (props.modelValue.some((f, i) => f.key === finalKey && i !== index)) {
    finalKey = `${baseKey}_${counter}`;
    counter++;
  }
  field.key = finalKey;
};
</script>
