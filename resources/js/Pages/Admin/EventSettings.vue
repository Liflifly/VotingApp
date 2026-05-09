<template>
  <AuthenticatedLayout title="EVENT SETTINGS">
    <div class="neo-page-header bg-white shadow-neo mb-6 md:mb-8">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-yellow/10 border-l-2 border-b-2 border-neo-yellow/20"></div>
      <div class="relative z-10">
        <h1 class="font-heading font-black text-lg md:text-h1 uppercase mb-1 md:mb-2 flex items-center gap-2 md:gap-3">
          <span class="material-symbols-outlined text-neo-yellow text-2xl md:text-3xl">settings</span>
          EVENT SETTINGS
        </h1>
        <p class="font-body text-xs md:text-sm text-neo-grey">Manage event details, share links, admins, and voter fields.</p>
      </div>
    </div>

    <!-- ─── SHARE LINKS & QR CODES ───────────────────────────────────────── -->
    <div class="neo-card p-4 md:p-6 mb-6 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-12 h-12 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="flex items-center justify-between mb-1">
        <h3 class="font-heading font-black text-base uppercase dark:text-white flex items-center gap-2">
          <span class="material-symbols-outlined text-neo-blue">share</span>
          Share Links & QR Codes
        </h3>
        <button
          @click="confirmRegenerateLinks"
          class="neo-btn-sm-secondary text-[10px] flex items-center gap-1 text-neo-red border-neo-red hover:bg-neo-red hover:text-white"
        >
          <span class="material-symbols-outlined text-sm">refresh</span>
          REGENERATE
        </button>
      </div>
      <p class="font-body text-xs text-neo-grey mb-5">Share these links with your voters and admins. Scan or copy the QR codes for easy distribution.</p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

        <!-- Voter Link -->
        <div class="border-2 border-neo-black p-4">
          <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 bg-neo-blue border-neo border-neo-black flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-white text-sm">how_to_vote</span>
            </div>
            <div>
              <div class="font-heading text-xs font-black uppercase dark:text-white">VOTER LINK</div>
              <div class="font-body text-[10px] text-neo-grey">Anyone with this link can register as a voter</div>
            </div>
          </div>

          <!-- QR Code -->
          <div class="flex justify-center mb-3">
            <div ref="voterQrContainer" class="border-2 border-neo-black p-2 bg-white w-36 h-36"></div>
          </div>

          <!-- URL display -->
          <div class="flex gap-2">
            <input
              :value="voterLink"
              readonly
              class="neo-input flex-1 text-xs font-mono bg-gray-50 dark:bg-neo-dark-bg"
            />
            <button
              @click="copyLink(voterLink, 'voter')"
              class="neo-btn-primary px-3 py-2 text-xs shrink-0"
            >
              <span class="material-symbols-outlined text-sm">{{ copied === 'voter' ? 'check' : 'content_copy' }}</span>
            </button>
            <button
              @click="downloadQr(voterQrContainer, 'voter_qr.png')"
              class="neo-btn-secondary px-3 py-2 text-xs shrink-0"
              title="Download QR Code"
            >
              <span class="material-symbols-outlined text-sm">download</span>
            </button>
          </div>
        </div>

        <!-- Admin Link -->
        <div class="border-2 border-neo-red p-4">
          <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 bg-neo-red border-neo border-neo-black flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-white text-sm">admin_panel_settings</span>
            </div>
            <div>
              <div class="font-heading text-xs font-black uppercase dark:text-white">ADMIN LINK</div>
              <div class="font-body text-[10px] text-neo-grey">Share this link to invite admins to join the event</div>
            </div>
          </div>

          <!-- QR Code -->
          <div class="flex justify-center mb-3">
            <div ref="adminQrContainer" class="border-2 border-neo-red p-2 bg-white w-36 h-36"></div>
          </div>

          <!-- URL display -->
          <div class="flex gap-2">
            <input
              :value="adminLink"
              readonly
              class="neo-input flex-1 text-xs font-mono bg-gray-50 dark:bg-neo-dark-bg"
            />
            <button
              @click="copyLink(adminLink, 'admin')"
              class="neo-btn bg-neo-red text-white border-neo-black shadow-neo hover:shadow-neo-hover px-3 py-2 text-xs shrink-0"
            >
              <span class="material-symbols-outlined text-sm">{{ copied === 'admin' ? 'check' : 'content_copy' }}</span>
            </button>
            <button
              @click="downloadQr(adminQrContainer, 'admin_qr.png')"
              class="neo-btn-secondary px-3 py-2 text-xs shrink-0 text-neo-red border-neo-red hover:bg-neo-red hover:text-white"
              title="Download QR Code"
            >
              <span class="material-symbols-outlined text-sm">download</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ─── GENERAL SETTINGS ──────────────────────────────────────────────── -->
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
        </div>
        <div>
          <label class="block font-heading text-label-caps uppercase dark:text-white mb-2">Theme</label>
          <select v-model="settingsForm.theme" class="neo-input">
            <option value="neo-brutalism">Neo-Brutalism</option>
            <option value="semi-formal" disabled>Semi-Formal (Coming Soon)</option>
            <option value="formal" disabled>Formal (Coming Soon)</option>
          </select>
        </div>

        <!-- Results Visibility -->
        <div>
          <label class="block font-heading text-label-caps uppercase dark:text-white mb-3">Results Visibility</label>
          <div class="grid grid-cols-2 gap-3">
            <button
              v-for="opt in visibilityOptions" :key="opt.value" type="button"
              @click="settingsForm.results_visibility = opt.value"
              :class="[
                'border-2 p-3 text-left transition-all cursor-pointer focus:outline-none',
                settingsForm.results_visibility === opt.value
                  ? 'border-neo-blue bg-neo-blue/10 shadow-[4px_4px_0px_#0048FF]'
                  : 'border-neo-black hover:border-neo-blue hover:bg-neo-blue/5'
              ]"
            >
              <span class="material-symbols-outlined text-lg mb-1 block" :class="settingsForm.results_visibility === opt.value ? 'text-neo-blue' : 'text-neo-grey'">{{ opt.icon }}</span>
              <div class="font-heading text-xs font-black uppercase mb-0.5 dark:text-white">{{ opt.label }}</div>
              <div class="font-body text-[10px] text-neo-grey">{{ opt.desc }}</div>
            </button>
          </div>
        </div>

        <button type="submit" :disabled="settingsForm.processing" class="neo-btn-primary py-2 px-6">SAVE SETTINGS</button>
      </form>
    </div>


    <!-- ─── DYNAMIC FIELDS ────────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <DynamicFieldBuilder
        v-model="voterFieldsLocal"
        title="Data Pemilih (Voter Fields)"
        description="Informasi yang dikumpulkan saat pengguna mendaftar sebagai pemilih."
        icon="how_to_vote"
        :processing="voterForm.processing"
        @save="saveVoterFields"
      />
      <DynamicFieldBuilder
        v-model="candidateFieldsLocal"
        title="Data Kandidat (Candidate Fields)"
        description="Informasi yang diperlukan untuk profil setiap kandidat."
        icon="person_pin"
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
import { ref, onMounted, nextTick } from 'vue';
import QRCode from 'qrcode';

const props = defineProps({
  event:           Object,
  voterFields:     Array,
  candidateFields: Array,
  voterLink:       String,
  adminLink:       String,
});

// ─── QR Code ──────────────────────────────────────────────────────────────────
const voterQrContainer = ref(null);
const adminQrContainer = ref(null);
const copied = ref('');

const generateQr = async (container, url) => {
  if (!container || !url) return;
  container.innerHTML = '';
  const canvas = document.createElement('canvas');
  canvas.width  = 128;
  canvas.height = 128;
  container.appendChild(canvas);
  await QRCode.toCanvas(canvas, url, {
    width: 128, margin: 1,
    color: { dark: '#000000', light: '#ffffff' },
  });
};

onMounted(async () => {
  await nextTick();
  generateQr(voterQrContainer.value, props.voterLink);
  generateQr(adminQrContainer.value, props.adminLink);
});

const copyLink = async (url, type) => {
  await navigator.clipboard.writeText(url);
  copied.value = type;
  setTimeout(() => { copied.value = ''; }, 2000);
};

const downloadQr = (container, filename) => {
  if (!container) return;
  const canvas = container.querySelector('canvas');
  if (!canvas) return;
  const url = canvas.toDataURL('image/png');
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  a.click();
};

const confirmRegenerateLinks = () => {
  if (confirm('⚠️ Regenerating links will invalidate ALL existing voter and admin QR codes and links. Are you sure?')) {
    router.post(route('events.admin.links.regenerate', props.event.slug), {}, {
      onSuccess: () => {
        generateQr(voterQrContainer.value, props.voterLink);
        generateQr(adminQrContainer.value, props.adminLink);
      },
    });
  }
};

// ─── General Settings ─────────────────────────────────────────────────────────
const visibilityOptions = [
  { value: 'private', label: 'Private', icon: 'lock',   desc: 'Only admins see results.' },
  { value: 'public',  label: 'Public',  icon: 'public', desc: 'All voters see results in real-time.' },
];

const settingsForm = useForm({
  name:               props.event.name,
  description:        props.event.description || '',
  theme:              props.event.theme || 'neo-brutalism',
  results_visibility: props.event.results_visibility || 'private',
});
const updateSettings = () => settingsForm.put(route('events.admin.settings.update', props.event.slug));


// ─── Dynamic Fields ───────────────────────────────────────────────────────────
const voterFieldsLocal     = ref(JSON.parse(JSON.stringify(props.voterFields || [])));
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
