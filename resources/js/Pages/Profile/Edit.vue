<template>
  <AuthenticatedLayout title="PROFIL">

    <!-- Page Header -->
    <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 md:mb-8 shadow-neo dark:shadow-neo-white">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="relative z-10 flex items-center gap-3">
        <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">manage_accounts</span>
        <div>
          <h1 class="font-heading font-black text-lg md:text-h1 uppercase">PROFILE SETTINGS</h1>
          <p class="font-body text-xs text-neo-grey dark:text-gray-400">Manage your digital identity on Vuwoting™</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- LEFT: Identity Card -->
      <div class="lg:col-span-1">
        <div class="neo-card dark:bg-neo-dark-card dark:border-white dark:shadow-neo-white p-6 text-center relative overflow-hidden">
          <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow border-l-2 border-b-2 border-neo-black dark:border-white"></div>

          <div class="relative group w-32 h-32 mx-auto mb-4">
            <div class="w-full h-full bg-neo-blue border-neo border-neo-black dark:border-white shadow-neo dark:shadow-neo-white flex items-center justify-center overflow-hidden rounded-full">
              <img v-if="user.avatar" :src="user.avatar" class="w-full h-full object-cover" />
              <div v-else class="relative w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400 text-7xl select-none">person</span>
                <span class="absolute material-symbols-outlined text-white text-3xl select-none font-bold filter drop-shadow-[2px_2px_0px_rgba(0,0,0,1)]">add_a_photo</span>
              </div>
            </div>
            <label class="absolute inset-0 rounded-full flex items-center justify-center cursor-pointer transition-colors duration-200"
              :class="user.avatar ? 'bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity' : 'bg-black/0 group-hover:bg-black/35'"
            >
              <input type="file" class="hidden" accept="image/*" @change="onFileChange" ref="fileInputRef" />
              <span v-if="user.avatar" class="material-symbols-outlined text-white text-3xl">add_a_photo</span>
            </label>
          </div>

          <!-- Avatar Control Buttons -->
          <div v-if="user.avatar" class="mb-4 space-y-2 max-w-[240px] mx-auto">
            <!-- Upload Foto Baru Button -->
            <button @click="triggerFileSelect" class="neo-btn bg-neo-blue text-white w-full py-1.5 px-3 text-[10px] flex items-center justify-center gap-1 shadow-[2px_2px_0px_#000] dark:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] font-heading font-black uppercase">
              <span class="material-symbols-outlined text-[14px]">upload_file</span>
              SELECT NEW PHOTO
            </button>
            
            <!-- Edit & Hapus Buttons Row -->
            <div class="flex gap-2">
              <button @click="editExistingAvatar" class="neo-btn bg-neo-yellow text-neo-black flex-1 py-1.5 px-2 text-[10px] flex items-center justify-center gap-1 shadow-[2px_2px_0px_#000] dark:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] font-heading font-black">
                <span class="material-symbols-outlined text-[14px]">crop_rotate</span>
                EDIT PHOTO
              </button>
              <button @click="promptDeleteAvatar" class="neo-btn-danger flex-1 py-1.5 px-2 text-[10px] flex items-center justify-center gap-1 shadow-[2px_2px_0px_#000] dark:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] font-heading font-black">
                <span class="material-symbols-outlined text-[14px]">delete</span>
                DELETE PHOTO
              </button>
            </div>
          </div>
          <div v-else class="mb-4 max-w-[240px] mx-auto">
            <button @click="triggerFileSelect" class="neo-btn bg-neo-blue text-white w-full py-1.5 px-3 text-[10px] flex items-center justify-center gap-1 shadow-[2px_2px_0px_#000] dark:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] font-heading font-black uppercase">
              <span class="material-symbols-outlined text-[14px]">upload_file</span>
              SELECT PHOTO
            </button>
          </div>

          <div class="font-heading font-black text-lg uppercase truncate mb-1 dark:text-white">{{ user.name }}</div>
          <div class="font-body text-xs text-neo-grey dark:text-gray-400 mb-3 truncate">{{ user.email }}</div>

          <div :class="[
            'inline-flex items-center gap-1.5 px-3 py-1 border-2 border-neo-black dark:border-white font-heading text-xs font-black uppercase tracking-wider mb-4',
            user.role === 'super_admin' ? 'bg-neo-red text-white' :
            user.role === 'admin'       ? 'bg-neo-blue text-white' :
                                          'bg-neo-yellow text-neo-black'
          ]">
            <span class="material-symbols-outlined text-sm">
              {{ user.role === 'super_admin' ? 'shield' : user.role === 'admin' ? 'admin_panel_settings' : 'how_to_vote' }}
            </span>
            {{ user.role?.toUpperCase().replace('_', ' ') }}
          </div>

          <div class="space-y-2 mt-2">
            <div v-if="user.nis" class="flex items-center justify-between p-2.5 bg-gray-50 dark:bg-neo-dark-surface border-2 border-gray-200 dark:border-gray-700">
              <span class="font-heading text-[10px] font-bold uppercase tracking-wider text-neo-grey dark:text-gray-400">NIS</span>
              <span class="font-heading text-xs font-black dark:text-white">{{ user.nis }}</span>
            </div>
            <div class="flex items-center justify-between p-2.5 bg-gray-50 dark:bg-neo-dark-surface border-2 border-gray-200 dark:border-gray-700">
              <span class="font-heading text-[10px] font-bold uppercase tracking-wider text-neo-grey dark:text-gray-400">STATUS VOTE</span>
              <span :class="[
                'font-heading text-[10px] font-black uppercase tracking-wider px-2 py-0.5 border border-neo-black dark:border-white',
                user.has_voted ? 'bg-neo-blue text-white' : 'bg-gray-100 dark:bg-gray-700 dark:text-white text-neo-grey'
              ]">{{ user.has_voted ? 'SUDAH' : 'BELUM' }}</span>
            </div>
          </div>

          <div v-if="$page.props.flash?.status === 'avatar-updated' || $page.props.flash?.status === 'avatar-deleted'" class="mt-4 p-3 bg-green-100 dark:bg-green-900/30 border-2 border-green-500 flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
            <span class="font-heading text-xs font-bold uppercase text-green-700 dark:text-green-400">
              {{ $page.props.flash?.status === 'avatar-deleted' ? 'Photo deleted successfully!' : 'Photo updated successfully!' }}
            </span>
          </div>
        </div>
      </div>

      <!-- RIGHT: Forms -->
      <div class="lg:col-span-2 space-y-6">
        <div class="neo-card dark:bg-neo-dark-card dark:border-white dark:shadow-neo-white p-4 sm:p-5 md:p-6 relative overflow-hidden">
          <div class="absolute top-0 right-0 px-4 py-1 bg-neo-black dark:bg-white text-white dark:text-neo-black font-heading font-black text-[10px] uppercase tracking-[0.2em]">01 / INFO</div>
          <h2 class="font-heading font-black text-md sm:text-lg md:text-xl uppercase mb-4 md:mb-5 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-neo-blue">badge</span> BASIC INFORMATION
          </h2>
          <form @submit.prevent="updateProfile">
            <div v-if="profileForm.errors.name || profileForm.errors.email" class="bg-neo-red border-neo border-neo-black p-4 mb-6 shadow-neo animate-in fade-in slide-in-from-top-4 duration-300">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-white font-bold">warning</span>
                <p class="font-heading text-xs font-bold text-white uppercase tracking-wider">
                  {{ profileForm.errors.name || profileForm.errors.email }}
                </p>
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4 md:mb-5">
              <div>
                <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">FULL NAME</label>
                <input v-model="profileForm.name" type="text" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" required />
                <p v-if="profileForm.errors.name" class="text-neo-red font-heading font-bold text-xs uppercase mt-1">{{ profileForm.errors.name }}</p>
              </div>
              <div>
                <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">EMAIL ADDRESS</label>
                <input v-model="profileForm.email" type="email" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" required />
                <p v-if="profileForm.errors.email" class="text-neo-red font-heading font-bold text-xs uppercase mt-1">{{ profileForm.errors.email }}</p>
              </div>
            </div>
            <div v-if="$page.props.flash?.status === 'profile-updated'" class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border-2 border-green-500 flex items-center gap-2">
              <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
              <span class="font-heading text-xs font-bold uppercase text-green-700 dark:text-green-400">Profile updated successfully!</span>
            </div>
            <button type="submit" :disabled="profileForm.processing" class="neo-btn bg-neo-blue text-white py-2.5 px-6 text-xs shadow-neo disabled:opacity-50">
              <span class="material-symbols-outlined text-base">save</span> SAVE CHANGES
            </button>
          </form>
        </div>

        <div class="neo-card dark:bg-neo-dark-card dark:border-white dark:shadow-neo-white p-4 sm:p-5 md:p-6 relative overflow-hidden">
          <div class="absolute top-0 right-0 px-4 py-1 bg-neo-yellow text-neo-black font-heading font-black text-[10px] uppercase tracking-[0.2em]">02 / SECURITY</div>
          <h2 class="font-heading font-black text-md sm:text-lg md:text-xl uppercase mb-4 md:mb-5 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-neo-yellow">lock</span> CHANGE PASSWORD
          </h2>
          <form @submit.prevent="updatePassword">
            <div v-if="passwordForm.hasErrors" class="bg-neo-red border-neo border-neo-black p-4 mb-6 shadow-neo animate-in fade-in slide-in-from-top-4 duration-300">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-white font-bold">warning</span>
                <p class="font-heading text-xs font-bold text-white uppercase tracking-wider">
                  {{ passwordForm.errors.current_password || passwordForm.errors.password || passwordForm.errors.password_confirmation || 'There was an error with your form submission.' }}
                </p>
              </div>
            </div>

            <div class="space-y-3 mb-4 md:mb-5">
              <div>
                <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">CURRENT PASSWORD</label>
                <input v-model="passwordForm.current_password" type="password" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" autocomplete="current-password" />
                <p v-if="passwordForm.errors.current_password" class="text-neo-red font-heading font-bold text-xs uppercase mt-1">{{ passwordForm.errors.current_password }}</p>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">NEW PASSWORD</label>
                  <input v-model="passwordForm.password" type="password" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" autocomplete="new-password" />
                  <p v-if="passwordForm.errors.password" class="text-neo-red font-heading font-bold text-xs uppercase mt-1">{{ passwordForm.errors.password }}</p>
                </div>
                <div>
                  <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">CONFIRM PASSWORD</label>
                  <input v-model="passwordForm.password_confirmation" type="password" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" autocomplete="new-password" />
                </div>
              </div>
            </div>
            <div v-if="$page.props.flash?.status === 'password-updated'" class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border-2 border-green-500 flex items-center gap-2">
              <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
              <span class="font-heading text-xs font-bold uppercase text-green-700 dark:text-green-400">Password updated successfully!</span>
            </div>
            <button type="submit" :disabled="passwordForm.processing" class="neo-btn bg-neo-yellow text-neo-black py-2.5 px-6 text-xs shadow-neo disabled:opacity-50">
              <span class="material-symbols-outlined text-base">key</span> CHANGE PASSWORD
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- ═══ CROP MODAL — Custom NeoCropper ═══ -->
    <NeoCropper
      v-model:show="showCropModal"
      :image-src="imageSrc"
      crop-shape="circle"
      @crop="onCropped"
    />

    <!-- ═══ NEO PASSWORD ALERT MODAL ═══ -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-4"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-4"
      >
        <div v-if="showPasswordAlert" class="fixed inset-0 z-50 flex items-center justify-center p-4">

          <!-- Backdrop -->
          <div
            class="absolute inset-0 bg-neo-black/60 backdrop-blur-[2px]"
            @click="showPasswordAlert = false"
          />

          <!-- Modal Card -->
          <div class="relative w-full max-w-sm border-[3px] border-neo-black dark:border-white bg-white dark:bg-neo-dark-card shadow-[8px_8px_0px_#000] dark:shadow-[8px_8px_0px_rgba(255,255,255,0.8)] overflow-hidden">

            <!-- Top accent bar -->
            <div class="h-2 bg-neo-yellow w-full" />

            <!-- Corner decoration -->
            <div class="absolute top-2 right-0 w-12 h-12 bg-neo-black dark:bg-white border-l-[3px] border-b-[3px] border-neo-black dark:border-white" />

            <!-- Body -->
            <div class="p-8 text-center">

              <!-- Icon box -->
              <div class="mx-auto mb-5 w-20 h-20 bg-neo-yellow border-[3px] border-neo-black dark:border-white shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] flex items-center justify-center">
                <span class="material-symbols-outlined text-neo-black text-4xl" style="font-variation-settings: 'FILL' 1;">key</span>
              </div>

              <!-- Label chip -->
              <div class="inline-block mb-3 px-3 py-0.5 bg-neo-black dark:bg-white text-white dark:text-neo-black font-heading font-black text-[10px] uppercase tracking-[0.25em]">
                SECURITY SYSTEM
              </div>

              <!-- Title -->
              <h3 class="font-heading font-black text-xl uppercase leading-tight mb-2 dark:text-white">
                PASSWORD<br/>UPDATED!
              </h3>

              <!-- Divider -->
              <div class="mx-auto mb-4 w-12 h-[3px] bg-neo-yellow" />

              <!-- Body text -->
              <p class="font-body text-sm text-neo-grey dark:text-gray-400 mb-7 leading-relaxed">
                Your password has been updated successfully.<br/>
                Use your new password on your next login.
              </p>

              <!-- CTA Button -->
              <button
                @click="showPasswordAlert = false"
                class="w-full py-3 px-6 bg-neo-blue text-white border-[3px] border-neo-black dark:border-white font-heading font-black text-sm uppercase tracking-widest shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#000] dark:hover:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-100 flex items-center justify-center gap-2"
              >
                <span class="material-symbols-outlined text-base">check_circle</span>
                GOT IT
              </button>
            </div>

            <!-- Bottom branding bar -->
            <div class="px-5 py-2 bg-gray-50 dark:bg-neo-dark-surface border-t-[3px] border-neo-black dark:border-white flex items-center justify-center gap-2">
              <span class="material-symbols-outlined text-neo-grey dark:text-gray-500 text-sm">lock</span>
              <span class="font-heading font-black text-[10px] uppercase tracking-[0.2em] text-neo-grey dark:text-gray-500">VUWOTING™ SECURITY</span>
            </div>
          </div>

        </div>
      </Transition>
    </Teleport>

    <!-- ═══ NEO DELETE AVATAR ALERT MODAL ═══ -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-4"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-4"
      >
        <div v-if="showDeleteAlert" class="fixed inset-0 z-50 flex items-center justify-center p-4">

          <!-- Backdrop -->
          <div
            class="absolute inset-0 bg-neo-black/60 backdrop-blur-[2px]"
            @click="showDeleteAlert = false"
          />

          <!-- Modal Card -->
          <div class="relative w-full max-w-sm border-[3px] border-neo-black dark:border-white bg-white dark:bg-neo-dark-card shadow-[8px_8px_0px_#000] dark:shadow-[8px_8px_0px_rgba(255,255,255,0.8)] overflow-hidden">

            <!-- Top accent bar -->
            <div class="h-2 bg-neo-red w-full" />

            <!-- Corner decoration -->
            <div class="absolute top-2 right-0 w-12 h-12 bg-neo-black dark:bg-white border-l-[3px] border-b-[3px] border-neo-black dark:border-white" />

            <!-- Body -->
            <div class="p-8 text-center">

              <!-- Icon box -->
              <div class="mx-auto mb-5 w-20 h-20 bg-neo-red border-[3px] border-neo-black dark:border-white shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-4xl" style="font-variation-settings: 'FILL' 1;">delete_forever</span>
              </div>

              <!-- Label chip -->
              <div class="inline-block mb-3 px-3 py-0.5 bg-neo-black dark:bg-white text-white dark:text-neo-black font-heading font-black text-[10px] uppercase tracking-[0.25em]">
                CONFIRM ACTION
              </div>

              <!-- Title -->
              <h3 class="font-heading font-black text-xl uppercase leading-tight mb-2 dark:text-white">
                DELETE PROFILE<br/>PHOTO?
              </h3>

              <!-- Divider -->
              <div class="mx-auto mb-4 w-12 h-[3px] bg-neo-red" />

              <!-- Body text -->
              <p class="font-body text-sm text-neo-grey dark:text-gray-400 mb-7 leading-relaxed">
                This action cannot be undone. Your profile photo will be permanently deleted from the system.
              </p>

              <!-- CTA Buttons -->
              <div class="flex gap-3">
                <button
                  @click="showDeleteAlert = false"
                  class="flex-1 py-3 px-2 bg-white dark:bg-neo-dark-surface text-neo-black dark:text-white border-[3px] border-neo-black dark:border-white font-heading font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#000] dark:hover:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-100 flex items-center justify-center gap-1"
                >
                  <span class="material-symbols-outlined text-[14px]">close</span>
                  CANCEL
                </button>
                <button
                  @click="confirmDeleteAvatar"
                  class="flex-1 py-3 px-2 bg-neo-red text-white border-[3px] border-neo-black dark:border-white font-heading font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-[4px_4px_0px_#000] dark:shadow-[4px_4px_0px_rgba(255,255,255,0.8)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#000] dark:hover:shadow-[2px_2px_0px_rgba(255,255,255,0.8)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-100 flex items-center justify-center gap-1"
                >
                  <span class="material-symbols-outlined text-[14px]">delete</span>
                  DELETE
                </button>
              </div>
            </div>

            <!-- Bottom branding bar -->
            <div class="px-5 py-2 bg-gray-50 dark:bg-neo-dark-surface border-t-[3px] border-neo-black dark:border-white flex items-center justify-center gap-2">
              <span class="material-symbols-outlined text-neo-grey dark:text-gray-500 text-sm">warning</span>
              <span class="font-heading font-black text-[10px] uppercase tracking-[0.2em] text-neo-grey dark:text-gray-500">VUWOTING™ SYSTEM</span>
            </div>
          </div>

        </div>
      </Transition>
    </Teleport>

  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NeoCropper from '@/Components/NeoCropper.vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

defineProps({ mustVerifyEmail: Boolean, status: String });

// ─── Forms ────────────────────────────────────────────────────────────────────
const profileForm  = useForm({ name: user.value.name, email: user.value.email });
const passwordForm = useForm({ current_password: '', password: '', password_confirmation: '' });

const updateProfile = () => profileForm.patch(route('profile.update'), {
  preserveScroll: true,
});

// ─── Custom Alert State ───────────────────────────────────────────────────────
const showPasswordAlert = ref(false);

const updatePassword = () => passwordForm.put(route('password.update'), {
  preserveScroll: true,
  errorBag: 'updatePassword',
  onSuccess: () => {
    passwordForm.reset();
    showPasswordAlert.value = true;
  },
});

// ─── Cropper state ────────────────────────────────────────────────────────────
const showCropModal        = ref(false);
const imageSrc             = ref(null);
const fileInputRef         = ref(null);
const selectedOriginalFile = ref(null); // Keep original file for high-res edit option

// ─── Trigger File Selection programmatically ──────────────────────────────────
const triggerFileSelect = () => {
  fileInputRef.value?.click();
};

// ─── File picker → open cropper ───────────────────────────────────────────────
const onFileChange = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;
  e.target.value = '';
  selectedOriginalFile.value = file; // Preserve original file object

  const reader = new FileReader();
  reader.onload = (ev) => {
    imageSrc.value      = ev.target.result;
    showCropModal.value = true;
  };
  reader.readAsDataURL(file);
};

// ─── Edit existing avatar ─────────────────────────────────────────────────────
const editExistingAvatar = () => {
  if (!user.value.avatar) return;
  // If the high-res original uncropped photo is stored, use it so they can re-crop/re-zoom!
  imageSrc.value      = user.value.avatar_original || user.value.avatar;
  showCropModal.value = true;
};

// ─── Receive cropped file → upload ────────────────────────────────────────────
const onCropped = (file) => {
  const data = { image: file };
  if (selectedOriginalFile.value) {
    data.original_image = selectedOriginalFile.value;
  }

  router.post(
    route('profile.avatar.update'),
    data,
    {
      forceFormData:  true,
      preserveScroll: true,
      onSuccess: () => {
        selectedOriginalFile.value = null; // Clear on success
      },
      onError: (errors) => {
        console.error('Upload errors:', errors);
      },
    },
  );
};

// ─── Delete Avatar ────────────────────────────────────────────────────────────
const showDeleteAlert = ref(false);

const promptDeleteAvatar = () => {
  showDeleteAlert.value = true;
};

const confirmDeleteAvatar = () => {
  showDeleteAlert.value = false;
  router.delete(route('profile.avatar.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      // Flash message handled by backend
    }
  });
};
</script>