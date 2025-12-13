#!/usr/bin/env node

/**
 * Documentation Audit Script
 * Vérifie la cohérence de tous les fichiers docs/*.md selon les règles PS Theme
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const DOCS_DIR = path.join(__dirname, '..', 'docs');

// Règles de validation
const RULES = {
  // Règle 1: Pas de valeurs hardcodées (couleurs hex, pixels)
  noHardcodedValues: {
    pattern: /(#[0-9A-Fa-f]{3,8}|(?<!\w)\d+px(?!\w))/g,
    message: 'Hardcoded value found (hex color or px)',
    severity: 'warning',
    exclude: ['ANALYSE_INCOHERENCES.md', 'AUDIT_COHERENCE.md', 'CHANGELOG.md'] // Rapports techniques
  },
  
  // Règle 2: Noms de couleurs au lieu de variants sémantiques
  colorNames: {
    pattern: /\b(green|purple|pink|red|blue|yellow|teal|gold|orange)\b(?!-)(?!-\d)/gi,
    message: 'Color name instead of semantic variant (use primary, secondary, success, danger, etc.)',
    severity: 'error',
    exclude: ['couleurs.md', 'CHANGELOG.md', 'ANALYSE_INCOHERENCES.md', 'glossaire.md'] // Documentation tokens
  },
  
  // Règle 3: Préfixe icon- dans le code
  iconPrefix: {
    pattern: /(?:icon-|"icon-|'icon-|data-icon="icon-)/g,
    message: 'Icon prefix "icon-" should not be used (use name directly)',
    severity: 'error',
    exclude: ['CHANGELOG.md']
  },
  
  // Règle 4: baseClass parameter (FORBIDDEN)
  baseClass: {
    pattern: /baseClass["\s:]/g,
    message: 'baseClass parameter is FORBIDDEN (use attributes.addClass() instead)',
    severity: 'error',
    exclude: ['CHANGELOG.md', 'ANALYSE_INCOHERENCES.md']
  },
  
  // Règle 5: Arrow functions in Twig (JS only, not PHP)
  arrowFunctions: {
    pattern: /\)\s*=>\s*[a-z{]/gi, // Match JS arrow functions: ) => { or ) => something
    message: 'Arrow functions not supported in Drupal Twig (use ternary operator)',
    severity: 'error',
    exclude: ['CHANGELOG.md', 'ANALYSE_INCOHERENCES.md', 'methodologie.md', '04-drupal-forms.md', '05-preprocess.md', '06-deploiement.md', 'README.md']
  },
  
  // Règle 6: Tokens --ps- avec fallback
  psPrefixWithFallback: {
    pattern: /var\(--ps-[a-z-]+,\s*[^)]+\)/g,
    message: 'Token --ps- with hardcoded fallback (remove fallback)',
    severity: 'error',
    exclude: ['CHANGELOG.md', 'ANALYSE_INCOHERENCES.md']
  },
  
  // Règle 7: Vérifier français dans chat vs anglais dans docs
  // (difficile à automatiser, on skip)
  
  // Règle 8: Références à README.md de composants (supprimés v2.1.0)
  componentReadme: {
    pattern: /source\/patterns\/[a-z]+\/[a-z-]+\/README\.md/g,
    message: 'Component README.md references (removed in v2.1.0, use docs/02-composants/)',
    severity: 'error',
    exclude: ['CHANGELOG.md']
  },
  
  // Règle 9: Score 100 points au lieu de 90 (structure 4 fichiers)
  oldAuditScore: {
    pattern: /(?:100\/100|score.*100)/gi,
    message: 'Audit score references might be outdated (v2.1.0+ uses 90-point system)',
    severity: 'warning',
    exclude: ['CHANGELOG.md', 'ANALYSE_INCOHERENCES.md']
  }
};

// Résultats
const results = {
  filesScanned: 0,
  filesWithIssues: 0,
  totalIssues: 0,
  errors: 0,
  warnings: 0,
  byRule: {}
};

/**
 * Vérifie un fichier contre toutes les règles
 */
function auditFile(filePath) {
  const content = fs.readFileSync(filePath, 'utf-8');
  const relativePath = path.relative(DOCS_DIR, filePath);
  const fileName = path.basename(filePath);
  
  const fileIssues = [];
  
  for (const [ruleName, rule] of Object.entries(RULES)) {
    // Skip si fichier exclu
    if (rule.exclude && rule.exclude.some(exc => fileName.includes(exc))) {
      continue;
    }
    
    const matches = content.matchAll(rule.pattern);
    for (const match of matches) {
      const lineNumber = content.substring(0, match.index).split('\n').length;
      const line = content.split('\n')[lineNumber - 1];
      
      fileIssues.push({
        rule: ruleName,
        severity: rule.severity,
        message: rule.message,
        line: lineNumber,
        match: match[0],
        context: line.trim().substring(0, 100)
      });
      
      // Update stats
      results.totalIssues++;
      if (rule.severity === 'error') results.errors++;
      if (rule.severity === 'warning') results.warnings++;
      
      if (!results.byRule[ruleName]) {
        results.byRule[ruleName] = 0;
      }
      results.byRule[ruleName]++;
    }
  }
  
  results.filesScanned++;
  if (fileIssues.length > 0) {
    results.filesWithIssues++;
    return { relativePath, issues: fileIssues };
  }
  
  return null;
}

/**
 * Scan récursif du dossier docs/
 */
function scanDirectory(dir) {
  const files = fs.readdirSync(dir);
  const fileIssues = [];
  
  for (const file of files) {
    const filePath = path.join(dir, file);
    const stat = fs.statSync(filePath);
    
    if (stat.isDirectory()) {
      // Skip backup directories
      if (file.startsWith('.') || file === 'node_modules' || file.includes('backup')) {
        continue;
      }
      fileIssues.push(...scanDirectory(filePath));
    } else if (file.endsWith('.md')) {
      const result = auditFile(filePath);
      if (result) {
        fileIssues.push(result);
      }
    }
  }
  
  return fileIssues;
}

/**
 * Affichage du rapport
 */
function printReport(fileIssues) {
  console.log('\n' + '='.repeat(80));
  console.log('📋 DOCUMENTATION AUDIT REPORT');
  console.log('='.repeat(80) + '\n');
  
  console.log(`Files scanned: ${results.filesScanned}`);
  console.log(`Files with issues: ${results.filesWithIssues}`);
  console.log(`Total issues: ${results.totalIssues} (${results.errors} errors, ${results.warnings} warnings)\n`);
  
  if (results.totalIssues === 0) {
    console.log('✅ No issues found! All documentation is compliant.\n');
    return;
  }
  
  console.log('Issues by rule:');
  for (const [rule, count] of Object.entries(results.byRule)) {
    console.log(`  - ${rule}: ${count}`);
  }
  console.log('');
  
  // Détails par fichier
  console.log('─'.repeat(80));
  console.log('DETAILED ISSUES\n');
  
  for (const { relativePath, issues } of fileIssues) {
    console.log(`\n📄 ${relativePath} (${issues.length} issues)`);
    console.log('─'.repeat(80));
    
    for (const issue of issues) {
      const icon = issue.severity === 'error' ? '❌' : '⚠️';
      console.log(`${icon} Line ${issue.line}: ${issue.message}`);
      console.log(`   Rule: ${issue.rule}`);
      console.log(`   Match: "${issue.match}"`);
      console.log(`   Context: ${issue.context}`);
      console.log('');
    }
  }
  
  console.log('\n' + '='.repeat(80));
  console.log(`SUMMARY: ${results.errors} errors, ${results.warnings} warnings`);
  console.log('='.repeat(80) + '\n');
  
  if (results.errors > 0) {
    console.log('❌ Audit FAILED: Fix errors before committing.');
    process.exit(1);
  } else if (results.warnings > 0) {
    console.log('⚠️  Audit PASSED with warnings: Review warnings before committing.');
  } else {
    console.log('✅ Audit PASSED: No issues found.');
  }
}

// Run audit
console.log('Starting documentation audit...\n');
const fileIssues = scanDirectory(DOCS_DIR);
printReport(fileIssues);
