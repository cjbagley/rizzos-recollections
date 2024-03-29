import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import { resolve } from 'path'
const root = resolve(__dirname)

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/admin.js'
            ],
            refresh: true,
        }),
        svelte({})
    ],
    resolve: {
        alias: {
            '@': resolve(root, 'resources/js'),
            '~': resolve(root, 'resources'),
        },
        extensions: ['.js', '.svelte', '.json', '.css'],
    }
});
