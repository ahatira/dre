import { defineConfig } from 'vite';
import yml from '@modyfi/vite-plugin-yaml';
import twig from 'vite-plugin-twig-drupal';
import { join } from 'node:path';
import path from 'node:path';
import { glob } from 'glob';

export default defineConfig({
  plugins: [
    // Twig namespaces for nesting components.
    twig({
      namespaces: {
        assets: join(__dirname, './source/assets'),
        base: join(__dirname, './source/patterns/base'),
        elements: join(__dirname, './source/patterns/elements'),
        components: join(__dirname, './source/patterns/components'),
        collections: join(__dirname, './source/patterns/collections'),
        layouts: join(__dirname, './source/patterns/layouts'),
        pages: join(__dirname, './source/patterns/pages'),
        theme: join(__dirname, './source/patterns/theme'),
      },
    }),
    // Allows Storybook to read data from YAML files.
    yml(),
  ],
  build: {
    emptyOutDir: true,
    outDir: 'dist',
    rollupOptions: {
      // Collect all CSS/JS under patterns; provide fallback if none found.
        // Collect JS entry points under patterns; CSS should be imported from JS.
        // Fallback to global.css if no JS entries are found.
        input: (() => {
          const entries = {
            // Single consolidated stylesheet that imports all component CSS via @import-glob
            styles: path.resolve(__dirname, 'source/patterns/styles.css'),
            // JS behaviors (add here as we create more)
            alert: path.resolve(__dirname, 'source/patterns/components/alert/alert.behavior.js'),
            accordion: path.resolve(__dirname, 'source/patterns/components/accordion/accordion.behavior.js'),
          };
          return entries;
        })(),
      output: {
        assetFileNames: (assetInfo) => {
          // Preserve original subfolder by prefixing fonts/ and using original name including subpath
          if (/\.(woff2?|ttf|eot|otf|svg)$/.test(assetInfo.name)) {
            // assetInfo.name may include subfolders like BNPPSans/BNPPSans-Bold.woff2
            return (assetInfo.name ? `fonts/${assetInfo.name}` : 'fonts/[name][extname]');
          }
            // Emit CSS bundles under css/
            return 'css/[name][extname]';
        },
        entryFileNames: 'js/[name].js',
      },
    },
  },
  publicDir: 'source/assets',
  css: {
    devSourcemap: true,
  },
});
