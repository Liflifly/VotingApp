<template>
  <AuthenticatedLayout title="MY DASHBOARD">
      <!-- WELCOME + QUICK ACTIONS -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 md:mb-10">
        <div>
          <h1 class="font-heading font-black text-h1 md:text-display uppercase dark:text-white">
            MY <span class="text-neo-blue">DASHBOARD</span>
          </h1>
          <p class="font-body text-sm text-neo-grey dark:text-gray-400 mt-1">
            Welcome back, <strong>{{ $page.props.auth.user?.name }}</strong>
          </p>
        </div>

        <div class="flex gap-3">
          <Link href="/events/create" class="neo-btn-primary text-sm">
            + CREATE EVENT
          </Link>
          <button @click="showJoinInput = !showJoinInput" class="neo-btn-secondary text-sm">
            JOIN EVENT
          </button>
        </div>
      </div>

      <!-- JOIN EVENT INPUT -->
      <Transition enter-active-class="transition-all duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
        <div v-if="showJoinInput" class="neo-card p-4 mb-6 shadow-neo max-w-lg">
          <p class="font-heading text-xs font-bold uppercase mb-3 dark:text-white">Paste Voter/Admin Link or Event Slug</p>
          <div class="flex gap-2">
            <input
              v-model="joinInput"
              type="text"
              class="neo-input flex-1 text-sm"
              placeholder="e.g. https://vuwoting.com/join/v/abc... or my-event"
              @keydown.enter="handleJoin"
            />
            <button @click="handleJoin" class="neo-btn-primary px-4 py-2 text-sm whitespace-nowrap">GO →</button>
          </div>
        </div>
      </Transition>

      <!-- FLASH MESSAGES -->
      <div v-if="$page.props.flash?.success" class="neo-card border-neo-green bg-green-50 dark:bg-green-900/20 p-4 mb-6 flex items-center gap-3">
        <span class="material-symbols-outlined text-green-600">check_circle</span>
        <p class="font-body text-sm text-green-700 dark:text-green-400">{{ $page.props.flash.success }}</p>
      </div>

      <!-- TABS -->
      <div class="flex border-b-2 border-neo-black dark:border-white mb-6">
        <button
          @click="activeTab = 'created'"
          class="font-heading font-bold text-xs uppercase tracking-wider px-5 py-3 border-r-2 border-neo-black dark:border-white transition-colors"
          :class="activeTab === 'created' ? 'bg-neo-black dark:bg-white text-white dark:text-neo-black' : 'bg-white dark:bg-neo-dark-card text-neo-grey hover:bg-neo-yellow hover:text-neo-black'"
        >
          MY EVENTS ({{ createdEvents.length }})
        </button>
        <button
          @click="activeTab = 'joined'"
          class="font-heading font-bold text-xs uppercase tracking-wider px-5 py-3 transition-colors"
          :class="activeTab === 'joined' ? 'bg-neo-black dark:bg-white text-white dark:text-neo-black' : 'bg-white dark:bg-neo-dark-card text-neo-grey hover:bg-neo-yellow hover:text-neo-black'"
        >
          JOINED EVENTS ({{ joinedEvents.length }})
        </button>
      </div>

      <!-- TAB: CREATED EVENTS -->
      <div v-if="activeTab === 'created'">
        <div v-if="createdEvents.length === 0" class="neo-card p-10 text-center">
          <span class="material-symbols-outlined text-5xl text-neo-grey mb-4 block">add_circle</span>
          <h2 class="font-heading font-black text-h2 uppercase mb-2 dark:text-white">NO EVENTS YET</h2>
          <p class="font-body text-sm text-neo-grey mb-6">Create your first voting event and share it with your voters.</p>
          <Link href="/events/create" class="neo-btn-primary">CREATE YOUR FIRST EVENT →</Link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
          <div
            v-for="event in createdEvents"
            :key="event.id"
            class="neo-card p-5 hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all cursor-pointer relative overflow-hidden"
            @click="goToEventAdmin(event)"
          >
            <!-- Status badge -->
            <div class="absolute top-0 right-0">
              <div v-if="event.active_election" class="neo-badge-live text-[9px] px-2 py-1">● LIVE</div>
              <div v-else class="bg-gray-200 text-neo-grey text-[9px] font-heading font-bold uppercase px-2 py-1 border-l border-b border-neo-black">IDLE</div>
            </div>

            <div class="mb-3">
              <h3 class="font-heading font-black text-h2 uppercase dark:text-white pr-14 truncate">{{ event.name }}</h3>
              <p class="font-body text-xs text-neo-grey mt-1">/e/{{ event.slug }}</p>
            </div>

            <div class="flex gap-2 mb-4">
              <div class="flex-1 bg-neo-surface dark:bg-neo-dark-bg border border-neo-black/20 p-2 text-center">
                <div class="font-heading font-black text-lg text-neo-blue">{{ event.voter_count }}</div>
                <div class="font-heading text-[9px] font-bold uppercase text-neo-grey">VOTERS</div>
              </div>
              <div class="flex-1 bg-neo-surface dark:bg-neo-dark-bg border border-neo-black/20 p-2 text-center">
                <div class="font-heading font-black text-xs text-neo-grey uppercase mt-1">{{ event.results_visibility }}</div>
                <div class="font-heading text-[9px] font-bold uppercase text-neo-grey">RESULTS</div>
              </div>
            </div>

            <div class="flex gap-2">
              <Link
                :href="`/e/${event.slug}/admin/settings`"
                class="neo-btn-sm-secondary text-[10px] flex-1 text-center"
                @click.stop
              >
                SETTINGS
              </Link>
              <Link
                :href="`/e/${event.slug}/admin/elections`"
                class="neo-btn-primary text-[10px] flex-1 text-center"
                @click.stop
              >
                MANAGE →
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: JOINED EVENTS -->
      <div v-if="activeTab === 'joined'">
        <div v-if="joinedEvents.length === 0" class="neo-card p-10 text-center">
          <span class="material-symbols-outlined text-5xl text-neo-grey mb-4 block">how_to_vote</span>
          <h2 class="font-heading font-black text-h2 uppercase mb-2 dark:text-white">NOT IN ANY EVENT</h2>
          <p class="font-body text-sm text-neo-grey">Ask an event organizer for the voter link or scan their QR code to join.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
          <div
            v-for="event in joinedEvents"
            :key="event.id"
            class="neo-card p-5 hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all cursor-pointer relative overflow-hidden"
            @click="router.visit(`/e/${event.slug}/dashboard`)"
          >
            <!-- Role badge -->
            <div class="absolute top-0 right-0">
              <div
                class="text-[9px] font-heading font-bold uppercase px-2 py-1 border-l border-b border-neo-black"
                :class="event.role === 'admin' ? 'bg-neo-yellow text-neo-black' : 'bg-white text-neo-grey'"
              >
                {{ event.role.toUpperCase() }}
              </div>
            </div>

            <div class="mb-3">
              <h3 class="font-heading font-black text-h2 uppercase dark:text-white pr-14 truncate">{{ event.name }}</h3>
              <p class="font-body text-xs text-neo-grey mt-1">/e/{{ event.slug }}</p>
            </div>

            <div class="flex items-center gap-2 mb-4">
              <div v-if="event.active_election" class="flex items-center gap-2 text-xs">
                <div class="w-2 h-2 bg-neo-blue rounded-full animate-pulse"></div>
                <span class="font-heading font-bold text-neo-blue uppercase">{{ event.active_election.name }} — LIVE</span>
              </div>
              <div v-else class="font-heading text-xs text-neo-grey uppercase">No active election</div>
            </div>

            <Link
              :href="`/e/${event.slug}/dashboard`"
              class="neo-btn-primary text-[10px] w-full text-center block"
              @click.stop
            >
              GO TO EVENT →
            </Link>
          </div>
        </div>
      </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useDarkMode } from '@/Composables/useDarkMode.js';

const props = defineProps({
  createdEvents: { type: Array, default: () => [] },
  joinedEvents:  { type: Array, default: () => [] },
});

const { isDark, toggle: toggleDark } = useDarkMode();

const activeTab    = ref(props.createdEvents.length > 0 ? 'created' : 'joined');
const showJoinInput = ref(false);
const joinInput    = ref('');

const handleJoin = () => {
  const val = joinInput.value.trim();
  if (!val) return;

  // Detect if it's a full URL (join link)
  if (val.includes('/join/v/') || val.includes('/join/a/')) {
    const path = new URL(val, window.location.origin).pathname;
    router.visit(path);
    return;
  }

  // Otherwise treat as event slug
  const slug = val.toLowerCase().replace(/[^a-z0-9-]/g, '-');
  router.visit(`/e/${slug}`);
};

const goToEventAdmin = (event) => {
  router.visit(`/e/${event.slug}/admin/elections`);
};
</script>
