/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    './assets/**/*.css',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1d4ed8',   // Couleur primaire (bleu)
        secondary: '#fbbf24', // Couleur secondaire (jaune)
        accent: '#ec4899',    // Couleur d'accent (rose)
        // Ajoute d'autres couleurs personnalisées ici si besoin
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

