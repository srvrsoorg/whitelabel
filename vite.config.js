import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
    server: {
        watch: {
            // Tell Vite to ignore changes to files or directories containing environment variables
            ignored: [
                '**/.env'
            ],
        },
    },
    plugins: [
        VitePWA({
            buildBase: '/build/',
            manifest: {
                fileName: 'manifest.json',
                // baseDir: '/',
            },
            devOptions: {
                enabled: true
            },
            precache: {
                include: [
                  'public/**/*'
                ],
                exclude: [
                  'public/index.html'
                ]
            },
            workbox: {
                manifestTransforms: [async (entries) => {
                  entries.push({ url: '/', revision: `${new Date().toString()}` })
                  return { manifest: entries, warnings: [] }
                }],
                navigateFallback: '/',
                globIgnores: ['build/sw.js', 'build/workbox-*'],
            },
        }),
        vue({
            template: {
                transformAssetUrls: {
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
          '@': '/resources/js/',
          'vue': 'vue/dist/vue.esm-bundler.js'
        }
    },
    build: {
        // manifest: true,
        base: '/',
        rollupOptions: {
            output: {
                entryFileNames: `[name]` + Math.floor(Math.random() * 90000) + 10000 + `.js`,
                chunkFileNames: `[name]` + Math.floor(Math.random() * 90000) + 10000 + `.js`,
                assetFileNames: `[name]` + Math.floor(Math.random() * 90000) + 10000 + `.[ext]`
            }
        }
    }
});