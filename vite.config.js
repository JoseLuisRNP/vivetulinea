import { defineConfig } from 'vite';

import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            outDir: 'public/build',
            registerType: 'autoUpdate',
            includeAssets: ['favicon.svg', 'robots.txt', 'safari-pinned-tab.svg'],
            workbox: {
                cleanupOutdatedCaches: true,
                directoryIndex: null,
                globPatterns: ['**/*.{css,js,html,svg,png,ico,txt,woff2}'],
                maximumFileSizeToCacheInBytes: 4194304,
                navigateFallback: null,
                navigateFallbackDenylist: [/\/[api,admin,livewire]+\/.*/],
            },
            manifest: {
                name: 'ViveTuLinea',
                short_name: 'ViveTuLinea',
                theme_color: '#c11387',
                id: '/',
                scope: '/',
                start_url: '/',
                icons: [
                    {
                        src: '/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },
        }),
    ],
});
