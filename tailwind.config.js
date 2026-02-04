import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                bg: "var(--color-bg)",
                accent: '#6d28d9',
                'accent-hover': '#5b21b6',
                'on-accent': '#fff',
                danger: '#dc2626',
                surface: "var(--color-surface)",
                border: "var(--color-border)",
                text: "var(--color-text)",
                muted: "var(--color-text-muted)",
            },
        },
    },

    plugins: [forms, typography],
};
