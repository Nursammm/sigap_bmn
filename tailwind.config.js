import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './app/View/Components/**/*.php',
  ],
  // ⬇⬇ Tambahkan ini
  safelist: [
    // width untuk sidebar/collapse
    { pattern: /^(w|md:w|lg:w)-(14|16|56|60|64|72)$/ },
    // warna/utility yang mungkin dipakai via binding dinamis
    'bg-slate-800','text-white','shadow-lg','sticky','top-0',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [forms],
}
