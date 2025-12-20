# Copilot Instructions for PS Theme (Surface)

**Version**: 4.0.0 (Major restructuring - 17 files → 6 files)  
**Last Updated**: 2025-12-12

---

## 📚 Documentation Structure

**Documentation consolidée en 6 fichiers numérotés sous `.github/instructions/` :**

- **01 Core Principles** → `instructions/01-core-principles.md` - Foundations (Atomic Design, BEM, tokens, terminology, accessibility)
- **02 Component Development** → `instructions/02-component-development.md` - Complete workflow (11 steps, Token-First cascade, real example)
- **03 Technical Implementation** → `instructions/03-technical-implementation.md` - Code standards (CSS, Twig/YAML, Storybook, JavaScript, Accessibility)
- **04 Quality Assurance** → `instructions/04-quality-assurance.md` - Validation (90-point audit, troubleshooting, flowcharts, testing)
- **05 Maintenance** → `instructions/05-maintenance.md` - Evolution (token creation, legacy migration, deprecation, breaking changes)
- **06 Drupal Integration** → `instructions/06-drupal-integration.md` - Drupal-specific patterns (preprocess, render arrays, Form API, cache, respecting Atomic Design)
- **README** → `instructions/README.md` - Navigation hub (quick scenarios, learning path)

**Navigation rapide** : Consultez d'abord `instructions/README.md` pour identifier le fichier adapté à votre tâche.

---

## 🎯 Project At-a-Glance

**PS Theme**: Custom Drupal 10/11 theme for BNP Paribas Real Estate  
**Stack**: Storybook (HTML edition) + Vite + PostCSS + Twig  
**Methodology**: Atomic Design (Brad Frost) + **Token-First Composition Workflow** + **Balanced Utility-First Approach**

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

**Drupal Integration?**  
→ Consult: `instructions/06-drupal-integration.md` (Preprocess, render arrays, Form API)  
→ CRITICAL: Respect Atomic Design (atoms = no preprocess, molecules+ = #theme hooks)  
→ Test: Create render array, clear cache (`drush cr`), verify in Drupal page


---

## 🚨 Zero Tolerance Rules

These will ALWAYS be rejected:

- ❌ Hardcoded values: `#00915A`, `16px`, `150ms ease` → Use tokens: `var(--primary)`, `var(--size-4)`
- ❌ Missing any of 4 required files: `.twig`, `.css`, `.yml`, `.stories.jsx`
- ❌ Missing `create_attribute()` fallback pattern: `(attributes ? attributes : create_attribute()).addClass(classes)` → MANDATORY for Drupal
- ❌ Manual attribute handling: `class="{{ classes|join(' ') }}" {{ attributes|without('class') }}` → Use create_attribute() instead
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
- ❌ **Utility class overuse**: Use utilities ONLY for variants/modifiers, NEVER for component structure → Maintain semantic component CSS for core styles
- ❌ **Border-radius by default**: NO rounded corners unless explicitly specified in design spec → Use `border-radius: 0` or remove property entirely (prefer removal)
  - Exception: Only add `border-radius` if design spec EXPLICITLY requires it
  - When specified: Use tokens (`var(--radius-1)` to `var(--radius-4)`)
  - Default state for all components: **SHARP CORNERS** (no border-radius)

---

## ⚠️ AI Common Mistakes (Traps to Avoid)

**These errors frequently occur when AI agents implement components. Double-check these BEFORE committing:**

### 1. 🚫 Not Using create_attribute() Fallback
**Error**: Manual attribute handling instead of Drupal Attribute object
```twig
{# ❌ WRONG - Manual pattern (error-prone) #}
{% set classes = ['ps-component', ...] %}
<div class="{{ classes|join(' ')|trim }}" {{ attributes|without('class') }}>{{ content }}</div>

{# ✅ CORRECT - Drupal Attribute object #}
{% set classes = ['ps-component', ...] %}
{% set attr = (attributes ? attributes : create_attribute()).addClass(classes) %}
<div{{ attr }}>{{ content }}</div>
```
**Impact**: No class deduplication, spacing issues, can't use .removeClass()/.setAttribute() methods

### 2. 🚫 Using Utilities for Component Structure
**Error**: Building component with utilities instead of semantic CSS
```twig
{# ❌ WRONG - Utility hell #}
<div class="flex flex-col gap-4 p-6 bg-white rounded-lg shadow">

{# ✅ CORRECT - Semantic component #}
<div class="ps-card">
```
**Impact**: Violates "Balanced Utility Strategy" (see section below)

### 3. 🚫 Skipping Token-First STEP 3
**Error**: Writing custom CSS overrides instead of using token overrides
```css
/* ❌ WRONG - Direct CSS override */
.ps-molecule {
  & .ps-button {
    padding: var(--size-6);
  }
}

/* ✅ CORRECT - Token override (STEP 3) */
.ps-molecule {
  --ps-button-padding-y: var(--size-6);
}
```
**Impact**: Breaks component encapsulation, harder to maintain

### 4. 🚫 Adding Border-Radius by Default
**Error**: Applying rounded corners without design spec requirement
```css
/* ❌ WRONG - Rounded by default */
.ps-card {
  border-radius: var(--radius-3);
}

/* ✅ CORRECT - Sharp corners (default) */
.ps-card {
  /* No border-radius unless spec requires it */
}
```
**Impact**: Violates design system default (sharp corners)

### 5. 🚫 Creating Tokens During Component Work
**Error**: Editing `source/props/*.css` when token missing
```bash
# ❌ WRONG
vim source/props/sizes.css  # Adding --size-7 directly

# ✅ CORRECT
# 1. Document need in component CSS comment
# 2. Use hardcoded value temporarily: 30px /* TODO: Token proposal */
# 3. Follow 05-maintenance.md token creation process
```
**Impact**: Bypasses design system governance

### 6. 🚫 Not Reading Drupal Baseline Templates
**Error**: Writing Twig from scratch without consulting Drupal core templates
```twig
{# ❌ WRONG - Custom structure #}
<button class="ps-button">{{ text }}</button>

{# ✅ CORRECT - Based on Drupal core/themes/starterkit_theme/templates #}
<button{{ attributes.addClass('ps-button') }}>{{ text }}</button>
```
**Impact**: Incompatible with Drupal render arrays
**See**: 03-technical-implementation.md Section 2.1

### 7. 🚫 Missing Component-Scoped Variables (Layer 2)
**Error**: Using global tokens directly without component variables
```css
/* ❌ WRONG - No override capability */
.ps-button {
  padding: var(--size-4);
  background: var(--primary);
}

/* ✅ CORRECT - Layer 2 variables */
.ps-button {
  --ps-button-padding-y: var(--size-4);
  --ps-button-bg: var(--primary);
  
  padding: var(--ps-button-padding-y);
  background: var(--ps-button-bg);
}
```
**Impact**: Consumers cannot customize via token overrides

### 8. 🚫 Arrow Functions in Twig
**Error**: Using JavaScript syntax in Drupal Twig
```twig
{# ❌ WRONG - Arrow function #}
{% set classes = items|filter(v => v) %}

{# ✅ CORRECT - Ternary with null #}
{% set classes = [
  'ps-component',
  variant != 'default' ? 'ps-component--' ~ variant : null
] %}
```
**Impact**: Breaks in Drupal (Twig limitation)

### 9. 🚫 Missing `tags: ['autodocs']` in Stories
**Error**: Storybook export without autodocs tag
```jsx
// ❌ WRONG
export default {
  title: 'Elements/Badge',
};

// ✅ CORRECT
export default {
  title: 'Elements/Badge',
  tags: ['autodocs'],
};
```
**Impact**: Component docs page empty in Storybook

### 10. 🚫 Using Semantic Color Names as Values
**Error**: Hardcoding semantic token names instead of using them
```css
/* ❌ WRONG - Color name as value */
.ps-badge--success {
  background: green;
}

/* ✅ CORRECT - Semantic token */
.ps-badge--success {
  background: var(--success);
}
```
**Impact**: Violates token system

### ✅ Prevention Checklist (Before Commit)

- [ ] `create_attribute()` fallback pattern used: `(attributes ? attributes : create_attribute()).addClass(classes)`?
- [ ] Root element uses `{{ attr }}` (NOT manual class + attributes)?
- [ ] Component CSS uses semantic classes (not utility soup)?
- [ ] Token-First STEP 3 applied for composition overrides?
- [ ] Border-radius ONLY if design spec explicitly requires it?
- [ ] NO tokens created (only documented if missing)?
- [ ] Drupal baseline template consulted (03-technical-implementation.md 2.1)?
- [ ] Component-scoped variables (Layer 2) defined?
- [ ] NO arrow functions in Twig (ternary + null instead)?
- [ ] `tags: ['autodocs']` in Storybook export?
- [ ] Semantic color tokens used (var(--primary), NOT green)?

**When in doubt**: Read the relevant instruction file section BEFORE implementing.

---

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

**Standard Pattern - data-icon attribute** (PREFERRED):
```twig
{# ✅ CORRECT - data-icon on element (matches button.twig pattern) #}
<span class="ps-button__icon" data-icon="check" aria-hidden="true"></span>
<span class="ps-breadcrumb__separator" data-icon="chevron-right" aria-hidden="true"></span>
<a href="/link" class="ps-link" data-icon="arrow-right">Link text</a>
<button data-icon="search" aria-label="Search"></button>

{# ❌ WRONG - Including icon- prefix #}
<span data-icon="icon-check"></span>
```

**Alternative Pattern - Icon component** (ONLY for standalone icon atom documentation):
```twig
{# ⚠️ RARE - Only use when documenting Icon atom itself #}
{% include '@elements/icon/icon.twig' with {
  icon: 'search',
  size: 'md',
  ariaLabel: 'Search'
} only %}

{# ❌ WRONG - Don't use in other component docs #}
{% include '@elements/icon/icon.twig' ... %} {# Use data-icon instead #}
```

**Rule**: All components MUST use `data-icon` attribute (like button.twig). The Icon component include is ONLY for documenting the Icon atom itself in `source/patterns/elements/icon/`.

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

## 🎯 Form Components Standards

**Overview**: Form components follow Drupal's native form API structure to ensure seamless theme integration with Drupal 10/11. All form controls are implemented as self-sufficient Atoms that natively support Drupal's `.form-control` class.

**Reference**: See [FORM_ATOMS_REFACTOR_PLAN.md](../../FORM_ATOMS_REFACTOR_PLAN.md) and [DAILY_WORK_SUMMARY.md](../../DAILY_WORK_SUMMARY.md) for comprehensive refactoring documentation.

### 📦 Form Control Atoms (Self-Sufficient)

All form control Atoms **MUST** natively include `.form-control` class in their templates:

**Input Atom** (`source/patterns/elements/input/`)
```twig
{# input.twig #}
<input 
  class="ps-input form-control {{ modifier_class }}"
  type="{{ type }}"
  {{ attributes }}
/>
```
- Supports: text, email, password, number, search, tel, url
- CSS selectors: `.ps-input` AND `.form-control[type="..."]` (dual support)
- Modifiers: `--error`, `--success`, `--warning` (apply to both classes)

**Textarea Atom** (`source/patterns/elements/textarea/`)
```twig
{# textarea.twig #}
<textarea
  class="ps-textarea form-control {{ modifier_class }}"
  {{ attributes }}
></textarea>
```
- CSS selectors: `.ps-textarea` AND `.form-control:is(textarea)` (dual support)
- Modifiers: `--error`, `--success`, `--warning` (apply to both classes)

**Select Atom** (`source/patterns/elements/select/`)
```twig
{# In select.twig - apply to __input element #}
<select class="ps-select__input form-control">
  {# Options #}
</select>
```
- CSS selectors: `.ps-select__input` AND `.form-control:is(select)` (dual support)
- Modifiers: `--error`, `--success`, `--warning` (apply to both classes)
- Note: Chevron icon is added via wrapper, not affecting select element

### 🧩 Form Field Molecule (Composition Pattern)

**Form Field** (`source/patterns/components/form-field/`) is a container molecule that **composes** (includes) form control Atoms:

```twig
{# form-field.twig - INCLUDES atoms, does NOT reimplement #}
<div class="ps-form-field {{ form_field_class }}">
  <div class="ps-form-label-wrapper">
    <label class="ps-form-label">{{ label }}</label>
    {% if optional %}<span class="ps-form-optional">Optional</span>{% endif %}
  </div>
  
  {# Compose Input Atom (already has form-control) #}
  {% include '@elements/input/input.twig' with {
    id: input_id,
    type: field_type,
    attributes: attributes
  } only %}
  
  {# Error/Helper text styling #}
  {% if error %}<span class="ps-form-error">{{ error }}</span>{% endif %}
</div>
```

**KEY RULE**: Form Field **does NOT** add `.addClass('form-control')` to included Atoms - they provide it natively.

**Composition Benefits**:
- ✅ Atoms remain self-sufficient (work alone or composed)
- ✅ Form Field adds presentation (label wrapper, icon positioning, error styling)
- ✅ No code duplication (Atom styles not reimplemented)
- ✅ Drupal integration (form-control class propagates from Atom)

### 🎨 Form States & Styling

**8 Required States** (documented in form-field.css):
1. **Default** - Border: `--border-default`, Background: transparent
2. **Placeholder** - Text: `--text-secondary`, Border: `--border-default`
3. **Hover** - Border: `--border-default`, Background: subtle change
4. **Focus** - Border: `--border-focus`, Outline: 2px offset
5. **Done/Success** - Border: `--success`, Color: `--success-text-emphasis`
6. **Error** - Border: `--danger`, Text: `--danger`, Error message: red text only
7. **Disabled (Normal)** - Background: `--gray-100`, Color: `--text-disabled`, Cursor: not-allowed
8. **Disabled (Error)** - Border: `--danger`, Background: `--gray-100`

**Icon Support** (3 variants):
- None: Label + input only
- Left (search): Icon positioned 12px from left edge
- Right (chevron): Icon positioned 12px from right edge
- Icon spacing: Input padding must accommodate icon width + offset (typically `var(--size-8)` = 32px)

**Error Styling Convention**:
- Error text: Simple red text, no background
- Error message: Red color via `var(--danger)`
- Border: Red line via `--danger` token
- No additional graphics or icons in error message

### ✅ Form Component Checklist

**Before Creating Form Components**:
- [ ] Atom naturally includes `.form-control` in template (Input/Textarea/Select)
- [ ] CSS supports BOTH `.ps-*` AND `.form-control` selectors (combined selectors)
- [ ] All colors use semantic tokens: `--danger`, `--success`, not hardcoded
- [ ] All spacing uses size tokens: `--size-1` through `--size-12`
- [ ] All transitions use duration/easing tokens: `--duration-*`, `--ease-*`
- [ ] Focus states include `focus-visible` pseudo-class
- [ ] Modifiers work independently (no combo requirements)
- [ ] Form Field COMPOSES atoms via `{% include %}`, does NOT reimplement
- [ ] Error text is RED ONLY (no background, no icons)
- [ ] Icon spacing doesn't overlap text (verify with 32px padding rule)

**Validation**:
- [ ] Build passes: `npm run build` (0 errors)
- [ ] Storybook: 30 stories render correctly (http://localhost:6006)
- [ ] Conformity audit: 100% score (see `instructions/04-quality-assurance.md`)

**Drupal Integration** (Molecules+ only):
- [ ] `#theme` hook registered in `ps.theme` (see `instructions/06-drupal-integration.md`)
- [ ] Preprocess function created (compose atoms, prepare data)
- [ ] Render array tested in Drupal controller/module
- [ ] Libraries attached (`ps.libraries.yml`)
- [ ] Cache tags/contexts defined
- [ ] Atoms remain autonomous (NO preprocess for atoms)

---

## 📋 Component Checklist (Quick)

**Before starting**:
- [ ] Read spec: `docs/design/{level}/{component}.md`
- [ ] Verify dependencies exist (atoms for molecules/organisms)
- [ ] Check required tokens exist (`grep -r "--token-name" source/props/`)

**Implementation**:
- [ ] Create 4 files: `.twig`, `.css`, `.yml`, `.stories.jsx`
- [ ] Twig: Header comment, defaults, ternary + `null`, `{% include %}` with `only`
- [ ] CSS: ALL tokens, nesting with `&`, cascade order, semantic colors, focus-visible
- [ ] YAML: Real Estate placeholders (property data, NOT generic lorem ipsum)
- [ ] Storybook: `tags: ['autodocs']`, argTypes categorized, Default + Showcases (NO dedicated "RealEstateContext" stories)

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
## ⚖️ Balanced Utility-First Strategy

**CRITICAL RULE**: Utilities must be used **intelligently and sparingly** to avoid "utility hell".

### ✅ WHEN TO USE UTILITIES (Good)

**1. Component Variants/Modifiers** (colors, sizes, weights):
```twig
{# ✅ GOOD - Heading with color/weight variants #}
<h1 class="h1 text-primary font-bold text-center">Title</h1>
```

**2. One-off Adjustments** (spacing tweaks, alignment):
```twig
{# ✅ GOOD - Quick spacing adjustment #}
<div class="ps-card mb-6">...</div>
```

**3. Prototyping/Rapid Development**:
```twig
{# ✅ GOOD - Quick prototype before extracting pattern #}
<div class="flex items-center gap-4 p-6 bg-white rounded-lg shadow">...</div>
```

### ❌ WHEN NOT TO USE UTILITIES (Bad)

**1. Complex Component Structure** (semantic meaning lost):
```twig
{# ❌ BAD - Too many utilities, no semantic meaning #}
<div class="flex flex-col gap-6 p-8 bg-white rounded-lg shadow-md border border-gray-200">
  <div class="flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-900">Title</h2>
    <span class="px-3 py-1 bg-primary text-white rounded-full text-sm">Badge</span>
  </div>
  <p class="text-gray-600 text-base leading-relaxed">Description...</p>
</div>

{# ✅ GOOD - Semantic component with minimal utilities #}
<div class="ps-card ps-card--with-badge">
  <h2 class="ps-card__title">Title</h2>
  <span class="ps-badge ps-badge--primary">Badge</span>
  <p class="ps-card__description">Description...</p>
</div>
```

**2. Repeated Patterns** (extract to component):
```twig
{# ❌ BAD - Repeated utility soup #}
<button class="px-6 py-3 bg-primary text-white font-semibold rounded-lg shadow hover:bg-primary-hover">CTA 1</button>
<button class="px-6 py-3 bg-primary text-white font-semibold rounded-lg shadow hover:bg-primary-hover">CTA 2</button>

{# ✅ GOOD - Component #}
<button class="ps-button ps-button--primary">CTA 1</button>
<button class="ps-button ps-button--primary">CTA 2</button>
```

**3. Component Core Structure** (base styles always in CSS):
```css
/* ✅ GOOD - Component has semantic CSS */
.ps-card {
  display: flex;
  flex-direction: column;
  gap: var(--size-6);
  padding: var(--size-8);
  background: var(--white);
  border-radius: var(--radius-3);
  box-shadow: var(--shadow-2);
}

.ps-card__title {
  font-size: var(--font-size-5);
  font-weight: var(--font-weight-700);
  color: var(--gray-900);
}
```

### 📏 BALANCE GUIDELINE

**Rule of Thumb**: If a component needs **more than 5-6 utility classes**, it should probably be a proper component with semantic CSS.

**Good balance example**:
```twig
{# Component structure = CSS, variants = utilities #}
<article class="ps-article">
  <h2 class="ps-article__title text-primary">{{ title }}</h2>  {# variant via utility ✓ #}
  <div class="ps-article__meta">{{ date }}</div>
  <p class="ps-article__excerpt">{{ excerpt }}</p>
  <a href="{{ url }}" class="ps-button ps-button--primary mt-4">Read more</a>  {# spacing via utility ✓ #}
</article>
```

### 🎯 DECISION TREE

```
Will this pattern be reused 3+ times?
├─ YES → Create semantic component with CSS
└─ NO → Is it complex (6+ utilities)?
   ├─ YES → Create semantic component anyway
   └─ NO → Use utilities ✓
```

**Examples**:
```bash
# New component
feat(elements): Add badge component with semantic colors

- Implement 4-file structure (twig, css, yml, stories)
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
- **Enhanced generator** (`scripts/generate-pattern.mjs`): Interactive scaffolding with 4-file structure
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
