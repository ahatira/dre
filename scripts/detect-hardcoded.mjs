#!/usr/bin/env node
/**
 * Detect hardcoded values (colors, sizes, durations) in CSS/Twig.
 */
import { promises as fs } from 'node:fs';
import path from 'node:path';
import { globSync } from 'glob';

const root = path.resolve(process.cwd());

const patterns = [
  { label: 'Hex color', regex: /#[0-9A-Fa-f]{3,6}\b/, files: ['**/*.css', '**/*.twig'] },
  { label: 'px size', regex: /\b\d+px\b/, files: ['**/*.css'] },
  { label: 'ms duration', regex: /\b\d+ms\b/, files: ['**/*.css'] },
  {
    label: 'named color',
    regex: /\b(red|green|blue|yellow|purple|gray|black|white)\b/,
    files: ['**/*.css'],
  },
];

async function scanFile(file) {
  const content = await fs.readFile(file, 'utf8');
  const lines = content.split(/\r?\n/);
  const issues = [];
  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    for (const p of patterns) {
      if (p.regex.test(line)) {
        issues.push({ line: i + 1, label: p.label, text: line.trim() });
      }
    }
  }
  return issues;
}

async function main() {
  const files = [];
  for (const p of patterns) {
    for (const gl of p.files) {
      const matches = globSync(path.join('source', 'patterns', gl), { cwd: root });
      matches.forEach((m) => files.push(path.join(root, m)));
    }
  }

  const seen = new Set();
  const uniqueFiles = files.filter((f) => {
    const k = f;
    if (seen.has(k)) return false;
    seen.add(k);
    return true;
  });
  let total = 0;
  for (const f of uniqueFiles) {
    const issues = await scanFile(f);
    if (issues.length) {
      console.log(`\nHardcoded values in: ${path.relative(root, f)}`);
      issues.forEach((i) => console.log(`- [${i.label}] Line ${i.line}: ${i.text}`));
      total += issues.length;
    }
  }
  if (total === 0) {
    console.log('✅ No hardcoded values detected in source/patterns.');
  } else {
    console.log(`\n⚠️ Found ${total} potential hardcoded values. Consider replacing with tokens.`);
    process.exitCode = 2;
  }
}

main();
