import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // Keep phone landscape on the "mobile" layout: width alone is not enough
            // (landscape phones often exceed 639px wide but stay short in height).
            screens: {
                sm: { raw: '(min-width: 640px) and (min-height: 500px)' },
            },
        },
    },

    plugins: [forms],
};
