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
                primary: {
                    50: '#e6f0f5',
                    100: '#cce1eb',
                    200: '#99c3d7',
                    300: '#66a5c3',
                    400: '#3387af',
                    500: '#003566',
                    600: '#002a52',
                    700: '#00203d',
                    800: '#001529',
                    900: '#000b14',
                    950: '#000508',
                },
                accent: {
                    50: '#fffef5',
                    100: '#fffceb',
                    200: '#fff9d6',
                    300: '#fff5c2',
                    400: '#fff2ad',
                    500: '#FFD60A',
                    600: '#ccab08',
                    700: '#998006',
                    800: '#665604',
                    900: '#332b02',
                    950: '#1a1501',
                },
            },
        },
    },

    plugins: [forms],
};
