/**
 * BNPPRE icon build script.
 *
 * What this script does:
 * 1) Renames SVG files to a strict, searchable naming convention.
 * 2) Normalizes dark color declarations (fill/stroke) to `currentColor`.
 * 3) Removes `fill` attributes from SVGs (color controlled by CSS).
 *
 * In normal mode, files are modified in place.
 * In `--check` mode, no file is modified and the script exits non-zero if any file
 * does not comply with naming/color/attribute rules.
 *
 * Naming rules implemented:
 * - ASCII only (accents/cedillas removed)
 * - lowercase only
 * - spaces/special characters converted to `-`
 * - French stop words removed when possible
 * - unique names guaranteed across all icon files
 * - max length: 30 characters (without extension)
 *
 * Usage:
 *   node scripts/bnppre-icon.js
 *   node scripts/bnppre-icon.js --check
 */

const fs = require('fs');
const path = require('path');

const ICONS_DIR = path.resolve(__dirname, '..', 'assets', 'icons');
const MAX_NAME_LENGTH = 30;
const CHECK_MODE = process.argv.includes('--check');

const STOP_WORDS = new Set([
  'le',
  'la',
  'les',
  'du',
  'de',
  'des',
  'en',
  'pour',
  'donc',
  'sur',
  'dans',
  'avec',
  'sans',
  'par',
  'et',
  'ou',
  'au',
  'aux',
  'un',
  'une',
  'd',
  'l',
]);

// Colors to replace with currentColor (covers both long and SVGO-minified short forms).
const COLOR_REPLACEMENTS = [
  // Attribute form: fill="..."
  [/fill="#333333"/gi, 'fill="currentColor"'],
  [/fill="#333"(?![0-9a-f])/gi, 'fill="currentColor"'],
  [/fill="#000000"/gi, 'fill="currentColor"'],
  [/fill="#000"(?![0-9a-f])/gi, 'fill="currentColor"'],
  [/fill="#1a1a1a"/gi, 'fill="currentColor"'],
  [/fill="#050505"/gi, 'fill="currentColor"'],
  [/fill="black"/gi, 'fill="currentColor"'],
  [/stroke="#333333"/gi, 'stroke="currentColor"'],
  [/stroke="#333"(?![0-9a-f])/gi, 'stroke="currentColor"'],
  [/stroke="#000000"/gi, 'stroke="currentColor"'],
  [/stroke="#000"(?![0-9a-f])/gi, 'stroke="currentColor"'],
  [/stroke="#1a1a1a"/gi, 'stroke="currentColor"'],
  [/stroke="#050505"/gi, 'stroke="currentColor"'],
  [/stroke="black"/gi, 'stroke="currentColor"'],
  // Style form: fill:... (inline CSS, before SVGO conversion)
  [/fill:\s*#333333/gi, 'fill:currentColor'],
  [/fill:\s*#333(?![0-9a-f])/gi, 'fill:currentColor'],
  [/fill:\s*#000000/gi, 'fill:currentColor'],
  [/fill:\s*#000(?![0-9a-f])/gi, 'fill:currentColor'],
  [/fill:\s*#1a1a1a/gi, 'fill:currentColor'],
  [/fill:\s*#050505/gi, 'fill:currentColor'],
  [/fill:\s*black/gi, 'fill:currentColor'],
  [/stroke:\s*#333333/gi, 'stroke:currentColor'],
  [/stroke:\s*#333(?![0-9a-f])/gi, 'stroke:currentColor'],
  [/stroke:\s*#000000/gi, 'stroke:currentColor'],
  [/stroke:\s*#000(?![0-9a-f])/gi, 'stroke:currentColor'],
  [/stroke:\s*#1a1a1a/gi, 'stroke:currentColor'],
  [/stroke:\s*#050505/gi, 'stroke:currentColor'],
  [/stroke:\s*black/gi, 'stroke:currentColor'],
];

let totalFiles = 0;
let renamedFiles = 0;
let cleanedFiles = 0;

const usedNames = new Set();

function getSvgFiles(rootDir) {
  const result = [];

  function walk(dir) {
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
      const fullPath = path.join(dir, entry.name);
      if (entry.isDirectory()) {
        walk(fullPath);
      } else if (entry.isFile() && entry.name.toLowerCase().endsWith('.svg')) {
        result.push(fullPath);
      }
    }
  }

  walk(rootDir);
  return result.sort((a, b) => a.localeCompare(b));
}

function stripAccents(value) {
  return value
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');
}

function toSlug(value) {
  const ascii = stripAccents(value).toLowerCase();
  return ascii
    // Keep letters, digits and separators, convert everything else to '-'.
    .replace(/[^a-z0-9\s_-]+/g, '-')
    .replace(/[\s_]+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
}

function removeStopWords(slug) {
  const tokens = slug.split('-').filter(Boolean);
  const filtered = tokens.filter((token) => !STOP_WORDS.has(token));
  if (filtered.length === 0) {
    return tokens.join('-');
  }
  return filtered.join('-');
}

function truncateSlug(slug, maxLength) {
  if (slug.length <= maxLength) {
    return slug;
  }

  const tokens = slug.split('-').filter(Boolean);
  const picked = [];
  let currentLength = 0;

  for (const token of tokens) {
    const tokenLength = picked.length === 0 ? token.length : token.length + 1;
    if (currentLength + tokenLength > maxLength) {
      break;
    }
    picked.push(token);
    currentLength += tokenLength;
  }

  if (picked.length > 0) {
    return picked.join('-');
  }

  return slug.slice(0, maxLength).replace(/-+$/g, '');
}

function buildBaseName(originalName) {
  let slug = toSlug(originalName);
  slug = removeStopWords(slug);
  slug = truncateSlug(slug, MAX_NAME_LENGTH);

  if (!slug) {
    return 'icon';
  }
  return slug;
}

function trimForSuffix(base, suffix) {
  const maxBaseLength = Math.max(1, MAX_NAME_LENGTH - suffix.length);
  return base.slice(0, maxBaseLength).replace(/-+$/g, '') || 'icon';
}

function uniqueName(base, group, namesSet = usedNames) {
  if (!namesSet.has(base)) {
    namesSet.add(base);
    return base;
  }

  const groupSlug = truncateSlug(toSlug(group), 8) || 'g';
  let candidate = `${trimForSuffix(base, `-${groupSlug}`)}-${groupSlug}`;

  if (!namesSet.has(candidate)) {
    namesSet.add(candidate);
    return candidate;
  }

  let index = 2;
  while (true) {
    const suffix = `-v${index}`;
    candidate = `${trimForSuffix(base, suffix)}${suffix}`;
    if (!namesSet.has(candidate)) {
      namesSet.add(candidate);
      return candidate;
    }
    index++;
  }
}

function safeRename(oldPath, newPath) {
  // Handle case-only renames safely on case-insensitive file systems.
  if (oldPath.toLowerCase() === newPath.toLowerCase() && oldPath !== newPath) {
    const tmpPath = `${newPath}.tmp-rename`;
    fs.renameSync(oldPath, tmpPath);
    fs.renameSync(tmpPath, newPath);
    return;
  }

  fs.renameSync(oldPath, newPath);
}

function normalizeAndRename(svgPath) {
  const dir = path.dirname(svgPath);
  const group = path.basename(dir);
  const ext = path.extname(svgPath);
  const oldBase = path.basename(svgPath, ext);

  const normalizedBase = buildBaseName(oldBase);
  const finalBase = uniqueName(normalizedBase, group);

  const newPath = path.join(dir, `${finalBase}${ext.toLowerCase()}`);
  if (newPath !== svgPath) {
    safeRename(svgPath, newPath);
    renamedFiles++;
    console.log(`Renamed: ${path.relative(ICONS_DIR, svgPath)} -> ${path.relative(ICONS_DIR, newPath)}`);
    return newPath;
  }

  return svgPath;
}

function replaceDarkColors(svgPath) {
  const source = fs.readFileSync(svgPath, 'utf8');
  let updated = source;

  for (const [pattern, replacement] of COLOR_REPLACEMENTS) {
    updated = updated.replace(pattern, replacement);
  }

  if (updated !== source) {
    fs.writeFileSync(svgPath, updated, 'utf8');
    cleanedFiles++;
    console.log(`Recolored: ${path.relative(ICONS_DIR, svgPath)}`);
  }
}

function removeFillAttributes(svgPath) {
  const source = fs.readFileSync(svgPath, 'utf8');
  let updated = source;

  // Remove fill attributes globally, including on <path>.
  // The Drupal icon wrapper controls final color.
  updated = updated
    .replace(/\sfill=("[^"]*"|'[^']*')/gi, '');

  // Remove fill declarations from inline styles when still present.
  updated = updated.replace(/\sstyle=("[^"]*"|'[^']*')/gi, (fullMatch, quotedStyle) => {
    const quote = quotedStyle[0];
    const styleBody = quotedStyle.slice(1, -1);
    const kept = styleBody
      .split(';')
      .map((decl) => decl.trim())
      .filter(Boolean)
      .filter((decl) => !/^fill\s*:/i.test(decl));

    if (kept.length === 0) {
      return '';
    }

    return ` style=${quote}${kept.join(';')}${quote}`;
  });

  if (updated !== source) {
    fs.writeFileSync(svgPath, updated, 'utf8');
    cleanedFiles++;
    console.log(`Cleaned attrs: ${path.relative(ICONS_DIR, svgPath)}`);
  }
}

function getContentIssues(svgPath) {
  const source = fs.readFileSync(svgPath, 'utf8');
  const issues = [];

  if (/\sfill=("[^"]*"|'[^']*')/i.test(source)) {
    issues.push('has fill attribute');
  }
  if (/fill\s*:/i.test(source)) {
    issues.push('has fill style declaration');
  }

  const darkColorPattern = /(fill|stroke)\s*=\s*("|')(#333333|#333|#000000|#000|#1a1a1a|#050505|black)\2/i;
  const darkStylePattern = /(fill|stroke)\s*:\s*(#333333|#333|#000000|#000|#1a1a1a|#050505|black)/i;
  if (darkColorPattern.test(source) || darkStylePattern.test(source)) {
    issues.push('has hardcoded dark fill/stroke color');
  }

  return issues;
}

function runCheck(files) {
  const checkNames = new Set();
  const violations = [];

  for (const svgPath of files) {
    const dir = path.dirname(svgPath);
    const group = path.basename(dir);
    const ext = path.extname(svgPath);
    const oldBase = path.basename(svgPath, ext);
    const normalizedBase = buildBaseName(oldBase);
    const expectedBase = uniqueName(normalizedBase, group, checkNames);

    if (expectedBase !== oldBase || ext !== '.svg') {
      violations.push(
        `${path.relative(ICONS_DIR, svgPath)}: expected filename ${expectedBase}.svg`
      );
    }

    for (const issue of getContentIssues(svgPath)) {
      violations.push(`${path.relative(ICONS_DIR, svgPath)}: ${issue}`);
    }
  }

  if (violations.length > 0) {
    console.error('Check failed. Icon files are not normalized:');
    for (const violation of violations) {
      console.error(`- ${violation}`);
    }
    console.error(`Total violations: ${violations.length}`);
    process.exit(1);
  }

  console.log(`Check passed. ${files.length} SVG files are normalized.`);
}

function main() {
  if (!fs.existsSync(ICONS_DIR)) {
    console.error(`Icons directory not found: ${ICONS_DIR}`);
    process.exit(1);
  }

  const files = getSvgFiles(ICONS_DIR);
  totalFiles = files.length;

  if (CHECK_MODE) {
    runCheck(files);
    return;
  }

  const renamedPaths = [];
  for (const svgPath of files) {
    const updatedPath = normalizeAndRename(svgPath);
    renamedPaths.push(updatedPath);
  }

  for (const svgPath of renamedPaths) {
    replaceDarkColors(svgPath);
    removeFillAttributes(svgPath);
  }

  console.log('');
  console.log(`Done. ${renamedFiles}/${totalFiles} renamed, ${cleanedFiles}/${totalFiles} cleaned.`);
}

main();
