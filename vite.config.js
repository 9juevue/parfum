import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import * as fs from "node:fs";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
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
    ],
    server: {
        host: "0.0.0.0",
        port: 5174,
        https: {
            key: fs.readFileSync(
                "docker/nginx/certs/parfummaniac.loc+4-key.pem",
            ),
            cert: fs.readFileSync("docker/nginx/certs/parfummaniac.loc+4.pem"),
        },
        hmr: {
            host: "parfummaniac.loc",
        },
        cors: true,
    },
});
