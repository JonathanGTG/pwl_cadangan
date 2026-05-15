import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                display: ["Poppins", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "elco-coffee": "#5C3D2E",
                "elco-mocha": "#8B5E3C",
                "elco-latte": "#C4956A",
                "elco-cream": "#F6F3F0",
                "elco-dark": "#2C1A0E",
            },
            boxShadow: {
                soft: "0 2px 15px -3px rgba(0,0,0,0.07), 0 10px 20px -2px rgba(0,0,0,0.04)",
                hover: "0 10px 40px -10px rgba(92,61,46,0.3)",
            },
        },
    },
    plugins: [],
};
