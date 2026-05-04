<template>
  <TransitionGroup
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="transform translate-y-4 opacity-0"
    enter-to-class="transform translate-y-0 opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="transform opacity-100"
    leave-to-class="transform opacity-0"
    tag="div"
    class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none"
  >
    <div
      v-for="toast in toasts"
      :key="toast.id"
      class="pointer-events-auto"
    >
      <div 
        class="min-w-[300px] max-w-md bg-white border-neo border-neo-black p-4 shadow-neo flex items-start gap-4 relative overflow-hidden"
        :class="toast.type === 'success' ? 'border-l-[12px] border-l-neo-blue' : 'border-l-[12px] border-l-neo-red'"
      >
        <!-- Decorative corner -->
        <div class="absolute top-0 right-0 w-6 h-6 border-l border-b border-neo-black opacity-10" 
             :class="toast.type === 'success' ? 'bg-neo-blue' : 'bg-neo-red'"></div>
        
        <!-- Icon -->
        <div class="w-10 h-10 shrink-0 border-2 border-neo-black flex items-center justify-center"
             :class="toast.type === 'success' ? 'bg-neo-blue text-white' : 'bg-neo-red text-white'">
          <span class="material-symbols-outlined text-xl">
            {{ toast.type === 'success' ? 'check_circle' : 'error' }}
          </span>
        </div>

        <!-- Content -->
        <div class="flex-1 pt-0.5">
          <div class="font-heading font-black text-[10px] uppercase tracking-[0.15em] mb-1"
               :class="toast.type === 'success' ? 'text-neo-blue' : 'text-neo-red'">
            {{ toast.type === 'success' ? 'SUCCESS ALERT' : 'SYSTEM ERROR' }}
          </div>
          <div class="font-body text-xs font-bold text-neo-black leading-tight">
            {{ toast.message }}
          </div>
        </div>

        <!-- Close button -->
        <button 
          @click="removeToast(toast.id)"
          class="w-6 h-6 flex items-center justify-center border border-neo-black hover:bg-gray-100 transition-colors"
        >
          <span class="material-symbols-outlined text-sm">close</span>
        </button>
      </div>
    </div>
  </TransitionGroup>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const toasts = ref([]);
const lastToast = ref({ type: null, message: null, timestamp: 0 });
let nextId = 1;

const removeToast = (id) => {
  toasts.value = toasts.value.filter(t => t.id !== id);
};

const addToast = (type, message) => {
  if (!message) return;
  
  // Dedup logic: ignore if same message within 1 second
  const now = Date.now();
  if (type === lastToast.value.type && message === lastToast.value.message && (now - lastToast.value.timestamp < 1000)) {
      return;
  }
  
  lastToast.value = { type, message, timestamp: now };
  
  const id = nextId++;
  toasts.value.push({ id, type, message });

  setTimeout(() => {
    removeToast(id);
  }, 5000);
};

const page = usePage();

// Watch for flash messages from Inertia
watch(() => page.props.flash, (flash) => {
  if (flash?.success) {
    addToast('success', flash.success);
  }
  if (flash?.error) {
    addToast('error', flash.error);
  }
}, { deep: true });
</script>
