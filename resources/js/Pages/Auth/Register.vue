<template>
  <GuestLayout title="CREATE ACCOUNT" subtitle="Register to create or join a voting event.">
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">FULL NAME</label>
        <input v-model="form.name" type="text" class="neo-input" placeholder="Your full name" required autofocus />
        <div v-if="form.errors.name" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.name }}</div>
      </div>
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">EMAIL</label>
        <input v-model="form.email" type="email" class="neo-input" placeholder="you@example.com" required />
        <div v-if="form.errors.email" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.email }}</div>
      </div>
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">PASSWORD</label>
        <input v-model="form.password" type="password" class="neo-input" placeholder="Min. 8 characters" required />
        <div v-if="form.errors.password" class="font-body text-xs text-neo-red mt-1.5 font-semibold">{{ form.errors.password }}</div>
      </div>
      <div>
        <label class="block font-heading text-label-caps uppercase text-neo-black dark:text-white mb-2">CONFIRM PASSWORD</label>
        <input v-model="form.password_confirmation" type="password" class="neo-input" placeholder="Repeat password" required />
      </div>
      <button type="submit" :disabled="form.processing" class="neo-btn-primary w-full py-4 text-base mt-2" :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
        REGISTER & CREATE EVENT →
      </button>
      <div class="text-center pt-2">
        <span class="font-body text-sm text-neo-grey">Already have an account? </span>
        <Link :href="route('login')" class="font-heading text-sm font-bold text-neo-blue uppercase hover:text-neo-red transition-colors">SIGN IN →</Link>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const submit = () => form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>
