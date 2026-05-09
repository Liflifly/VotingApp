<template>
  <div :id="id" class="neo-card p-0 border-2 flex flex-col select-none">

    <!-- ══ SECTION HEADER ══════════════════════════════════════════ -->
    <div class="px-4 pt-4 pb-3 border-b-2 border-neo-black dark:border-white bg-white dark:bg-neo-dark-card flex items-center justify-between gap-3">
      <div class="flex items-center gap-2.5">
        <span class="material-symbols-outlined text-neo-blue text-xl font-bold">{{ icon }}</span>
        <div>
          <h3 class="font-heading font-black text-sm uppercase dark:text-white tracking-tight leading-none">{{ title }}</h3>
          <span class="font-body text-[10px] text-gray-400 dark:text-gray-500 leading-none mt-0.5 block">
            {{ modelValue.length }} {{ modelValue.length === 1 ? 'field' : 'fields' }} configured
          </span>
        </div>
      </div>

      <button
        type="button"
        @click.prevent.stop="handleSave"
        :disabled="processing"
        class="neo-btn-primary text-[10px] py-1.5 px-4 shrink-0 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5"
      >
        <span class="material-symbols-outlined text-xs font-bold">save</span>
        SAVE
      </button>
    </div>

    <!-- Error Banner -->
    <transition name="fade-slide">
      <div v-if="localError" class="bg-neo-red/10 border-b-2 border-neo-red px-4 py-2 flex items-center gap-2">
        <span class="material-symbols-outlined text-neo-red text-sm font-bold">warning</span>
        <span class="font-heading text-[10px] font-black text-neo-red uppercase tracking-wider leading-tight">
          {{ localError }}
        </span>
      </div>
    </transition>

    <!-- ══ FIELDS LIST ══════════════════════════════════════════════ -->
    <div class="px-3 py-4 space-y-2.5 bg-gray-50 dark:bg-neo-dark-bg flex-1">

      <!-- Empty State -->
      <transition name="fade">
        <div
          v-if="modelValue.length === 0"
          class="py-10 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 bg-white dark:bg-neo-dark-card"
        >
          <span class="material-symbols-outlined text-4xl text-gray-200 dark:text-gray-700 block mb-2">dynamic_form</span>
          <p class="font-heading text-[10px] text-gray-300 dark:text-gray-600 uppercase font-black tracking-widest">No fields yet</p>
          <p class="font-body text-[11px] text-gray-300 dark:text-gray-600 mt-1">Click "Add Field" to get started</p>
        </div>
      </transition>

      <!-- Primary Key Alert -->
      <div v-if="requirePrimaryKey && !hasPrimaryKey" class="mx-0.5 mb-4 p-3 bg-neo-red/10 border-2 border-neo-red flex items-start gap-3">
        <span class="material-symbols-outlined text-neo-red text-xl">error</span>
        <div>
          <div class="font-heading text-xs font-black uppercase text-neo-red mb-1">Primary Key Required</div>
          <div class="font-body text-[10px] text-gray-700 dark:text-gray-300">You must select one field to act as the unique identifier (Primary Key) to differentiate entries. Usually, this is an ID number, NIK, or Email.</div>
        </div>
      </div>

      <!-- ── FIELD CARDS ─────────────────────────────────────────── -->
      <div
        v-for="(f, i) in modelValue"
        :key="f._uid || i"
        class="relative transition-all duration-200"
        :class="expandedFields[i] ? 'z-10' : 'z-0'"
      >
        <!-- Card Shell -->
        <div
          :class="[
            'neo-card dark:bg-neo-dark-card transition-all duration-300 relative group/field mb-4',
            expandedFields[i] 
              ? 'border-neo-blue shadow-[4px_4px_0px_0px_rgba(59,130,246,0.4)]' 
              : 'border-neo-black dark:border-white shadow-[3px_3px_0px_0px_rgba(0,0,0,0.8)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.08)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]'
          ]"
        >
          <!-- Blue left accent bar -->
          <div
            :class="[
              'absolute left-0 top-0 bottom-0 w-[3px] transition-all duration-300',
              expandedFields[i] ? 'bg-neo-blue' : 'bg-transparent'
            ]"
          ></div>

          <div 
            class="flex items-center gap-3 px-4 py-3 pl-5 cursor-pointer"
            @click="expandedFields[i] = !expandedFields[i]"
          >
            <!-- Drag handle -->
            <div
              class="shrink-0 cursor-grab active:cursor-grabbing flex flex-col gap-[3px] opacity-30 hover:opacity-70 transition-opacity"
              title="Drag to reorder"
            >
              <span class="block w-4 h-[2px] bg-current dark:bg-white rounded-full"></span>
              <span class="block w-4 h-[2px] bg-current dark:bg-white rounded-full"></span>
              <span class="block w-4 h-[2px] bg-current dark:bg-white rounded-full"></span>
            </div>

            <!-- Field Number badge -->
            <span class="shrink-0 w-5 h-5 flex items-center justify-center bg-gray-100 dark:bg-neo-dark-surface border border-gray-200 dark:border-gray-700 font-heading font-black text-[9px] text-gray-400 dark:text-gray-500">
              {{ i + 1 }}
            </span>

            <!-- Label preview -->
            <div class="flex-1 min-w-0">
              <p class="font-heading font-black text-sm dark:text-white truncate leading-tight" :class="!f.label ? 'text-gray-300 dark:text-gray-600 italic font-normal' : ''">
                {{ f.label || 'Untitled field' }}
              </p>
              <div class="flex items-center gap-2 mt-0.5">
                <span class="font-body text-[10px] text-gray-400 dark:text-gray-500">{{ getTypeName(f.type) }}</span>
                <span v-if="f.key" class="font-mono text-[9px] text-gray-300 dark:text-gray-700 bg-gray-50 dark:bg-neo-dark-surface px-1.5 py-px border border-gray-100 dark:border-gray-800 truncate max-w-[120px]">{{ f.key }}</span>
                <span v-if="f.is_primary" class="shrink-0 bg-neo-yellow text-neo-black border border-neo-black text-[8px] font-heading font-black uppercase px-1.5 py-0.5" title="Primary Key (Unique Identifier)">
                  PRIMARY KEY
                </span>
                <span v-if="f.required" class="shrink-0 bg-neo-red text-white border border-neo-black dark:border-white text-[8px] font-heading font-black uppercase px-1.5 py-0.5">
                  REQ
                </span>
              </div>
            </div>

            <!-- Collapse/Expand chevron -->
            <span
              class="material-symbols-outlined text-base text-gray-300 dark:text-gray-600 shrink-0 transition-transform duration-200"
              :class="expandedFields[i] ? 'rotate-180 text-neo-blue' : ''"
            >expand_more</span>
          </div>

          <!-- ── EXPANDED BODY ─────────────────────────────────────── -->
          <div
            class="transition-all duration-300 ease-in-out"
            :style="expandedFields[i] ? 'max-height: 1000px; opacity: 1; overflow: visible;' : 'max-height: 0px; opacity: 0; overflow: hidden;'"
            @click.stop
          >
            <div class="border-t border-gray-100 dark:border-gray-800">

              <!-- Label + Type Row -->
              <div class="flex flex-col md:flex-row gap-3 px-5 pt-4 pb-2">
                <!-- Label input -->
                <div class="flex-1 relative">
                  <label class="font-heading text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-1">Label</label>
                  <div class="relative">
                    <input
                      v-model="f.label"
                      @input="updateLabel(f, i); emitUpdate()"
                      @click.stop
                      type="text"
                      :placeholder="getPlaceholder(f.type)"
                      class="w-full bg-gray-50 dark:bg-neo-dark-surface border-2 border-gray-200 dark:border-gray-700 focus:border-neo-blue dark:focus:border-neo-blue outline-none font-heading font-black text-sm dark:text-white px-3 py-2 placeholder:font-body placeholder:font-normal placeholder:text-gray-300 dark:placeholder:text-gray-600 transition-colors"
                      @focus.stop="expandedFields[i] = true"
                    />
                  </div>
                </div>

                <!-- Type Selector -->
                <div class="shrink-0 relative">
                  <label class="font-heading text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-1">Field Type</label>
                  <button
                    type="button"
                    @click.stop="openTypeDropdown = openTypeDropdown === i ? null : i"
                    class="w-full md:w-auto bg-gray-50 dark:bg-neo-dark-surface border-2 border-gray-200 dark:border-gray-700 hover:border-neo-blue pl-3 pr-9 py-2 font-heading text-[10px] font-black uppercase tracking-widest dark:text-white outline-none min-w-[160px] flex items-center gap-2 transition-colors relative"
                    :class="openTypeDropdown === i ? 'border-neo-blue' : ''"
                  >
                    <span class="material-symbols-outlined text-sm text-neo-blue">{{ getTypeIcon(f.type) }}</span>
                    <span class="flex-1 text-left">{{ getTypeName(f.type) }}</span>
                    <span class="material-symbols-outlined text-base absolute right-2 text-gray-400 transition-transform" :class="openTypeDropdown === i ? 'rotate-180' : ''">expand_more</span>
                  </button>

                  <!-- Dropdown overlay -->
                  <div v-if="openTypeDropdown === i" @click.stop="openTypeDropdown = null" class="fixed inset-0 z-40"></div>

                  <!-- Dropdown menu -->
                  <div
                    v-if="openTypeDropdown === i"
                    class="absolute top-full mt-1 right-0 w-[190px] bg-white dark:bg-neo-dark-card border-2 border-neo-blue shadow-[4px_4px_0px_rgba(59,130,246,0.3)] z-50 py-1"
                  >
                    <button
                      v-for="opt in typeOptions"
                      :key="opt.value"
                      @click.stop="f.type = opt.value; openTypeDropdown = null; handleTypeChange(f); emitUpdate()"
                      class="w-full px-3 py-2 text-left font-heading text-[10px] font-black uppercase tracking-widest hover:bg-neo-blue hover:text-white transition-colors dark:text-white flex items-center gap-2.5"
                      :class="f.type === opt.value ? 'text-neo-blue bg-neo-blue/5' : ''"
                    >
                      <span class="material-symbols-outlined text-sm">{{ opt.icon }}</span>
                      {{ opt.label }}
                      <span v-if="f.type === opt.value" class="material-symbols-outlined text-xs ml-auto">check</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Field preview / Options area -->
              <div class="px-5 pb-4">

                <!-- Text/Number/Email preview -->
                <div v-if="['text', 'number', 'email'].includes(f.type)" class="mt-4 group/preview">
                  <label class="font-heading text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-1.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[10px]">visibility</span>
                    Field Preview
                  </label>
                  <div class="h-10 border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-neo-dark-surface flex items-center px-3 transition-colors group-hover/preview:border-neo-blue">
                    <span class="font-body text-xs text-gray-400 dark:text-gray-500 italic">{{ getPreviewText(f.type) }}</span>
                  </div>
                </div>

                <!-- Textarea preview -->
                <div v-if="f.type === 'textarea'" class="mt-4 group/preview">
                  <label class="font-heading text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-1.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[10px]">visibility</span>
                    Field Preview
                  </label>
                  <div class="h-16 border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-neo-dark-surface flex items-start p-3 transition-colors group-hover/preview:border-neo-blue">
                    <span class="font-body text-xs text-gray-400 dark:text-gray-500 italic">Long text input preview...</span>
                  </div>
                </div>

                <!-- Image upload preview (Static) -->
                <div v-if="f.type === 'image'" class="mt-4 group/preview">
                  <label class="font-heading text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-1.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[10px]">visibility</span>
                    Field Preview
                  </label>
                  <div class="max-w-[200px] aspect-video border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-neo-dark-surface flex flex-col items-center justify-center gap-1 transition-colors group-hover/preview:border-neo-blue">
                    <span class="material-symbols-outlined text-2xl text-gray-300 dark:text-gray-600">cloud_upload</span>
                    <span class="font-heading text-[9px] font-black uppercase tracking-widest text-gray-300 dark:text-gray-600">Upload area</span>
                  </div>
                </div>

                <!-- ── FIELD BANNER (Admin can attach an image to any field) ── -->
                <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                  <div class="flex items-center justify-between mb-2">
                    <label class="font-heading text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
                      <span class="material-symbols-outlined text-xs">add_photo_alternate</span>
                      Attached Image / Banner (Optional)
                    </label>
                    <span v-if="f.banner" class="font-heading text-[8px] font-black text-neo-blue uppercase tracking-tighter bg-neo-blue/10 px-1.5 py-0.5 border border-neo-blue/20">Active</span>
                  </div>
                  
                  <div class="flex flex-col sm:flex-row items-stretch gap-4">
                    <!-- Banner Preview/Upload -->
                    <div 
                      class="relative flex-1 sm:max-w-[200px] aspect-video border-2 border-dashed transition-all duration-200 cursor-pointer overflow-hidden flex flex-col items-center justify-center group/banner"
                      :class="f.banner 
                        ? 'border-neo-blue bg-white dark:bg-neo-dark-card' 
                        : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-neo-dark-surface hover:border-neo-blue hover:bg-neo-blue/5'"
                      @click.stop="triggerBannerUpload(i)"
                    >
                      <template v-if="f.banner">
                        <img :src="f.banner" class="w-full h-full object-cover block" />
                        <div class="absolute inset-0 bg-neo-black/60 opacity-0 group-hover/banner:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1">
                          <span class="material-symbols-outlined text-white text-base">edit</span>
                          <span class="font-heading text-[8px] font-black text-white uppercase tracking-widest">Change Banner</span>
                        </div>
                      </template>
                      <template v-else>
                        <div class="flex flex-col items-center justify-center gap-1 text-gray-300 dark:text-gray-600 group-hover/banner:text-neo-blue transition-colors">
                          <span class="material-symbols-outlined text-2xl">upload_file</span>
                          <span class="font-heading text-[8px] font-black uppercase tracking-widest">Click to upload</span>
                        </div>
                      </template>
                    </div>

                    <div class="flex-1 flex flex-col justify-center">
                      <p class="font-body text-[10px] text-gray-400 dark:text-gray-500 leading-relaxed mb-3">
                        Upload an image to show it as a header banner for this field. 
                        <span class="text-neo-blue font-bold">Recommended: 16:9 aspect ratio.</span>
                      </p>
                      <div class="flex items-center gap-3">
                        <button 
                          type="button"
                          @click.stop="triggerBannerUpload(i)"
                          class="neo-btn-sm-secondary !text-[8px] !px-3 !py-1.5 flex items-center gap-1.5"
                        >
                          <span class="material-symbols-outlined text-xs">cloud_upload</span>
                          {{ f.banner ? 'CHANGE IMAGE' : 'SELECT IMAGE' }}
                        </button>
                        <button 
                          v-if="f.banner"
                          type="button" 
                          @click.stop="f.banner = null; emitUpdate()"
                          class="text-[8px] font-heading font-black uppercase text-neo-red hover:underline flex items-center gap-1"
                        >
                          <span class="material-symbols-outlined text-xs">delete</span>
                          REMOVE
                        </button>
                      </div>
                    </div>
                  </div>

                  <input
                    :ref="el => { if (el) bannerInputs[i] = el }"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleBannerUpload($event, f, i)"
                  />
                </div>

                <!-- Dropdown options builder -->
                <div v-if="f.type === 'select'" class="mt-3 space-y-2">
                  <label class="font-heading text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 block">Options</label>

                  <!-- Existing options as pills -->
                  <div v-if="f.options && f.options.length" class="flex flex-wrap gap-1.5">
                    <div
                      v-for="(opt, oi) in f.options"
                      :key="oi"
                      class="flex items-center gap-1.5 bg-neo-blue/10 border border-neo-blue/30 pl-2.5 pr-1.5 py-1 group/pill"
                    >
                      <span class="font-heading font-black text-[10px] text-neo-blue tracking-wide uppercase">{{ opt }}</span>
                      <button
                        @click.stop="removeOption(f, oi)"
                        class="w-4 h-4 flex items-center justify-center hover:bg-neo-red/20 hover:text-neo-red text-neo-blue/50 transition-colors rounded-full"
                      >
                        <span class="material-symbols-outlined text-xs font-bold">close</span>
                      </button>
                    </div>
                  </div>

                  <!-- Add option input -->
                  <div class="flex items-center gap-2 border-2 border-dashed border-gray-200 dark:border-gray-700 focus-within:border-neo-blue transition-colors px-3 py-2 bg-gray-50 dark:bg-neo-dark-surface">
                    <span class="material-symbols-outlined text-sm text-gray-300 dark:text-gray-600">add</span>
                    <input
                      type="text"
                      v-model="newOptions[i]"
                      @keydown.enter.prevent="addOption(f, i)"
                      @blur="addOption(f, i)"
                      @click.stop
                      class="flex-1 bg-transparent border-0 outline-none font-body text-xs dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600"
                      :placeholder="f.options && f.options.length ? 'Add another option...' : 'Type option name + press Enter'"
                      @focus.stop="focusedIndex = i"
                    />
                    <kbd class="hidden sm:inline-block font-mono text-[8px] px-1 py-0.5 border border-gray-200 dark:border-gray-700 text-gray-300 dark:text-gray-600 bg-white dark:bg-neo-dark-card tracking-wider">↵</kbd>
                  </div>
                  <p v-if="!f.options || f.options.length === 0" class="font-body text-[10px] text-gray-300 dark:text-gray-600 italic">No options yet — type above and press Enter to add</p>
                </div>
              </div>

              <!-- ── BOTTOM TOOLBAR ──────────────────────────────── -->
              <div class="border-t border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center justify-between bg-gray-50/80 dark:bg-neo-dark-surface/50">
                <!-- Left: Core Actions -->
                <div class="flex items-center gap-2">
                  <!-- Reorder buttons -->
                  <div class="flex items-center bg-white dark:bg-neo-dark-surface border border-gray-200 dark:border-gray-700 rounded-sm shadow-[1px_1px_0px_rgba(0,0,0,0.05)]">
                    <button
                      type="button" @click.stop="moveUp(i)" :disabled="i === 0"
                      class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-neo-blue disabled:opacity-20 transition-all"
                    >
                      <span class="material-symbols-outlined text-sm">arrow_upward</span>
                    </button>
                    <div class="w-px h-3 bg-gray-100 dark:bg-gray-800"></div>
                    <button
                      type="button" @click.stop="moveDown(i)" :disabled="i === modelValue.length - 1"
                      class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-neo-blue disabled:opacity-20 transition-all"
                    >
                      <span class="material-symbols-outlined text-sm">arrow_downward</span>
                    </button>
                  </div>

                  <div class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-2"></div>

                  <!-- Copy/Delete -->
                  <div class="flex items-center gap-1">
                    <button
                      type="button" @click.stop="duplicateField(i)"
                      class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-neo-blue hover:bg-neo-blue/10 transition-all rounded-sm"
                      title="Duplicate"
                    >
                      <span class="material-symbols-outlined text-base">content_copy</span>
                    </button>
                    <button
                      type="button" @click.stop="removeField(i)"
                      class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-neo-red hover:bg-neo-red/10 transition-all rounded-sm"
                      title="Delete"
                    >
                      <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                  </div>
                </div>

                <!-- Right: Configuration Toggles -->
                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2 sm:gap-6">
                  <!-- Primary Key toggle -->
                  <button
                    v-if="requirePrimaryKey && ['text', 'email', 'number'].includes(f.type)"
                    type="button"
                    @click.stop="togglePrimaryKey(f)"
                    class="flex items-center gap-2.5 group/toggle"
                  >
                    <span
                      class="font-heading text-[8px] font-black uppercase tracking-[0.15em] transition-colors"
                      :class="f.is_primary ? 'text-neo-yellow' : 'text-gray-400 dark:text-gray-500'"
                    >Primary</span>
                    <div
                      class="relative w-9 h-[18px] border-2 border-neo-black dark:border-white transition-colors duration-200 shadow-[2px_2px_0px_rgba(0,0,0,1)]"
                      :class="f.is_primary ? 'bg-neo-yellow' : 'bg-gray-200 dark:bg-neo-dark-surface'"
                    >
                      <div
                        class="absolute top-[1px] w-2.5 h-2.5 border border-neo-black bg-white transition-all duration-200"
                        :class="f.is_primary ? 'left-[19px]' : 'left-[1px]'"
                      ></div>
                    </div>
                  </button>

                  <!-- Divider (desktop only) -->
                  <div v-if="requirePrimaryKey && ['text', 'email', 'number'].includes(f.type)" class="hidden sm:block w-px h-4 bg-gray-200 dark:bg-gray-700"></div>

                  <!-- Required toggle -->
                  <button
                    type="button"
                    @click.stop="toggleRequired(f)"
                    class="flex items-center gap-2.5 group/toggle"
                    :class="f.is_primary ? 'opacity-50 cursor-not-allowed' : ''"
                  >
                    <span
                      class="font-heading text-[8px] font-black uppercase tracking-[0.15em] transition-colors"
                      :class="f.required ? 'text-neo-red' : 'text-gray-400 dark:text-gray-500'"
                    >Required</span>
                    <div
                      class="relative w-9 h-[18px] border-2 border-neo-black dark:border-white transition-colors duration-200 shadow-[2px_2px_0px_rgba(0,0,0,1)]"
                      :class="f.required ? 'bg-neo-red' : 'bg-gray-200 dark:bg-neo-dark-surface'"
                    >
                      <div
                        class="absolute top-[1px] w-2.5 h-2.5 border border-neo-black bg-white transition-all duration-200"
                        :class="f.required ? 'left-[19px]' : 'left-[1px]'"
                      ></div>
                    </div>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── ADD FIELD BUTTON ─────────────────────────────────────── -->
      <button
        type="button"
        @click="addField"
        class="w-full flex items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-neo-dark-card py-4 font-heading text-[11px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 hover:border-neo-blue hover:text-neo-blue hover:bg-neo-blue/5 dark:hover:bg-neo-blue/5 transition-all focus:outline-none group"
      >
        <span class="material-symbols-outlined text-lg group-hover:scale-110 transition-transform">add_circle</span>
        ADD NEW FIELD
      </button>
      
      <!-- Close All / Scroll to Top Button -->
      <button
        v-if="modelValue.length > 0"
        type="button"
        @click="closeAllFields"
        class="mt-3 w-full flex items-center justify-center gap-2 border-2 border-neo-black dark:border-white bg-white py-3 font-heading text-[11px] font-black uppercase tracking-widest text-neo-black hover:bg-neo-yellow transition-all shadow-[4px_4px_0px_#000] active:shadow-none active:translate-x-[1px] active:translate-y-[1px]"
      >
        <span class="material-symbols-outlined text-lg">unfold_less</span>
        CLOSE ALL & GO TO SAVE
      </button>
      <!-- NeoCropper Integration -->
      <NeoCropper
        v-model:show="cropperActive"
        :image-src="imageToCrop"
        crop-shape="rect"
        :aspect-ratio="16 / 9"
        @crop="onCropDone"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import NeoCropper from '@/Components/NeoCropper.vue';

const props = defineProps({
  id:          String,
  modelValue:  { type: Array, required: true },
  title:       { type: String, default: 'Form Fields' },
  description: String,
  processing:  Boolean,
  icon:        { type: String, default: 'list_alt' },
  requirePrimaryKey: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'save', 'dirty']);

const expandedFields = ref({});
const localError     = ref('');
const newOptions     = ref({});
const openTypeDropdown = ref(null);
const focusedIndex   = ref(null);

const scrollToTop = () => {
  if (props.id) {
    const el = document.getElementById(props.id);
    if (el) {
      // Offset to accommodate sticky header + some breathing room
      const yOffset = -100;
      const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
      window.scrollTo({ top: y, behavior: 'smooth' });
    }
  }
};

const closeAllFields = () => {
  expandedFields.value = {};
  // Wait for the collapse animation to complete before scrolling
  setTimeout(() => {
    scrollToTop();
  }, 350);
};

// Unique ID / Primary Key Check
const hasPrimaryKey = computed(() => {
  return props.modelValue.some(f => f.is_primary);
});

const togglePrimaryKey = (field) => {
  const isCurrentlyPrimary = field.is_primary;
  
  // If turning on, turn off all others first
  if (!isCurrentlyPrimary) {
    props.modelValue.forEach(f => {
      f.is_primary = false;
    });
    field.is_primary = true;
    field.required = true; // Primary keys must be required
  } else {
    field.is_primary = false;
  }
  emit('dirty');
};

// Banner upload state
const bannerInputs = ref({});

// Cropper state
const cropperActive = ref(false);
const imageToCrop   = ref(null);
const croppingIndex = ref(null);

const typeOptions = [
  { value: 'text',     label: 'Short Text',   icon: 'short_text' },
  { value: 'textarea', label: 'Long Text',    icon: 'notes' },
  { value: 'number',   label: 'Number',       icon: 'numbers' },
  { value: 'email',    label: 'Email',        icon: 'alternate_email' },
  { value: 'select',   label: 'Dropdown',     icon: 'list' },
  { value: 'image',    label: 'Image Upload', icon: 'image' },
];

const getTypeName = (val) => typeOptions.find(o => o.value === val)?.label ?? 'Short Text';
const getTypeIcon = (val) => typeOptions.find(o => o.value === val)?.icon ?? 'short_text';

const emitUpdate = () => {
  localError.value = '';
  emit('update:modelValue', [...props.modelValue]);
  emit('dirty');
};

const getPlaceholder = (type) => ({
  number:   'E.g. Phone or Year',
  email:    'E.g. Work Email',
  textarea: 'E.g. Personal Bio',
  image:    'E.g. ID Card Photo',
  select:   'E.g. Class or Region',
})[type] ?? 'E.g. Full Name';

const getPreviewText = (type) => ({
  number: 'Numeric input...',
  email:  'Email address...',
})[type] ?? 'Short text...';

// ── Field array manipulation ──────────────────────────────────────────────────

const addField = () => {
  const newFields = [...props.modelValue, { key: '', label: '', type: 'text', required: false, options: null }];
  emit('update:modelValue', newFields);
  focusedIndex.value = newFields.length - 1;
  emit('dirty');
};

const duplicateField = (index) => {
  const newFields = [...props.modelValue];
  const copy = JSON.parse(JSON.stringify(newFields[index]));
  copy.key = copy.key ? copy.key + '_copy' : '';
  newFields.splice(index + 1, 0, copy);
  emit('update:modelValue', newFields);
  focusedIndex.value = index + 1;
  emit('dirty');
};

const removeField = (index) => {
  const newFields = [...props.modelValue];
  newFields.splice(index, 1);
  emit('update:modelValue', newFields);
  focusedIndex.value = null;
  emit('dirty');
};

const moveUp = (index) => {
  if (index === 0) return;
  const newFields = [...props.modelValue];
  [newFields[index - 1], newFields[index]] = [newFields[index], newFields[index - 1]];
  const tmp = newOptions.value[index - 1];
  newOptions.value[index - 1] = newOptions.value[index];
  newOptions.value[index] = tmp;
  emit('update:modelValue', newFields);
  focusedIndex.value = index - 1;
  emit('dirty');
};

const moveDown = (index) => {
  if (index === props.modelValue.length - 1) return;
  const newFields = [...props.modelValue];
  [newFields[index + 1], newFields[index]] = [newFields[index], newFields[index + 1]];
  const tmp = newOptions.value[index + 1];
  newOptions.value[index + 1] = newOptions.value[index];
  newOptions.value[index] = tmp;
  emit('update:modelValue', newFields);
  focusedIndex.value = index + 1;
  emit('dirty');
};

const toggleRequired = (field) => {
  if (field.is_primary) return;
  field.required = !field.required;
  emitUpdate();
};

// ── Options & type logic ──────────────────────────────────────────────────────

const handleTypeChange = (field) => {
  if (field.type !== 'select') field.options = null;
  else if (!field.options) field.options = [];
};

const addOption = (field, index) => {
  const val = newOptions.value[index]?.trim();
  if (val) {
    if (!field.options) field.options = [];
    if (!field.options.includes(val)) {
      field.options.push(val);
      emitUpdate();
    }
    newOptions.value[index] = '';
  }
};

const removeOption = (field, index) => {
  field.options?.splice(index, 1);
  emitUpdate();
};

const slugify = (text) =>
  text.toString().toLowerCase()
    .replace(/\s+/g, '_')
    .replace(/[^\w]+/g, '')
    .replace(/_+/g, '_')
    .replace(/^_|_$/g, '');

const updateLabel = (field, index) => {
  if (!field.label) { field.key = ''; return; }
  let base = slugify(field.label) || 'field';
  let key = base;
  let n = 1;
  while (props.modelValue.some((f, i) => f.key === key && i !== index)) key = `${base}_${n++}`;
  field.key = key;
};

// ── Banner upload logic (Admin attaches image to field) ──────────────────────

const triggerBannerUpload = (index) => {
  bannerInputs.value[index]?.click();
};

const handleBannerUpload = (event, fieldObj, index) => {
  const file = event.target.files?.[0];
  if (!file || !file.type.startsWith('image/')) return;
  
  const reader = new FileReader();
  reader.onload = (e) => {
    imageToCrop.value = e.target.result;
    croppingIndex.value = index;
    cropperActive.value = true;
  };
  reader.readAsDataURL(file);
};

const onCropDone = (croppedFile) => {
  const index = croppingIndex.value;
  if (index === null) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    props.modelValue[index].banner = e.target.result;
    emitUpdate();
    cropperActive.value = false;
  };
  reader.readAsDataURL(croppedFile);
};

// ── Validation & save ─────────────────────────────────────────────────────────

const handleSave = () => {
  localError.value = '';

  if (props.modelValue.length === 0) {
    localError.value = 'Please add at least one field before saving';
    return;
  }

  for (let i = 0; i < props.modelValue.length; i++) {
    const f = props.modelValue[i];
    if (!f.label?.trim()) {
      focusedIndex.value = i;
      localError.value = `Field #${i + 1} needs a label`;
      return;
    }
    if (!f.key) {
      focusedIndex.value = i;
      localError.value = `Field #${i + 1} has an invalid key`;
      return;
    }
    if (f.type === 'select' && (!f.options?.length)) {
      focusedIndex.value = i;
      localError.value = `"${f.label}" dropdown needs options`;
      return;
    }
  }
  emit('save');
};
</script>

<style scoped>
.fade-slide-enter-active, .fade-slide-leave-active { transition: all 0.2s ease; }
.fade-slide-enter-from, .fade-slide-leave-to { opacity: 0; transform: translateX(8px); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>