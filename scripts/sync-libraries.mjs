#!/usr/bin/env node
import { watch, writeFileSync } from 'node:fs';
import path from 'node:path';
import { glob } from 'glob';

const ROOT = path.resolve(process.cwd());
const THEME_MACHINE_NAME = 'ps_theme';
const LIB_FILE = path.resolve(ROOT, 'ps.libraries.yml');

function buildEntryName(file) {
  const base = path.basename(file, '.js');
  const parent = path.basename(path.dirname(file));
  return base === parent ? base : `${parent}-${base}`;
}

function main() {
  const files = glob.sync('source/patterns/**/*.js', { nodir: true, cwd: ROOT });

  const behaviorFiles = files
    .map((f) => f.replace(/\\/g, '/'))
    .filter((f) => !/stories\.|\.spec\.|\.test\./.test(f))
    .filter((f) => f !== 'source/patterns/scripts.js')
    .filter((f) => !/^(storybook|documentation)\//.test(f.replace(/^source\/patterns\//, '')));

  const names = new Set();
  const libs = behaviorFiles.map((f) => {
    const name = buildEntryName(f);
    names.add(name);
    return { name, dist: `dist/js/${name}.js` };
  });

  // YAML build helper
  const lines = [];
  lines.push('global:');
  lines.push('  version: VERSION');
  lines.push('  css:');
  lines.push('    base:');
  lines.push('      dist/css/styles.css: {}');
  lines.push('');
  // Vendors shared chunk
  lines.push('vendors:');
  lines.push('  js:');
  lines.push('    dist/js/vendors/vendors.js: {}');
  lines.push('  dependencies:');
  lines.push('    - core/drupal');
  lines.push('    - core/drupalSettings');
  lines.push('    - core/once');
  lines.push('');

  // Individual libraries
  libs
    .sort((a, b) => a.name.localeCompare(b.name))
    .forEach(({ name, dist }) => {
      lines.push(`${name}:`);
      lines.push('  js:');
      lines.push(`    ${dist}: {}`);
      lines.push('  dependencies:');
      lines.push('    - core/drupal');
      lines.push('    - core/drupalSettings');
      lines.push('    - core/once');
      lines.push(`    - ${THEME_MACHINE_NAME}/vendors`);
      lines.push('');
    });

  const content = lines.join('\n') + '\n';
  writeFileSync(LIB_FILE, content, 'utf8');
  // eslint-disable-next-line no-console
  console.log(`Generated ${LIB_FILE} with ${libs.length} JS libraries.`);
}

function startWatch() {
  let timer;
  const trigger = () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      try {
        main();
      } catch (e) {
        console.error('sync-libraries error:', e);
      }
    }, 200);
  };
  // Initial run
  main();
  // Watch recursively on Windows/macOS
  const watcher = watch(
    path.resolve(ROOT, 'source/patterns'),
    { recursive: true },
    (event, filename) => {
      if (!filename || !filename.endsWith('.js')) return;
      trigger();
    }
  );
  console.log('Watching JS files to sync ps.libraries.yml...');
  return watcher;
}

if (process.argv.includes('--watch')) {
  startWatch();
} else {
  main();
}
