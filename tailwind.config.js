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
                sans: ['Poppins', 'Rubik', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
                body: ['Rubik', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                burgundy: {
                    50: '#fdf2f8',
                    100: '#fce7f3',
                    200: '#f9c6de',
                    300: '#f09ec3',
                    400: '#e06da0',
                    500: '#c94080',
                    600: '#a82865',
                    700: '#8b1a4e',
                    800: '#680c3e',
                    900: '#570a34',
                    950: '#33041e',
                },
                navy: {
                    50: '#eef1fb',
                    100: '#d8ddf5',
                    200: '#b5bfed',
                    300: '#8a9ae0',
                    400: '#6475d0',
                    500: '#4557b8',
                    600: '#3545a0',
                    700: '#2d3a82',
                    800: '#273272',
                    900: '#1e2758',
                    950: '#121840',
                },
                slate_custom: {
                    100: '#e9eaee',
                    200: '#c6d3e3',
                    300: '#9d9ec0',
                    400: '#8d8ea3',
                    500: '#565872',
                },
            },
        },
    },

    plugins: [forms],
};
