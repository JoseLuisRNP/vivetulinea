import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary-focus': '#AD0B71',
                'secondary-focus': '#94B143',
            },
        },
    },

    plugins: [forms, require("daisyui")],
    daisyui: {
        themes: [
            {
                'light': {
                    'primary' : '#c11387',
                    'primary-focus' : '#AD0B71',
                    'primary-content' : '#ffffff',

                    'secondary' : '#9ec151',
                    'secondary-focus' : '#94B143',
                    'secondary-content' : '#ffffff',

                    'accent' : '#37cdbe',
                    'accent-focus' : '#2ba69a',
                    'accent-content' : '#ffffff',

                    'neutral' : '#3b424e',
                    'neutral-focus' : '#2a2e37',
                    'neutral-content' : '#ffffff',

                    'base-100' : '#ffffff',
                    'base-200' : '#f9fafb',
                    'base-300' : '#ced3d9',
                    'base-content' : '#1e2734',

                    'info' : '#1c92f2',
                    'success' : '#9ec151',
                    'warning' : '#ff9900',
                    'error' : '#ff5724',

                    '--rounded-box': '1rem',
                    '--rounded-btn': '.5rem',
                    '--rounded-badge': '1.9rem',

                    '--animation-btn': '.25s',
                    '--animation-input': '.2s',

                    '--btn-text-case': 'uppercase',
                    '--navbar-padding': '.5rem',
                    '--border-btn': '1px',
                },
            },
        ],
        darkTheme: 'light',
    },
};
