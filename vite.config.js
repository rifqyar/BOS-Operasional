import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
import fs from 'fs';

const jsInputs = fs.readdirSync('resources/js')
    .filter(file => file.endsWith('.js'))
    .map(file => `resources/js/${file}`);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', ...jsInputs],
            refresh: [`resources/views/**/*`],
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
});
