/** @type {import('tailwindcss').Config} */

import forms from "@tailwindcss/forms";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontSize: {
                tiny: ["14px", "22px"],
            },
            colors: {
                custom: {
                    50: "var(--color50, #E6E6FF)",
                    100: "var(--color100, #CECDFE)",
                    200: "var(--color200, #9C9BFD)",
                    300: "var(--color300, #6663FC)",
                    400: "var(--color400, #3531FC)",
                    500: "var(--color500, #159C8C)",
                    600: "var(--color600, #0603C4)",
                    700: "var(--color700, #050292)",
                    800: "var(--color800, #030264)",
                    900: "var(--color900, #020132)",
                },
            },
            borderColor: {
                primary: "#e5e7eb",
            },
            screens:{
                'xs': '425px',
            }
        },
    },
    plugins: [
        forms,
    ],
};
