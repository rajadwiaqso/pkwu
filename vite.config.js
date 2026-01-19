import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Remove additionalData to avoid path conflicts
            },
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '~': path.resolve(__dirname, 'node_modules'),
        },
    },
    publicDir: 'public',
 server: {
    host: '0.0.0.0',
    port: 3000,
    strictPort: true,
    cors: true,
    hmr: {
      host: 'localhost',
      protocol: 'ws'
    },
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      },
      '/sanctum': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      }
    }
  }
});