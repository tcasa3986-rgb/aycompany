/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                farmacia: {
                    50:  '#ecfdf6',
                    100: '#d1fae9',
                    200: '#a7f0d4',
                    300: '#6fe0bb',
                    400: '#3fc99e',
                    500: '#22b388',
                    600: '#199172',
                    700: '#16735c',
                    800: '#155b4b',
                    900: '#114a3e',
                },
                topbar: '#2a8f88',
                sidebar: '#46b8a4',
            },
            fontFamily: {
                sans: ['"Figtree"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
