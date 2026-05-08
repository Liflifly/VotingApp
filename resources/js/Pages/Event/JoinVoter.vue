<template>
  <GuestLayout :title="`JOIN — ${event.name}`" subtitle="Complete your voter registration for this event.">

    <!-- Event Info -->
    <div class="neo-card p-4 mb-6 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-8 h-8 bg-neo-blue/10 border-l border-b border-neo-blue/20"></div>
      <div class="font-heading text-[9px] font-bold uppercase text-neo-grey tracking-wider mb-1">JOINING AS VOTER</div>
      <div class="font-heading font-black text-lg uppercase dark:text-white">{{ event.name }}</div>
      <p v-if="event.description" class="font-body text-sm text-neo-grey mt-1">{{ event.description }}</p>
    </div>

    <!-- Already logged in notice -->
    <div class="neo-card p-3 bg-neo-blue/5 border-neo-blue/30 mb-4">
      <p class="font-body text-sm text-neo-grey">
        Registering as voter with account:
        <span class="font-heading font-bold text-neo-blue uppercase">{{ $page.props.auth.user?.name }}</span>
        (<Link :href="route('logout')" method="post" as="button" class="text-neo-red hover:underline text-xs">switch account</Link>)
      </p>
    </div>

    <!-- Voter Registration Form -->
    <form @submit.prevent="submit" class="space-y-4">

      <div v-if="voterFields.length === 0" class="neo-card p-5 text-center">
        <span class="material-symbols-outlined text-3xl text-neo-grey mb-2 block">how_to_vote</span>
        <p class="font-body text-sm text-neo-grey">No additional information required. Click below to join!</p>
      </div>

      <div v-else>
        <p class="font-heading text-xs font-bold uppercase text-neo-grey mb-4">
          REQUIRED INFORMATION ({{ voterFields.length }} field{{ voterFields.length !== 1 ? 's' : '' }})
        </p>
        <DynamicFieldRenderer
          :fields="voterFields"
          v-model="form.fields"
          :errors="form.errors"
        />
      </div>

      <div v-if="form.errors?.general" class="font-body text-xs text-neo-red font-semibold">
        {{ form.errors.general }}
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="neo-btn-primary w-full py-4 text-base"
        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
      >
        <span class="material-symbols-outlined text-base align-middle mr-1">how_to_vote</span>
        CONFIRM & JOIN AS VOTER →
      </button>
    </form>

  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import DynamicFieldRenderer from '@/Components/DynamicFieldRenderer.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
  event:            { type: Object, required: true },
  voterFields:      { type: Array, default: () => [] },
  voterAccessToken: { type: String, required: true },
});

// Initialize dynamic fields
const initialFields = {};
props.voterFields.forEach(f => { initialFields[f.key] = ''; });

const form = useForm({ fields: { ...initialFields } });

const submit = () => {
  form.post(`/join/v/${props.voterAccessToken}`);
};
</script>
