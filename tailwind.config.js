import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        // Override screens to use standard breakpoints
        screens: {
            'sm': '640px',
            'md': '768px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
        },
        extend: {
            fontFamily: {
                heading: ['"Space Grotesk"', ...defaultTheme.fontFamily.sans],
                body: ['"Work Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"Space Grotesk"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                neo: {
                    blue:    'var(--theme-primary)',
                    yellow:  'var(--theme-secondary)',
                    red:     'var(--theme-accent)',
                    black:   'var(--theme-text)',
                    surface: 'var(--theme-surface)',
                    white:   'var(--theme-card)',
                    grey:    'var(--theme-muted)',
                    'dark-bg':      'var(--theme-dark-bg)',
                    'dark-card':    'var(--theme-dark-card)',
                    'dark-surface': 'var(--theme-dark-surface)',
                },
            },
            boxShadow: {
                'neo':        'var(--theme-shadow-x) var(--theme-shadow-y) 0px var(--theme-shadow-color)',
                'neo-sm':     'calc(var(--theme-shadow-x) / 2) calc(var(--theme-shadow-y) / 2) 0px var(--theme-shadow-color)',
                'neo-blue':   'var(--theme-shadow-x) var(--theme-shadow-y) 0px var(--theme-primary)',
                'neo-yellow': 'var(--theme-shadow-x) var(--theme-shadow-y) 0px var(--theme-secondary)',
                'neo-red':    'var(--theme-shadow-x) var(--theme-shadow-y) 0px var(--theme-accent)',
                'neo-hover':  'calc(var(--theme-shadow-x) * 1.5) calc(var(--theme-shadow-y) * 1.5) 0px var(--theme-shadow-color)',
                'neo-none':   '0px 0px 0px transparent',
                'neo-white':  'var(--theme-shadow-x) var(--theme-shadow-y) 0px var(--theme-card)',
            },
            borderWidth: {
                'neo': 'var(--theme-border-width)',
            },
            borderRadius: {
                'neo': 'var(--theme-radius)',
            },
            fontSize: {
                // Responsive-first scale — smaller base values
                'display': ['40px',  { lineHeight: '1.0',  letterSpacing: '-0.02em', fontWeight: '900' }],
                'h1':      ['28px',  { lineHeight: '1.1',  letterSpacing: '-0.01em', fontWeight: '800' }],
                'h2':      ['20px',  { lineHeight: '1.2',  fontWeight: '800' }],
                'body-lg': ['17px',  { lineHeight: '1.6',  fontWeight: '600' }],
                'body-md': ['15px',  { lineHeight: '1.5',  fontWeight: '500' }],
                'label-caps': ['12px', { lineHeight: '1.0', letterSpacing: '0.05em', fontWeight: '800' }],
                'stats':   ['28px',  { lineHeight: '1.0',  fontWeight: '700' }],
            },
            spacing: {
                'neo-xs': '4px',
                'neo-sm': '8px',
                'neo-md': '16px',
                'neo-lg': '24px',
                'neo-xl': '32px',
            },
            maxWidth: {
                'content': '1200px',
            },
        },
    },

    plugins: [forms],
};
