import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/home-clock/focus-clock.css',
        'resources/js/home-clock/focus-clock.js',
        'resources/css/rewards.css',
        'resources/js/rewards.js'
      ],
      refresh: true,
    }),
  ],
  server: {
    host: true,             // allow external access from Docker (0.0.0.0)
    port: 5173,             // must match your docker-compose port
    strictPort: true,
    hmr: {
      host: 'localhost',    // how the browser reaches it from your machine
      port: 5173,
      protocol: 'ws',
    },
    watch: {
      usePolling: true,     // needed for file changes to be detected in Docker volumes (esp. on Windows)
    },
  },
})
