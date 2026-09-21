import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  server: { port: 4000, strictPort: true },
  build: {
    // Las librerías van en trozos aparte: cambian poco, así que el celular las
    // guarda en caché y en los despliegues siguientes solo baja el código nuevo.
    rollupOptions: {
      output: {
        manualChunks: {
          react:  ['react', 'react-dom', 'react-router-dom'],
          iconos: ['lucide-react'],
          // recharts NO va aquí a propósito: como trozo manual Vite lo precarga
          // en cada apertura. Dejándolo libre, solo viaja con Marketing, que es
          // la única pantalla que lo usa.
        },
      },
    },
    chunkSizeWarningLimit: 600,
  },
});
