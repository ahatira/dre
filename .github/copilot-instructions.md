# Copilot Instructions for PS Theme (Surface)

## Project Overview
- **PS Theme** is a custom Drupal theme for BNP Paribas RealEstate, compatible with Drupal 10/11.
- Built with [Storybook](https://storybook.js.org/) (HTML edition) and [Vite](https://vitejs.dev/) (Vanilla JS edition).
- **87 composants à implémenter** suivant les spécifications de `docs/design/`.
- **1 composant en cours (button)** - CSS rewritten pixel perfect, awaiting visual validation.
- **PIXEL PERFECT OBLIGATOIRE** : Chaque composant doit respecter EXACTEMENT les specs (dimensions, couleurs, espacements, typographie).
- Uses NodeJS tooling for automation; see `package.json` for dependencies.

## Architecture & Structure
- **Design System** : Composants développés dans Storybook (`storybook/`) avant intégration Drupal.
- **Source Code** : Patterns dans `source/patterns/` :
  - `elements/` (Atoms) - 19 composants (1 fait pixel perfect, 18 à faire)
  - `components/` (Molecules) - 20 composants (0 faits, 20 à faire)
  - `collections/` (Organisms) - 12 composants (0 faits, 12 à faire)
  - `layouts/` (Templates) - 8 composants (0 faits, 8 à faire)
  - `pages/` (Pages) - 8 composants (0 faits, 8 à faire)
- **Templates Drupal** : Exemples d'intégration dans `templates/`.
- **Design Tokens** : `source/props/*.css` organisés par catégorie (colors, fonts, brand, sizes, shadows, borders, animations, easing, zindex). **NE PAS utiliser ps-tokens.css**.
- **BEM strict** : Préfixe `ps-` obligatoire pour nouveaux composants, legacy sans préfixe à migrer.
- **JavaScript** : ES6, comportements modulaires.

## Developer Workflows
- **Build** : `npm run build` (compile tous les assets ; à exécuter en premier sur nouveau setup)
- **Watch/Dev** : `npm run watch` (lance Vite + Storybook, nettoie `dist/`, lint, watch changements)
- **Storybook Build** : `npm run storybook:build` (build statique dans `/storybook`)
- **Storybook Dev** : Accessible sur [http://localhost:6006](http://localhost:6006) après `npm run watch`
- **Vite Config** : Détails des tâches build/watch dans `vite.config.js`

## Conventions & Patterns
- **Component Naming** : Utiliser categories spécifiques (elements, components, collections, layouts, pages).
- **BEM avec `ps-` prefix** : Nouveaux composants DOIVENT utiliser BEM avec préfixe `ps-` (ex : `ps-badge`, `ps-badge__icon`, `ps-badge--small`). Composants legacy sans préfixe existent et peuvent être migrés progressivement.
- **Design Tokens** : TOUJOURS utiliser CSS Custom Properties de `source/props/*.css` (ex : `var(--brand-primary)`, `var(--font-size-1)`, `var(--size-4)`). ❌ JAMAIS de valeurs en dur (#00915A, 16px, etc.). Si un token manque, l'ajouter dans le fichier approprié (`colors.css`, `fonts.css`, `sizes.css`, etc.) en respectant les conventions existantes.
- **Structure de composant** : 5 fichiers obligatoires par composant :
  1. `.twig` - Template avec params commentés
  2. `.css` - Styles BEM avec tokens uniquement
  3. `.yml` - Données par défaut pour preview
  4. `.stories.jsx` - Stories Storybook (variants)
  5. `.mdx` - Documentation Storybook
- **Intégration Drupal** : Exemples dans `templates/`.
- **Linting** : Automatique via watch/build (voir `vite.config.js`).
- **Demo** : Storybook statique [ici](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/).

## Key Files & Directories
- `source/patterns/` — codebase principal des composants (1/87 en cours)
  - `elements/` — Atoms : button (CSS pixel perfect, awaiting validation), + 18 à faire
  - `components/` — Molecules : 20 à faire
  - `collections/` — Organisms : 12 à faire
  - `layouts/` — Templates : 8 à faire
  - `pages/` — Pages : 8 à faire
- `source/props/*.css` — Design tokens organisés par catégorie (colors, fonts, brand, sizes, shadows, borders, animations, easing, zindex)
- `storybook/` — Build statique Storybook
- `templates/` — Exemples d'intégration Drupal
- `vite.config.js` — Configuration build/watch
- `package.json` — Scripts et dépendances
- `docs/design/` — **Spécifications complètes des 87 composants à implémenter** :
  - `atoms/` (19 fichiers .md), `molecules/` (20), `organisms/` (12), `templates/` (8), `pages/` (8)
  - `tokens/` (7 fichiers YAML de référence)
  - Documentation détaillée : BEM, props, variants, tokens, accessibilité, exemples
- `docs/ps-design/` — Documentation du projet réel (état actuel + roadmap) :
  - `README.md` — Documentation principale + workflow
  - `INDEX.md` — Inventaire complet + progression (5/87 = 6%)
  - `CHANGELOG.md` — Historique des implémentations
  - `COMPONENT_TEMPLATE.md` — Template standard avec structure 5 fichiers obligatoires

## References
- [Drupal Theming Guide](https://www.drupal.org/docs/develop/theming-drupal)
- [Storybook Blog Series](https://mariohernandez.io/series/storybook/)
- See `docs/ps-design/README.md` for PS Design System overview, workflow, and roadmap.
- See `docs/ps-design/COMPONENT_TEMPLATE.md` for standard component structure (5 required files).
- See `docs/ps-design/INDEX.md` for complete inventory and implementation phases.
- See `docs/design/` for detailed specifications of all 87 components to implement.
- **See `.github/COMPLETE_RULES.md` for ABSOLUTE REFERENCE - ALL project rules (1000+ lines covering EVERY standard).**
- **See `.github/COMPONENT_TEMPLATE_STANDARD.md` for MANDATORY component template (analyzed from button reference).**
- **See `.github/CSS_STANDARDS.md` for complete CSS standards (stack, nesting, tokens, accessibility, performance).**
- **See `.github/STORYBOOK_DOC_TEMPLATE.md` for Storybook documentation format (Autodocs, argTypes, stories structure).**
- **See `.github/COMPONENT_AUDIT_PROMPT.md` for conformity audit (run after implementation).**
- **See `.github/STANDARDIZE_COMPONENT_PROMPT.md` for standardization workflow.**

---
**For AI agents:**

## 🔒 PRIMARY DIRECTIVE

**BEFORE ANY COMPONENT WORK**: Read `.github/COMPLETE_RULES.md` - the ABSOLUTE REFERENCE (1000+ lines, 18 sections, covering ALL standards).

This is the **SINGLE SOURCE OF TRUTH**. All other documents are subsets or implementations of these rules.

### 🌍 CRITICAL: Documentation Language

**ALL documentation MUST be written in English.**

This includes:
- README.md files
- Storybook descriptions and argTypes
- Code comments (Twig, CSS, JS)
- Props tables and usage examples
- Accessibility notes

**Exception**: User-facing content in templates (button labels, form text) can be in French.

## 🎯 Quick Decision Tree

**New Component?** → Follow `.github/COMPLETE_RULES.md` Section 18 (Checklist Complet)

**Refactor/Fix?** → Audit with `.github/COMPONENT_AUDIT_PROMPT.md`, fix per `.github/COMPLETE_RULES.md`

**CSS Issue?** → Consult `.github/COMPLETE_RULES.md` Sections 4-6 (Tokens, Nesting, Cascade)

**Storybook?** → Follow `.github/COMPLETE_RULES.md` Section 11 + `.github/STORYBOOK_DOC_TEMPLATE.md`

**Unsure?** → Default to `.github/COMPLETE_RULES.md`

## ⚡ Critical Rules Summary (Non-Exhaustive)

These are the **most common violations** - but `.github/COMPLETE_RULES.md` contains MANY more:

### 1. Design Tokens (ABSOLUTE)
- ❌ NEVER hardcode: `#00915A`, `16px`, `150ms ease`
- ✅ ALWAYS tokens: `var(--brand-primary)`, `var(--size-4)`, `cubic-bezier(0.4, 0.0, 0.2, 1)`
- Before creating token: `grep -r "--token-name" source/props/` (reuse if exists)

### 2. CSS Nesting (MANDATORY)
- ✅ Use `&` syntax for all new components (postcss-nested supported)
- ✅ Order: Base → Elements → Modifiers → States
- ❌ No over-nesting (max 3 levels)

### 3. Cascade Order (CRITICAL)
- ✅ Base styles BEFORE modifiers in source order
- Example: `.ps-component__text { }` then `.ps-component--lg .ps-component__text { }`
- Wrong order = modifiers won't override

### 4. Minimal Markup (REQUIRED)
- ✅ Default: `<div class="ps-component">` (no modifier classes)
- ✅ Only add modifiers when value differs from default
- Twig: conditional `merge()`, never ternary with empty strings

### 5. Modifiers Independence (REQUIRED)
- ✅ Each modifier works alone on base class
- ❌ Never: `.ps-component--a.ps-component--b { }` (requires both)
- ✅ Always: `.ps-component--a { }` (works alone)

### 6. Semantic Colors (MANDATORY)
- ✅ primary | secondary | success | warning | danger | info
- ❌ NEVER: green | purple | blue | red | yellow
- If component has color variants, support ALL 6

### 7. Icons System (STRICT)
- **Controllable icon**: Use `@elements/icon/icon.twig` (prop: `icon`, no "icon-" prefix)
- **Decorative icon**: `<span data-icon="check">` (no "icon-" prefix)
- Component CSS: NO `[data-icon]` mappings (centralized in `icons.css`)

### 8. Storybook (NO REACT)
- ✅ Import: `import componentTwig from './component.twig';`
- ✅ Render: `render: (args) => componentTwig(args)`
- ✅ Stories: Default + Showcases (AllColors, AllSizes, UseCases)
- ❌ NO individual stories (Primary, Secondary, Small, etc.)
- ✅ ArgTypes categorized: Content | Appearance | Behavior | Link | Accessibility | Layout

### 9. Required Files (5 ALWAYS)
- `.twig` - Template with commented params
- `.css` - Tokens + nesting + BEM
- `.yml` - Defaults + comments
- `.stories.jsx` - Default + showcases + Autodocs
- `README.md` - Props table + BEM + tokens + usage + accessibility

### 10. BEM Strict
- ✅ Prefix `ps-` mandatory
- ✅ Format: `.ps-block__element--modifier`
- ❌ NO double underscore: `.ps-block__element__nested`

## 📋 Implementation Workflow

1. **Read spec**: `docs/design/{level}/{component}.md` (COMPLETE, all sections)
2. **Verify tokens**: `grep -r` in `source/props/` before creating new
3. **Follow template**: `.github/COMPONENT_TEMPLATE_STANDARD.md` (5 files structure)
4. **Apply rules**: `.github/COMPLETE_RULES.md` (all 18 sections)
5. **Build**: `npm run build` (check errors)
6. **Test**: `npm run watch` → Storybook visual verification
7. **Audit**: "Vérifie la cohérence du composant [Name] avec nos règles du projet"
8. **Commit**: Structured message with detailed changes
9. **Update**: `docs/ps-design/CHANGELOG.md`

## 🚨 Zero Tolerance Rules

These will ALWAYS be rejected:

- Hardcoded values (colors, sizes, spacing, transitions)
- Missing any of the 5 required files
- React/JSX in Storybook stories
- Color names instead of semantic (green → primary)
- Icon names with "icon-" prefix
- Modifier classes requiring combinations
- Wrong cascade order (modifiers before base)
- Classes for default values in markup
- Flat CSS without nesting (new components)
- Skipping accessibility (focus-visible, ARIA, contrast)

## 📚 Complete Documentation Hierarchy

1. **`.github/COMPLETE_RULES.md`** ← START HERE (absolute reference, 1000+ lines)
2. `.github/COMPONENT_TEMPLATE_STANDARD.md` ← Structure & examples
3. `.github/CSS_STANDARDS.md` ← CSS deep dive (400+ lines)
4. `.github/STORYBOOK_DOC_TEMPLATE.md` ← Autodocs format
5. `.github/COMPONENT_AUDIT_PROMPT.md` ← Post-implementation audit
6. `.github/STANDARDIZE_COMPONENT_PROMPT.md` ← Refactor workflow

**When in doubt**: Consult `.github/COMPLETE_RULES.md` first, then specific docs as needed.

## 🎓 Reference Components

Perfect implementations to study:

- **`source/patterns/elements/button/`** - CSS nesting, all states, complete stories
- **`source/patterns/elements/avatar/`** - Minimal markup, adaptive sizing, gender SVG fallback
- **`source/patterns/elements/badge/`** - Semantic colors, pill variant, icon integration
- **`source/patterns/elements/divider/`** - Simplicity, orientation variants, minimal code

Always prefer reading actual component code over guessing patterns.
