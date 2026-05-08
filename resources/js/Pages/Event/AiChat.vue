<template>
  <AuthenticatedLayout :title="`AI ASSISTANT — ${election.name}`">
    <div class="max-w-2xl mx-auto">

      <!-- Header -->
      <div class="neo-card bg-neo-blue p-5 mb-5 relative overflow-hidden neo-scanline">
        <div class="absolute top-0 right-0 w-16 h-16 bg-neo-yellow border-l-neo border-b-neo border-neo-black"></div>
        <div class="relative z-10">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-white border-neo border-neo-black flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-neo-blue text-xl">psychology</span>
            </div>
            <div>
              <div class="font-heading text-[9px] font-bold uppercase text-blue-200">AI VOTING ASSISTANT</div>
              <h1 class="font-heading font-black text-h2 uppercase text-white">{{ election.name }}</h1>
            </div>
          </div>
          <p class="font-body text-xs text-blue-100">
            Describe your needs or priorities and the AI will recommend a candidate. Your conversation is private and not stored.
          </p>
        </div>
      </div>

      <!-- Candidates Quick View -->
      <div class="neo-card p-4 mb-5">
        <div class="font-heading text-[9px] font-bold uppercase text-neo-grey mb-3">
          {{ candidates.length }} CANDIDATES IN THIS ELECTION
        </div>
        <div class="flex flex-wrap gap-2">
          <div
            v-for="candidate in candidates"
            :key="candidate.id"
            class="flex items-center gap-2 border-2 border-neo-black/20 px-2 py-1.5 text-xs"
          >
            <img
              v-if="candidate.photo_url"
              :src="candidate.photo_url"
              class="w-6 h-6 object-cover border border-neo-black/20 rounded-full"
            />
            <span class="font-heading font-bold text-xs dark:text-white uppercase">
              {{ getFirstField(candidate.fields) || `#${candidate.order_number}` }}
            </span>
          </div>
        </div>
      </div>

      <!-- Chat Window -->
      <div class="neo-card overflow-hidden mb-4">
        <!-- Messages -->
        <div
          ref="chatContainer"
          class="p-4 space-y-4 overflow-y-auto"
          style="min-height: 300px; max-height: 400px;"
        >
          <!-- Empty state -->
          <div v-if="messages.length === 0" class="text-center py-8">
            <span class="material-symbols-outlined text-5xl text-neo-grey/40 block mb-3">psychology</span>
            <p class="font-body text-sm text-neo-grey">
              Start by telling the AI what matters most to you in a candidate.<br>
              <span class="text-xs">e.g. "I want someone who is experienced in finance and has strong leadership"</span>
            </p>
          </div>

          <!-- Messages -->
          <div v-for="(msg, i) in messages" :key="i" class="flex" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
            <div
              class="max-w-[85%] p-3 border-2"
              :class="msg.role === 'user'
                ? 'bg-neo-blue text-white border-neo-black'
                : 'bg-white dark:bg-neo-dark-card border-neo-black dark:border-white'"
            >
              <!-- AI avatar -->
              <div v-if="msg.role === 'assistant'" class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-sm text-neo-blue">psychology</span>
                <span class="font-heading text-[9px] font-bold uppercase text-neo-grey">AI ASSISTANT</span>
              </div>
              <p class="font-body text-sm whitespace-pre-wrap dark:text-white" :class="msg.role === 'user' ? 'text-white' : ''">{{ msg.content }}</p>
            </div>
          </div>

          <!-- Loading indicator -->
          <div v-if="loading" class="flex justify-start">
            <div class="border-2 border-neo-black p-3 bg-white dark:bg-neo-dark-card">
              <div class="flex gap-1.5 items-center">
                <span class="material-symbols-outlined text-sm text-neo-blue animate-spin">autorenew</span>
                <span class="font-heading text-[9px] font-bold uppercase text-neo-grey">AI IS THINKING...</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Input Area -->
        <div class="border-t-2 border-neo-black p-3 flex gap-2">
          <textarea
            v-model="userInput"
            class="neo-input flex-1 resize-none text-sm"
            style="min-height: 44px; max-height: 120px;"
            placeholder="Ask the AI about candidates..."
            @keydown.enter.exact.prevent="sendMessage"
            @keydown.enter.shift.exact="userInput += '\n'"
            :disabled="loading"
          ></textarea>
          <button
            @click="sendMessage"
            :disabled="loading || !userInput.trim()"
            class="neo-btn-primary px-4 py-2 shrink-0 self-end"
            :class="{ 'opacity-50 cursor-not-allowed': loading || !userInput.trim() }"
          >
            <span class="material-symbols-outlined text-base">send</span>
          </button>
        </div>
      </div>

      <!-- Disclaimer -->
      <div class="flex items-start gap-2 mb-4">
        <span class="material-symbols-outlined text-sm text-neo-grey shrink-0 mt-0.5">info</span>
        <p class="font-body text-xs text-neo-grey">
          AI recommendations are based on candidate information only. They are suggestions — the final vote is yours.
          Press <kbd class="border border-neo-black/30 px-1 rounded text-[10px]">Enter</kbd> to send, <kbd class="border border-neo-black/30 px-1 rounded text-[10px]">Shift+Enter</kbd> for new line.
        </p>
      </div>

      <!-- Error -->
      <div v-if="error" class="neo-card p-3 bg-neo-red/10 border-2 border-neo-red mb-4">
        <p class="font-body text-sm text-neo-red">{{ error }}</p>
      </div>

      <!-- Navigation -->
      <div class="flex gap-3">
        <Link :href="`/e/${$page.props.currentEvent?.slug}/dashboard`" class="neo-btn-secondary text-sm flex-1 text-center">
          ← BACK TO DASHBOARD
        </Link>
        <Link :href="`/e/${$page.props.currentEvent?.slug}/vote`" class="neo-btn-primary text-sm flex-1 text-center">
          CAST YOUR VOTE →
        </Link>
      </div>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
  event:           { type: Object, required: true },
  election:        { type: Object, required: true },
  candidates:      { type: Array, default: () => [] },
  candidateFields: { type: Array, default: () => [] },
});

const messages     = ref([]);
const userInput    = ref('');
const loading      = ref(false);
const error        = ref('');
const chatContainer = ref(null);

const getFirstField = (fields) => {
  if (!fields) return '';
  const data = typeof fields === 'string' ? JSON.parse(fields) : fields;
  return Object.values(data)[0] || '';
};

const scrollToBottom = async () => {
  await nextTick();
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
  }
};

const sendMessage = async () => {
  const msg = userInput.value.trim();
  if (!msg || loading.value) return;

  messages.value.push({ role: 'user', content: msg });
  userInput.value = '';
  loading.value   = true;
  error.value     = '';
  await scrollToBottom();

  // Build conversation history (exclude last user msg — it's in the payload separately)
  const history = messages.value.slice(0, -1).map(m => ({
    role: m.role, content: m.content,
  }));

  try {
    const resp = await axios.post(`/e/${props.event.slug}/ai/recommend`, {
      message:      msg,
      conversation: history,
    });

    messages.value.push({ role: 'assistant', content: resp.data.response });
  } catch (err) {
    const errMsg = err.response?.data?.error || 'Something went wrong. Please try again.';
    error.value = errMsg;
    // Remove the user message if AI failed
    messages.value.pop();
  } finally {
    loading.value = false;
    await scrollToBottom();
  }
};
</script>
