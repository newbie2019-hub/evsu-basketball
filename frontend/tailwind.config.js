/** @type {import('tailwindcss').Config} */
const defaultTheme = require("tailwindcss/defaultTheme");
module.exports = {
  content: ["*.html", "./public/**/*.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Space Grotesk", ...defaultTheme.fontFamily.sans],
      },
    },
  },
  safelist: [
    'disabled:bg-red-200',
    'bg-red-100',
    'bg-red-500',
    'bg-red-600',
    'bg-red-700',
    'hover:bg-red-700',
    'disabled:bg-blue-200',
    'bg-blue-100',
    'bg-blue-500',
    'bg-blue-600',
    'hover:bg-blue-600',
    'bg-blue-700',
    'hover:bg-blue-700',
    'disabled:bg-green-200',
    'bg-green-100',
    'bg-green-500',
    'bg-green-600',
    'bg-green-700',
    'hover:bg-green-700',
    'disabled:bg-orange-200',
    'bg-orange-100',
    'bg-orange-500',
    'bg-orange-600',
    'bg-orange-700',
    'hover:bg-orange-700',
    'disabled:cursor-progress',
    'cursor-progress'
  ],
  variants: {
    extend: {
      margin: ["first"],
    },
  },
  plugins: [],
};