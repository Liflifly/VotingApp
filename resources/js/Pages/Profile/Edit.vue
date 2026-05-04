<template>
  <AuthenticatedLayout title="PROFIL">

    <!-- Page Header -->
    <div class="neo-page-header bg-white dark:bg-neo-dark-card mb-6 md:mb-8 shadow-neo dark:shadow-neo-white">
      <div class="absolute top-0 right-0 w-16 h-16 bg-neo-blue/10 border-l-2 border-b-2 border-neo-blue/20"></div>
      <div class="relative z-10 flex items-center gap-3">
        <span class="material-symbols-outlined text-neo-blue text-2xl md:text-3xl">manage_accounts</span>
        <div>
          <h1 class="font-heading font-black text-lg md:text-h1 uppercase">PENGATURAN PROFIL</h1>
          <p class="font-body text-xs text-neo-grey dark:text-gray-400">Identitas digital Anda dalam sistem KOSGORO™</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- LEFT: Identity Card -->
      <div class="lg:col-span-1">
        <div class="neo-card dark:bg-neo-dark-card dark:border-white dark:shadow-neo-white p-6 text-center relative overflow-hidden">
          <div class="absolute top-0 right-0 w-10 h-10 bg-neo-yellow border-l-2 border-b-2 border-neo-black dark:border-white"></div>

          <!-- Avatar with Upload Overlay -->
          <div class="relative group w-32 h-32 mx-auto mb-4">
            <div class="w-full h-full bg-neo-blue border-neo border-neo-black dark:border-white shadow-neo dark:shadow-neo-white flex items-center justify-center overflow-hidden">
              <img v-if="user.avatar" :src="user.avatar" class="w-full h-full object-cover" />
              <span v-else class="material-symbols-outlined text-white text-6xl">person</span>
            </div>
            
            <!-- Upload Trigger -->
            <label class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
              <input type="file" class="hidden" accept="image/*" @change="onFileChange" />
              <span class="material-symbols-outlined text-white text-3xl">add_a_photo</span>
            </label>
          </div>

          <div class="font-heading font-black text-lg uppercase truncate mb-1 dark:text-white">{{ user.name }}</div>
          <div class="font-body text-xs text-neo-grey dark:text-gray-400 mb-3 truncate">{{ user.email }}</div>

          <!-- Role Badge -->
          <div :class="[
            'inline-flex items-center gap-1.5 px-3 py-1 border-2 border-neo-black dark:border-white font-heading text-xs font-black uppercase tracking-wider mb-4',
            user.role === 'super_admin' ? 'bg-neo-red text-white' :
            user.role === 'admin' ? 'bg-neo-blue text-white' :
            'bg-neo-yellow text-neo-black'
          ]">
            <span class="material-symbols-outlined text-sm">
              {{ user.role === 'super_admin' ? 'shield' : user.role === 'admin' ? 'admin_panel_settings' : 'how_to_vote' }}
            </span>
            {{ user.role?.toUpperCase().replace('_', ' ') }}
          </div>

          <!-- NIS & Voting Status -->
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
              ]">
                {{ user.has_voted ? 'SUDAH' : 'BELUM' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: Forms -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Profile Info Form -->
        <div class="neo-card dark:bg-neo-dark-card dark:border-white dark:shadow-neo-white p-6 md:p-8 relative overflow-hidden">
          <div class="absolute top-0 right-0 px-4 py-1 bg-neo-black dark:bg-white text-white dark:text-neo-black font-heading font-black text-[10px] uppercase tracking-[0.2em]">
            01 / INFO
          </div>
          <h2 class="font-heading font-black text-h2 uppercase mb-6 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-neo-blue">badge</span>
            INFORMASI DASAR
          </h2>
          <form @submit.prevent="updateProfile">
            <div class="grid md:grid-cols-2 gap-5 mb-6">
              <div>
                <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">NAMA LENGKAP</label>
                <input v-model="profileForm.name" type="text" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" required />
                <p v-if="profileForm.errors.name" class="text-neo-red font-heading font-bold text-xs uppercase mt-1">{{ profileForm.errors.name }}</p>
              </div>
              <div>
                <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">ALAMAT EMAIL</label>
                <input v-model="profileForm.email" type="email" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" required />
                <p v-if="profileForm.errors.email" class="text-neo-red font-heading font-bold text-xs uppercase mt-1">{{ profileForm.errors.email }}</p>
              </div>
            </div>
            <div v-if="$page.props.flash?.status === 'profile-updated'" class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border-2 border-green-500 flex items-center gap-2">
              <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
              <span class="font-heading text-xs font-bold uppercase text-green-700 dark:text-green-400">Profil berhasil diperbarui!</span>
            </div>
            <button type="submit" :disabled="profileForm.processing" class="neo-btn bg-neo-blue text-white py-2.5 px-6 text-xs shadow-neo disabled:opacity-50">
              <span class="material-symbols-outlined text-base">save</span> SIMPAN PERUBAHAN
            </button>
          </form>
        </div>

        <!-- Password Form -->
        <div class="neo-card dark:bg-neo-dark-card dark:border-white dark:shadow-neo-white p-6 md:p-8 relative overflow-hidden">
          <div class="absolute top-0 right-0 px-4 py-1 bg-neo-yellow text-neo-black font-heading font-black text-[10px] uppercase tracking-[0.2em]">02 / KEAMANAN</div>
          <h2 class="font-heading font-black text-h2 uppercase mb-6 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-neo-yellow">lock</span> GANTI PASSWORD
          </h2>
          <form @submit.prevent="updatePassword">
            <div class="space-y-4 mb-6">
              <div>
                <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">PASSWORD SAAT INI</label>
                <input v-model="passwordForm.current_password" type="password" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" autocomplete="current-password" />
                <p v-if="passwordForm.errors.current_password" class="text-neo-red font-heading font-bold text-xs uppercase mt-1">{{ passwordForm.errors.current_password }}</p>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">PASSWORD BARU</label>
                  <input v-model="passwordForm.password" type="password" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" autocomplete="new-password" />
                  <p v-if="passwordForm.errors.password" class="text-neo-red font-heading font-bold text-xs uppercase mt-1">{{ passwordForm.errors.password }}</p>
                </div>
                <div>
                  <label class="block font-heading text-xs font-bold uppercase tracking-wider mb-2 dark:text-gray-300">KONFIRMASI PASSWORD</label>
                  <input v-model="passwordForm.password_confirmation" type="password" class="neo-input dark:bg-neo-dark-surface dark:text-white dark:border-white" autocomplete="new-password" />
                </div>
              </div>
            </div>
            <div v-if="$page.props.flash?.status === 'password-updated'" class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border-2 border-green-500 flex items-center gap-2">
              <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
              <span class="font-heading text-xs font-bold uppercase text-green-700 dark:text-green-400">Password berhasil diperbarui!</span>
            </div>
            <button type="submit" :disabled="passwordForm.processing" class="neo-btn bg-neo-yellow text-neo-black py-2.5 px-6 text-xs shadow-neo disabled:opacity-50">
              <span class="material-symbols-outlined text-base">key</span> GANTI PASSWORD
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- CROP MODAL -->
    <div v-if="showCropModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-neo-black/80 backdrop-blur-sm">
      <div class="neo-card w-full max-w-lg bg-white dark:bg-neo-dark-card border-neo border-neo-black dark:border-white shadow-neo-white">
        <div class="p-4 border-b-neo border-neo-black dark:border-white flex justify-between items-center bg-neo-blue text-white">
          <h3 class="font-heading font-black text-sm uppercase tracking-wider">SESUAIKAN FOTO PROFIL</h3>
          <button @click="cancelCrop" class="hover:rotate-90 transition-transform">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>
        <div class="p-6 bg-neo-surface dark:bg-neo-dark-surface">
          <div class="w-full aspect-square border-neo border-neo-black dark:border-white overflow-hidden bg-neo-black">
            <img ref="imageToCrop" :src="imageSrc" class="max-w-full block" />
          </div>
        </div>
        <div class="p-4 border-t-neo border-neo-black dark:border-white flex justify-end gap-3 bg-white dark:bg-neo-dark-card">
          <button @click="cancelCrop" class="neo-btn-secondary py-2 text-xs">BATAL</button>
          <button @click="cropImage" class="neo-btn-primary py-2 text-xs">POTONG & SIMPAN</button>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const props = defineProps({
  user: Object,
  mustVerifyEmail: Boolean,
  status: String,
});

const profileForm = useForm({
  name: props.user.name,
  email: props.user.email,
});

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const updateProfile = () => {
  profileForm.patch(route('profile.update'));
};

const updatePassword = () => {
  passwordForm.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => passwordForm.reset(),
  });
};

// CROPPER LOGIC
const showCropModal = ref(false);
const imageSrc = ref(null);
const imageToCrop = ref(null);
let cropper = null;

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (event) => {
    imageSrc.value = event.target.result;
    showCropModal.value = true;
    nextTick(() => {
      initCropper();
    });
  };
  reader.readAsDataURL(file);
};

const initCropper = () => {
  if (cropper) cropper.destroy();
  cropper = new Cropper(imageToCrop.value, {
    aspectRatio: 1,
    viewMode: 1,
    dragMode: 'move',
    autoCropArea: 1,
    restore: false,
    guides: true,
    center: true,
    highlight: false,
    cropBoxMovable: true,
    cropBoxResizable: true,
    toggleDragModeOnDblclick: false,
  });
};

const cancelCrop = () => {
  showCropModal.value = false;
  imageSrc.value = null;
  if (cropper) cropper.destroy();
};

const cropImage = () => {
  const canvas = cropper.getCroppedCanvas({
    width: 400,
    height: 400,
  });

  const croppedImage = canvas.toDataURL('image/jpeg');
  
  router.post(route('profile.avatar.update'), {
    image: croppedImage
  }, {
    onSuccess: () => {
      showCropModal.value = false;
    }
  });
};
</script>
