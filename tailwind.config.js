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
                // Your existing custom colors (kept as is)
                'custom-white': '#ffffff',
                'custom-light-cream': '#f8f1e1',
                'custom-light-grey-green': '#e1e7dd',
                'custom-light-grey-brown': '#bcbabb',
                'custom-ochre': '#cc8e45',
                'custom-ochre-darker': '#b37e3d',
                'custom-dark-teal': '#33595a',
                'custom-dark-teal-darker': '#2a4c4d',
                'custom-green': '#4CAF50',
                'custom-green-light': '#8BC34A',

                // New / Refined Modern Colors (using more descriptive names for context)
                'primary-dark': '#2C494A', // Deeper green for main actions/headings
                'primary-light': '#51797B', // Lighter green for accents
                'secondary-bg': '#F9FAFB', // Lightest background, almost white
                'accent-yellow': '#FBBF24', // A friendly yellow for highlights
                'border-light': '#E5E7EB', // Lighter, subtle border
                'chat-outgoing': '#DCFCE7', // Light green for outgoing messages
                'chat-incoming': '#F3F4F6', // Light gray for incoming messages
                'text-dark': '#374151',    // Darker gray for main text
                'text-light': '#6B7280',   // Lighter gray for secondary text
            },
            boxShadow: {
                'smooth': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                'md-light': '0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.05)',
            }
        },
    },

    plugins: [forms],
};