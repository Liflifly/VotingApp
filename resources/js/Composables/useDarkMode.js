import { ref, watch } from 'vue';

const STORAGE_KEY = 'kosgoro-dark';
const isDark = ref(false);

// Function to update the DOM
const updateDOM = (val) => {
    if (val) {
        document.documentElement.classList.add('dark');
        localStorage.setItem(STORAGE_KEY, '1');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem(STORAGE_KEY, '0');
    }
};

// Initialize early
const saved = localStorage.getItem(STORAGE_KEY);
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

if (saved === '1' || (saved === null && prefersDark)) {
    isDark.value = true;
    document.documentElement.classList.add('dark');
}

watch(isDark, (val) => {
    updateDOM(val);
});

export function useDarkMode() {
    const toggle = () => {
        isDark.value = !isDark.value;
    };

    return { isDark, toggle };
}
