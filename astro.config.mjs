import { defineConfig } from 'astro/config';
import tailwind from '@astrojs/tailwind';
import react from '@astrojs/react';

// https://astro.build/config
export default defineConfig({
  integrations: [tailwind(), react()],
  output: 'static', // Modo estático para Hostinger (no soporta SSR)
  site: 'https://delprado.hechoencorrientes.com',
  compressHTML: true,
  build: {
    inlineStylesheets: 'auto'
  }
});
