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
                    blue:    '#0048FF',
                    yellow:  '#FFDE00',
                    red:     '#FF3C3C',
                    black:   '#000000',
                    surface: '#F9F9F9',
                    white:   '#FFFFFF',
                    grey:    '#555555',
                    'dark-bg':      '#111111',
                    'dark-card':    '#1a1a1a',
                    'dark-surface': '#222222',
                },
            },
            boxShadow: {
                'neo':        '4px 4px 0px #000000',
                'neo-sm':     '2px 2px 0px #000000',
                'neo-blue':   '4px 4px 0px #0048FF',
                'neo-yellow': '4px 4px 0px #FFDE00',
                'neo-red':    '4px 4px 0px #FF3C3C',
                'neo-hover':  '6px 6px 0px #000000',
                'neo-none':   '0px 0px 0px #000000',
                'neo-white':  '4px 4px 0px #FFFFFF',
            },
            borderWidth: {
                'neo': '3px',
            },
            borderRadius: {
                'neo': '0px',
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
