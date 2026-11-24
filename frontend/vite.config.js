import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";
import { defineConfig } from "vite";

// https://vite.dev/config/
export default defineConfig({
    plugins: [vue(), tailwindcss()],
    server: {
        // Escuta em 0.0.0.0 para ser acessível de fora do container do Sail
        host: true,
        port: 5173,
        strictPort: true,
        proxy: {
            // Encaminha a API e o endpoint de CSRF do Sanctum para o Laravel.
            // "laravel.test" é o serviço no compose (o Vite roda em outro container).
            "/api": "http://laravel.test",
            "/sanctum": "http://laravel.test",
        },
    },
});
