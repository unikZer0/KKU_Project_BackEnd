import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                pkm: ['PKM', 'sans-serif'],
                noto: ['NotoSansThai', 'sans-serif'],
                thai: ['NotoSansThai', 'PKM', 'sans-serif'], // Fallback chain for Thai text
            },
        },
    },

    plugins: [forms],
};
