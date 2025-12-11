#!/usr/bin/env node
/**
 * Pattern generator for PS Theme (BNP Paribas RealEstate).
 *
 * Usage (interactive):
 *   npm run generate:pattern
 *
 * Usage (with flags):
 *   npm run generate:pattern -- --type=element --name="Badge"
 *   npm run generate:pattern -- --type=component --name="Card Header"
 *
 * Types: element, component, collection, layout, page
 * Generates: <slug>.twig, <slug>.yml, <slug>.css, <slug>.stories.jsx, README.md
 */
import { existsSync, mkdirSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import readline from 'node:readline';

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

function parseArgs() {
  const args = process.argv.slice(2);
  const parsed = { type: null, name: null };

  for (const arg of args) {
    if (arg.startsWith('--type=')) {
      parsed.type = arg.split('=')[1];
    } else if (arg.startsWith('--name=')) {
      parsed.name = arg.split('=')[1];
    } else if (!arg.startsWith('--')) {
      // Legacy positional args support
      if (!parsed.type) parsed.type = arg;
      else if (!parsed.name) parsed.name = arg;
    }
  }

  return parsed;
}

async function promptUser(question) {
  const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
  });

  return new Promise((resolve) => {
    rl.question(question, (answer) => {
      rl.close();
      resolve(answer.trim());
    });
  });
}

async function main() {
  let { type: rawType, name: rawName } = parseArgs();

  // Interactive mode if missing arguments
  if (!rawType) {
    console.log('\n📦 PS Theme Pattern Generator\n');
    console.log('Available types: element, component, collection, layout, page\n');
    rawType = await promptUser('Pattern type: ');
  }

  const dirType = typeMap[rawType];
  if (!dirType) {
    console.error(`❌ Unknown type '${rawType}'. Allowed: ${Object.keys(typeMap).join(', ')}`);
    process.exit(1);
  }

  if (!rawName) {
    rawName = await promptUser('Pattern name (e.g., "Badge", "Card Header"): ');
  }

  if (!rawName) {
    console.error('❌ Pattern name is required');
    process.exit(1);
  }

  const name = rawName.trim();
  const slug = slugify(name);
  const baseDir = path.resolve('source/patterns', dirType, slug);

  if (existsSync(baseDir)) {
    console.error(`❌ Pattern directory already exists: ${baseDir}`);
    process.exit(1);
  }

  console.log(`\n✨ Creating ${rawType}: ${name}\n`);
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
    `import markup from './${slug}.twig';\nimport data from './${slug}.yml';\n\nconst settings = {\n  title: '${TitleRoot}/${name}',\n  tags: ['autodocs'],\n  args: { ...data },\n};\n\nexport const ${ExportName.replace(/^(\d)/, '_$1')} = {\n  name: '${name}',\n  render: (args) => markup(args),\n  args: { ...data },\n};\n\nexport default settings;\n`
  );

  // README.md
  writeFileSync(
    path.join(baseDir, 'README.md'),
    `# ${name}\n\n**Type**: ${rawType.charAt(0).toUpperCase() + rawType.slice(1)}\n\n## Usage\n\n\`\`\`twig\n{% include '@${dirType}/${slug}/${slug}.twig' with {\n  text: 'Example text'\n} only %}\n\`\`\`\n\n## Props\n\n| Prop | Type | Default | Description |\n|------|------|---------|-------------|\n| text | string | - | Component text content |\n\n## BEM Structure\n\n\`\`\`\n.${slug}                 # Block\n  .${slug}__text         # Element\n\`\`\`\n\n## Design Tokens\n\n- Background: \`var(--primary)\`\n- Text: \`var(--white)\`\n- Padding: \`var(--size-2)\` \`var(--size-4)\`\n- Border radius: \`var(--radius-1)\`\n\n## Accessibility\n\n- Semantic HTML structure\n- WCAG 2.2 AA compliant\n\n## Examples\n\nSee \`${slug}.stories.jsx\` for usage examples.\n`
  );

  console.log('✅ Files generated:');
  console.log(`   - ${slug}.twig`);
  console.log(`   - ${slug}.css`);
  console.log(`   - ${slug}.yml`);
  console.log(`   - ${slug}.stories.jsx`);
  console.log(`   - README.md`);
  console.log(`\n📁 Location: ${baseDir}\n`);
  console.log('💡 Next steps:');
  console.log('   1. Update Twig template with proper structure');
  console.log('   2. Add design tokens to CSS');
  console.log('   3. Configure Storybook argTypes');
  console.log('   4. Document props in README.md');
  console.log('   5. Run: npm run watch\n');
}

main().catch((err) => {
  console.error('❌ Error:', err.message);
  process.exit(1);
});
