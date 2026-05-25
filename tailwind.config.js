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
        './app/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                bg: "var(--color-bg)",
                'bg-elevated': "var(--color-bg-elevated)",
                accent: "var(--color-accent)",
                'accent-hover': "var(--color-accent-hover)",
                'on-accent': "var(--color-on-accent)",
                hover: "var(--color-hover)",
                danger: "var(--color-danger)",
                'danger-soft': "var(--color-danger-soft)",
                'danger-solid': "var(--color-danger-solid)",
                'danger-hover': "var(--color-danger-hover)",
                'on-danger': "var(--color-on-danger)",
                warning: "var(--color-warning)",
                'warning-soft': "var(--color-warning-soft)",
                'warning-hover': "var(--color-warning-hover)",
                'on-warning': "var(--color-on-warning)",
                surface: "var(--color-surface)",
                'surface-2': "var(--color-surface-2)",
                border: "var(--color-border)",
                text: "var(--color-text)",
                muted: "var(--color-text-muted)",
                gold: "var(--color-gold)",
                'gold-soft': "var(--color-gold-soft)",
                teal: "var(--color-teal)",
                'teal-soft': "var(--color-teal-soft)",
                'focus-ring': "var(--color-focus-ring)",
            },
        },
    },

    plugins: [forms, typography],
};
