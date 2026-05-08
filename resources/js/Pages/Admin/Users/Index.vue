<template>
  <AuthenticatedLayout title="MANAGE MEMBERS">
    <!-- Header -->
    <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-red/10 border-l-2 border-b-2 border-neo-red/20"></div>
      <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>

      <div class="relative z-10">
        <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 md:mb-2 flex items-center gap-2 md:gap-3">
          <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">group</span>
          MANAGE MEMBERS
        </h1>
        <p class="font-body text-xs md:text-sm text-neo-grey">Manage roles and access for this event</p>
      </div>
    </div>

    <div class="space-y-2 md:space-y-3">
      <div v-for="u in members" :key="u.id" class="neo-card p-3 md:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative overflow-visible bg-white dark:bg-neo-dark-card">
        <div class="absolute top-0 right-0 w-6 h-6 border-l border-b" :class="u.role === 'super_admin' ? 'bg-neo-red/10 border-neo-red/20' : u.role === 'admin' ? 'bg-neo-blue/10 border-neo-blue/20' : 'bg-gray-100 border-gray-200 dark:bg-gray-700 dark:border-gray-600'"></div>

        <div class="flex items-center gap-3">
          <div class="w-9 h-9 md:w-10 md:h-10 border-2 border-neo-black dark:border-white flex items-center justify-center shrink-0 overflow-hidden" :class="u.role === 'super_admin' ? 'bg-neo-red' : u.role === 'admin' ? 'bg-neo-blue' : 'bg-gray-200 dark:bg-gray-700'">
            <img v-if="u.avatar" :src="u.avatar" :alt="u.name" class="w-full h-full object-cover grayscale" />
            <span v-else class="material-symbols-outlined text-lg" :class="u.role === 'super_admin' || u.role === 'admin' ? 'text-white' : 'text-neo-black dark:text-white'">person</span>
          </div>
          <div class="min-w-0">
            <div class="font-heading font-bold text-xs md:text-sm uppercase truncate dark:text-white">{{ u.name }}</div>
            <div class="font-body text-[10px] md:text-xs text-neo-grey dark:text-gray-400 truncate">{{ u.email }}</div>
          </div>
        </div>
        
        <div class="flex items-center gap-2 pl-12 sm:pl-0">
          <span :class="['neo-badge text-[9px] md:text-xs', u.role === 'super_admin' ? 'bg-neo-red text-white' : u.role === 'admin' ? 'bg-neo-blue text-white' : 'bg-gray-200 text-neo-black']">
            {{ u.role?.replace('_', ' ').toUpperCase() }}
          </span>

          <template v-if="u.role !== 'super_admin' && $page.props.auth.user.role === 'super_admin'">
            <!-- Role Dropdown -->
            <div class="relative">
              <button @click="toggleDropdown(u.id)" class="neo-input flex items-center justify-between gap-2 py-1 md:py-1.5 px-2 md:px-3 text-[10px] md:text-xs w-28 bg-white dark:bg-neo-dark-card cursor-pointer hover:bg-gray-50 dark:hover:bg-neo-dark-surface focus:outline-none transition-colors">
                <span class="font-heading font-bold tracking-wider">{{ u.role.toUpperCase() }}</span>
                <span class="material-symbols-outlined text-[16px]">expand_more</span>
              </button>
              
              <div v-if="openDropdown === u.id" @click="closeDropdown" class="fixed inset-0 z-40"></div>
              
              <div v-show="openDropdown === u.id" class="absolute top-full right-0 mt-1 w-full bg-white dark:bg-neo-dark-card border-2 border-neo-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] z-50 flex flex-col">
                <button @click="updateRole(u, 'voter')" class="px-3 py-2 text-left font-heading text-xs font-bold hover:bg-neo-blue hover:text-white transition-colors border-b-2 border-neo-black dark:border-white dark:text-white cursor-pointer">VOTER</button>
                <button @click="updateRole(u, 'admin')" class="px-3 py-2 text-left font-heading text-xs font-bold hover:bg-neo-blue hover:text-white transition-colors dark:text-white cursor-pointer">ADMIN</button>
              </div>
            </div>

            <!-- Remove Member -->
            <button @click="removeMember(u)" title="Remove Member" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center border-2 border-neo-black dark:border-white bg-white dark:bg-neo-dark-card hover:bg-neo-red hover:text-white transition-colors text-neo-black dark:text-white">
              <span class="material-symbols-outlined text-[16px]">delete</span>
            </button>
          </template>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ event: Object, members: Array });

const openDropdown = ref(null);

const toggleDropdown = (id) => { openDropdown.value = openDropdown.value === id ? null : id; };
const closeDropdown = () => { openDropdown.value = null; };

const updateRole = (user, newRole) => {
  closeDropdown();
  router.put(route('events.admin.users.role', [props.event.slug, user.id]), { role: newRole });
};

const removeMember = (user) => {
  if (confirm(`Are you sure you want to remove ${user.name} from this event?`)) {
    router.delete(route('events.admin.users.destroy', [props.event.slug, user.id]));
  }
};
</script>
