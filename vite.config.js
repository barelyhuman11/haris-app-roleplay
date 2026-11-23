import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/tailwind.css', 
                'resources/js/app.js',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
