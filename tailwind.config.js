// /** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./vendor/symfony/twig-bridge/Resources/views/Form/tailwind_2_layout.html.twig",
    "./templates/**/*.html.twig",
    "./assets/scripts/*.js",
    "./node_modules/tw-elements/dist/js/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [require("tw-elements/dist/plugin"), require("@tailwindcss/forms")],
};
