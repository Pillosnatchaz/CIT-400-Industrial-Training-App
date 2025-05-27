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
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],

    safelist: [
        'bg-green-100', 'bg-green-200', 'bg-green-500', 'text-green-800',
        'bg-yellow-100', 'bg-yellow-200', 'bg-yellow-500', 'text-yellow-800',
        'bg-red-100', 'bg-red-200', 'bg-red-500', 'text-red-800',
        'bg-blue-100', 'bg-blue-200', 'bg-blue-500', 'text-blue-800',
        'bg-gray-100', 'bg-gray-200', 'bg-gray-300', 'bg-gray-500', 'text-gray-800', 'text-black-800',
        'min-w-[5rem]', 'min-w-[6rem]', 'min-w-[7rem]', 'min-w-[4rem]', 'w-28','text-center', 'dt-head-center',
        'text-blue-600', 'hover:underline',
        'whitespace-normal',
        'break-words',
        'max-w-md',
    ]
};
