import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
        // Custom plugin to ensure Select2 UMD wrapper executes
        {
            name: 'select2-umd-fix',
            transformIndexHtml(html) {
                // This ensures Select2 is available globally
                return html;
            },
            buildStart() {
                // Ensure jQuery is available before Select2 loads
            },
        },
    ],
    optimizeDeps: {
        include: ['jquery', 'select2'],
        esbuildOptions: {
            define: {
                global: 'globalThis',
            },
        },
    },
    resolve: {
        alias: {
            'jquery': 'jquery',
        },
    },
    define: {
        // Ensure global is defined for UMD modules
        'global': 'globalThis',
    },
    build: {
        commonjsOptions: {
            transformMixedEsModules: true,
            include: [/select2/, /node_modules/],
        },
        rollupOptions: {
            output: {
                // Ensure Select2 is bundled correctly
                manualChunks: undefined,
            },
        },
    },
});

