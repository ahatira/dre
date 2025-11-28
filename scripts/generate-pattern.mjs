#!/usr/bin/env node
/**
 * Pattern generator for PS Theme (BNP Paribas RealEstate).
 * Usage: node scripts/generate-pattern.mjs <type> <Name>
 * Types map to directories:
 *  element => elements
 *  component => components
 *  collection => collections
 *  layout => layouts
 *  page => pages
 * Generates: <slug>.twig, <slug>.yml, <slug>.css, <slug>.stories.jsx
 */
import { mkdirSync, writeFileSync, existsSync } from 'node:fs';
import path from 'node:path';

const typeMap = {
  element: 'elements',
  component: 'components',
  collection: 'collections',
  layout: 'layouts',
  page: 'pages',
};
const storyRoots = {
  element: 'Elements',
  component: 'Components',
  collection: 'Collections',
  layout: 'Layouts',
  page: 'Pages',
};

function slugify(str) {
  return str
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

const [, , rawType, rawName] = process.argv;
if (!rawType || !rawName) {
  console.error('Missing arguments. Usage: node scripts/generate-pattern.mjs <type> <Name>');
  process.exit(1);
}

const dirType = typeMap[rawType];
if (!dirType) {
  console.error(`Unknown type '${rawType}'. Allowed: ${Object.keys(typeMap).join(', ')}`);
  process.exit(1);
}

const name = rawName.trim();
const slug = slugify(name);
const baseDir = path.resolve('source/patterns', dirType, slug);
if (existsSync(baseDir)) {
  console.error(`Pattern directory already exists: ${baseDir}`);
  process.exit(1);
}
mkdirSync(baseDir, { recursive: true });

// Twig template.
writeFileSync(
  path.join(baseDir, `${slug}.twig`),
  `{# ${name} (${rawType}) - generated for PS Theme. #}\n<div class="${slug}">\n  {% if text %}<p class="${slug}__text">{{ text }}</p>{% endif %}\n</div>`
);

// YAML default data.
writeFileSync(
  path.join(baseDir, `${slug}.yml`),
  `text: '${name} component placeholder'\nmodifier: ''\n`
);

// CSS file using BNP brand token fallback.
writeFileSync(
  path.join(baseDir, `${slug}.css`),
  `@layer components {\n  .${slug} {\n    /* Base styling */\n    padding: 0.5rem 1rem;\n    background: var(--bnp-green, #00A862);\n    color: #fff;\n    border-radius: 4px;\n  }\n  .${slug}__text {\n    font: inherit;\n  }\n}`
);

// Storybook story.
const TitleRoot = storyRoots[rawType];
const ExportName = slug
  .split('-')
  .map((s, i) => (i === 0 ? s : s.charAt(0).toUpperCase() + s.slice(1)))
  .join('');

writeFileSync(
  path.join(baseDir, `${slug}.stories.jsx`),
  `import markup from './${slug}.twig';\nimport data from './${slug}.yml';\n\nconst settings = {\n  title: '${TitleRoot}/${name}',\n  args: { ...data },\n};\n\nexport const ${ExportName.replace(/^(\d)/, '_$1')} = {\n  name: '${name}',\n  render: (args) => markup(args),\n  args: { ...data },\n};\n\nexport default settings;\n`
);

console.log(`Pattern generated: ${baseDir}`);