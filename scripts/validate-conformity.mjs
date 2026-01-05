#!/usr/bin/env node
/**
 * Validate component conformity (90-point audit simplified)
 * Checks: 4-file structure, Twig attributes, Storybook autodocs, CSS nesting, focus-visible, Twig JS syntax.
 */
import { promises as fs } from 'node:fs';
import path from 'node:path';
import { globSync } from 'glob';

const root = path.resolve(process.cwd());
const patternsDir = path.join(root, 'source', 'patterns');

function log(msg) {
  console.log(msg);
}
function fail(msg) {
  console.error(msg);
}

function readFileSafe(p) {
  return fs.readFile(p, 'utf8').catch(() => '');
}

function getComponents() {
  const levels = ['elements', 'components', 'collections', 'layouts', 'pages'];
  const dirs = [];
  for (const lvl of levels) {
    const levelDir = path.join(patternsDir, lvl);
    try {
      const pattern = path.posix.join(levelDir.replace(/\\/g, '/'), '*/');
      const items = globSync(pattern);
      items.forEach((d) => dirs.push(d));
    } catch {}
  }
  return dirs;
}

async function auditComponent(dir) {
  const name = path.basename(dir);
  const base = path.join(dir, name);
  const files = {
    twig: `${base}.twig`,
    css: `${base}.css`,
    yml: `${base}.yml`,
    stories: `${base}.stories.jsx`,
  };

  let score = 90;
  const report = [];

  // 1) 4-file structure
  for (const [key, file] of Object.entries(files)) {
    try {
      await fs.access(file);
    } catch {
      score -= 5;
      report.push(`Missing required file: ${path.relative(root, file)} (-5)`);
    }
  }

  // Load contents
  const twig = await readFileSafe(files.twig);
  const css = await readFileSafe(files.css);
  const stories = await readFileSafe(files.stories);

  // 2) Twig: attribute handling pattern (create_attribute fallback or legacy without('class'))
  if (twig) {
    const usesCreateAttribute = /create_attribute\(\)/.test(twig);
    const usesAddClass = /\.addClass\(/.test(twig);
    const usesAttrOutput =
      /<[^>]+\{\{\s*attr\s*\}\}/.test(twig) || /<[^>]+\{\{\s*wrapper_attr\s*\}\}/.test(twig);
    const usesWithoutClass = /attributes\s*\|\s*without\('class'\)/.test(twig);

    const handlesAttributes = usesCreateAttribute || usesWithoutClass;
    if (!handlesAttributes) {
      score -= 5;
      report.push(
        `Twig missing attribute handling (create_attribute() or attributes|without('class')) (-5)`
      );
    }

    const usesCanonicalPattern = (usesCreateAttribute && usesAddClass) || usesAttrOutput;
    if (!usesCanonicalPattern && !usesWithoutClass) {
      score -= 5;
      report.push(
        `Twig root should use create_attribute().addClass(...) and render via {{ attr }} (or legacy attributes|without('class')) (-5)`
      );
    }

    // 3) Twig: no arrow functions / JS methods
    if (/=>/.test(twig) || /\.map\(|\.filter\(|\.includes\(/.test(twig)) {
      score -= 5;
      report.push(`Twig uses JS syntax (arrow/functions) (-5)`);
    }
  }

  // 4) CSS: nesting and focus-visible
  if (css) {
    const hasNesting = /&__|&--|&:\w|\n\s*&\s*\{/.test(css);
    if (!hasNesting) {
      score -= 5;
      report.push(`CSS missing nesting with & syntax (-5)`);
    }
    const hasFocusVisible = /focus-visible/.test(css);
    // Not all components are interactive; warn but no score deduction.
    if (!hasFocusVisible) {
      report.push(`CSS: focus-visible not found (warn)`);
    }
  }

  // 5) Stories: autodocs tag
  if (stories) {
    const hasAutodocs = /tags\s*:\s*\[\s*'autodocs'\s*\]/.test(stories);
    if (!hasAutodocs && !dir.includes(path.join('source', 'patterns', 'base'))) {
      score -= 5;
      report.push(`Stories missing tags: ['autodocs'] (-5)`);
    }
  }

  return { name, dir, score, report };
}

async function main() {
  const components = getComponents();
  if (!components.length) {
    fail('No components found under source/patterns.');
    process.exit(1);
  }
  let ok = true;
  for (const dir of components) {
    const { name, score, report } = await auditComponent(dir);
    const status = score >= 80 ? '✅' : score >= 70 ? '⚠️' : '❌';
    log(`\nComponent: ${name} → Score: ${score}/90 ${status}`);
    if (report.length) {
      report.forEach((r) => log(`- ${r}`));
    }
    if (score < 80) ok = false;
  }
  process.exit(ok ? 0 : 2);
}

main();
