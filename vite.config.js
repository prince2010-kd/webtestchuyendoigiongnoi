import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => ({
    plugins: [
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        minify: mode === 'production' ? 'terser' : false,
        terserOptions: {
            compress: true,
            mangle: true,
        },
    },
}));
