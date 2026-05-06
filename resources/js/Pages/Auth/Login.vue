<template>
  <GuestLayout title="MASUK" subtitle="Masukkan kredensial Anda untuk mengakses sistem.">
    <!-- Error Message -->
    <div v-if="$page.props.flash.error" class="bg-neo-red border-neo border-neo-black p-4 mb-6 shadow-neo animate-in fade-in slide-in-from-top-4 duration-300">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-white font-bold">warning</span>
        <p class="font-heading text-xs font-bold text-white uppercase tracking-wider">
          {{ $page.props.flash.error }}
        </p>
      </div>
    </div>

    <!-- Status Message -->
    <div v-if="status" class="bg-neo-yellow border-neo border-neo-black p-4 mb-6 shadow-neo">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-neo-black font-bold">info</span>
        <p class="font-heading text-xs font-bold text-neo-black uppercase tracking-wider">
          {{ status }}
        </p>
      </div>
    </div>

    <form @submit.prevent="submit" class="space-y-5">
      <!-- Email -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">EMAIL</label>
        <input v-model="form.email" type="email" class="neo-input" placeholder="nama@kosgoro.id" required autofocus />
        <div v-if="form.errors.email" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.email }}</div>
      </div>

      <!-- Password -->
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black mb-2">PASSWORD</label>
        <input v-model="form.password" type="password" class="neo-input" placeholder="••••••••" required />
        <div v-if="form.errors.password" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.password }}</div>
      </div>

      <!-- Remember + Forgot -->
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" v-model="form.remember" class="w-5 h-5 border-neo border-neo-black accent-neo-blue cursor-pointer" style="border-radius:0">
          <span class="font-heading text-xs font-bold uppercase">Ingat saya</span>
        </label>
        <Link v-if="canResetPassword" :href="route('password.request')" class="font-heading text-xs font-bold uppercase text-neo-blue hover:text-neo-red transition-colors">
          Lupa?
        </Link>
      </div>

      <!-- Submit -->
      <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-4 text-base" :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
        MASUK →
      </button>

      <!-- Register Link -->
      <div class="text-center pt-2">
        <span class="font-body text-sm text-neo-grey">Belum punya akun? </span>
        <Link :href="route('register')" class="font-heading text-sm font-bold text-neo-blue uppercase hover:text-neo-red transition-colors">
          DAFTAR →
        </Link>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>
