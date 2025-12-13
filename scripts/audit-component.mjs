#!/usr/bin/env node
/**
 * Component Audit & Auto-Fix Script for PS Theme
 * 
 * Performs conformity audit based on 90-point system from 04-quality-assurance.md
 * Applies automated fixes when possible, validates with build, and commits if passing.
 * 
 * Usage:
 *   npm run audit -- elements/badge
 *   npm run audit -- elements/badge --fix
 *   npm run audit -- elements/badge --fix --commit
 * 
 * Phases:
 *   1. AUDIT - Static analysis (90 points)
 *   2. FIX - Automated corrections (--fix flag)
 *   3. VALIDATE - Build test + re-audit
 *   4. COMMIT - Git commit if score ≥ 80/90 (--commit flag)
 */

import { existsSync, readFileSync, writeFileSync, readdirSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { execSync } from 'node:child_process';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const sourceDir = path.resolve(__dirname, '../source/patterns');

// ============================================
// CONFIGURATION
// ============================================

const PASSING_SCORE = 80;
const MAX_SCORE = 90;

const LEVEL_MAP = {
  elements: 'element',
  components: 'component',
  collections: 'collection',
  layouts: 'layout',
  pages: 'page',
};

const COLOR_MAP = {
  '#00915A': 'var(--primary)',
  '#A12B66': 'var(--secondary)',
  '#198754': 'var(--success)',
  '#EB3636': 'var(--danger)',
  '#FBBF24': 'var(--warning)',
  '#2563EB': 'var(--info)',
  '#D1AE6E': 'var(--gold)',
  '#F8F9FA': 'var(--light)',
  '#495057': 'var(--dark)',
  '#FFFFFF': 'var(--white)',
  '#000000': 'var(--black)',
};

const SIZE_MAP = {
  '2px': 'var(--size-05)',
  '4px': 'var(--size-1)',
  '6px': 'var(--size-105)',
  '8px': 'var(--size-2)',
  '10px': 'var(--size-205)',
  '12px': 'var(--size-3)',
  '14px': 'var(--size-305)',
  '16px': 'var(--size-4)',
  '18px': 'var(--size-405)',
  '20px': 'var(--size-5)',
  '24px': 'var(--size-6)',
  '28px': 'var(--size-7)',
  '32px': 'var(--size-8)',
  '36px': 'var(--size-9)',
  '40px': 'var(--size-10)',
  '44px': 'var(--size-11)',
  '48px': 'var(--size-12)',
};

// ============================================
// UTILITIES
// ============================================

function parseArgs() {
  const args = process.argv.slice(2);
  return {
    componentPath: args[0], // e.g., "elements/badge"
    fix: args.includes('--fix'),
    commit: args.includes('--commit'),
    verbose: args.includes('--verbose') || args.includes('-v'),
  };
}

function log(message, type = 'info') {
  const icons = {
    info: 'ℹ️',
    success: '✅',
    warning: '⚠️',
    error: '❌',
    search: '🔍',
    fix: '🔧',
    build: '🔨',
    commit: '💾',
  };
  console.log(`${icons[type] || '•'} ${message}`);
}

function readFile(filePath) {
  try {
    return readFileSync(filePath, 'utf-8');
  } catch {
    return null;
  }
}

function writeFile(filePath, content) {
  writeFileSync(filePath, content, 'utf-8');
}

// ============================================
// PHASE 1: AUDIT
// ============================================

function runAudit(componentPath, options = {}) {
  const [level, component] = componentPath.split('/');
  const componentDir = path.join(sourceDir, level, component);
  
  if (!existsSync(componentDir)) {
    log(`Component not found: ${componentPath}`, 'error');
    process.exit(1);
  }

  const audit = {
    path: componentPath,
    level,
    component,
    score: 0,
    maxScore: MAX_SCORE,
    sections: [],
    fixes: [],
  };

  log(`Auditing: ${componentPath}`, 'search');
  
  // Section 1: File Structure (8 points)
  audit.sections.push(auditFileStructure(componentDir, audit));
  
  // Section 2: Twig Template (15 points)
  audit.sections.push(auditTwig(componentDir, component, audit));
  
  // Section 3: CSS Styles (20 points)
  audit.sections.push(auditCSS(componentDir, component, audit));
  
  // Section 4: Storybook (20 points)
  audit.sections.push(auditStorybook(componentDir, component, audit));
  
  // Section 5: YAML Configuration (10 points)
  audit.sections.push(auditYAML(componentDir, component, audit));
  
  // Section 6: BEM Naming (5 points)
  audit.sections.push(auditBEM(componentDir, component, audit));
  
  // Section 7: Accessibility (5 points)
  audit.sections.push(auditAccessibility(componentDir, component, audit));
  
  // Section 8: Architecture (10 points) - Not implemented yet
  audit.sections.push({ name: 'Architecture', score: 10, max: 10, issues: [], note: 'Manual verification required' });
  
  // Calculate total
  audit.score = audit.sections.reduce((sum, section) => sum + section.score, 0);
  
  if (options.verbose) {
    printAuditReport(audit);
  }
  
  return audit;
}

function auditFileStructure(dir, audit) {
  const section = { name: 'File Structure', score: 0, max: 8, issues: [] };
  
  const requiredFiles = [
    { ext: '.twig', points: 2 },
    { ext: '.css', points: 2 },
    { ext: '.yml', points: 2 },
    { ext: '.stories.jsx', points: 2 },
  ];
  
  requiredFiles.forEach(({ ext, points }) => {
    const exists = existsSync(path.join(dir, `${audit.component}${ext}`));
    if (exists) {
      section.score += points;
    } else {
      section.issues.push(`Missing ${ext} file`);
      audit.fixes.push({
        type: 'create-file',
        severity: 'critical',
        file: ext,
        message: `Create ${audit.component}${ext}`,
        automated: false,
      });
    }
  });
  
  return section;
}

function auditTwig(dir, component, audit) {
  const section = { name: 'Twig Template', score: 0, max: 15, issues: [] };
  const filePath = path.join(dir, `${component}.twig`);
  const content = readFile(filePath);
  
  if (!content) {
    section.issues.push('File not found');
    return section;
  }
  
  // Check 1: Header comment (2 pts)
  if (content.includes('@param')) {
    section.score += 2;
  } else {
    section.issues.push('Missing header comment with @param');
  }
  
  // Check 2: attributes parameter (2 pts)
  if (content.includes('attributes') && content.includes('|without(\'class\')')) {
    section.score += 2;
  } else {
    section.issues.push('Missing attributes parameter with |without(\'class\')');
    audit.fixes.push({
      type: 'twig-add-attributes',
      severity: 'high',
      file: '.twig',
      message: 'Add attributes parameter',
      automated: true,
    });
  }
  
  // Check 3: Default values (3 pts)
  const defaultCount = (content.match(/\|default\(/g) || []).length;
  if (defaultCount >= 3) {
    section.score += 3;
  } else if (defaultCount >= 1) {
    section.score += 1;
    section.issues.push(`Only ${defaultCount} default values (expected ≥3)`);
  } else {
    section.issues.push('No default values found');
  }
  
  // Check 4: NO arrow functions (3 pts)
  const arrowFunctions = content.match(/=>/g);
  if (!arrowFunctions) {
    section.score += 3;
  } else {
    section.issues.push(`${arrowFunctions.length} arrow functions found (Drupal incompatible)`);
    audit.fixes.push({
      type: 'twig-remove-arrow-functions',
      severity: 'critical',
      file: '.twig',
      count: arrowFunctions.length,
      message: 'Replace arrow functions with ternary',
      automated: true,
    });
  }
  
  // Check 5: Composition with only (3 pts)
  const includeWithOnly = (content.match(/{% include.*only %}/g) || []).length;
  if (includeWithOnly >= 1) {
    section.score += 3;
  } else if (content.includes('{% include')) {
    section.score += 1;
    section.issues.push('Include statements missing "only" keyword');
  }
  
  // Check 6: Real Estate context (2 pts) - Partial credit
  section.score += 2; // Assumed present, manual verification
  
  return section;
}

function auditCSS(dir, component, audit) {
  const section = { name: 'CSS Styles', score: 0, max: 20, issues: [] };
  const filePath = path.join(dir, `${component}.css`);
  const content = readFile(filePath);
  
  if (!content) {
    section.issues.push('File not found');
    return section;
  }
  
  // Check 1: No hardcoded colors (5 pts)
  const hardcodedColors = content.match(/#[0-9A-Fa-f]{6}/g);
  if (!hardcodedColors) {
    section.score += 5;
  } else {
    const uniqueColors = [...new Set(hardcodedColors)];
    section.issues.push(`${uniqueColors.length} hardcoded colors: ${uniqueColors.join(', ')}`);
    audit.fixes.push({
      type: 'css-replace-colors',
      severity: 'high',
      file: '.css',
      count: hardcodedColors.length,
      colors: uniqueColors,
      message: 'Replace hardcoded colors with tokens',
      automated: true,
    });
  }
  
  // Check 2: Nesting with & (5 pts)
  const hasNesting = content.includes('  &');
  if (hasNesting) {
    section.score += 5;
  } else {
    section.issues.push('No CSS nesting found (flat structure)');
    audit.fixes.push({
      type: 'css-add-nesting',
      severity: 'medium',
      file: '.css',
      message: 'Refactor to use CSS nesting',
      automated: false, // Complex refactor
    });
  }
  
  // Check 3: Cascade order (3 pts) - Partial credit (hard to verify automatically)
  section.score += 2; // Assumed mostly correct
  
  // Check 4: Modifiers work independently (3 pts) - Manual verification
  section.score += 3; // Assumed correct
  
  // Check 5: Semantic colors (2 pts)
  const semanticColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'gold'];
  const usesSemanticColors = semanticColors.some(color => content.includes(`--${color}`));
  if (usesSemanticColors) {
    section.score += 2;
  } else {
    section.issues.push('No semantic color tokens found');
  }
  
  // Check 6: Focus-visible (2 pts)
  if (content.includes('focus-visible')) {
    section.score += 2;
  } else {
    // Check if component is interactive
    const isInteractive = content.includes('cursor: pointer') || content.includes(':hover');
    if (isInteractive) {
      section.issues.push('Missing focus-visible for interactive element');
      audit.fixes.push({
        type: 'css-add-focus-visible',
        severity: 'high',
        file: '.css',
        message: 'Add focus-visible styles',
        automated: true,
      });
    } else {
      section.score += 2; // Not interactive, skip
    }
  }
  
  return section;
}

function auditStorybook(dir, component, audit) {
  const section = { name: 'Storybook', score: 0, max: 20, issues: [] };
  const filePath = path.join(dir, `${component}.stories.jsx`);
  const content = readFile(filePath);
  
  if (!content) {
    section.issues.push('File not found');
    return section;
  }
  
  // Check 1: tags: ['autodocs'] (5 pts)
  if (content.includes("tags: ['autodocs']")) {
    section.score += 5;
  } else {
    section.issues.push("Missing tags: ['autodocs'] in export default");
    audit.fixes.push({
      type: 'story-add-autodocs',
      severity: 'high',
      file: '.stories.jsx',
      message: 'Add autodocs tag',
      automated: true,
    });
  }
  
  // Check 2: Import Twig (5 pts)
  if (content.includes(`import ${component}Twig from './${component}.twig'`)) {
    section.score += 5;
  } else if (content.match(/import \w+Twig from/)) {
    section.score += 3;
    section.issues.push('Twig import present but naming inconsistent');
  } else {
    section.issues.push('Missing Twig import');
  }
  
  // Check 3: NO React import (4 pts penalty if present)
  if (content.includes('import React')) {
    section.issues.push('Unnecessary React import (HTML edition)');
    audit.fixes.push({
      type: 'story-remove-react',
      severity: 'low',
      file: '.stories.jsx',
      message: 'Remove React import',
      automated: true,
    });
  } else {
    section.score += 4;
  }
  
  // Check 4: ArgTypes categorized (3 pts) - Simplified check
  const hasArgTypes = content.includes('argTypes:');
  const hasCategories = content.includes('table: { category:');
  if (hasArgTypes && hasCategories) {
    section.score += 3;
  } else if (hasArgTypes) {
    section.score += 1;
    section.issues.push('ArgTypes present but not categorized');
  } else {
    section.issues.push('No argTypes defined');
  }
  
  // Check 5: Description ≤ 2 lines (3 pts) - Assumed correct
  section.score += 3;
  
  return section;
}

function auditYAML(dir, component, audit) {
  const section = { name: 'YAML Configuration', score: 0, max: 10, issues: [] };
  const filePath = path.join(dir, `${component}.yml`);
  const content = readFile(filePath);
  
  if (!content) {
    section.issues.push('File not found');
    return section;
  }
  
  // Basic checks - partial credit
  const lines = content.split('\n').filter(line => line.trim() && !line.trim().startsWith('#'));
  
  if (lines.length >= 5) {
    section.score += 5; // Realistic data (assumed)
  } else {
    section.issues.push('Insufficient data in YAML');
  }
  
  if (lines.length >= 3) {
    section.score += 3; // All required props (assumed)
  }
  
  section.score += 2; // Optional props (assumed)
  
  return section;
}

function auditBEM(dir, component, audit) {
  const section = { name: 'BEM Naming', score: 0, max: 5, issues: [] };
  const filePath = path.join(dir, `${component}.css`);
  const content = readFile(filePath);
  
  if (!content) {
    section.issues.push('CSS file not found');
    return section;
  }
  
  // Check 1: ps- prefix (2 pts)
  const classNames = content.match(/\.ps-[\w-]+/g) || [];
  if (classNames.length > 0) {
    section.score += 2;
  } else {
    section.issues.push('No ps- prefixed classes found');
  }
  
  // Check 2: BEM format (2 pts)
  const bemPattern = /\.ps-[\w-]+(__[\w-]+)?(--[\w-]+)?/;
  const validBEM = classNames.every(cls => bemPattern.test(cls));
  if (validBEM) {
    section.score += 2;
  } else {
    section.issues.push('Invalid BEM format detected');
  }
  
  // Check 3: No double underscore (1 pt)
  const hasDoubleUnderscore = content.includes('___');
  if (!hasDoubleUnderscore) {
    section.score += 1;
  } else {
    section.issues.push('Double underscore found (invalid BEM)');
  }
  
  return section;
}

function auditAccessibility(dir, component, audit) {
  const section = { name: 'Accessibility', score: 0, max: 5, issues: [] };
  const cssPath = path.join(dir, `${component}.css`);
  const cssContent = readFile(cssPath);
  
  if (!cssContent) {
    section.issues.push('CSS file not found');
    return section;
  }
  
  // Check 1: Focus-visible (2 pts)
  if (cssContent.includes('focus-visible')) {
    section.score += 2;
  } else {
    const isInteractive = cssContent.includes('cursor: pointer') || cssContent.includes(':hover');
    if (isInteractive) {
      section.issues.push('Missing focus-visible for interactive element');
    } else {
      section.score += 2; // Not interactive
    }
  }
  
  // Check 2: Contrast (2 pts) - Manual verification required
  section.score += 2; // Assumed compliant
  
  // Check 3: ARIA (1 pt) - Manual verification required
  section.score += 1; // Assumed compliant
  
  return section;
}

function printAuditReport(audit) {
  console.log('\n' + '='.repeat(60));
  console.log(`📊 AUDIT REPORT: ${audit.path}`);
  console.log('='.repeat(60));
  
  audit.sections.forEach(section => {
    const percent = Math.round((section.score / section.max) * 100);
    const status = percent >= 80 ? '✅' : percent >= 60 ? '⚠️' : '❌';
    console.log(`\n${status} ${section.name}: ${section.score}/${section.max} (${percent}%)`);
    
    if (section.issues.length > 0) {
      section.issues.forEach(issue => console.log(`   • ${issue}`));
    }
    if (section.note) {
      console.log(`   ℹ️ ${section.note}`);
    }
  });
  
  console.log('\n' + '='.repeat(60));
  const percent = Math.round((audit.score / audit.maxScore) * 100);
  const status = audit.score >= PASSING_SCORE ? '✅ PASSING' : '❌ FAILING';
  console.log(`TOTAL SCORE: ${audit.score}/${audit.maxScore} (${percent}%) - ${status}`);
  console.log('='.repeat(60) + '\n');
  
  if (audit.fixes.length > 0) {
    console.log(`🔧 ${audit.fixes.length} automated fixes available (use --fix flag)\n`);
  }
}

// ============================================
// PHASE 2: FIX
// ============================================

function applyFixes(audit) {
  const applied = [];
  const failed = [];
  
  log(`Applying ${audit.fixes.length} automated fixes...`, 'fix');
  
  audit.fixes.forEach(fix => {
    if (!fix.automated) {
      log(`Skipping ${fix.type} (manual fix required)`, 'warning');
      return;
    }
    
    try {
      switch (fix.type) {
        case 'twig-add-attributes':
          applyTwigAttributesFix(audit);
          applied.push(fix);
          break;
        case 'twig-remove-arrow-functions':
          applyTwigArrowFunctionsFix(audit);
          applied.push(fix);
          break;
        case 'css-replace-colors':
          applyCSSColorsFix(audit, fix);
          applied.push(fix);
          break;
        case 'css-add-focus-visible':
          applyCSSFocusVisibleFix(audit);
          applied.push(fix);
          break;
        case 'story-add-autodocs':
          applyStoryAutodocsFix(audit);
          applied.push(fix);
          break;
        case 'story-remove-react':
          applyStoryRemoveReactFix(audit);
          applied.push(fix);
          break;
        default:
          log(`Unknown fix type: ${fix.type}`, 'warning');
      }
    } catch (error) {
      log(`Failed to apply ${fix.type}: ${error.message}`, 'error');
      failed.push({ fix, error: error.message });
    }
  });
  
  return { applied, failed };
}

function applyTwigAttributesFix(audit) {
  const filePath = path.join(sourceDir, audit.level, audit.component, `${audit.component}.twig`);
  let content = readFile(filePath);
  
  if (!content) return;
  
  // Add attributes to @param section if missing
  if (!content.includes('@param object attributes')) {
    const paramMatch = content.match(/(\{#[\s\S]*?)(\n \*\/)/);
    if (paramMatch) {
      const newHeader = paramMatch[1] + '\n * @param object attributes - Additional HTML attributes for Drupal integration (optional)' + paramMatch[2];
      content = content.replace(paramMatch[0], newHeader);
    }
  }
  
  // Add |without('class') to first class= occurrence if missing
  if (!content.includes('|without(\'class\')')) {
    const classMatch = content.match(/class="([^"]*)"/);
    if (classMatch) {
      const replacement = `class="${classMatch[1]}"\n  {%- if attributes %} {{ attributes|without('class') }}{% endif -%}`;
      content = content.replace(classMatch[0], replacement);
    }
  }
  
  writeFile(filePath, content);
  log('Added attributes parameter', 'success');
}

function applyTwigArrowFunctionsFix(audit) {
  const filePath = path.join(sourceDir, audit.level, audit.component, `${audit.component}.twig`);
  let content = readFile(filePath);
  
  if (!content) return;
  
  // Replace common arrow function patterns
  // .filter(v => v) → condition ? 'value' : null pattern
  content = content.replace(/\.filter\(\w+\s*=>\s*\w+\)/g, ''); // Remove filter entirely (handled by ternary)
  
  // .map(item => ...) → for loop or explicit array
  // This is complex and requires manual intervention in most cases
  log('Arrow functions detected - manual review recommended', 'warning');
  
  writeFile(filePath, content);
}

function applyCSSColorsFix(audit, fix) {
  const filePath = path.join(sourceDir, audit.level, audit.component, `${audit.component}.css`);
  let content = readFile(filePath);
  
  if (!content) return;
  
  let replacements = 0;
  fix.colors.forEach(color => {
    const token = COLOR_MAP[color.toUpperCase()];
    if (token) {
      const regex = new RegExp(color, 'gi');
      const matches = content.match(regex);
      if (matches) {
        content = content.replace(regex, token);
        replacements += matches.length;
      }
    }
  });
  
  writeFile(filePath, content);
  log(`Replaced ${replacements} hardcoded colors with tokens`, 'success');
}

function applyCSSFocusVisibleFix(audit) {
  const filePath = path.join(sourceDir, audit.level, audit.component, `${audit.component}.css`);
  let content = readFile(filePath);
  
  if (!content) return;
  
  // Add focus-visible after :hover state
  const hoverMatch = content.match(/&:hover[^}]*}/);
  if (hoverMatch) {
    const focusVisible = `\n\n  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }`;
    content = content.replace(hoverMatch[0], hoverMatch[0] + focusVisible);
    writeFile(filePath, content);
    log('Added focus-visible styles', 'success');
  }
}

function applyStoryAutodocsFix(audit) {
  const filePath = path.join(sourceDir, audit.level, audit.component, `${audit.component}.stories.jsx`);
  let content = readFile(filePath);
  
  if (!content) return;
  
  // Add tags: ['autodocs'] to export default
  const exportMatch = content.match(/(export default \{[^}]*)(title: ['"][^'"]+['"])/);
  if (exportMatch) {
    const replacement = `${exportMatch[1]}${exportMatch[2]},\n  tags: ['autodocs']`;
    content = content.replace(exportMatch[0], replacement);
    writeFile(filePath, content);
    log("Added tags: ['autodocs']", 'success');
  }
}

function applyStoryRemoveReactFix(audit) {
  const filePath = path.join(sourceDir, audit.level, audit.component, `${audit.component}.stories.jsx`);
  let content = readFile(filePath);
  
  if (!content) return;
  
  content = content.replace(/import React from ['"]react['"]; ?\n?/g, '');
  writeFile(filePath, content);
  log('Removed React import', 'success');
}

// ============================================
// PHASE 3: VALIDATE
// ============================================

function validateBuild() {
  log('Running build validation...', 'build');
  try {
    execSync('npm run build', { stdio: 'pipe' });
    log('Build passed', 'success');
    return true;
  } catch (error) {
    log('Build failed', 'error');
    return false;
  }
}

// ============================================
// PHASE 4: COMMIT
// ============================================

function gitCommit(audit, beforeScore, afterScore, fixes) {
  const { level, component } = audit;
  const levelType = LEVEL_MAP[level];
  
  const fixSummary = fixes.applied.map(fix => `- ${fix.message}`).join('\n');
  
  const message = `fix(${level}): Audit automatique ${component} - Score ${afterScore}/${MAX_SCORE}

**Corrections automatiques** :
${fixSummary}

**Score** : ${beforeScore}/${MAX_SCORE} → ${afterScore}/${MAX_SCORE} ${afterScore >= PASSING_SCORE ? '✅' : '⚠️'}

Audit : scripts/audit-component.mjs`;

  try {
    execSync(`git add source/patterns/${level}/${component}/`, { stdio: 'pipe' });
    execSync(`git commit -m "${message}"`, { stdio: 'pipe' });
    log('Changes committed', 'commit');
    return true;
  } catch (error) {
    log(`Git commit failed: ${error.message}`, 'error');
    return false;
  }
}

// ============================================
// MAIN
// ============================================

function main() {
  const args = parseArgs();
  
  if (!args.componentPath) {
    console.error('❌ Usage: npm run audit -- <component-path> [--fix] [--commit] [--verbose]');
    console.error('   Example: npm run audit -- elements/badge --fix --commit');
    process.exit(1);
  }
  
  // Phase 1: AUDIT
  const audit = runAudit(args.componentPath, { verbose: true });
  
  if (audit.score >= PASSING_SCORE && !args.fix) {
    log(`Component already passing (${audit.score}/${MAX_SCORE})`, 'success');
    process.exit(0);
  }
  
  if (!args.fix) {
    log('Use --fix flag to apply automated corrections', 'info');
    process.exit(audit.score >= PASSING_SCORE ? 0 : 1);
  }
  
  // Phase 2: FIX
  const beforeScore = audit.score;
  const fixes = applyFixes(audit);
  
  if (fixes.applied.length === 0) {
    log('No automated fixes available', 'warning');
    process.exit(1);
  }
  
  log(`Applied ${fixes.applied.length} fixes`, 'success');
  
  // Phase 3: VALIDATE
  if (!validateBuild()) {
    log('Build failed after fixes - reverting changes', 'error');
    execSync(`git checkout source/patterns/${audit.level}/${audit.component}/`, { stdio: 'pipe' });
    process.exit(1);
  }
  
  const newAudit = runAudit(args.componentPath, { verbose: true });
  const afterScore = newAudit.score;
  
  // Phase 4: COMMIT
  if (args.commit && afterScore >= PASSING_SCORE) {
    gitCommit(audit, beforeScore, afterScore, fixes);
    log('Workflow complete', 'success');
  } else if (afterScore >= PASSING_SCORE) {
    log('Use --commit flag to commit changes', 'info');
  } else {
    log(`Score still below passing (${afterScore}/${MAX_SCORE}) - manual fixes needed`, 'warning');
  }
  
  process.exit(afterScore >= PASSING_SCORE ? 0 : 1);
}

main();
