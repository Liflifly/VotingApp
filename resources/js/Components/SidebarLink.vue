<template>
  <Link :href="href" :class="[
    'flex items-center gap-3 font-heading text-xs font-bold uppercase tracking-wider border-neo transition-all duration-100 group dark:text-white',
    collapsed ? 'px-2 py-2.5 justify-center' : 'px-3 py-2.5',
    active
      ? 'bg-neo-blue text-white border-neo-black dark:border-white shadow-neo-sm'
      : 'bg-transparent text-neo-black border-transparent hover:bg-neo-yellow hover:border-neo-black dark:hover:text-neo-black hover:shadow-neo-sm'
  ]" :title="collapsed ? label : undefined">
    <span class="material-symbols-outlined text-lg transition-transform duration-150 group-hover:scale-110"
          :style="active ? 'font-variation-settings: \'FILL\' 1' : ''">{{ icon }}</span>
    <span v-if="!collapsed" class="flex-1 truncate">{{ label }}</span>
    <span v-if="active && !collapsed" class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  href: String,
  active: Boolean,
  icon: String,
  label: String,
  collapsed: { type: Boolean, default: false },
});
</script>
