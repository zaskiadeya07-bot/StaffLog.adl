/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Http/Controllers/**/*.php",
    "./app/Models/**/*.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}