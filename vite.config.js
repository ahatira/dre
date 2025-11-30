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
      // Auto-managed entries: one CSS bundle + one JS entry per behavior file
      // JS files are discovered automatically under source/patterns/**, excluding Storybook files.
      input: (() => {
        const entries = {
          styles: path.resolve(__dirname, 'source/patterns/styles.css'),
        };
        const files = glob.sync('source/patterns/**/*.js', { nodir: true });
        for (const file of files) {
          const rel = path.relative(path.resolve(__dirname, 'source/patterns'), path.resolve(__dirname, file)).replace(/\\/g, '/');
          // Exclusions: storybook stories, test/spec files, and internal build helpers
          if (
            rel.includes('stories.') ||
            rel.includes('.spec.') ||
            rel.includes('.test.') ||
            rel.startsWith('storybook/') ||
            rel === 'scripts.js'
          ) {
            continue;
          }
          const base = path.basename(file, '.js');
          const parent = path.basename(path.dirname(file));
          const name = base === parent ? base : `${parent}-${base}`;
          entries[name] = path.resolve(__dirname, file);
        }
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
        // Shared vendor chunk to avoid duplication. Each component library should depend on ps_theme/vendors.
        chunkFileNames: (chunkInfo) => (chunkInfo.name === 'vendors' ? 'js/vendors/[name].js' : 'js/[name].js'),
        manualChunks: (id) => (id.includes('node_modules') ? 'vendors' : undefined),
      },
    },
  },
  publicDir: 'source/assets',
  css: {
    devSourcemap: true,
  },
});
