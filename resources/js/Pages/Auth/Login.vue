<template>
  <GuestLayout title="SIGN IN" subtitle="Enter your credentials to access your events.">
    <div v-if="$page.props.flash?.error" class="bg-neo-red border-neo border-neo-black p-4 mb-6 shadow-neo">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-white font-bold">warning</span>
        <p class="font-heading text-xs font-bold text-white uppercase tracking-wider">{{ $page.props.flash.error }}</p>
      </div>
    </div>
    <div v-if="status" class="bg-neo-yellow border-neo border-neo-black p-4 mb-6 shadow-neo">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-neo-black font-bold">info</span>
        <p class="font-heading text-xs font-bold text-neo-black uppercase tracking-wider">{{ status }}</p>
      </div>
    </div>

    <form @submit.prevent="submit" class="space-y-5">
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">EMAIL</label>
        <input v-model="form.email" type="email" class="neo-input" placeholder="you@example.com" required autofocus />
        <div v-if="form.errors.email" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.email }}</div>
      </div>
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">PASSWORD</label>
        <input v-model="form.password" type="password" class="neo-input" placeholder="••••••••" required />
        <div v-if="form.errors.password" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.password }}</div>
      </div>
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" v-model="form.remember" class="w-5 h-5 border-neo border-neo-black accent-neo-blue cursor-pointer" style="border-radius:0">
          <span class="font-heading text-xs font-bold uppercase dark:text-white">Remember me</span>
        </label>
        <Link v-if="canResetPassword" :href="route('password.request')" class="font-heading text-xs font-bold uppercase text-neo-blue hover:text-neo-red transition-colors">
          Forgot?
        </Link>
      </div>
      <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-4 text-base" :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
        SIGN IN →
      </button>
      <div class="text-center pt-2">
        <span class="font-body text-sm text-neo-grey">Don't have an account? </span>
        <Link :href="route('register')" class="font-heading text-sm font-bold text-neo-blue uppercase hover:text-neo-red transition-colors">REGISTER →</Link>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

defineProps({ canResetPassword: Boolean, status: String });

const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>
