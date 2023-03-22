/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{html,js}",
    "./vendor/symfony/twig-bridge/Resources/views/Form/tailwind_2_layout.html.twig",
    "./templates/**/*.html.twig",
    "./assets/**/*.js",
    "./node_modules/tw-elements/dist/js/**/*.js",
  ],
  theme: {
    colors: {
      red: "#DC3545",
    },
    extend: {},
  },
  plugins: [
    require("tw-elements/dist/plugin"),
    require("@tailwindcss/forms"),
  ],
};
