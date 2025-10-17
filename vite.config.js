import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/home-clock/focus-clock.css',
                'resources/js/home-clock/focus-clock.js',
                'resources/css/rewards.css'
            ],
            refresh: true,
        }),
    ],
});
