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
            colors: {
                'custom-white': '#ffffff',
                'custom-light-cream': '#f8f1e1',
                'custom-light-grey-green': '#e1e7dd',
                'custom-light-grey-brown': '#bcbabb',
                'custom-ochre': '#cc8e45',
                'custom-ochre-darker': '#b37e3d', // Slightly darker for hover states
                'custom-dark-teal': '#33595a',
                'custom-dark-teal-darker': '#2a4c4d', // Slightly darker for hover states
                'custom-dark-olive': '#3e4732',
                'custom-green': '#4CAF50', // Example green shade (you can pick your own hex code)
                'custom-green-light': '#8BC34A', // Example lighter green for ring (optional)
            },
        },
    },

    plugins: [forms],
};
