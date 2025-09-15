/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "ui-sans-serif", "system-ui", "sans-serif"],
            },
            colors: {
                primary: {
                    50: "#eff6ff",
                    100: "#dbeafe",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                    900: "#1e3a8a",
                },
                success: {
                    50: "#f0fdf4",
                    500: "#22c55e",
                    600: "#16a34a",
                },
                warning: {
                    50: "#fffbeb",
                    500: "#f59e0b",
                    600: "#d97706",
                },
                error: {
                    50: "#fef2f2",
                    500: "#ef4444",
                    600: "#dc2626",
                },
            },
            animation: {
                "fade-in": "fadeIn 0.3s ease-in-out",
                "slide-in": "slideIn 0.3s ease-out",
                "bounce-in": "bounceIn 0.5s ease-out",
            },
            keyframes: {
                fadeIn: {
                    "0%": { opacity: "0" },
                    "100%": { opacity: "1" },
                },
                slideIn: {
                    "0%": { transform: "translateX(-100%)" },
                    "100%": { transform: "translateX(0)" },
                },
                bounceIn: {
                    "0%": { transform: "scale(0)" },
                    "50%": { transform: "scale(1.05)" },
                    "100%": { transform: "scale(1)" },
                },
            },
        },
    },
    plugins: [],
};
