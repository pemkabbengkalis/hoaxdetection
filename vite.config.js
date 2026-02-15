import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/filament/admin/theme.css", // <-- wajib ini
                "resources/js/app.js",
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            overlay: false, // <-- tambahkan ini sementara untuk disable error overlay (opsional)
        },
    },
});
