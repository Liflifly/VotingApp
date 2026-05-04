<template>
  <GuestLayout title="DAFTAR AKUN" subtitle="Buat akun baru untuk memasuki arena pemilihan.">
    <form @submit.prevent="submit" class="space-y-4">
      <!-- Name -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NAMA LENGKAP</label>
        <input v-model="form.name" type="text" class="neo-input" placeholder="Nama lengkapmu" required autofocus />
        <div v-if="form.errors.name" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.name }}</div>
      </div>

      <!-- Email -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">EMAIL</label>
        <input v-model="form.email" type="email" class="neo-input" placeholder="nama@kosgoro.id" required />
        <div v-if="form.errors.email" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.email }}</div>
      </div>

      <!-- NIS -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">NIS</label>
        <input v-model="form.nis" type="text" class="neo-input" placeholder="Nomor Induk Siswa" />
        <div v-if="form.errors.nis" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.nis }}</div>
      </div>

      <!-- Kelas -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">KELAS</label>
        <input v-model="form.kelas" type="text" class="neo-input" placeholder="Contoh: XII RPL 1" />
        <div v-if="form.errors.kelas" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.kelas }}</div>
      </div>

      <!-- Password -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">PASSWORD</label>
        <input v-model="form.password" type="password" class="neo-input" placeholder="Min. 8 karakter" required />
        <div v-if="form.errors.password" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.password }}</div>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">KONFIRMASI PASSWORD</label>
        <input v-model="form.password_confirmation" type="password" class="neo-input" placeholder="Ulangi password" required />
      </div>

      <!-- Submit -->
      <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-4 text-base mt-2" :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
        DAFTAR & MASUK ARENA →
      </button>

      <!-- Login Link -->
      <div class="text-center pt-2">
        <span class="font-body text-sm text-neo-grey">Sudah punya akun? </span>
        <Link :href="route('login')" class="font-heading text-sm font-bold text-neo-blue uppercase hover:text-neo-red transition-colors">
          MASUK →
        </Link>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  nis: '',
  kelas: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>
