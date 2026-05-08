<template>
  <GuestLayout :title="`JOIN AS ADMIN — ${event.name}`" subtitle="Enter your invite token to join as an event admin.">

    <!-- Event Info -->
    <div class="neo-card p-4 mb-6 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-8 h-8 bg-neo-red/10 border-l border-b border-neo-red/20"></div>
      <div class="font-heading text-[9px] font-bold uppercase text-neo-grey tracking-wider mb-1">JOINING AS ADMIN</div>
      <div class="font-heading font-black text-lg uppercase dark:text-white">{{ event.name }}</div>
      <p v-if="event.description" class="font-body text-sm text-neo-grey mt-1">{{ event.description }}</p>
    </div>

    <!-- Already logged in notice -->
    <div class="neo-card p-3 bg-neo-blue/5 border-neo-blue/30 mb-4">
      <p class="font-body text-sm text-neo-grey">
        Joining with account:
        <span class="font-heading font-bold text-neo-blue uppercase">{{ $page.props.auth.user?.name }}</span>
        (<Link :href="route('logout')" method="post" as="button" class="text-neo-red hover:underline text-xs">switch account</Link>)
      </p>
    </div>

    <!-- Token warning -->
    <div class="neo-card p-4 bg-neo-red/5 border-2 border-neo-red/20 mb-5">
      <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-neo-red shrink-0">key</span>
        <div>
          <div class="font-heading text-xs font-bold uppercase mb-1 dark:text-white">ADMIN INVITE TOKEN REQUIRED</div>
          <p class="font-body text-xs text-neo-grey">
            Request a one-time invite token from the event Super Admin. Tokens can only be used once.
          </p>
        </div>
      </div>
    </div>

    <!-- Admin Join Form -->
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">
          INVITE TOKEN <span class="text-neo-red">*</span>
        </label>
        <input
          v-model="form.token"
          type="text"
          class="neo-input font-mono uppercase tracking-widest"
          placeholder="Paste invite token here"
          required
        />
        <div v-if="form.errors.token" class="font-body text-xs text-neo-red mt-1.5 font-semibold">
          {{ form.errors.token }}
        </div>
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="neo-btn bg-neo-red text-white border-neo-black shadow-neo hover:shadow-neo-hover w-full py-4 text-base"
        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
      >
        <span class="material-symbols-outlined text-base align-middle mr-1">admin_panel_settings</span>
        JOIN AS ADMIN →
      </button>
    </form>

  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
  event:            { type: Object, required: true },
  adminAccessToken: { type: String, required: true },
});

const form = useForm({ token: '' });

const submit = () => {
  form.post(`/join/a/${props.adminAccessToken}`);
};
</script>
