/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    './assets/**/*.css',
  ],


  theme: {
    extend: {
      fontFamily: {
        sans: ['Roboto', 'sans-serif'], // Ajoute Roboto comme police sans-serif
        serif: ['Merriweather', 'serif'], // Exemple d'ajout d'une police serif
        mono: ['Menlo', 'monospace'], // Exemple d'ajout d'une police monospace
        poppins: ['Poppins', 'sans-serif'],
      },
    },
  },



  plugins: [
    require('@tailwindcss/forms'),
  ],
}

