<template>
  <AuthenticatedLayout title="EVENT SETTINGS">
    <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>
      <div class="relative z-10">
        <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 md:mb-2 flex items-center gap-2 md:gap-3">
          <span class="material-symbols-outlined text-neo-yellow text-2xl md:text-3xl">settings</span>
          EVENT SETTINGS
        </h1>
        <p class="font-body text-xs md:text-sm text-neo-grey">Manage event details, admins, and dynamic fields.</p>
      </div>
    </div>

    <!-- GENERAL SETTINGS -->
    <div class="neo-card p-4 md:p-6 mb-6">
      <h3 class="font-heading font-black text-base uppercase mb-4 dark:text-white">General Information</h3>
      <form @submit.prevent="updateSettings" class="space-y-4 max-w-xl">
        <div>
          <label class="block font-heading text-label-caps uppercase dark:text-white mb-2">Event Name</label>
          <input v-model="settingsForm.name" type="text" class="neo-input" required />
          <div v-if="settingsForm.errors.name" class="font-body text-xs text-neo-red mt-1 font-semibold">{{ settingsForm.errors.name }}</div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase dark:text-white mb-2">Description</label>
          <textarea v-model="settingsForm.description" class="neo-input resize-none h-20"></textarea>
          <div v-if="settingsForm.errors.description" class="font-body text-xs text-neo-red mt-1 font-semibold">{{ settingsForm.errors.description }}</div>
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase dark:text-white mb-2">Theme</label>
          <select v-model="settingsForm.theme" class="neo-input">
            <option value="neo-brutalism">Neo-Brutalism</option>
            <option value="semi-formal" disabled>Semi-Formal (Coming Soon)</option>
            <option value="formal" disabled>Formal (Coming Soon)</option>
          </select>
        </div>
        <button type="submit" :disabled="settingsForm.processing" class="neo-btn-primary py-2 px-6">SAVE SETTINGS</button>
      </form>
    </div>

    <!-- INVITE TOKENS -->
    <div class="neo-card p-4 md:p-6 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-heading font-black text-base uppercase dark:text-white">Admin Invite Tokens</h3>
      </div>
      <p class="font-body text-xs text-neo-grey mb-4 max-w-2xl">Generate one-time tokens to invite other users to help manage your event. Send the token directly to the person. Once they use it to join, it will become invalid.</p>
      
      <form @submit.prevent="generateToken" class="flex flex-col sm:flex-row gap-3 mb-6">
        <select v-model="tokenForm.role" class="neo-input bg-white w-full sm:w-40" required>
          <option value="admin">Admin</option>
          <option value="super_admin">Super Admin</option>
        </select>
        <select v-model="tokenForm.expires_in" class="neo-input bg-white w-full sm:w-48">
          <option :value="24">Expires in 24 hours</option>
          <option :value="72">Expires in 3 days</option>
          <option :value="null">Never expires</option>
        </select>
        <button type="submit" :disabled="tokenForm.processing" class="neo-btn-secondary whitespace-nowrap">GENERATE TOKEN</button>
      </form>

      <div v-if="tokens.length > 0" class="overflow-x-auto">
        <table class="w-full text-left border-collapse border-2 border-neo-black dark:border-white">
          <thead>
            <tr class="bg-gray-100 dark:bg-gray-800 border-b-2 border-neo-black dark:border-white">
              <th class="p-3 font-heading text-xs font-bold uppercase tracking-wider dark:text-white">Token</th>
              <th class="p-3 font-heading text-xs font-bold uppercase tracking-wider dark:text-white">Role</th>
              <th class="p-3 font-heading text-xs font-bold uppercase tracking-wider dark:text-white">Expires At</th>
              <th class="p-3 font-heading text-xs font-bold uppercase tracking-wider text-right dark:text-white">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in tokens" :key="t.id" class="border-b border-gray-200 dark:border-gray-700">
              <td class="p-3 font-mono font-bold text-neo-blue">{{ t.token }}</td>
              <td class="p-3 font-heading text-xs uppercase dark:text-white">{{ t.role.replace('_', ' ') }}</td>
              <td class="p-3 font-body text-xs text-neo-grey">{{ t.expires_at ? new Date(t.expires_at).toLocaleString() : 'Never' }}</td>
              <td class="p-3 text-right">
                <button @click="revokeToken(t.id)" class="text-neo-red hover:underline text-xs font-heading font-bold uppercase">Revoke</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="text-center py-6 border-2 border-dashed border-gray-300 dark:border-gray-700 text-neo-grey text-sm font-heading">
        NO ACTIVE TOKENS
      </div>
    </div>

    <!-- DYNAMIC FIELDS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <DynamicFieldBuilder
        v-model="voterFieldsLocal"
        title="Voter Fields"
        description="Define custom information collected when a user joins as a voter."
        :processing="voterForm.processing"
        @save="saveVoterFields"
      />

      <DynamicFieldBuilder
        v-model="candidateFieldsLocal"
        title="Candidate Fields"
        description="Define custom information required for candidate profiles. (Name and Photo are standard)."
        :processing="candidateForm.processing"
        @save="saveCandidateFields"
      />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DynamicFieldBuilder from '@/Components/DynamicFieldBuilder.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ event: Object, voterFields: Array, candidateFields: Array, tokens: Array });

// General Settings
const settingsForm = useForm({
  name: props.event.name,
  description: props.event.description || '',
  theme: props.event.theme || 'neo-brutalism',
});
const updateSettings = () => settingsForm.put(route('events.admin.settings.update', props.event.slug));

// Tokens
const tokenForm = useForm({ role: 'admin', expires_in: 24 });
const generateToken = () => {
  tokenForm.post(route('events.admin.tokens.generate', props.event.slug), {
    onSuccess: () => tokenForm.reset('role', 'expires_in'),
  });
};
const revokeToken = (id) => {
  if (confirm('Revoke this invite token?')) {
    router.delete(route('events.admin.tokens.revoke', [props.event.slug, id]));
  }
};

// Dynamic Fields Local State
const voterFieldsLocal = ref(JSON.parse(JSON.stringify(props.voterFields || [])));
const candidateFieldsLocal = ref(JSON.parse(JSON.stringify(props.candidateFields || [])));

const voterForm = useForm({ target: 'voter', fields: [] });
const saveVoterFields = () => {
  voterForm.fields = voterFieldsLocal.value;
  voterForm.put(route('events.admin.fields.update', props.event.slug));
};

const candidateForm = useForm({ target: 'candidate', fields: [] });
const saveCandidateFields = () => {
  candidateForm.fields = candidateFieldsLocal.value;
  candidateForm.put(route('events.admin.fields.update', props.event.slug));
};
</script>
