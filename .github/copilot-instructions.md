# Copilot Instructions for PS Theme (Surface)

**Version**: 4.0.0 (Major restructuring - 17 files → 6 files)  
**Last Updated**: 2025-12-12

---

## 📚 Documentation Structure

**Documentation consolidée en 6 fichiers numérotés sous `.github/instructions/` :**

- **01 Core Principles** → `instructions/01-core-principles.md` - Foundations (Atomic Design, BEM, tokens, terminology, accessibility)
- **02 Component Development** → `instructions/02-component-development.md` - Complete workflow (11 steps, Token-First cascade, real example)
- **03 Technical Implementation** → `instructions/03-technical-implementation.md` - Code standards (CSS, Twig/YAML, Storybook, JavaScript, Accessibility)
- **04 Quality Assurance** → `instructions/04-quality-assurance.md` - Validation (100-point audit, troubleshooting, flowcharts, testing)
- **05 Maintenance** → `instructions/05-maintenance.md` - Evolution (token creation, legacy migration, deprecation, breaking changes)
- **README** → `instructions/README.md` - Navigation hub (quick scenarios, learning path)

**Navigation rapide** : Consultez d'abord `instructions/README.md` pour identifier le fichier adapté à votre tâche.

---

## 🎯 Project At-a-Glance

**PS Theme**: Custom Drupal 10/11 theme for BNP Paribas Real Estate  
**Stack**: Storybook (HTML edition) + Vite + PostCSS + Twig  
**Methodology**: Atomic Design (Brad Frost) + **Token-First Composition Workflow**

**87 Components to Implement**:
- 19 Atoms (elements/) - Autonomous, Token-First does NOT apply
- 20 Molecules (components/) - Token-First APPLIES
- 12 Organisms (collections/) - Token-First APPLIES
- 8 Templates (layouts/) - Token-First APPLIES
- 8 Pages (pages/) - Token-First APPLIES

**Current Progress**: 6/87 (7%) - See `docs/ps-design/INDEX.md`

---

## 🗣️ Language Directive

**Strict policy for maintainability and consistency:**

| Context | Language | Rule | Examples |
|---------|----------|------|----------|
| **AI Chat Responses** | 🇫🇷 French | ALWAYS respond in French to user | Explanations, status updates, questions |
| **Documentation Files** | 🇬🇧 English | ALL `.md`, `.mdx`, README | `README.md`, instruction files, specs |
| **Code Comments** | 🇬🇧 English | Inline `//` and `/* */` | CSS comments, JS annotations, Twig notes |
| **Storybook Content** | 🇬🇧 English | Stories, descriptions, Autodocs | Component descriptions, argTypes labels |
| **Commit Messages** | 🇫🇷 French | Git commit bodies (type/scope EN) | `feat(elements): Ajout du badge...` |
| **Project Logs** | 🇫🇷 French | CHANGELOG, audit reports | `docs/ps-design/CHANGELOG.md` |
| **User-Facing Content** | 🇫🇷 French | Template strings, YAML mocks | Button labels: "En savoir plus" |
| **Technical Identifiers** | 🇬🇧 English | NEVER translate | Tokens, classes, ARIA, file names |

**Rationale**:
- **English docs** = Universal accessibility, design system standard
- **French chat** = Natural collaboration with French-speaking team
- **English code** = Industry standard, tooling compatibility
- **Mixed commits** = Type/scope (English conventions) + body (French context)

---

## ⚡ Quick Decision Tree

**New Component?**  
→ Read spec: `docs/design/{level}/{component}.md`  
→ Follow workflow: `instructions/02-component-development.md`  
→ Use standards: `instructions/03-technical-implementation.md`  
→ **Composing other components?** Follow Token-First: `instructions/02-component-development.md` (section 2)

**CSS Issue?**  
→ Consult: `instructions/03-technical-implementation.md` (section 1)  
→ Token missing? Document need, don't add (see `instructions/05-maintenance.md` section 1)  
→ **Overriding parent/child styles?** Follow Token-First workflow (STEP 3 preferred)


**New Component?**  
→ Read spec: `docs/design/{level}/{component}.md`  
→ Follow workflow: `instructions/02-component-development.md`  
→ Use standards: `instructions/03-technical-implementation.md`  
→ **Composing other components?** Follow Token-First: `instructions/02-component-development.md` (section 2)

**CSS Issue?**  
→ Consult: `instructions/03-technical-implementation.md` (section 1)  
→ Token missing? Document need, don't add (see `instructions/05-maintenance.md` section 1)  
→ **Overriding parent/child styles?** Follow Token-First workflow (STEP 3 preferred)

**Storybook Config?**  
→ Follow: `instructions/03-technical-implementation.md` (section 3)  
→ MANDATORY: `tags: ['autodocs']` in export default

**Twig Template?**  
→ Standards: `instructions/03-technical-implementation.md` (section 2)  
→ CRITICAL: NO arrow functions, NO `.filter(v => v)` (Drupal incompatible)

**JavaScript Behavior?**  
→ Patterns: `instructions/03-technical-implementation.md` (section 4)  
→ Use Drupal behaviors with `once()` for idempotency

**Accessibility?**  
→ Requirements: `instructions/03-technical-implementation.md` (section 5)  
→ WCAG 2.2 AA minimum (contrast, focus-visible, ARIA, keyboard)

**Refactor Legacy?**  
→ Audit: `instructions/04-quality-assurance.md` (Conformity checklist)  
→ Standardize: Fix tokens, nesting, BEM, Autodocs


---

## 🚨 Zero Tolerance Rules

These will ALWAYS be rejected:

- ❌ Hardcoded values: `#00915A`, `16px`, `150ms ease` → Use tokens: `var(--primary)`, `var(--size-4)`
- ❌ Missing any of 5 required files: `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md` (exception: `base/*` stories use 4 files, no README)
- ❌ Missing `tags: ['autodocs']` in Storybook export default (exception: `base/*` stories don't use autodocs)
- ❌ Arrow functions in Twig: `filter(v => v)` → Use ternary: `condition ? 'class' : null`
- ❌ JavaScript methods in Twig: `.map()`, `.filter()`, `.includes()` → Drupal incompatible
- ❌ Color names instead of semantic: `green` → `success`, `red` → `danger`
- ❌ Icon names with prefix: `icon-check` → `check` (prefix auto-added by CSS)
- ❌ **Modifying parent component CSS directly**: Use Token-First workflow (override tokens in consumer's CSS)
- ❌ Modifier classes requiring combinations: `.ps-badge--a.ps-badge--b` → Each must work alone
- ❌ Wrong cascade order: Modifiers before base → Base FIRST, then modifiers
- ❌ Flat CSS without nesting: New components MUST use `&` syntax
- ❌ Missing focus-visible: All interactives MUST have visible focus indicator
- ❌ Editing `source/props/*.css` directly: Propose tokens via separate process
- ❌ `baseClass` parameter for composition: FORBIDDEN → Use `attributes.addClass()` instead

### 🎨 Semantic Colors Reference

**Always use semantic tokens** (from `brand.css`), never raw color names:

| Semantic Token | Base Color | Usage | States Available |
|----------------|------------|-------|------------------|
| **--primary** | Green #00915A | Brand actions, main CTAs | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |
| **--secondary** | Pink #A12B66 | Secondary actions, accents | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |
| **--success** | Teal #198754 | Success states, confirmations | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |
| **--danger** | Red #EB3636 | Errors, destructive actions | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |
| **--warning** | Yellow #FBBF24 | Warnings, cautions | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |
| **--info** | Blue #2563EB | Informational content | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |
| **--gold** | Gold #D1AE6E | Premium features, highlights | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |
| **--light** | Gray 100 | Light backgrounds | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |
| **--dark** | Gray 700 | Dark backgrounds | -hover, -active, -text, -border, -subtle, -bg-subtle, -border-subtle, -text-emphasis |

**Note on "neutral" variant:**
- `neutral` is a **component-level default state**, not a global semantic token
- Components typically use `var(--gray-500)` or context-appropriate gray for neutral states
- Each component can define its own neutral appearance based on its design requirements

**Additional semantic tokens**:
- **Text**: `--text-primary`, `--text-secondary`, `--text-disabled`, `--text-inverse`
- **Borders**: `--border-default`, `--border-light`, `--border-focus`, `--border-disabled`, `--border-error`, `--border-success`
- **Overlays**: `--overlay-dark-heavy`, `--overlay-dark-medium`, `--overlay-dark-light`, `--overlay-brand-base`, `--overlay-brand-medium`, `--overlay-brand-light`

**Examples**:
```css
/* ✅ CORRECT - Semantic tokens */
.ps-button--primary { background: var(--primary); }
.ps-alert--success { background: var(--success-subtle); color: var(--success-text-emphasis); }
.ps-badge--danger { background: var(--danger-bg-subtle); color: var(--danger); }

/* ✅ CORRECT - Component-level neutral (gray) */
.ps-button--neutral { background: var(--gray-500); color: var(--white); }
.ps-badge--neutral { background: var(--gray-100); color: var(--gray-700); }

/* ❌ WRONG - Color names or raw palette */
.ps-button--primary { background: green; }
.ps-button--primary { background: var(--green-600); }
```

### 🎭 Icon System Reference

**Icon prefix handling** - NEVER include `icon-` prefix in code:

**How it works**:
1. Store SVG files in `source/icons-source/` (e.g., `check.svg`)
2. Build script (`npm run build:icons`) generates sprite with `#icon-{name}` IDs
3. CSS maps `[data-icon="name"]` → `url('/icons/icons-sprite.svg#icon-{name}')`
4. Use icon name WITHOUT prefix in templates

**Examples**:
```twig
{# ✅ CORRECT - No icon- prefix #}
<span class="ps-button__icon" data-icon="check"></span>
<span class="ps-button__icon" data-icon="arrow-right"></span>

{# ✅ CORRECT - Via icon atom #}
{% include '@elements/icon/icon.twig' with {
  icon: 'search',
  size: 'md'
} only %}

{# ❌ WRONG - Including icon- prefix #}
<span data-icon="icon-check"></span>
{% include '@elements/icon/icon.twig' with { icon: 'icon-search' } %}
```

**CSS Implementation** (`source/props/icons.css`):
```css
/* Base styling for all icons */
[data-icon] {
  display: inline-block;
  width: 1em;
  height: 1em;
  background-repeat: no-repeat;
  background-position: center;
  background-size: contain;
}

/* Auto-generated mappings (scripts/build-icons.mjs) */
[data-icon="check"] { background-image: url('/icons/icons-sprite.svg#icon-check'); }
[data-icon="search"] { background-image: url('/icons/icons-sprite.svg#icon-search'); }
```

**Available icons**: See `source/patterns/documentation/icons-registry.json` (auto-generated) or Storybook Elements/Icon story.

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
- [ ] Conformity audit: 100% score (see `instructions/04-quality-assurance.md`)
- [ ] Commit with structured message (see format below)
- [ ] Update `docs/ps-design/CHANGELOG.md`

---

## 📝 Git Commit Message Format

**Structure**:
```
type(scope): Subject line (max 72 chars)

- Detailed explanation point 1
- Detailed explanation point 2
- References spec: docs/design/{level}/{component}.md
- Closes #issue-number (if applicable)
```

**Types**:
- `feat` - New component, feature, or enhancement
- `fix` - Bug fix or correction
- `refactor` - Code restructuring without functional changes
- `docs` - Documentation updates (README, instructions)
- `style` - Code formatting, whitespace, CSS adjustments
- `test` - Adding or updating tests
- `chore` - Build process, tooling, dependencies

**Scopes**:
- `elements` - Atoms (button, badge, icon, etc.)
- `components` - Molecules (card, form-field, etc.)
- `collections` - Organisms (header, footer, etc.)
- `layouts` - Templates (page layouts)
- `pages` - Page implementations
- `base` - Base stories (colors, typography, etc.)
- `tokens` - Design tokens (colors.css, sizes.css, etc.)
- `docs` - Documentation files
- `build` - Build system, scripts, config

**Examples**:
```bash
# New component
feat(elements): Add badge component with semantic colors

- Implement 5-file structure (twig, css, yml, stories, README)
- Support 9 semantic colors with all state variants
- Add pill modifier and icon integration
- Full Autodocs with categorized argTypes
- References spec: docs/design/atoms/badge.md

# Bug fix
fix(components): Correct card CTA alignment on mobile

- Fix flexbox gap issue causing CTA misalignment
- Update breakpoint from 768px to 640px
- Tested on iPhone SE, Pixel 5, iPad

# Refactoring
refactor(base): Standardize all base stories with _base-story.twig

- Convert colors, fonts, shadows, sizes to template
- Remove custom CSS, use storybook.css classes only
- Add header metadata (title, badge, meta)
- Update stories exports (remove autodocs tags)

# Documentation
docs(instructions): Clarify icon prefix handling and composition rules

- Document data-icon attribute system in copilot-instructions.md
- Add exception for atoms including rendering systems
- Update semantic colors reference table
```

---

## 🎓 Reference Components

**Perfect implementations to study**:

- **Button** (`source/patterns/elements/button/`) - CSS nesting, all states, complete stories
- **Avatar** (`source/patterns/elements/avatar/`) - Minimal markup, adaptive sizing, SVG fallback
- **Badge** (`source/patterns/elements/badge/`) - Semantic colors, pill variant, icon integration
- **Divider** (`source/patterns/elements/divider/`) - Simplicity, orientation variants, minimal code

Always prefer reading actual component code over guessing patterns.

---

## 🔧 Build & Productivity Commands

```bash
npm run build          # Compile assets + lint/format checks
npm run watch          # Vite + Storybook (http://localhost:6006)
npm run storybook:build # Static Storybook output (storybook/)

# Component generation
npm run generate:pattern              # Interactive mode (prompts for type/name)
npm run generate:pattern -- --type=element --name="Badge"  # Flag mode

# Token utilities
npm run tokens:check -- <token-name>  # Search token in props/ (definition + usages)
# Example: npm run tokens:check -- --primary
```

**Build validates**:
- Biome lint/format (JavaScript, JSON)
- CSS compilation (Vite + PostCSS)
- No syntax errors (Twig via Storybook)

**Productivity tools**:
- **Token checker** (`scripts/check-tokens.mjs`): Search design tokens with line numbers and statistics
- **Enhanced generator** (`scripts/generate-pattern.mjs`): Interactive scaffolding with README.md generation
- **VS Code snippets** (`.vscode/ps-theme.code-snippets`): 10 snippets (type `ps<TAB>` in files):
  - **Twig**: `psheader`, `psclasses`, `psinclude`, `psdefault`
  - **CSS**: `pscomponent`, `pselement`, `psmodifier`
  - **Storybook**: `psstory`, `psargtype`
  - **Markdown**: `psreadme`

---

## 🔗 Key Resources

- **Design Specs**: `docs/design/` - Complete specifications for all 87 components
- **Project Status**: `docs/ps-design/INDEX.md` - Inventory + phases
- **Changelog**: `docs/ps-design/CHANGELOG.md` - Implementation history
- **Storybook Demo**: [Surface Storybook](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/)

---

## 🤖 For AI Agents

**PRIMARY DIRECTIVE**:  
Before ANY component work, consult the relevant instruction file(s) under `.github/instructions/`. These are the SINGLE SOURCE OF TRUTH.

**Standard Workflow**:
1. Read: `instructions/README.md` (Navigation - identify relevant file for task)
2. Read: Relevant numbered file (01-05 based on task type)
3. Apply: Domain-specific standards and workflows
4. Validate: Conformity audit (100% score required)
5. Commit: Structured message + changelog update

**When in doubt**: Consult instruction files first via README.md navigation, then ask for clarification (never guess).

---

**Maintainers**: Design System Team  
**Contact**: See project README for support channels
---

**Maintainers**: Design System Team  
**Contact**: See project README for support channels
