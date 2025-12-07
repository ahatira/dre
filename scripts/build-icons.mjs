import fs from 'fs';
import { glob } from 'glob';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const ICONS_SOURCE_DIR = path.join(__dirname, '../source/icons-source');
const SPRITE_OUTPUT_PATH = path.join(__dirname, '../source/assets/icons/icons-sprite.svg');
const LIST_PATH = path.join(__dirname, '../source/patterns/documentation/icons-list.json');

const SVG_SYMBOL_RE = /<svg[^>]*>([\s\S]*?)<\/svg>/i;
const VIEWBOX_RE = /viewBox="([^"]+)"/i;

/**
 * Strip inline fill/stroke from SVG path elements to allow CSS currentColor control.
 * Keeps viewBox and other attributes, removes fill="#..." and stroke="#..." from paths.
 */
function stripSVGFillStroke(svgContent) {
  // Remove fill and stroke attributes more carefully
  return (
    svgContent
      .replace(/\s*fill="[^"]*"/g, '')
      .replace(/\s*stroke="[^"]*"/g, '')
      .replace(/\s*fill='[^']*'/g, '')
      .replace(/\s*stroke='[^']*'/g, '')
      // Remove any double spaces that may have resulted
      .replace(/\s+/g, ' ')
      .trim()
  );
}

async function buildIcons() {
  const svgFiles = await glob('*.svg', {
    cwd: ICONS_SOURCE_DIR,
  });

  if (!svgFiles.length) {
    throw new Error(`No SVG files found in ${ICONS_SOURCE_DIR}`);
  }

  const symbols = [];
  const names = [];

  for (const file of svgFiles.sort()) {
    const name = path.basename(file, '.svg');
    const raw = fs.readFileSync(path.join(ICONS_SOURCE_DIR, file), 'utf8');

    const viewBox = VIEWBOX_RE.exec(raw)?.[1] ?? '0 0 24 24';
    const bodyMatch = SVG_SYMBOL_RE.exec(raw);
    let body = bodyMatch ? bodyMatch[1].trim() : raw.trim();

    // Strip inline fill/stroke to allow currentColor
    body = stripSVGFillStroke(body);

    names.push(name);
    symbols.push(`<symbol id="icon-${name}" viewBox="${viewBox}">${body}</symbol>`);
  }

  const spriteContent = [
    '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden">',
    symbols.join('\n'),
    '</svg>',
    '',
  ].join('\n');

  const listContent = JSON.stringify({ all: names, regular: names, poi: [] }, null, 2) + '\n';

  // Only write if content changed (prevents infinite reload in watch mode)
  let spriteChanged = true;
  let listChanged = true;

  if (fs.existsSync(SPRITE_OUTPUT_PATH)) {
    const existing = fs.readFileSync(SPRITE_OUTPUT_PATH, 'utf8');
    spriteChanged = existing !== spriteContent;
  }

  if (fs.existsSync(LIST_PATH)) {
    const existing = fs.readFileSync(LIST_PATH, 'utf8');
    listChanged = existing !== listContent;
  }

  if (spriteChanged) {
    fs.mkdirSync(path.dirname(SPRITE_OUTPUT_PATH), { recursive: true });
    fs.writeFileSync(SPRITE_OUTPUT_PATH, spriteContent, 'utf8');
    console.log(`✔ Updated sprite (${symbols.length} symbols) → ${SPRITE_OUTPUT_PATH}`);
  }

  if (listChanged) {
    fs.mkdirSync(path.dirname(LIST_PATH), { recursive: true });
    fs.writeFileSync(LIST_PATH, listContent, 'utf8');
    console.log(`✔ Updated icons list (${names.length} entries) → ${LIST_PATH}`);
  }

  if (!spriteChanged && !listChanged) {
    console.log(`✔ No changes detected (${symbols.length} icons)`);
  }
}

const isWatch = process.argv.includes('--watch');

async function run() {
  await buildIcons();

  if (!isWatch) {
    return;
  }

  console.log(`👀 Watching ${ICONS_SOURCE_DIR} for SVG changes...`);

  let timer = null;
  let lastWriteTime = Date.now();
  const debounce = (fn, delay = 150) => {
    clearTimeout(timer);
    timer = setTimeout(fn, delay);
  };

  fs.watch(ICONS_SOURCE_DIR, { recursive: true }, (event, filename) => {
    // Ignore events on non-SVG files
    if (!filename || !filename.endsWith('.svg')) return;

    // Ignore if we just wrote to the files (prevent self-triggering)
    if (Date.now() - lastWriteTime < 500) return;

    debounce(async () => {
      try {
        await buildIcons();
        lastWriteTime = Date.now();
      } catch (error) {
        console.error('Icon build failed during watch:', error);
      }
    });
  });
}

run().catch((error) => {
  console.error('Icon build failed:', error);
  process.exit(1);
});
