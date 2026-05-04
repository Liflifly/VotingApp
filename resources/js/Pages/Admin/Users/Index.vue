<template>
  <AuthenticatedLayout title="KELOLA ADMIN">
    <!-- Header -->
    <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-red/10 border-l-2 border-b-2 border-neo-red/20"></div>
      <div class="absolute bottom-0 left-0 w-10 h-10 bg-neo-yellow/10 border-r-2 border-t-2 border-neo-yellow/20"></div>
      
      <div class="relative z-10">
        <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 md:mb-2 flex items-center gap-2 md:gap-3">
          <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">admin_panel_settings</span>
          KELOLA ADMIN
        </h1>
        <p class="font-body text-xs md:text-sm text-neo-grey">Manajemen peran pengguna sistem</p>
      </div>
    </div>

    <div class="space-y-2 md:space-y-3">
      <div v-for="u in users" :key="u.id" class="neo-card p-3 md:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative overflow-visible">
        <div class="absolute top-0 right-0 w-6 h-6 border-l border-b" :class="u.role === 'super_admin' ? 'bg-neo-red/10 border-neo-red/20' : u.role === 'admin' ? 'bg-neo-blue/10 border-neo-blue/20' : 'bg-gray-100 border-gray-200'"></div>
        
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 md:w-10 md:h-10 border-2 border-neo-black flex items-center justify-center shrink-0" :class="u.role === 'super_admin' ? 'bg-neo-red' : u.role === 'admin' ? 'bg-neo-blue' : 'bg-gray-200'">
            <span class="material-symbols-outlined text-lg" :class="u.role === 'super_admin' || u.role === 'admin' ? 'text-white' : 'text-neo-black'">person</span>
          </div>
          <div class="min-w-0">
            <div class="font-heading font-bold text-xs md:text-sm uppercase truncate">{{ u.name }}</div>
            <div class="font-body text-[10px] md:text-xs text-neo-grey truncate">{{ u.email }}</div>
          </div>
        </div>
        <div class="flex items-center gap-2 pl-12 sm:pl-0">
          <span :class="['neo-badge text-[9px] md:text-xs', u.role === 'super_admin' ? 'bg-neo-red text-white' : u.role === 'admin' ? 'bg-neo-blue text-white' : 'bg-gray-200']">
            {{ u.role?.toUpperCase() }}
          </span>
          
          <!-- Custom Neo-Brutalist Dropdown -->
          <div v-if="u.role !== 'super_admin'" class="relative">
            <button @click="toggleDropdown(u.id)" class="neo-input flex items-center justify-between gap-2 py-1 md:py-1.5 px-2 md:px-3 text-[10px] md:text-xs w-28 bg-white cursor-pointer hover:bg-gray-50 focus:outline-none transition-colors">
              <span class="font-heading font-bold tracking-wider">{{ u.role.toUpperCase() }}</span>
              <span class="material-symbols-outlined text-[16px]">expand_more</span>
            </button>
            
            <!-- Overlay for outside click -->
            <div v-if="openDropdown === u.id" @click="closeDropdown" class="fixed inset-0 z-40"></div>
            
            <!-- Dropdown Menu -->
            <div v-show="openDropdown === u.id" class="absolute top-full right-0 mt-1 w-full bg-white border-2 border-neo-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] z-50 flex flex-col">
              <button @click="updateRole(u, 'user')" class="px-3 py-2 text-left font-heading text-xs font-bold hover:bg-neo-blue hover:text-white transition-colors border-b-2 border-neo-black cursor-pointer">USER</button>
              <button @click="updateRole(u, 'admin')" class="px-3 py-2 text-left font-heading text-xs font-bold hover:bg-neo-blue hover:text-white transition-colors cursor-pointer">ADMIN</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ users: Array });

const openDropdown = ref(null);

const toggleDropdown = (id) => {
  openDropdown.value = openDropdown.value === id ? null : id;
};

const closeDropdown = () => {
  openDropdown.value = null;
};

const updateRole = (user, newRole) => {
  closeDropdown();
  router.put(route('admin.users.updateRole', user.id), { role: newRole });
};
</script>
