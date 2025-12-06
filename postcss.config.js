import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import postcssGlobalData from '@csstools/postcss-global-data';
import autoprefixer from 'autoprefixer';
import postcssImport from 'postcss-import';
import postcssImportExtGlob from 'postcss-import-ext-glob';
import postcssNested from 'postcss-nested';
import postcssPresetEnv from 'postcss-preset-env';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default {
  plugins: [
    autoprefixer(),
    postcssImportExtGlob(),
    postcssImport({
      // Allow imports from node_modules (for external libs like Swiper)
      path: [join(__dirname, 'node_modules')],
    }),
    postcssNested(),
    postcssGlobalData({
      files: ['./source/props/media.css'],
    }),
    postcssPresetEnv({
      stage: 4,
      features: {
        'custom-media-queries': { preserve: false },
      },
    }),
  ],
};
