# Prompt: Analyze Project Status

**Purpose**: Generate comprehensive project status report with statistics and recommendations.

---

## 📋 Prompt Template

```
Generate comprehensive status report for PS Theme project

ANALYSIS SCOPE:
- Component inventory and progress
- Code quality metrics
- Token system usage
- Documentation completeness
- Technical debt identification
- Accessibility compliance
- Recommendations for next phases

SECTION 1: PROJECT OVERVIEW

Read: docs/ps-design/INDEX.md

Calculate:
- Total components planned: {X}
- Components implemented: {Y}
- Completion percentage: {Y/X * 100}%
- Progress by level:
  * Atoms: {implemented}/{total} ({percentage}%)
  * Molecules: {implemented}/{total} ({percentage}%)
  * Organisms: {implemented}/{total} ({percentage}%)
  * Templates: {implemented}/{total} ({percentage}%)
  * Pages: {implemented}/{total} ({percentage}%)

SECTION 2: CODE QUALITY METRICS

For each implemented component, run audit-component.md:

Generate statistics:
- Average conformity score: {X}/100
- Components with score ≥ 90: {Y} ({percentage}%)
- Components needing fixes (< 90): {Z}
- Most common issues:
  1. {Issue type}: {count} components
  2. {Issue type}: {count} components
  3. {Issue type}: {count} components

Breakdown by category:
- Twig quality: {average}/15
- CSS quality: {average}/20
- Storybook quality: {average}/20
- Documentation quality: {average}/10
- Accessibility: {average}/5

SECTION 3: TOKEN SYSTEM ANALYSIS

Analyze token usage:

Command: grep -r "var(--" source/patterns/ | wc -l
Result: {count} token usages

Command: grep -r "#[0-9a-fA-F]\{3,6\}" source/patterns/**/*.css | wc -l
Result: {count} hardcoded colors (should be 0)

Command: grep -r "px\|rem\|em" source/patterns/**/*.css | grep -v "var(--" | wc -l
Result: {count} hardcoded sizes (should be 0)

Token categories usage:
- Colors: {count} usages (grep "var(--.*color" + semantic colors)
- Sizes: {count} usages (grep "var(--size-")
- Typography: {count} usages (grep "var(--font-")
- Borders: {count} usages (grep "var(--radius\|--border")
- Shadows: {count} usages (grep "var(--shadow")
- Animations: {count} usages (grep "var(--duration\|--ease")

Most used tokens (top 10):
1. {token-name}: {count} usages
2. {token-name}: {count} usages
[...]

SECTION 4: DOCUMENTATION COMPLETENESS

Check documentation status:

Component READMEs:
- Components with README: {count}/{total}
- Average README completeness: {percentage}%
- Missing sections (common):
  * Usage examples: {count} components
  * Props documentation: {count} components
  * Accessibility notes: {count} components

Storybook coverage:
- Components with stories: {count}/{total}
- Components with Autodocs: {count}/{total}
- Average argTypes categorization: {percentage}%

Instructions documentation:
- Read: .github/instructions/README.md
- Status: v4.0.0 consolidated structure (6 files)
- Last updated: 2025-12-12

SECTION 5: TECHNICAL DEBT

Identify issues requiring attention:

CRITICAL (P0):
- Components with audit score < 60: {list}
- Build errors: {list}
- Accessibility violations (WCAG AA): {list}

HIGH PRIORITY (P1):
- Components with score 60-89: {list}
- Missing focus-visible: {count} components
- Hardcoded values: {count} files
- Flat CSS (no nesting): {count} components
- Missing Autodocs: {count} components

MEDIUM PRIORITY (P2):
- Documentation gaps: {list}
- Missing examples: {count} components
- Token optimization opportunities: {list}

LOW PRIORITY (P3):
- Nice-to-have improvements: {list}
- Additional showcases: {count} components
- Performance optimizations: {list}

SECTION 6: ACCESSIBILITY AUDIT

Check WCAG 2.2 AA compliance:

Color contrast:
- Components checked: {count}
- Passing contrast (4.5:1 text / 3:1 UI): {count}
- Failing contrast: {count} (list components)

Focus indicators:
- Interactives with focus-visible: {count}/{total}
- Missing focus-visible: {count} (list components)

Keyboard navigation:
- Keyboard accessible: {count} components
- Needs improvement: {count} (list components)

ARIA usage:
- Components using ARIA: {count}
- Proper ARIA usage: {count}/{count}
- Incorrect ARIA: {list if any}

SECTION 7: RECOMMENDATIONS

Based on analysis, provide prioritized recommendations:

IMMEDIATE ACTIONS (This Sprint):
1. {Action with estimated time}
2. {Action with estimated time}
3. {Action with estimated time}

SHORT TERM (Next 2 Sprints):
1. {Action with estimated time}
2. {Action with estimated time}
3. {Action with estimated time}

LONG TERM (Backlog):
1. {Action}
2. {Action}
3. {Action}

SECTION 8: COMPONENT PRIORITIZATION

Suggest next components to implement based on:
- Design system dependencies (atoms before molecules)
- Business value (high-traffic pages)
- Reusability (used in multiple contexts)

Priority queue (next 5 components):
1. {Component name} ({level}) - {reason}
2. {Component name} ({level}) - {reason}
3. {Component name} ({level}) - {reason}
4. {Component name} ({level}) - {reason}
5. {Component name} ({level}) - {reason}

SECTION 9: BUILD & PERFORMANCE

Check build system:

Build time: {measure with time npm run build}
Storybook build time: {measure with time npm run storybook:build}

CSS output size:
- dist/css/styles.css: {size in KB}
- dist/css/icons.css: {size in KB}
- Total CSS: {size in KB}

Icon sprite:
- source/assets/icons/icons-sprite.svg: {size in KB}
- Number of icons: {count}

Dependencies:
- Total packages: {count} (from package.json)
- Vulnerabilities: {run npm audit}

OUTPUT FORMAT:

# PS Theme - Project Status Report

**Date**: {YYYY-MM-DD}  
**Version**: 4.0.0  
**Analyst**: AI Agent

## Executive Summary
[2-3 paragraph overview of project health]

## 1. Project Overview
- Total: {X} components planned
- Implemented: {Y} ({percentage}%)
- Remaining: {Z}

### Progress by Level
[Table with atoms/molecules/organisms/templates/pages progress]

## 2. Code Quality
- Average Score: {X}/100
- Production Ready (≥90): {Y} components
- Needs Fixes (<90): {Z} components

[Detailed breakdown]

## 3. Token System
- Total Token Usages: {X}
- Hardcoded Values: {Y} (target: 0)
- Token Coverage: {percentage}%

[Top 10 used tokens table]

## 4. Documentation
- READMEs: {X}/{Y} complete
- Storybook: {X}/{Y} with Autodocs
- Instructions: v4.0.0 (6 files consolidated)

## 5. Technical Debt
### Critical (P0)
[List]

### High Priority (P1)
[List]

[Continue with P2, P3]

## 6. Accessibility
- WCAG 2.2 AA Compliance: {percentage}%
- Focus Indicators: {X}/{Y} complete
- Contrast Issues: {count} components

## 7. Recommendations
### Immediate
[Ordered list with time estimates]

### Short Term
[List]

### Long Term
[List]

## 8. Next Components
[Priority queue with rationale]

## 9. Build Metrics
- Build Time: {X}s
- CSS Size: {Y}KB
- Dependencies: {Z} packages
- Vulnerabilities: {count}

---

**Generated by**: AI Agent  
**Reference**: .github/instructions/ + docs/ps-design/INDEX.md
```

---

**Estimated Time**: 15-30 minutes  
**Output**: Comprehensive report with actionable insights  
**Use Case**: Sprint planning, stakeholder updates, technical reviews
