<template>
  <GuestLayout :title="`JOIN — ${event.name}`" subtitle="Choose how you want to participate in this event.">

    <!-- Event Info Card -->
    <div class="neo-card p-4 mb-6 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-8 h-8 bg-neo-blue/10 border-l border-b border-neo-blue/20"></div>
      <div class="font-heading text-[9px] font-bold uppercase text-neo-grey tracking-wider mb-1">EVENT</div>
      <div class="font-heading font-black text-lg uppercase dark:text-white">{{ event.name }}</div>
      <p v-if="event.description" class="font-body text-sm text-neo-grey mt-1">{{ event.description }}</p>
    </div>

    <!-- Tab Switcher -->
    <div class="flex border-2 border-neo-black mb-6">
      <button
        @click="mode = 'voter'"
        :class="['flex-1 py-3 font-heading text-xs font-black uppercase tracking-wider transition-colors focus:outline-none', mode === 'voter' ? 'bg-neo-blue text-white' : 'bg-white dark:bg-neo-dark-card hover:bg-neo-yellow/20 dark:text-white']"
      >
        <span class="material-symbols-outlined text-base align-middle mr-1">how_to_vote</span>
        JOIN AS VOTER
      </button>
      <button
        @click="mode = 'admin'"
        :class="['flex-1 py-3 font-heading text-xs font-black uppercase tracking-wider transition-colors border-l-2 border-neo-black focus:outline-none', mode === 'admin' ? 'bg-neo-red text-white' : 'bg-white dark:bg-neo-dark-card hover:bg-neo-yellow/20 dark:text-white']"
      >
        <span class="material-symbols-outlined text-base align-middle mr-1">admin_panel_settings</span>
        JOIN AS ADMIN
      </button>
    </div>

    <!-- VOTER MODE -->
    <form v-if="mode === 'voter'" @submit.prevent="submitVoter" class="space-y-4">
      <div v-if="$page.props.auth?.user" class="neo-card p-3 bg-neo-blue/5 border-neo-blue/30">
        <p class="font-body text-sm text-neo-grey">
          Joining as <span class="font-heading font-bold text-neo-blue uppercase">{{ $page.props.auth.user.name }}</span>.
          <Link :href="route('logout')" method="post" as="button" class="text-neo-red hover:underline text-xs">Switch account</Link>
        </p>
      </div>

      <template v-if="!$page.props.auth?.user">
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">FULL NAME <span class="text-neo-red">*</span></label>
          <input v-model="voterForm.name" type="text" class="neo-input" placeholder="Your full name" required />
          <div v-if="voterForm.errors.name" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ voterForm.errors.name }}</div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">EMAIL <span class="text-neo-red">*</span></label>
          <input v-model="voterForm.email" type="email" class="neo-input" placeholder="you@example.com" required />
          <div v-if="voterForm.errors.email" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ voterForm.errors.email }}</div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">PASSWORD <span class="text-neo-red">*</span></label>
          <input v-model="voterForm.password" type="password" class="neo-input" placeholder="Min. 8 characters" required />
          <div v-if="voterForm.errors.password" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ voterForm.errors.password }}</div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">CONFIRM PASSWORD <span class="text-neo-red">*</span></label>
          <input v-model="voterForm.password_confirmation" type="password" class="neo-input" placeholder="Repeat password" required />
        </div>
      </template>

      <DynamicFieldRenderer
        :fields="voterFields"
        v-model="voterForm.fields"
        :errors="voterForm.errors"
      />

      <button type="submit" :disabled="voterForm.processing" class="neo-btn-primary w-full py-4 text-base" :class="{ 'opacity-50 cursor-not-allowed': voterForm.processing }">
        <span class="material-symbols-outlined text-base">how_to_vote</span>
        JOIN AS VOTER →
      </button>
      <div class="text-center">
        <Link :href="route('login')" class="font-heading text-xs font-bold text-neo-blue uppercase hover:text-neo-red transition-colors">
          Already registered? Sign in →
        </Link>
      </div>
    </form>

    <!-- ADMIN MODE -->
    <form v-else @submit.prevent="submitAdmin" class="space-y-5">
      <div class="neo-card p-4 bg-neo-red/5 border-2 border-neo-red/20">
        <div class="flex items-start gap-3">
          <span class="material-symbols-outlined text-neo-red shrink-0">key</span>
          <div>
            <div class="font-heading text-xs font-bold uppercase mb-1 dark:text-white">ADMIN INVITE TOKEN REQUIRED</div>
            <p class="font-body text-xs text-neo-grey">Request a one-time invite token from the event Super Admin.</p>
          </div>
        </div>
      </div>

      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">INVITE TOKEN <span class="text-neo-red">*</span></label>
        <input v-model="adminForm.token" type="text" class="neo-input font-mono uppercase tracking-widest" placeholder="XXXX-XXXX-XXXX" required />
        <div v-if="adminForm.errors.token" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ adminForm.errors.token }}</div>
      </div>

      <template v-if="!$page.props.auth?.user">
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">FULL NAME <span class="text-neo-red">*</span></label>
          <input v-model="adminForm.name" type="text" class="neo-input" required />
          <div v-if="adminForm.errors.name" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ adminForm.errors.name }}</div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">EMAIL <span class="text-neo-red">*</span></label>
          <input v-model="adminForm.email" type="email" class="neo-input" required />
          <div v-if="adminForm.errors.email" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ adminForm.errors.email }}</div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">PASSWORD <span class="text-neo-red">*</span></label>
          <input v-model="adminForm.password" type="password" class="neo-input" required />
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">CONFIRM PASSWORD <span class="text-neo-red">*</span></label>
          <input v-model="adminForm.password_confirmation" type="password" class="neo-input" required />
        </div>
      </template>

      <div v-if="$page.props.auth?.user" class="neo-card p-3 bg-neo-blue/5 border-neo-blue/30">
        <p class="font-body text-sm text-neo-grey">
          Joining as <span class="font-heading font-bold text-neo-blue uppercase">{{ $page.props.auth.user.name }}</span>
        </p>
      </div>

      <button type="submit" :disabled="adminForm.processing" class="neo-btn-danger w-full py-4 text-base" :class="{ 'opacity-50 cursor-not-allowed': adminForm.processing }">
        <span class="material-symbols-outlined text-base">admin_panel_settings</span>
        JOIN AS ADMIN →
      </button>
    </form>

  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import DynamicFieldRenderer from '@/Components/DynamicFieldRenderer.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  event: Object,
  voterFields: Array,
});

const mode = ref('voter');

// Dynamic voter fields
const initialFields = {};
(props.voterFields || []).forEach(f => { initialFields[f.key] = ''; });

const voterForm = useForm({
  name: '', email: '', password: '', password_confirmation: '',
  fields: { ...initialFields },
});

const adminForm = useForm({
  token: '', name: '', email: '', password: '', password_confirmation: '',
});

const submitVoter = () => voterForm.post(route('events.join.voter', props.event.slug));
const submitAdmin = () => adminForm.post(route('events.join.admin', props.event.slug));
</script>
