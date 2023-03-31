/** @type {import('tailwindcss').Config} */

// const colors = require("tailwindcss/colors");

module.exports = {
  content: [
    "./src/**/*.{html,js}",
    "./vendor/symfony/twig-bridge/Resources/views/Form/tailwind_2_layout.html.twig",
    "./templates/**/*.html.twig",
    "./assets/**/*.js",
    "./node_modules/tw-elements/dist/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        "custom-violet": "#6d184e",
        "custom-dark-orange": "#ed6d3f",
        "custom-light-orange": "#fbc57f",
      },
      fontFamily: {
        aller: ["Aller", "sans-serif"],
      },
    },
  },
  plugins: [require("tw-elements/dist/plugin"), require("@tailwindcss/forms")],
};
