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
- **See `.github/COMPONENT_TEMPLATE_STANDARD.md` for MANDATORY component template (analyzed from button reference).**

---
**For AI agents:**
- Always follow the custom category naming and BEM conventions.
- Use the provided npm scripts for builds and dev workflows.
- Reference `vite.config.js` for automation details.
- **CRITICAL - COMPONENT TEMPLATE**: ALWAYS follow `.github/COMPONENT_TEMPLATE_STANDARD.md` EXACTLY. This template is based on the validated button reference and defines the MANDATORY structure for all components (5 files: .twig, .css, .yml, .stories.jsx, README.md).
- **CRITICAL - COMPONENT AUDIT**: After implementing a component, run conformity audit using `.github/COMPONENT_AUDIT_PROMPT.md` to ensure 100% compliance with all project rules.
- **CRITICAL - COMPLETE IMPLEMENTATION**: When implementing a component, ALWAYS deliver:
  1. ✅ Functional Twig template with all props and variants
  2. ✅ Complete CSS with ALL modifiers and states working on base class
  3. ✅ YAML with sensible defaults and documented options
  4. ✅ **COMPLETE Storybook stories** with ALL variants individually + grouped showcase stories (AllStyles, UseCases, etc.)
  5. ✅ **DETAILED README.md** with: description, props table, BEM structure, design tokens used, usage examples, real-world use cases, accessibility notes
  - ❌ NEVER stop at just template/CSS - stories and docs are MANDATORY
- **CRITICAL - STORYBOOK STORIES**: 
  - ❌ NEVER use React/JSX in `.stories.jsx` files (this is HTML-Vite Storybook, not React)
  - ✅ ALWAYS import Twig: `import componentTwig from './component.twig';` (use unique name like `componentTwig` to avoid conflicts)
  - ✅ ALWAYS import YAML: `import data from './component.yml';`
  - ✅ ALWAYS use Twig render: `render: (args) => componentTwig(args)`
  - ✅ ALWAYS return HTML strings for showcase stories: `` `${componentTwig(args)}` ``
  - ✅ Use Storybook Autodocs exclusively: set `tags: ['autodocs']` on the default export and DO NOT create `.mdx` docs files for components.
  - ✅ Create individual variant stories (Default, Variant1, Variant2, etc.) AND grouped showcase stories (AllStyles, UseCases, etc.)
- **CRITICAL - MINIMAL HTML OUTPUT**: 
  - Default component render should produce MINIMAL markup with ONLY base class
  - Example: `<hr class="ps-divider" />` NOT `<hr class="ps-divider ps-divider--horizontal ps-divider--solid ps-divider--medium" />`
  - Only add modifier classes when values differ from defaults
  - Base class (`.ps-divider`) must contain all default styles
  - Example Twig logic:
    ```twig
    {%- set root_classes = ['ps-component'] -%}
    {%- if variant != 'default' -%}
      {%- set root_classes = root_classes|merge(['ps-component--' ~ variant]) -%}
    {%- endif -%}
    ```
- **CRITICAL - CSS MODIFIERS INDEPENDENCE**: 
  - ALL modifiers MUST work on the base class alone
  - ❌ WRONG: `.ps-divider--horizontal.ps-divider--primary { color: green; }` (requires two classes)
  - ✅ CORRECT: `.ps-divider--primary { border-color: var(--color-primary); }` (works alone)
  - Base class contains default styles; modifiers override only what changes
  - Example:
    ```css
    .ps-divider {
      border-top: 2px solid var(--gray-300); /* defaults */
      margin: var(--size-4) 0;
    }
    .ps-divider--primary { border-top-color: var(--bnp-green); } /* works alone */
    .ps-divider--thick { border-top-width: 4px; } /* works alone */
    ```
- **CRITICAL - ICONS IMPLEMENTATION**: 
  - **Component icon element**: Use dedicated icon component `@elements/icon/icon.twig`
    ```twig
    {%- include '@elements/icon/icon.twig' with {
      name: icon,
      size: 'small'
    } only -%}
    ```
  - **Decorative icons in CSS**: Render via CSS pseudo-elements when icon is purely decorative
  - ❌ Avoid extra markup: `<i class="icon-name"></i>` or `<svg>` or classes `ps-icon ps-icon-*`
  - ✅ For decorative icons, use `data-icon` attribute **WITHOUT "icon-" prefix**:
    ```html
    <span class="ps-component__icon" data-icon="check"></span>
    <span class="ps-component__icon" data-icon="calendar"></span>
    ```
  - ✅ CSS implementation: **DO NOT add data-icon mappings in component CSS**
    - All `[data-icon]` mappings are centralized in `source/props/icons.css`
    - Component CSS only needs font-family and basic styling:
    ```css
    .ps-component__icon {
      font-family: 'bnpre-icons';
      font-style: normal;
      line-height: 1;
    }
    /* NO content mappings here - they're in icons.css */
    ```
  - **Props naming**: Icon prop should be named `icon` (string) accepting icon name WITHOUT prefix (e.g., 'check', 'calendar', 'medal')
  - **Storybook control**: Use select control with `iconsList.categories.generic` for icon picker
  - **Important**: Icon names are stored/used WITHOUT "icon-" prefix in props, data-icon, and documentation
- **CRITICAL - SEMANTIC COLOR NAMING**:
  - ❌ **NEVER** use color names: `'green'`, `'purple'`, `'blue'`, `'red'`, `'yellow'`
  - ✅ **ALWAYS** use semantic names:
    - `primary` → `--brand-primary` (green #00915A) - Main action
    - `secondary` → `--brand-secondary` (purple #E0388C) - Secondary action
    - `success` → `--btn-success` (green-600) - Success/validation
    - `warning` → `--btn-warning` (yellow-500) - Warning
    - `danger` → `--btn-danger` (red-600) - Error/danger
    - `info` → `--btn-info` (blue-600) - Information
  - Apply to: props, BEM classes, CSS tokens, documentation, stories, YAML
  - If component has color variants, it MUST support ALL 6 semantic colors
  - Example:
    ```yaml
    # ❌ WRONG
    color: 'green'  # Options: green | purple | blue
    
    # ✅ CORRECT
    color: 'primary'  # Options: primary | secondary | success | warning | danger | info
    ```
- **CRITICAL - DESIGN TOKENS FROM SPEC**: 
  - ALWAYS read `docs/design/{level}/{component}.md` to identify correct tokens
  - Use official spec tokens (e.g., `--ps-color-primary-600`, `--ps-color-neutral-300`) not generic ones
  - Add fallbacks for compatibility: `var(--ps-color-neutral-300, var(--gray-300))`
  - Verify token hex values match spec exactly
  - Check spec sections: "Design Tokens", "🎨 Design Tokens"
- **CRITICAL - TWIG CLASS HANDLING**: 
  - Never add empty or undefined classes to markup
  - Use conditional `merge()` to add classes only when needed
  - ❌ WRONG: `text ? 'class--with-text' : ''` (adds empty string)
  - ✅ CORRECT: 
    ```twig
    {%- set classes = ['base-class'] -%}
    {%- if text -%}
      {%- set classes = classes|merge(['base-class--with-text']) -%}
    {%- endif -%}
    ```
  - Filter functions may not exist in Twig - use conditional merge instead
- **CRITICAL - PIXEL PERFECT MANDATORY**: BEFORE implementing ANY component:
  1. Read COMPLETE spec in `docs/design/{level}/{component}.md` (ALL sections: BEM, Props, Variants, Tokens, States, Accessibility)
  2. Verify ALL dimensions are EXACT (heights, widths, paddings, margins, gaps, borders)
  3. Verify ALL colors match tokens EXACTLY (check hex values in source/props/*.css)
  4. Verify ALL typography specs (font-family, font-size, font-weight, line-height, letter-spacing)
  5. Implement ALL interactive states (hover, focus, active, disabled) with EXACT values from spec
  6. Test visually in Storybook - component must be IDENTICAL to design spec
  7. NO approximations, NO "close enough" - PIXEL PERFECT or it's wrong
- **CRITICAL**: Follow `.github/COMPONENT_TEMPLATE_STANDARD.md` structure for every new component (5 files: .twig, .css, .yml, .stories.jsx, README.md).
- **CRITICAL**: After implementation, run audit: "Vérifie la cohérence du composant [Name] avec nos règles du projet" (see `.github/COMPONENT_AUDIT_PROMPT.md`).
- **CRITICAL**: Always use tokens from `source/props/*.css` (colors.css, fonts.css, brand.css, sizes.css, etc.). NEVER hardcode values (#00915A, 16px, etc.).
- **CRITICAL**: BEFORE creating a new token, ALWAYS:
  1. Search if similar token exists: `grep -r "--token-name" source/props/`
  2. Check for consistency with existing tokens (naming, values, progression)
  3. Reuse existing tokens when possible
  4. Only add new token if truly necessary
  5. Add to appropriate file following exact naming conventions (--brand-*, --font-*, --size-*, etc.)
  6. Document in CHANGELOG.md with justification
- **CRITICAL - SEMANTIC COLOR NAMING**: When specs or requirements mention color variants, ALWAYS use semantic tokens from `source/props/brand.css`:
  - **Primary** = `--brand-primary` (green #00915A)
  - **Secondary** = `--brand-secondary` (purple/pink #E0388C)
  - **Success** = `--btn-success` (green-600)
  - **Warning** = `--btn-warning` (yellow-500)
  - **Danger** = `--btn-danger` (red-600)
  - **Info** = `--btn-info` (blue-600)
  - ❌ NEVER use arbitrary colors - these semantic tokens are the ONLY source for variant colors
  - Example: `.component--primary { color: var(--brand-primary); }` NOT `var(--bnp-green)` directly
- **CRITICAL**: Read spec in `docs/design/{level}/{component}.md` before implementing.
- **CRITICAL**: DO NOT create `ps-tokens.css` or similar - tokens are organized in separate files by category.
- Refer to `source/patterns/elements/button/` and `source/patterns/elements/divider/` as reference implementation examples.
- Update `docs/ps-design/CHANGELOG.md` after each component implementation and any token additions.

### Example: Token Verification Workflow

**❌ WRONG - Creating token without checking:**
```css
.ps-card {
  padding: 1.5rem; /* JAMAIS en dur ! */
  background: #FFFFFF; /* JAMAIS en dur ! */
}
```

**✅ CORRECT - Verify then use existing:**
```bash
# 1. Search for spacing tokens
grep -r "--size-" source/props/sizes.css
# Found: --size-6: 1.5rem; /* 24px */

# 2. Search for white color
grep -r "--white" source/props/colors.css
# Found: --white: hsl(0 0% 100%);

# 3. Use existing tokens
```
```css
.ps-card {
  padding: var(--size-6);      /* ✅ Existing token */
  background: var(--white);    /* ✅ Existing token */
}
```

**✅ If truly needed - Add with justification:**
```bash
# 1. Verified: --size-5 doesn't exist (progression: --size-4 = 16px, --size-6 = 24px)
# 2. Needed: 20px spacing for specific design requirement
# 3. Add to source/props/sizes.css following convention:
```
```css
/* sizes.css */
--size-5: 1.25rem;  /* 20px - Added for card header spacing */
```
```bash
# 4. Document in CHANGELOG.md:
echo "- Added --size-5 (20px) for card header spacing consistency" >> docs/ps-design/CHANGELOG.md
```
