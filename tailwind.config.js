import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                pulsate: 'pulsate 1s infinite alternate',
            },
            keyframes: {
                pulsate: {
                    '0%': { transform: 'scale(1)', opacity: '0.8' },
                    '100%': { transform: 'scale(1.1)', opacity: '1' },
                },
            },
            transitionProperty: {
                height: 'height',
                spacing: 'margin, padding',
                opacity: 'opacity',
                transform: 'transform',
            },
        },
    },

    plugins: [forms, typography],
};
