import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',   // para que el contenedor escuche desde fuera
        port: 5173,
        hmr: {
            host: 'localhost', // para que el navegador vaya a localhost:5173, NO a 0.0.0.0
        },
    },
});
