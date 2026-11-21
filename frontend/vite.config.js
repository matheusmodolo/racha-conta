import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import { defineConfig } from 'vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), tailwindcss()],
  server: {
    // Escuta em 0.0.0.0 para ser acessível de fora do container do Sail
    host: true,
    port: 5173,
    strictPort: true,
  },
})
