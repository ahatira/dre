# Copilot Instructions for PS Theme (Surface)

**Version**: 3.0.0  
**Last Updated**: 2025-12-05

---

## 📚 Documentation Structure

**All detailed rules are now in modular instruction files under `.github/instructions/`:**

- **Core** → `instructions/core.instructions.md` - Stack, tokens, build system
- **Atomic Design** → `instructions/atomic-design.instructions.md` - Composition methodology
- **Components** → `instructions/components.instructions.md` - 5-file structure, BEM
- **CSS** → `instructions/css.instructions.md` - Tokens, nesting, cascade
- **Storybook** → `instructions/storybook.instructions.md` - Autodocs, stories format
- **Base Stories** → `instructions/base-stories.instructions.md` - Token documentation stories
- **JavaScript** → `instructions/javascript.instructions.md` - Drupal behaviors, ES6
- **Templates** → `instructions/templates.instructions.md` - Twig, YAML, Faker.js
- **Accessibility** → `instructions/accessibility.instructions.md` - WCAG, ARIA, keyboard
- **Workflows** → `instructions/workflows.instructions.md` - Generation, audit, standardization

**These files are path-scoped** (YAML frontmatter `applyTo:`) for contextual AI assistance.

---

## 🎯 Project At-a-Glance

**PS Theme**: Custom Drupal 10/11 theme for BNP Paribas Real Estate  
**Stack**: Storybook (HTML edition) + Vite + PostCSS + Twig  
**Methodology**: Atomic Design (Brad Frost)

**87 Components to Implement**:
- 19 Atoms (elements/)
- 20 Molecules (components/)
- 12 Organisms (collections/)
- 8 Templates (layouts/)
- 8 Pages (pages/)

**Current Progress**: 6/87 (7%) - See `docs/ps-design/INDEX.md`

---

## 🗣️ Language Directive

**Chat responses**: French (default)  
**Documentation**: English (README, Storybook, code comments)  
**Exception**: User-facing content in templates (button labels, form text) can be French

All technical identifiers (tokens, classes, ARIA) remain unchanged (English).

---

## ⚡ Quick Decision Tree

**New Component?**  
→ Read spec: `docs/design/{level}/{component}.md`  
→ Follow workflow: `instructions/workflows.instructions.md`  
→ Use standards: `instructions/components.instructions.md`

**CSS Issue?**  
→ Consult: `instructions/css.instructions.md`  
→ Token missing? Document need, don't add (see `instructions/core.instructions.md`)

**Storybook Config?**  
→ Follow: `instructions/storybook.instructions.md`  
→ MANDATORY: `tags: ['autodocs']` in export default

**Twig Template?**  
→ Standards: `instructions/templates.instructions.md`  
→ CRITICAL: NO arrow functions, NO `.filter(v => v)` (Drupal incompatible)

**JavaScript Behavior?**  
→ Patterns: `instructions/javascript.instructions.md`  
→ Use Drupal behaviors with `once()` for idempotency

**Accessibility?**  
→ Requirements: `instructions/accessibility.instructions.md`  
→ WCAG 2.2 AA minimum (contrast, focus-visible, ARIA, keyboard)

**Refactor Legacy?**  
→ Audit: `instructions/workflows.instructions.md` (Conformity checklist)  
→ Standardize: Fix tokens, nesting, BEM, Autodocs

---

## 🚨 Zero Tolerance Rules

These will ALWAYS be rejected:

- ❌ Hardcoded values: `#00915A`, `16px`, `150ms ease` → Use tokens: `var(--primary)`, `var(--size-4)`
- ❌ Missing any of 5 required files: `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
- ❌ Missing `tags: ['autodocs']` in Storybook export default
- ❌ Arrow functions in Twig: `filter(v => v)` → Use ternary: `condition ? 'class' : null`
- ❌ JavaScript methods in Twig: `.map()`, `.filter()`, `.includes()` → Drupal incompatible
- ❌ Color names instead of semantic: `green` → `success`, `red` → `danger`
- ❌ Icon names with prefix: `icon-check` → `check` (prefix handled by system)
- ❌ Modifier classes requiring combinations: `.ps-badge--a.ps-badge--b` → Each must work alone
- ❌ Wrong cascade order: Modifiers before base → Base FIRST, then modifiers
- ❌ Flat CSS without nesting: New components MUST use `&` syntax
- ❌ Missing focus-visible: All interactives MUST have visible focus indicator
- ❌ Editing `source/props/*.css` directly: Propose tokens via separate process

---

## 📋 Component Checklist (Quick)

**Before starting**:
- [ ] Read spec: `docs/design/{level}/{component}.md`
- [ ] Verify dependencies exist (atoms for molecules/organisms)
- [ ] Check required tokens exist (`grep -r "--token-name" source/props/`)

**Implementation**:
- [ ] Create 5 files: `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
- [ ] Twig: Header comment, defaults, ternary + `null`, `{% include %}` with `only`
- [ ] CSS: ALL tokens, nesting with `&`, cascade order, semantic colors, focus-visible
- [ ] YAML: Real Estate context, Faker.js in stories
- [ ] Storybook: `tags: ['autodocs']`, argTypes categorized, Default + Showcases
- [ ] README: Usage, Props table, BEM structure, Tokens, Accessibility, Examples

**Validation**:
- [ ] Build passes: `npm run build`
- [ ] Visual check: `npm run watch` → http://localhost:6006
- [ ] Conformity audit: 100% score (see `instructions/workflows.instructions.md`)
- [ ] Commit with structured message
- [ ] Update `docs/ps-design/CHANGELOG.md`

---

## 🎓 Reference Components

**Perfect implementations to study**:

- **Button** (`source/patterns/elements/button/`) - CSS nesting, all states, complete stories
- **Avatar** (`source/patterns/elements/avatar/`) - Minimal markup, adaptive sizing, SVG fallback
- **Badge** (`source/patterns/elements/badge/`) - Semantic colors, pill variant, icon integration
- **Divider** (`source/patterns/elements/divider/`) - Simplicity, orientation variants, minimal code

Always prefer reading actual component code over guessing patterns.

---

## 🔧 Build Commands

```bash
npm run build          # Compile assets + lint/format checks
npm run watch          # Vite + Storybook (http://localhost:6006)
npm run storybook:build # Static Storybook output (storybook/)
npm run generate:pattern # Scaffold new component (interactive)
```

**Build validates**:
- Biome lint/format (JavaScript, JSON)
- CSS compilation (Vite + PostCSS)
- No syntax errors (Twig via Storybook)

---

## 🔗 Key Resources

- **Design Specs**: `docs/design/` - Complete specifications for all 87 components
- **Project Status**: `docs/ps-design/INDEX.md` - Inventory + phases
- **Changelog**: `docs/ps-design/CHANGELOG.md` - Implementation history
- **Storybook Demo**: [Surface Storybook](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/)

---

## 🤖 For AI Agents

**PRIMARY DIRECTIVE**:  
Before ANY component work, consult the relevant instruction file(s) under `.github/instructions/`. These are the SINGLE SOURCE OF TRUTH (replacing old monolithic COMPLETE_RULES.md).

**Workflow**:
1. Read: `instructions/workflows.instructions.md` (Component generation steps 1-11)
2. Apply: Domain-specific instructions (CSS, Storybook, Templates, etc.)
3. Validate: Conformity audit (100% score required)
4. Commit: Structured message + changelog update

**When in doubt**: Consult instruction files first, then ask for clarification (never guess).

---

**Maintainers**: Design System Team  
**Contact**: See project README for support channels
