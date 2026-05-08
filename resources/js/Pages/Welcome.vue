<template>
  <div class="min-h-screen bg-neo-surface dark:bg-neo-dark-bg flex flex-col">

    <!-- NAVBAR -->
    <nav class="sticky top-0 z-50 bg-white dark:bg-neo-dark-card border-b-neo border-neo-black dark:border-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <div class="font-heading font-black text-xl tracking-tight dark:text-white">
          KOSGORO<span class="text-neo-blue">&#8482;</span>
        </div>
        <div class="hidden md:flex items-center gap-1">
          <a href="#about" class="neo-btn-sm-secondary">TENTANG</a>
          <a href="#features" class="neo-btn-sm-secondary">FITUR</a>
          <a href="#faq" class="neo-btn-sm-secondary">FAQ</a>
        </div>
        <div class="flex items-center gap-2">
          <!-- Dark mode toggle -->
          <button @click="toggleDark()" class="w-10 h-10 border-2 border-neo-black dark:border-white flex items-center justify-center hover:bg-neo-yellow dark:hover:bg-neo-yellow dark:hover:text-neo-black transition-colors dark:text-white">
            <span class="material-symbols-outlined text-base">{{ isDark ? 'light_mode' : 'dark_mode' }}</span>
          </button>
          <Link :href="route('login')" class="neo-btn-primary text-xs py-2.5 px-4 sm:px-6">
            MASUK &#8594;
          </Link>
        </div>
      </div>
    </nav>

    <!-- TICKER BAR -->
    <div class="neo-ticker-bar">
      <div class="neo-ticker-content">
        <template v-for="n in 4" :key="n">
          <span class="neo-ticker-item">
            <span class="neo-ticker-dot"></span>
            KOSGORO™ VOTING SYSTEM
          </span>
          <span class="neo-ticker-item">
            <span class="neo-ticker-dot"></span>
            PERIODE PEMILIHAN AKTIF
          </span>
          <span class="neo-ticker-item">
            <span class="neo-ticker-dot"></span>
            SUARAMU MENENTUKAN PEMIMPIN
          </span>
          <span class="neo-ticker-item">
            <span class="neo-ticker-dot"></span>
            TRANSPARANSI ABSOLUT
          </span>
        </template>
      </div>
    </div>

    <!-- HERO -->
    <section class="py-16 md:py-20 lg:py-28 px-4 sm:px-6 neo-crosshatch relative overflow-hidden">
      <!-- Decorative corner blocks -->
      <div class="absolute top-0 right-0 w-24 md:w-40 h-24 md:h-40 bg-neo-yellow border-l-neo border-b-neo border-neo-black"></div>
      <div class="absolute bottom-0 left-0 w-20 md:w-32 h-20 md:h-32 bg-neo-red border-r-neo border-t-neo border-neo-black"></div>
      <div class="absolute top-20 left-0 w-12 h-12 bg-neo-blue/20 border-r-2 border-b-2 border-neo-blue/30 hidden lg:block"></div>
      <div class="absolute bottom-20 right-0 w-16 h-16 bg-neo-yellow/20 border-l-2 border-t-2 border-neo-yellow/30 hidden lg:block"></div>

      <div class="max-w-5xl mx-auto text-center relative z-10">
        <!-- Status Ticker -->
        <div v-if="activeElection" class="inline-flex items-center gap-2 neo-badge-live shadow-neo-sm mb-6 md:mb-8 neo-pulse">
          <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
          PEMILIHAN AKTIF
        </div>
        <div v-else class="inline-flex items-center gap-2 bg-gray-200 border-2 border-neo-black text-neo-grey px-4 py-1.5 shadow-neo-sm mb-6 md:mb-8 font-heading font-black text-xs md:text-sm uppercase tracking-wider">
          <div class="w-2 h-2 bg-neo-grey rounded-full"></div>
          BELUM ADA PEMILIHAN
        </div>

        <h1 class="font-heading font-black text-[36px] sm:text-[48px] md:text-display uppercase leading-[1.0] tracking-tight text-neo-black dark:text-white mb-4 md:mb-6">
          SUARAMU<br>
          <span class="text-neo-blue">MENENTUKAN</span><br>
          PEMIMPIN KITA
        </h1>

        <p class="font-body text-base md:text-body-lg text-neo-grey dark:text-gray-400 max-w-2xl mx-auto mb-8 md:mb-10 px-2">
          Sistem pemilihan digital terpercaya. Pilih kandidat Anda, pantau pergerakan suara secara real-time, dan tentukan masa depan hari ini.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-4 sm:px-0">
          <Link :href="route('login')" class="neo-btn-primary">
            MASUK KE SISTEM →
          </Link>
          <Link :href="route('register')" class="neo-btn-secondary bg-white hover:bg-neo-yellow text-neo-black">
            DAFTAR BARU
          </Link>
        </div>
      </div>
    </section>

    <!-- STRIPE DIVIDER -->
    <div class="neo-stripe-divider"></div>

    <!-- STATS SECTION -->
    <section id="about" class="py-12 md:py-16 px-4 sm:px-6 neo-dotgrid">
      <div class="max-w-5xl mx-auto">
        <h2 class="font-heading font-black text-h2 md:text-h1 uppercase text-center mb-8 md:mb-12">
          SISTEM <span class="text-neo-blue">DALAM ANGKA</span>
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6">
          <div class="neo-stat-card">
            <div class="neo-stat-value text-neo-blue">{{ totalUsers || 0 }}</div>
            <div class="neo-stat-label">PEMILIH TERDAFTAR</div>
          </div>
          <div class="neo-stat-card">
            <div class="neo-stat-value text-neo-black">99.9%</div>
            <div class="neo-stat-label">UPTIME SISTEM</div>
          </div>
          <div class="neo-stat-card">
            <div class="neo-stat-value text-neo-red">&lt;1s</div>
            <div class="neo-stat-label">WAKTU RESPON</div>
          </div>
          <div class="neo-stat-card">
            <div class="neo-stat-value text-neo-blue">100%</div>
            <div class="neo-stat-label">TERENKRIPSI</div>
          </div>
        </div>
      </div>
    </section>

    <!-- STRIPE DIVIDER -->
    <div class="neo-stripe-divider"></div>

    <!-- FEATURES: SISTEM POWER-UP -->
    <section id="features" class="py-12 md:py-16 px-4 sm:px-6 neo-gridlines relative overflow-hidden">
      <!-- Decorative blocks -->
      <div class="absolute top-0 left-0 w-16 h-16 bg-neo-blue/10 border-r-2 border-b-2 border-neo-blue/20 hidden lg:block"></div>
      <div class="absolute bottom-0 right-0 w-20 h-20 bg-neo-red/10 border-l-2 border-t-2 border-neo-red/20 hidden lg:block"></div>

      <div class="max-w-5xl mx-auto relative z-10">
        <h2 class="font-heading font-black text-h2 md:text-h1 uppercase text-center mb-8 md:mb-12">
          SISTEM <span class="text-neo-blue">POWER-UP</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
          <!-- Feature 1 -->
          <div class="neo-card p-5 md:p-6 hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all relative overflow-hidden">
            <div class="absolute top-0 right-0 w-8 h-8 bg-neo-blue/10 border-l border-b border-neo-blue/20"></div>
            <div class="w-12 h-12 md:w-14 md:h-14 bg-neo-blue border-neo border-neo-black flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-white text-xl md:text-2xl">shield</span>
            </div>
            <h3 class="font-heading font-black text-lg md:text-h2 uppercase mb-2">KEAMANAN BESI</h3>
            <p class="font-body text-sm md:text-body-md text-neo-grey">
              Enkripsi tingkat militer menjaga setiap vote tetap anonim dan tidak dapat dimanipulasi.
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="neo-card p-5 md:p-6 hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all relative overflow-hidden">
            <div class="absolute top-0 right-0 w-8 h-8 bg-neo-yellow/20 border-l border-b border-neo-yellow/30"></div>
            <div class="w-12 h-12 md:w-14 md:h-14 bg-neo-yellow border-neo border-neo-black flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-neo-black text-xl md:text-2xl">bolt</span>
            </div>
            <h3 class="font-heading font-black text-lg md:text-h2 uppercase mb-2">LIVE STATS</h3>
            <p class="font-body text-sm md:text-body-md text-neo-grey">
              Pantau pergerakan suara secepat kilat. Dashboard analitik real-time memberikan transparansi total.
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="neo-card p-5 md:p-6 hover:shadow-neo-hover hover:-translate-x-[1px] hover:-translate-y-[1px] transition-all relative overflow-hidden">
            <div class="absolute top-0 right-0 w-8 h-8 bg-neo-red/10 border-l border-b border-neo-red/20"></div>
            <div class="w-12 h-12 md:w-14 md:h-14 bg-neo-red border-neo border-neo-black flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-white text-xl md:text-2xl">emoji_events</span>
            </div>
            <h3 class="font-heading font-black text-lg md:text-h2 uppercase mb-2">LEADERBOARD</h3>
            <p class="font-body text-sm md:text-body-md text-neo-grey">
              Sistem pemeringkatan kandidat terintegrasi. Lihat siapa yang memimpin dalam periode pemilihan.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- STRIPE DIVIDER -->
    <div class="neo-stripe-divider"></div>

    <!-- FAQ SECTION -->
    <section id="faq" class="py-12 md:py-16 px-4 sm:px-6 neo-dotgrid">
      <div class="max-w-3xl mx-auto">
        <h2 class="font-heading font-black text-h2 md:text-h1 uppercase text-center mb-8 md:mb-12">
          <span class="text-neo-blue">FAQ</span> — PERTANYAAN UMUM
        </h2>

        <div class="space-y-3 md:space-y-4">
          <div v-for="(faq, i) in faqs" :key="i" class="neo-card overflow-hidden">
            <button @click="toggleFaq(i)" class="w-full p-4 md:p-5 flex items-center justify-between text-left hover:bg-neo-yellow/5 transition-colors">
              <span class="font-heading font-bold text-xs md:text-sm uppercase tracking-wider pr-4 dark:text-white">{{ faq.q }}</span>
              <span class="material-symbols-outlined text-neo-blue shrink-0 transition-transform duration-200" :class="openFaq === i ? 'rotate-45' : ''">add</span>
            </button>
            <div v-if="openFaq === i" class="px-4 md:px-5 pb-4 md:pb-5 border-t-2 border-dashed border-gray-200 dark:border-gray-700">
              <p class="font-body text-sm text-neo-grey dark:text-gray-400 pt-3 md:pt-4 leading-relaxed">{{ faq.a }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- STRIPE DIVIDER -->
    <div class="neo-stripe-divider"></div>

    <!-- CTA SECTION -->
    <section class="py-12 md:py-16 px-4 sm:px-6 bg-neo-blue relative overflow-hidden neo-scanline">
      <div class="absolute top-0 right-0 w-24 md:w-40 h-24 md:h-40 bg-neo-yellow border-l-neo border-b-neo border-neo-black"></div>
      <div class="absolute bottom-0 left-0 w-16 md:w-24 h-16 md:h-24 bg-neo-red/30 border-r-2 border-t-2 border-neo-red/40"></div>
      
      <div class="max-w-3xl mx-auto text-center relative z-10">
        <h2 class="font-heading font-black text-[28px] sm:text-[36px] md:text-[48px] text-white uppercase leading-[1.1] mb-4 md:mb-6">
          SIAP MENENTUKAN<br>MASA DEPAN?
        </h2>
        <p class="font-body text-sm md:text-base text-blue-100 mb-6 md:mb-8 max-w-lg mx-auto">
          Bergabunglah dengan ribuan pemilih yang sudah mempercayakan suaranya melalui sistem kami.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <Link :href="route('register')" class="neo-btn bg-neo-yellow text-neo-black py-3 sm:py-4 px-8 sm:px-10 text-sm sm:text-lg shadow-neo">
            DAFTAR SEKARANG &#8594;
          </Link>
          <Link :href="route('login')" class="neo-btn bg-white text-neo-black py-3 sm:py-4 px-8 sm:px-10 text-sm sm:text-lg shadow-neo hover:bg-neo-yellow">
            SUDAH PUNYA AKUN
          </Link>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="mt-auto bg-neo-black text-white border-t-neo border-neo-black">
      <div class="neo-stripe-thin"></div>
      <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 md:py-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="text-center md:text-left">
            <div class="font-heading font-black text-lg">KOSGORO<span class="text-neo-yellow">™</span></div>
            <p class="font-body text-xs text-gray-400 mt-1">
              Sistem pemilihan masa depan. Didesain untuk transparansi absolut.
            </p>
          </div>
          <div class="flex items-center gap-2 md:gap-3">
            <a href="#" class="neo-btn-secondary text-[10px] py-1.5 px-3 bg-neo-black text-white border-white hover:bg-neo-yellow hover:text-neo-black hover:border-neo-black">PRIVACY</a>
            <a href="#" class="neo-btn-secondary text-[10px] py-1.5 px-3 bg-neo-black text-white border-white hover:bg-neo-yellow hover:text-neo-black hover:border-neo-black">TERMS</a>
          </div>
        </div>
        <div class="mt-4 md:mt-6 pt-4 border-t border-gray-800 text-center">
          <span class="font-heading text-[10px] font-bold text-gray-600 uppercase tracking-[0.2em]">
            © 2026 KOSGORO™ • ALL RIGHTS RESERVED • POWERED BY NEOBRUTALISM
          </span>
        </div>
      </div>
    </footer>
    <NeoToast />
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import NeoToast from '@/Components/NeoToast.vue';
import { useDarkMode } from '@/Composables/useDarkMode.js';

const props = defineProps({
  activeElection: Object,
  totalUsers: Number,
});

const { isDark, toggle: toggleDark } = useDarkMode();
const openFaq = ref(null);

const toggleFaq = (index) => {
  openFaq.value = openFaq.value === index ? null : index;
};

const faqs = [
  {
    q: 'BAGAIMANA CARA MENGGUNAKAN HAK PILIH?',
    a: 'Login dengan akun yang sudah terdaftar, masuk ke halaman "Live Ballots", pilih kandidat yang kamu dukung, lalu konfirmasi pilihanmu. Setiap akun hanya bisa memberikan 1 suara.'
  },
  {
    q: 'APAKAH SUARA SAYA AMAN DAN RAHASIA?',
    a: 'Ya, sistem kami menggunakan enkripsi end-to-end. Identitas pemilih dan pilihan yang dibuat dijaga kerahasiaannya secara ketat. Tidak ada yang bisa melihat siapa memilih siapa.'
  },
  {
    q: 'BISAKAH SAYA MENGUBAH PILIHAN SETELAH SUBMIT?',
    a: 'Tidak. Demi menjaga integritas pemilihan, suara yang sudah dikonfirmasi bersifat final dan tidak dapat diubah. Pastikan pilihanmu sudah tepat sebelum konfirmasi.'
  },
  {
    q: 'KAPAN HASIL PEMILIHAN DIUMUMKAN?',
    a: 'Hasil pemilihan dapat dilihat secara real-time di halaman "Voter Analytics" setelah periode pemilihan berakhir. Admin akan mengumumkan hasil resmi melalui saluran komunikasi sekolah.'
  },
  {
    q: 'SAYA LUPA PASSWORD, BAGAIMANA?',
    a: 'Gunakan fitur "Lupa Password" di halaman login untuk mereset password melalui email yang terdaftar. Jika masih bermasalah, hubungi admin sekolah.'
  },
];
</script>
