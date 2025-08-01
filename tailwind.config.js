/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      // Tailwind will scan these paths for classes:
      "./templates/**/*.php",
      "./inc/**/*.php",
      "./src/**/*.{js,jsx,ts,tsx,vue}",
    ],
    theme: {
      extend: {},
    },
    plugins: [
      require('@tailwindcss/forms')
    ],
  }
  