<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="visible"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4"
        @click.self="handleCancel"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-neo-black/70 dark:bg-black/80 backdrop-blur-sm"></div>

        <!-- Dialog card -->
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-2"
          appear
        >
          <div
            v-if="visible"
            class="relative z-10 bg-white dark:bg-neo-dark-card border-4 border-neo-black dark:border-white shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] dark:shadow-[12px_12px_0px_0px_rgba(255,255,255,0.15)] w-full max-w-sm"
          >
            <!-- Corner accent -->
            <div class="absolute top-0 right-0 w-8 h-8 border-l-4 border-b-4 border-neo-black dark:border-white"
              :class="variant === 'danger' ? 'bg-neo-red' : variant === 'warning' ? 'bg-neo-yellow' : 'bg-neo-blue'"
            ></div>

            <!-- Header -->
            <div class="px-6 pt-6 pb-4 border-b-2 border-gray-100 dark:border-gray-800 flex items-start gap-4">
              <!-- Icon badge -->
              <div class="w-12 h-12 border-4 border-neo-black dark:border-white flex items-center justify-center shrink-0"
                :class="variant === 'danger' ? 'bg-neo-red' : variant === 'warning' ? 'bg-neo-yellow' : 'bg-neo-blue'"
              >
                <span class="material-symbols-outlined text-2xl font-black"
                  :class="variant === 'warning' ? 'text-neo-black' : 'text-white'"
                >
                  {{ variant === 'danger' ? 'delete_forever' : variant === 'warning' ? 'warning' : 'help' }}
                </span>
              </div>

              <div class="flex-1 pt-0.5">
                <div class="font-heading font-black text-[10px] uppercase tracking-[0.2em] mb-1"
                  :class="variant === 'danger' ? 'text-neo-red' : variant === 'warning' ? 'text-amber-600' : 'text-neo-blue'"
                >
                  {{ variant === 'danger' ? 'CONFIRM DELETE' : variant === 'warning' ? 'CONFIRM ACTION' : 'CONFIRMATION' }}
                </div>
                <h3 class="font-heading font-black text-base uppercase dark:text-white leading-tight">
                  {{ title }}
                </h3>
              </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
              <p class="font-body text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                {{ message }}
              </p>

              <!-- Checkbox for "Make Sure" -->
              <div v-if="requireCheckbox" class="mt-4 flex items-start gap-3 p-3 bg-gray-50 dark:bg-neo-dark-surface border-2 border-neo-black dark:border-white">
                <div class="relative flex items-center">
                  <input
                    id="confirm-checkbox"
                    type="checkbox"
                    v-model="isChecked"
                    class="peer appearance-none w-5 h-5 border-2 border-neo-black dark:border-white bg-white dark:bg-neo-dark-card checked:bg-neo-blue transition-all cursor-pointer"
                  />
                  <span class="material-symbols-outlined absolute pointer-events-none text-white text-base font-black opacity-0 peer-checked:opacity-100 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">check</span>
                </div>
                <label for="confirm-checkbox" class="font-heading text-[10px] font-black uppercase text-neo-black dark:text-gray-300 leading-tight cursor-pointer select-none">
                  {{ checkboxLabel }}
                </label>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 pb-6 flex items-center justify-end gap-3">
              <!-- Cancel -->
              <button
                @click="handleCancel"
                class="neo-btn-secondary text-xs py-2.5 px-5 min-w-[90px] justify-center"
              >
                CANCEL
              </button>

              <!-- Confirm -->
              <button
                @click="handleConfirm"
                :disabled="requireCheckbox && !isChecked"
                class="text-xs py-2.5 px-5 min-w-[90px] justify-center font-heading font-black uppercase border-4 border-neo-black dark:border-white tracking-widest transition-all hover:-translate-y-0.5 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
                :class="[
                  variant === 'danger' ? 'bg-neo-red text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]' :
                  variant === 'warning' ? 'bg-neo-yellow text-neo-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]' :
                  'bg-neo-blue text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]'
                ]"
              >
                <span class="material-symbols-outlined text-sm font-black">{{ confirmIcon }}</span>
                {{ confirmLabel }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue';

/**
 * NeoConfirm — Neobrutalist themed confirm dialog.
 *
 * Usage (via useNeoConfirm composable):
 *   const confirmed = await confirm({ title, message, variant })
 *
 * Or direct v-model usage:
 *   <NeoConfirm v-model="show" ... @confirm="..." @cancel="..." />
 */

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title:        { type: String,  default: 'Are you sure?' },
  message:      { type: String,  default: 'This action cannot be undone.' },
  confirmLabel: { type: String,  default: 'CONFIRM' },
  variant:      { type: String,  default: 'danger' }, // 'danger' | 'warning' | 'info'
  requireCheckbox: { type: Boolean, default: false },
  checkboxLabel:   { type: String,  default: 'I understand that this action is permanent and cannot be undone.' },
});

const isChecked = ref(false);

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

const visible = computed(() => props.modelValue);

const confirmIcon = computed(() => {
  if (props.variant === 'danger')  return 'delete';
  if (props.variant === 'warning') return 'check';
  return 'check';
});

const handleConfirm = () => {
  if (props.requireCheckbox && !isChecked.value) return;
  emit('confirm');
  emit('update:modelValue', false);
  isChecked.value = false; // Reset for next time
};

const handleCancel = () => {
  emit('cancel');
  emit('update:modelValue', false);
  isChecked.value = false; // Reset for next time
};
</script>
