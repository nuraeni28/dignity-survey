/** @type {import('tailwindcss').Config} */
module.exports = {
    purge: [
        './resources/**/*.js',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js'
    ],
    darkMode: false,
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('flowbite/plugin'),
    ],
}
