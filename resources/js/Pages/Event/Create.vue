<template>
  <GuestLayout title="CREATE EVENT" subtitle="Set up a new voting event. You'll be the Super Admin.">
    <form @submit.prevent="submit" class="space-y-5">
      <!-- Event Name -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">EVENT NAME <span class="text-neo-red">*</span></label>
        <input v-model="form.name" type="text" class="neo-input" placeholder="e.g. Student Council Election 2026" required autofocus />
        <div v-if="form.errors.name" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.name }}</div>
        <div v-if="form.name" class="font-body text-xs text-neo-grey mt-1">
          URL: <span class="font-mono text-neo-blue">/e/{{ slugPreview }}</span>
        </div>
      </div>

      <!-- Description -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">DESCRIPTION</label>
        <textarea v-model="form.description" class="neo-input resize-none h-24" placeholder="Brief description of your event (optional)"></textarea>
        <div v-if="form.errors.description" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.description }}</div>
      </div>

      <!-- Theme -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-3">VISUAL THEME <span class="text-neo-red">*</span></label>
        <div class="grid grid-cols-3 gap-3">
          <button
            v-for="theme in themes"
            :key="theme.value"
            type="button"
            @click="form.theme = theme.value"
            :class="[
              'border-2 p-3 text-left transition-all cursor-pointer focus:outline-none',
              form.theme === theme.value
                ? 'border-neo-blue bg-neo-blue/10 shadow-[4px_4px_0px_#0048FF]'
                : 'border-neo-black hover:border-neo-blue hover:bg-neo-blue/5'
            ]"
          >
            <div class="font-heading text-xs font-black uppercase mb-1 dark:text-white">{{ theme.label }}</div>
            <div class="font-body text-[10px] text-neo-grey">{{ theme.desc }}</div>
            <div v-if="theme.value !== 'neo-brutalism'" class="mt-1.5 inline-block font-heading text-[8px] font-bold uppercase px-1.5 py-0.5 bg-neo-yellow/20 border border-neo-yellow/40 text-neo-grey">COMING SOON</div>
          </button>
        </div>
        <div v-if="form.errors.theme" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.theme }}</div>
      </div>

      <!-- Results Visibility -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-3">RESULTS VISIBILITY <span class="text-neo-red">*</span></label>
        <div class="grid grid-cols-2 gap-3">
          <button
            v-for="opt in visibilityOptions"
            :key="opt.value"
            type="button"
            @click="form.results_visibility = opt.value"
            :class="[
              'border-2 p-4 text-left transition-all cursor-pointer focus:outline-none',
              form.results_visibility === opt.value
                ? 'border-neo-blue bg-neo-blue/10 shadow-[4px_4px_0px_#0048FF]'
                : 'border-neo-black hover:border-neo-blue hover:bg-neo-blue/5'
            ]"
          >
            <span class="material-symbols-outlined text-xl mb-1 block" :class="form.results_visibility === opt.value ? 'text-neo-blue' : 'text-neo-grey'">{{ opt.icon }}</span>
            <div class="font-heading text-xs font-black uppercase mb-1 dark:text-white">{{ opt.label }}</div>
            <div class="font-body text-[10px] text-neo-grey">{{ opt.desc }}</div>
          </button>
        </div>
        <div v-if="form.errors.results_visibility" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.results_visibility }}</div>
      </div>

      <!-- Must be logged in notice -->
      <div v-if="!$page.props.auth?.user" class="neo-card p-4 border-neo-yellow bg-neo-yellow/10">
        <div class="flex items-start gap-3">
          <span class="material-symbols-outlined text-neo-black shrink-0">info</span>
          <div>
            <div class="font-heading text-xs font-bold uppercase mb-1">ACCOUNT REQUIRED</div>
            <p class="font-body text-xs text-neo-grey">You need to be logged in to create an event.</p>
            <div class="flex gap-2 mt-2">
              <Link :href="route('register')" class="neo-btn-primary text-xs py-1.5 px-3">REGISTER</Link>
              <Link :href="route('login')" class="neo-btn-secondary text-xs py-1.5 px-3">SIGN IN</Link>
            </div>
          </div>
        </div>
      </div>

      <button
        v-else
        type="submit"
        :disabled="form.processing"
        class="neo-btn-primary w-full py-4 text-base"
        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
      >
        <span class="material-symbols-outlined text-base">rocket_launch</span>
        CREATE EVENT →
      </button>
    </form>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm({
  name: '',
  description: '',
  theme: 'neo-brutalism',
  results_visibility: 'private',
});

const slugPreview = computed(() =>
  form.name.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').slice(0, 50)
);

const themes = [
  { value: 'neo-brutalism', label: 'Neo-Brutalism', desc: 'Bold, high-contrast, raw energy.' },
  { value: 'semi-formal',   label: 'Semi-Formal',   desc: 'Clean and professional.' },
  { value: 'formal',        label: 'Formal',        desc: 'Corporate and polished.' },
];

const visibilityOptions = [
  { value: 'private', label: 'Private',  icon: 'lock',       desc: 'Only admins can see results.' },
  { value: 'public',  label: 'Public',   icon: 'public',     desc: 'All voters can see results in real-time.' },
];

const submit = () => form.post(route('events.store'));
</script>
