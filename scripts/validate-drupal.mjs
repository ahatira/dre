#!/usr/bin/env node
/**
 * Validate Twig templates for Drupal compatibility.
 * Checks: attributes parameter usage, attributes|without('class'), includes with 'only', no arrow functions or JS methods.
 */
import { promises as fs } from 'node:fs';
import path from 'node:path';
import { globSync } from 'glob';

const root = path.resolve(process.cwd());

async function validateTwig(file) {
  const raw = await fs.readFile(file, 'utf8');
  // Strip Twig comments {# ... #} to avoid false positives from examples in comments
  const content = raw.replace(/\{#([\s\S]*?)#\}/g, '');
  const rel = path.relative(root, file);
  const issues = [];

  // Must include attribute handling (create_attribute() fallback or legacy without('class'))
  const usesCreateAttribute = /create_attribute\(\)/.test(content);
  const usesWithoutClass = /attributes\s*\|\s*without\('class'\)/.test(content);
  if (!usesCreateAttribute && !usesWithoutClass) {
    issues.push("Missing attribute handling (create_attribute() or attributes|without('class'))");
  }
  // Root should use create_attribute().addClass(...) and render via {{ attr }} (or legacy without('class'))
  const usesAddClass = /\.addClass\(/.test(content);
  const usesAttrOutput =
    /<[^>]+\{\{\s*attr\s*\}\}/.test(content) || /<[^>]+\{\{\s*wrapper_attr\s*\}\}/.test(content);
  if (!(usesAddClass || usesAttrOutput || usesWithoutClass)) {
    issues.push(
      "Root element should use create_attribute().addClass(...) and render via {{ attr }} (or legacy attributes|without('class'))"
    );
  }
  // Includes should have 'only'
  if (/{%\s*include\s+[^%]+%}/.test(content) && !/{%\s*include[^%]+only\s*%}/.test(content)) {
    issues.push("Include statements should use 'only' to prevent variable leakage");
  }
  // No arrow functions or JS array methods
  if (/=>/.test(content) || /\.map\(|\.filter\(|\.includes\(/.test(content)) {
    issues.push('Twig template uses JS syntax (arrow/functions) which is not supported in Drupal');
  }

  if (issues.length) {
    console.log(`\nDrupal validation issues in: ${rel}`);
    issues.forEach((i) => console.log(`- ${i}`));
    return false;
  }
  return true;
}

async function main() {
  const files = globSync('source/patterns/**/*.twig', { cwd: root });
  if (!files.length) {
    console.error('No Twig files found under source/patterns.');
    process.exit(1);
  }
  let ok = true;
  for (const f of files) {
    const pass = await validateTwig(path.join(root, f));
    if (!pass) ok = false;
  }
  if (ok) console.log('✅ Drupal Twig validation passed.');
  process.exit(ok ? 0 : 2);
}

main();
