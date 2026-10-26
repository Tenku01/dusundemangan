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
            colors: {
                emas: {
                    50: '#FDFBF5',
                    100: '#FAF4E4',
                    200: '#F3E5C2',
                    300: '#EAD199',
                    400: '#DCB96D',
                    500: '#CEA042', // Primary Gold (#CEA042)
                    600: '#BA8832',
                    700: '#9B6C26',
                    800: '#7E5522',
                    900: '#674620',
                    950: '#3A240E',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
