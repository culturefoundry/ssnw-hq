import UnoCSS from 'unocss/vite';
import { defineConfig } from 'vite';
import { liveReload } from "vite-plugin-live-reload";
import { globSync } from 'glob';

export default defineConfig({
  plugins: [
    UnoCSS({
      fetchMode: "no-cors",
    }),
    liveReload([
      __dirname + '/**/*.(twig)',
      __dirname + '/*.js',
    ]),
  ],
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    manifest: true,
    sourcemap: true,
    rollupOptions: {
      input: [
        ...globSync('libraries/*/*.{css,js}'),
        ...globSync(['components/**/*.{css,js}', '!components/**/css/**']),
        // ...globSync('layouts/**/*.{css,js}'),
      ],
      // Print file without hash.
      output: {
        entryFileNames: `[name].js`,
        assetFileNames: `[name].[ext]`,
      },
    },
  },
  // CSS sourcemaps in dev mode.
  css: { devSourcemap: true },
  server: {
    // Listen for connections.
    host: true,
    // Use vite server for generated paths (@unocss-devtools-update).
    origin: 'https://ssnw.ddev.site:5257',
    port: 5257,
    headers: {
      'Access-Control-Allow-Origin': 'https://ssnw.ddev.site',
    },
    cors: {
      origin: true,
    },
    hmr: {
      host: process.env.DDEV_HOSTNAME,
      protocol: 'wss',
    },
  },
});
