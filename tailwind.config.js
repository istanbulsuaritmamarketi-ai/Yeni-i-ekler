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
                rose: {
                    50: '#eff6ff',   // soft pastel ice-blue (light background / card hover)
                    100: '#dbeafe',  // light blue border
                    200: '#bfdbfe',  // soft blue border
                    300: '#93c5fd',  // muted blue
                    400: '#60a5fa',  // accent sky/blue
                    500: '#3b82f6',  // bright blue
                    600: '#1d4ed8',  // vibrant royal blue (primary brand color matching banner)
                    700: '#1e40af',  // deep royal blue hover
                    800: '#1e3a8a',  // rich navy blue
                    900: '#0f2356',  // deep midnight navy (banner title color)
                    950: '#0a1936',  // dark navy
                },
                pink: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#1d4ed8',
                    700: '#1e40af',
                    800: '#1e3a8a',
                    900: '#0f2356',
                    950: '#0a1936',
                },
                royal: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    500: '#3b82f6',
                    600: '#1d4ed8',
                    700: '#1e40af',
                    800: '#1e3a8a',
                    900: '#0f2356',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'xs': ['1.125rem', { lineHeight: '1.65rem' }],       /* 18px (eski 12px, +%50) */
                'sm': ['1.3125rem', { lineHeight: '1.85rem' }],     /* 21px (eski 14px, +%50) */
                'base': ['1.5rem', { lineHeight: '2.15rem' }],       /* 24px (eski 16px, +%50) */
                'lg': ['1.6875rem', { lineHeight: '2.35rem' }],     /* 27px (eski 18px, +%50) */
                'xl': ['1.875rem', { lineHeight: '2.45rem' }],      /* 30px (eski 20px, +%50) */
                '2xl': ['2.25rem', { lineHeight: '2.85rem' }],      /* 36px (eski 24px, +%50) */
                '3xl': ['2.8125rem', { lineHeight: '3.35rem' }],    /* 45px (eski 30px, +%50) */
                '4xl': ['3.375rem', { lineHeight: '3.85rem' }],     /* 54px (eski 36px, +%50) */
                '5xl': ['4.5rem', { lineHeight: '1.1' }],           /* 72px (eski 48px, +%50) */
                '6xl': ['5.625rem', { lineHeight: '1.1' }],         /* 90px (eski 60px, +%50) */
            },
        },
    },

    plugins: [forms],
};
