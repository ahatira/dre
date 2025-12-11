#!/usr/bin/env node
/**
 * Token checker for PS Theme
 * Searches for a token name across all CSS files in source/props/
 *
 * Usage:
 *   node scripts/check-tokens.mjs <token-name>
 *   npm run tokens:check -- <token-name>
 *
 * Examples:
 *   npm run tokens:check -- --primary
 *   npm run tokens:check -- --size-badge
 *   npm run tokens:check -- --font-size-3
 */
import { readdirSync, readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const propsDir = path.resolve(__dirname, '../source/props');

const [, , tokenName] = process.argv;

if (!tokenName) {
  console.error('❌ Missing token name argument');
  console.log('\nUsage: npm run tokens:check -- <token-name>');
  console.log('\nExamples:');
  console.log('  npm run tokens:check -- --primary');
  console.log('  npm run tokens:check -- --size-badge');
  console.log('  npm run tokens:check -- --font-size-3');
  process.exit(1);
}

// Ensure token starts with --
const searchToken = tokenName.startsWith('--') ? tokenName : `--${tokenName}`;

console.log(`\n🔍 Searching for token: ${searchToken}\n`);

const cssFiles = readdirSync(propsDir)
  .filter((file) => file.endsWith('.css') && file !== 'index.css')
  .sort();

let found = false;
const results = [];

for (const file of cssFiles) {
  const filePath = path.join(propsDir, file);
  const content = readFileSync(filePath, 'utf-8');
  const lines = content.split('\n');

  const matches = [];
  lines.forEach((line, index) => {
    // Match token definition: --token-name: value;
    const definitionMatch = line.match(new RegExp(`^\\s*(${searchToken}):\\s*([^;]+);`, 'i'));
    if (definitionMatch) {
      matches.push({
        line: index + 1,
        type: 'definition',
        content: line.trim(),
        value: definitionMatch[2].trim(),
      });
    }

    // Match token usage: var(--token-name)
    const usageMatch = line.match(new RegExp(`var\\(${searchToken}\\)`, 'i'));
    if (usageMatch && !definitionMatch) {
      matches.push({
        line: index + 1,
        type: 'usage',
        content: line.trim(),
      });
    }
  });

  if (matches.length > 0) {
    found = true;
    results.push({ file, matches });
  }
}

if (!found) {
  console.log(`❌ Token "${searchToken}" not found in any props file\n`);
  console.log('💡 Searched in:');
  cssFiles.forEach((file) => console.log(`   - ${file}`));
  console.log('\n📝 To add a new token:');
  console.log('   1. See: .github/instructions/core.instructions.md (Token Verification Workflow)');
  console.log('   2. Document need in component README');
  console.log('   3. Propose via separate tokens-change PR/process');
  process.exit(1);
}

console.log('✅ Token found!\n');

results.forEach(({ file, matches }) => {
  console.log(`📄 ${file}`);
  matches.forEach((match) => {
    const icon = match.type === 'definition' ? '  ├─ 🎨' : '  ├─ 🔗';
    const label = match.type === 'definition' ? 'Definition' : 'Usage';
    console.log(`${icon} [Line ${match.line}] ${label}`);
    console.log(`     ${match.content}`);
    if (match.value) {
      console.log(`     Value: ${match.value}`);
    }
  });
  console.log();
});

console.log('📊 Summary:');
const totalDefinitions = results.reduce(
  (sum, r) => sum + r.matches.filter((m) => m.type === 'definition').length,
  0
);
const totalUsages = results.reduce(
  (sum, r) => sum + r.matches.filter((m) => m.type === 'usage').length,
  0
);
console.log(`   Definitions: ${totalDefinitions}`);
console.log(`   Usages: ${totalUsages}`);
console.log(`   Files: ${results.length}`);
console.log();
