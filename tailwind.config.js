import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Poppins', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // SMK YADIKA BANGIL Theme Colors
                // Light Mode: #FFF500 (Yellow), #00923F (Green), #00923F, #00923F
                // Dark Mode: #393D46 (Dark Gray), #242632 (Darker Gray), #EBEBE9 (Light Gray), #DCAE25 (Gold), #1B95A0 (Teal)
                
                'yadika': {
                    'yellow': '#FFF500',
                    'green': '#00923F',
                    'dark-gray': '#393D46',
                    'darker-gray': '#242632',
                    'light-gray': '#EBEBE9',
                    'gold': '#DCAE25',
                    'teal': '#1B95A0',
                },
                
                // Primary color (Green for light mode, Teal for dark mode)
                primary: {
                    DEFAULT: '#00923F',
                    50: '#e6f7ee',
                    100: '#ccefdd',
                    200: '#99dfbb',
                    300: '#66cf99',
                    400: '#33bf77',
                    500: '#00923F', // Main green
                    600: '#007533',
                    700: '#005827',
                    800: '#003a1a',
                    900: '#001d0d',
                    dark: '#1B95A0', // Teal for dark mode
                },
                
                // Secondary color (Yellow for light mode, Gold for dark mode)
                secondary: {
                    DEFAULT: '#FFF500',
                    50: '#fffef0',
                    100: '#fffde1',
                    200: '#fffbc3',
                    300: '#fff9a5',
                    400: '#fff787',
                    500: '#FFF500', // Main yellow
                    600: '#e6dc00',
                    700: '#ccc400',
                    800: '#b3ab00',
                    900: '#999300',
                    dark: '#DCAE25', // Gold for dark mode
                },
                
                // Semantic Colors
                success: {
                    DEFAULT: '#00923F',
                    50: '#e6f7ee',
                    100: '#ccefdd',
                    500: '#00923F',
                    600: '#007533',
                    700: '#005827',
                },
                warning: {
                    DEFAULT: '#FFF500',
                    50: '#fffef0',
                    100: '#fffde1',
                    500: '#FFF500',
                    600: '#e6dc00',
                    700: '#ccc400',
                },
                error: {
                    DEFAULT: '#ef4444',
                    50: '#fef2f2',
                    100: '#fee2e2',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                },
                info: {
                    DEFAULT: '#1B95A0',
                    50: '#e6f7f9',
                    100: '#cceff3',
                    500: '#1B95A0',
                    600: '#157680',
                    700: '#105860',
                },
                
                // Dark mode backgrounds
                'dark': {
                    bg: '#242632',
                    card: '#393D46',
                    border: '#4A4E5A',
                    text: '#EBEBE9',
                },
            },
            backgroundImage: {
                'gradient-yadika': 'linear-gradient(135deg, #00923F 0%, #1B95A0 100%)',
                'gradient-yadika-dark': 'linear-gradient(135deg, #242632 0%, #393D46 100%)',
                'gradient-yellow': 'linear-gradient(135deg, #FFF500 0%, #DCAE25 100%)',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'bounce-subtle': 'bounceSubtle 1s ease-in-out infinite',
                'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                bounceSubtle: {
                    '0%, 100%': { transform: 'translateY(-5%)' },
                    '50%': { transform: 'translateY(0)' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.8' },
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
                '4xl': '2rem',
            },
            boxShadow: {
                'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'medium': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                'large': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                'yadika': '0 10px 15px -3px rgba(0, 146, 63, 0.2), 0 4px 6px -2px rgba(0, 146, 63, 0.1)',
                'yadika-dark': '0 10px 15px -3px rgba(27, 149, 160, 0.2), 0 4px 6px -2px rgba(27, 149, 160, 0.1)',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
