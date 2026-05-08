import { useDark, useToggle } from '@vueuse/core';

/**
 * @vueuse/core-powered dark mode composable.
 * Replaces the manual localStorage + classList approach.
 * - Persists via localStorage key 'vuwoting-dark'
 * - Respects system prefers-color-scheme on first visit
 * - Applies 'dark' class on <html>
 */
const isDark = useDark({
    storageKey: 'vuwoting-dark',
    valueDark: 'dark',
    valueLight: '',
    attribute: 'class',
    selector: 'html',
});

const toggle = useToggle(isDark);

export function useDarkMode() {
    return { isDark, toggle };
}
