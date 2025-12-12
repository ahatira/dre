---
title: Card Component Analysis Prompt
version: 2.1.0
lastUpdated: 2025-12-10
applyTo:
  - "source/patterns/components/card-*.twig"
  - "source/patterns/components/card-*.css"
priority: LOW
related:
  - card-inheritance.instructions.md
  - composition-token-first.instructions.md
status: ACTIVE
---

# Card Component Inheritance - Analysis & Implementation Prompt

**Purpose:** Complete prompt for AI agents to analyze, verify, or implement Card-based components

**Changelog:**
- **v2.1.0 (2025-12-10)**: Added CRITICAL variable passing requirement in Phase 3.2 with examples
- **v2.0.0 (2025-12-10)**: Added naming convention section with card-{bundle}-{view_mode} pattern

---

## 🎯 MISSION

You are tasked with [SELECT ONE]:
- [ ] **Analyzing** an existing Card-based component for conformity
- [ ] **Verifying** a Card-based component implementation
- [ ] **Creating** a new Card-based component from scratch
- [ ] **Correcting** issues in an existing Card-based component

**Component Name:** `card-[bundle]-[view_mode]` (e.g., `card-offer-slide`, `card-article-teaser`)  
**Component Path:** `source/patterns/components/card-[bundle]-[view_mode]/`  
**Design Spec:** `docs/design/[level]/[component-name].md`

---

## 🎯 CRITICAL: NAMING CONVENTION

**ALL Card-based components MUST follow this pattern:**

```
card-{bundle}-{view_mode}
```

**Examples:**
- ✅ `card-offer-slide` - Property card for listing sliders
- ✅ `card-offer-search` - Property card for search results
- ✅ `card-article-teaser` - Article preview card
- ✅ `card-product-grid` - Product card for grid layouts
- ❌ `offer-card-list` - WRONG (old pattern)
- ❌ `property-card` - WRONG (missing view_mode)

**This naming:**
- Aligns with Drupal's `bundle` + `view_mode` class system
- Creates predictable folder structure
- Enables CSS specificity with `.ps-card.ps-card-{bundle}-{view_mode}`
- Improves searchability and scalability

---

## 📋 PHASE 1: PREREQUISITES CHECK

### 1.1 Documentation Review

**Read these files BEFORE starting any work:**

1. **Primary Instructions:**
   - [ ] Read `.github/instructions/card-inheritance.instructions.md` (THIS IS YOUR BIBLE)
   - [ ] Read `source/patterns/components/card/card.twig` (understand blocks)
   - [ ] Read `source/patterns/components/card/card.css` (understand CSS variables)

2. **Supporting Instructions:**
   - [ ] Read `.github/instructions/components.instructions.md` (5-file structure)
   - [ ] Read `.github/instructions/css.instructions.md` (3-layer system)
   - [ ] Read `.github/instructions/storybook.instructions.md` (autodocs format)
   - [ ] Read `.github/instructions/accessibility.instructions.md` (WCAG 2.2 AA)

3. **Design Specification:**
   - [ ] Read `docs/design/[level]/[component-name].md`
   - [ ] Note required features, variants, states
   - [ ] Identify visual requirements (image ratio, spacing, colors)

4. **Reference Implementation:**
   - [ ] Study `source/patterns/components/offer-card-list/` (complete example)

### 1.2 Dependencies Check

**Verify required atomic components exist:**

```bash
# Check if Image atom is production-ready
ls source/patterns/elements/image/

# Check if Button atom is production-ready  
ls source/patterns/elements/button/

# List all available icons
cat source/patterns/documentation/icons-registry.json
```

**Document findings:**
- [ ] Image atom: ✅ Available | ❌ Needs fix | ❌ Missing
- [ ] Button atom: ✅ Available | ❌ Needs fix | ❌ Missing
- [ ] Required icons: [LIST] - ✅ Available | ❌ Missing
- [ ] Other dependencies: [LIST]

---

## 📋 PHASE 2: ARCHITECTURE ANALYSIS

### 2.1 Card Component Mapping

**Answer these questions:**

1. **Which Card blocks will be used?**
   - [ ] `media` - For: [IMAGE/VIDEO/VISUAL]
   - [ ] `media_overlay` - For: [BADGES/FAVORITE BUTTON/NAVIGATION]
   - [ ] `header` - For: [TAGS/DATE/LOCATION/METADATA]
   - [ ] `body` - For: [TITLE/DESCRIPTION/MAIN CONTENT]
   - [ ] `footer` - For: [CTA LINK/BUTTONS/ACTIONS]

2. **Which Card parameters are needed?**
   - `variant`: [default | outlined | flat | elevated] - Why: [REASON]
   - `radius`: [none | sm | md | lg] - Why: [REASON]
   - `size`: [small | medium | large] - Why: [REASON]
   - `layout`: [vertical | horizontal] - Why: [REASON]
   - `url`: [YES/NO] - Should entire card be clickable?

3. **Card CSS variables to override?**
   - `--ps-card-padding-y`: [VALUE] - Why: [REASON]
   - `--ps-card-padding-x`: [VALUE] - Why: [REASON]
   - `--ps-card-gap`: [VALUE] - Why: [REASON]
   - `--ps-card-border-radius`: [VALUE] - Why: [REASON]
   - Other: [LIST]

### 2.2 Atomic Component Reuse

**Document which atoms will be included:**

| Atom Component | Purpose | Custom Class | Parameters |
|----------------|---------|--------------|------------|
| Image | [MAIN IMAGE] | `ps-[component]__image` | src, alt, class |
| Button | [FAVORITE] | `ps-[component]__favorite` | icon, class, attributes |
| Badge | [STATUS] | `ps-[component]__badge` | text, variant, class |
| Link | [CTA] | `ps-[component]__cta` | url, text, class |
| [OTHER] | [PURPOSE] | `ps-[component]__[element]` | [PARAMS] |

### 2.3 Custom Elements

**List elements that need custom HTML (not atoms):**

| Element | Purpose | BEM Class | Reason for Custom HTML |
|---------|---------|-----------|------------------------|
| Header | Price • m² | `ps-[component]__header` | Complex layout with separator |
| Title | Property name | `ps-[component]__title` | Simple h3, no component needed |
| Location | City/address | `ps-[component]__location` | Icon + text layout |
| [OTHER] | [PURPOSE] | `ps-[component]__[element]` | [REASON] |

---

## 📋 PHASE 3: TWIG IMPLEMENTATION PLAN

### 3.1 Component Parameters

**Define all props with types and defaults:**

```twig
{#
/**
 * [Component Name] (Component/Molecule)
 * [Brief description]
 * 
 * @param string title - [Description] (required)
 * @param object image - Image data: { url: string, alt: string } (required)
 * @param string [param3] - [Description] (optional, default: [VALUE])
 * @param [type] [param4] - [Description]
 * ... [COMPLETE LIST]
 */
#}

{%- set title = title|default('[DEFAULT]') -%}
{%- set image = image|default(null) -%}
{%- set [param3] = [param3]|default('[DEFAULT]') -%}
... [ALL PARAMS]
```

### 3.2 Embed Structure

**Plan the embed configuration:**

```twig
{% embed '@components/card/card.twig' with {
  variant: '[VARIANT]',              {# Why: [REASON] #}
  radius: '[RADIUS]',                {# Why: [REASON] #}
  size: '[SIZE]',                    {# Why: [REASON] #}
  layout: '[LAYOUT]',                {# Why: [REASON] #}
  url: [URL_PARAM],                  {# Conditional: [CONDITION] #}
  attributes: attributes.addClass('ps-card-[bundle]-[view_mode]'),
  bundle: bundle|default('[bundle]'),
  view_mode: view_mode|default('[view_mode]'),
  {# ⚠️ CRITICAL: Pass ALL variables used in blocks #}
  title: title,
  price: price,
  surface: surface,
  image: image,
  location: location,
  locationIcon: locationIcon,
  cta: cta,
  isFavorite: isFavorite
  {# ... [ALL DATA PARAMS] #}
} %}
```

**CRITICAL: Component class name MUST match folder name:**
- Folder: `source/patterns/components/card-offer-slide/`
- Class: `attributes.addClass('ps-card-offer-slide')`
- Bundle: `bundle|default('offer')`
- View mode: `view_mode|default('slide')`

**⚠️ CRITICAL - Variable Scope in Embed Blocks:**

Variables are **NOT automatically accessible** inside `{% block %}` definitions unless explicitly passed in the `with {}` hash.

**Example (card-offer-slide.twig):**
```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes.addClass('ps-card-offer-slide'),
  bundle: bundle,
  view_mode: view_mode,
  title: title,           {# ← Pass ALL variables used in blocks #}
  price: price,           {# ← Required for body block #}
  surface: surface,       {# ← Required for body block #}
  image: image,           {# ← Required for media block #}
  location: location,     {# ← Required for body block #}
  locationIcon: locationIcon,
  cta: cta,              {# ← Required for footer block #}
  isFavorite: isFavorite {# ← Required for media_overlay block #}
} %}
  {% block body %}
    {# ✅ Variables NOW accessible because passed in with {} #}
    {% if price or surface %}
      <div class="ps-card-offer-slide__header">
        {{ price }}{% if price and surface %} • {% endif %}{{ surface }}
      </div>
    {% endif %}
  {% endblock %}
{% endembed %}
```

**❓ Will you use `only` in embed?**
- [ ] NO - All variables passed explicitly (MANDATORY)
- [ ] YES - Strict isolation (forbidden - use explicit passing instead)

### 3.3 Block Implementations

**Plan each block's content:**

#### Media Block
```twig
{% block media %}
  {# Image atom #}
  {% include '@elements/image/image.twig' with {
    src: [IMAGE_SOURCE],
    alt: [ALT_TEXT],
    class: 'ps-card-[bundle]-[view_mode]__image'
  } only %}
{% endblock %}
```

#### Media Overlay Block (if needed)
```twig
{% block media_overlay %}
  {# MANDATORY wrapper div for overlay elements #}
  <div class="ps-card-[bundle]-[view_mode]__overlay">
    {% include '@elements/button/button.twig' with {
      class: 'ps-card-[bundle]-[view_mode]__favorite',
      icon: [ICON_NAME],
      toggle: true,
      active: [IS_ACTIVE]
    } only %}
  </div>
{% endblock %}
```

#### Body Block
```twig
{% block body %}
  {# Header section (if needed) #}
  <div class="ps-card-[bundle]-[view_mode]__header">
    [CUSTOM HTML FOR HEADER]
  </div>
  
  {# Title #}
  <h3 class="ps-card-[bundle]-[view_mode]__title">{{ title }}</h3>
  
  {# Other elements #}
  <div class="ps-card-[bundle]-[view_mode]__location">
    [ADDITIONAL ELEMENTS]
  </div>
{% endblock %}
```

#### Footer Block
```twig
{% block footer %}
  {# CTA link #}
  <a href="{{ cta.url }}" class="ps-card-[bundle]-[view_mode]__cta">
    <span class="ps-card-[bundle]-[view_mode]__cta-text">{{ cta.text }}</span>
    <span class="ps-card-[bundle]-[view_mode]__cta-icon" data-icon="arrow-right"></span>
  </a>
{% endblock %}
```

---

## 📋 PHASE 4: CSS IMPLEMENTATION PLAN

### 4.1 Token Definitions

**CRITICAL: Two-block structure for overlay tokens**

```css
/* ==========================================
   Overlay tokens (if using media_overlay)
   MUST be defined on Card selector for cascade
   ========================================== */
:where(.ps-card.ps-card-[bundle]-[view_mode]) {
  --favorite-size: var(--size-8);
  --favorite-bg: var(--white);
  --favorite-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  --favorite-icon-size: var(--size-5);
}

/* ==========================================
   Component tokens + styles
   ========================================== */
.ps-card-[bundle]-[view_mode] {
  /* ==========================================
     Card Variable Overrides (if needed)
     ========================================== */
  --ps-card-padding-y: [VALUE];
  --ps-card-padding-x: [VALUE];
  --ps-card-gap: [VALUE];
  
  /* ==========================================
     Component Tokens
     ALL body/footer tokens MUST be defined here
     ========================================== */
  
  /* Container */
  --ps-card-[bundle]-[view_mode]-max-width: [VALUE];
  
  /* Image */
  --ps-card-[bundle]-[view_mode]-image-aspect-ratio: [RATIO];
  
  /* Typography */
  --ps-card-[bundle]-[view_mode]-title-font-size: [VALUE];
  --ps-card-[bundle]-[view_mode]-title-font-weight: [VALUE];
  --ps-card-[bundle]-[view_mode]-title-line-height: [VALUE];
  --ps-card-[bundle]-[view_mode]-title-color: [COLOR];
  
  /* [OTHER TOKENS] */
  
  /* ==========================================
     Component Styles (Layer 3)
     ========================================== */
```

**WHY :where() for overlay tokens?**
- Elements in `media_overlay` are children of `.ps-card__media`, NOT `.ps-card-[bundle]-[view_mode]`
- They can't access tokens from component selector due to CSS cascade
- `:where(.ps-card.ps-card-[bundle]-[view_mode])` makes tokens accessible with zero specificity

### 4.2 Element Styles

**Plan styles for each element:**

```css
.ps-card-[bundle]-[view_mode] {
  /* Container constraints */
  max-width: var(--ps-card-[bundle]-[view_mode]-max-width);
  
  /* Image */
  &__image {
    aspect-ratio: var(--ps-card-[bundle]-[view_mode]-image-aspect-ratio);
    object-fit: cover;
  }
  
  /* Overlay wrapper (MANDATORY for media_overlay elements) */
  &__overlay {
    position: absolute;
    inset: var(--size-3); /* or custom token */
    z-index: 10;
    display: flex;
    justify-content: flex-end; /* or flex-start, center */
    pointer-events: none; /* Allow clicks through to image */
  }
  
  /* Overlay button/element */
  &__favorite {
    width: var(--favorite-size); /* From :where() block */
    background: var(--favorite-bg); /* From :where() block */
    pointer-events: auto; /* Re-enable clicks on button */
    /* ... [MORE STYLES] */
  }
  
  /* Body elements */
  &__title {
    font-size: var(--ps-card-[bundle]-[view_mode]-title-font-size);
    /* ... [MORE STYLES] */
  }
  
  /* [OTHER ELEMENTS] */
}
```

### 4.3 Token Verification Checklist

**Ensure these rules are followed:**

- [ ] **Overlay tokens**: If using `media_overlay`, defined in `:where(.ps-card.ps-card-[bundle]-[view_mode]) {}` BEFORE main selector
- [ ] **Body/footer tokens**: Defined inside `.ps-card-[bundle]-[view_mode] {}` block
- [ ] **Overlay wrapper**: If using `media_overlay`, wrapper div with `position: absolute`, `inset`, `z-index`, `pointer-events: none`
- [ ] **Overlay children**: `pointer-events: auto` to re-enable clicks on buttons/badges
- [ ] **NO hardcoded values**: ALL values use tokens (`var(--token-name)`)
- [ ] **Semantic colors**: Use `--primary`, `--danger`, NOT `--green-600`
- [ ] **Size tokens**: Use `var(--size-N)` for spacing, dimensions
- [ ] **Typography tokens**: Use `var(--font-size-N)`, `var(--font-weight-N)`
- [ ] **Naming consistency**: All tokens prefixed with `--ps-card-[bundle]-[view_mode]-*`
- [ ] Overlay elements use `position: absolute` (Card sets parent to `relative`)
- [ ] Focus-visible styles for all interactive elements
- [ ] Transitions for state changes (hover, focus, active)

---

## 📋 PHASE 5: CONFORMITY AUDIT

### 5.1 Twig Standards

**Check against these rules:**

- [ ] **File header**: Complete JSDoc with all params documented
- [ ] **Defaults**: All params have `|default()` values
- [ ] **Embed syntax**: Uses `{% embed '@components/card/card.twig' %}`
- [ ] **Custom class**: `attributes: attributes.addClass('ps-[component]')`
- [ ] **Block overrides**: Only override needed blocks (media, body, footer)
- [ ] **Atom reuse**: ALL atoms via `{% include %}`, NO raw HTML for atoms
- [ ] **Custom classes**: Each atom gets `class: 'ps-[component]__[element]'`
- [ ] **No arrow functions**: Use ternary `condition ? 'a' : 'b'` NOT `items.filter(v => v)`
- [ ] **No JS methods**: No `.map()`, `.filter()`, `.includes()` in Twig
- [ ] **Accessibility**: `aria-label`, `aria-pressed` for all interactive elements
- [ ] **Icon names**: NO `icon-` prefix (e.g., `'heart'` NOT `'icon-heart'`)

### 5.2 CSS Standards

**Check against these rules:**

- [ ] **Single block**: All tokens + styles in ONE `.ps-[component] {}` block
- [ ] **Token scope**: ALL tokens accessible to child elements
- [ ] **Zero hardcoded**: No `16px`, `#00915A`, `150ms`, etc.
- [ ] **Semantic colors**: Use `--primary`, `--success`, `--danger`, NOT color names
- [ ] **BEM nesting**: Use `&__element` syntax
- [ ] **Cascade order**: Base first, then modifiers
- [ ] **Focus-visible**: All interactive have `:focus-visible` styles
- [ ] **Transitions**: Smooth state changes defined
- [ ] **No !important**: Unless absolutely necessary (document why)

### 5.3 Storybook Standards

**Check against these rules:**

- [ ] **Export default**: `tags: ['autodocs']` present
- [ ] **argTypes**: Categorized (Content, Appearance, Link, Layout, Behavior)
- [ ] **Default story**: Shows all features
- [ ] **Variant stories**: 3-5 stories showing different configurations
- [ ] **Real Estate data**: Property-related content (offices, warehouses, retail)
- [ ] **Faker.js**: Realistic addresses, prices, areas
- [ ] **No Drupal schemas**: YAML data only, no `$schema` keys

### 5.4 Accessibility Standards (WCAG 2.2 AA)

**Check against these rules:**

- [ ] **Color contrast**: 4.5:1 minimum for text
- [ ] **Focus-visible**: All interactive elements have visible focus
- [ ] **Keyboard navigation**: All actions accessible via keyboard
- [ ] **ARIA labels**: All icon-only buttons have `aria-label`
- [ ] **ARIA states**: Toggle buttons have `aria-pressed`
- [ ] **Alt text**: All images have descriptive `alt` attributes
- [ ] **Semantic HTML**: Use `<h3>`, `<a>`, `<button>` (not `<div>` with role)

---

## 📋 PHASE 6: ISSUES DETECTION & CORRECTION

### 6.1 Common Issue Checklist

**For EACH issue found, document:**

| Issue # | Category | Description | Severity | Fix Required |
|---------|----------|-------------|----------|--------------|
| 1 | [CSS/Twig/Stories/README/YAML] | [Description] | [CRITICAL/HIGH/MEDIUM/LOW] | [Action] |
| 2 | [Category] | [Description] | [Severity] | [Action] |
| ... | ... | ... | ... | ... |

### 6.2 Priority Issues (Fix First)

**CRITICAL (breaks build or renders incorrectly):**

- [ ] CSS tokens not defined / not accessible
- [ ] Missing required files (twig, css, yml, stories, README)
- [ ] Syntax errors (Twig, CSS, JSX)
- [ ] Missing `tags: ['autodocs']` in stories
- [ ] Hardcoded values causing design inconsistency
- [ ] Broken atom includes (wrong parameters)

**HIGH (violates standards but works):**

- [ ] Arrow functions in Twig (Drupal incompatible)
- [ ] Missing accessibility attributes
- [ ] Incorrect semantic colors (color names instead of tokens)
- [ ] Missing focus-visible styles
- [ ] Incorrect BEM structure

**MEDIUM (can be improved):**

- [ ] Incomplete documentation
- [ ] Missing variant stories
- [ ] Non-optimal token usage
- [ ] CSS nesting not used

**LOW (nice to have):**

- [ ] Additional stories for edge cases
- [ ] Enhanced documentation examples
- [ ] Additional accessibility features beyond AA

### 6.3 Correction Plan

**For each CRITICAL/HIGH issue, provide:**

1. **Issue description**
2. **Current code** (exact excerpt)
3. **Corrected code** (exact replacement)
4. **Explanation** (why this fixes it)
5. **Related files** (if multiple files need changes)

**Example:**

```markdown
### Issue #1: CSS tokens not accessible

**Severity:** CRITICAL  
**File:** `offer-card-list.css`  
**Line:** 15-40

**Problem:**
Tokens defined in separate Layer 2 block outside `.ps-offer-card-list {}`. 
Child elements cannot access these tokens due to CSS scope with Card parent.

**Current Code:**
```css
.ps-offer-card-list {
  --ps-offer-card-list-title-font-size: var(--font-size-2);
}

.ps-offer-card-list {
  &__title {
    font-size: var(--ps-offer-card-list-title-font-size);  /* ❌ Token not accessible */
  }
}
```

**Corrected Code:**
```css
.ps-offer-card-list {
  /* ALL tokens defined here */
  --ps-offer-card-list-title-font-size: var(--font-size-2);
  
  /* Styles immediately after */
  &__title {
    font-size: var(--ps-offer-card-list-title-font-size);  /* ✅ Token accessible */
  }
}
```

**Explanation:**
Card component creates CSS scope. All component tokens MUST be defined inside main selector to be accessible via cascade to child elements.
```

---

## 📋 PHASE 7: IMPLEMENTATION EXECUTION

### 7.1 File Creation Order

**Create files in this order:**

1. **YAML** (`[component-name].yml`)
   - Define data structure first
   - Use Real Estate context (properties, offices, warehouses)
   - No Drupal schemas, data only

2. **Twig** (`[component-name].twig`)
   - Implement embed structure
   - Override Card blocks
   - Include atomic components
   - Use data from YAML for defaults

3. **CSS** (`[component-name].css`)
   - Single block structure
   - Define all tokens
   - Style all elements
   - Verify tokens accessible

4. **Stories** (`[component-name].stories.jsx`)
   - Import Twig + YAML
   - Define argTypes
   - Create Default + variant stories
   - Test data combinations

5. **README** (`[component-name].README.md`)
   - Document all above
   - Props table
   - BEM structure
   - Tokens list
   - Accessibility notes
   - Usage examples

### 7.2 Iterative Testing

**After EACH file, run:**

```bash
# Test build
npm run build

# Visual check (if Twig/CSS/Stories created)
npm run watch
```

**Fix issues immediately before proceeding to next file.**

### 7.3 Final Validation

**Before declaring "DONE":**

```bash
# Full build validation
npm run build

# Visual inspection
npm run watch
# ✅ Check Default story
# ✅ Check all variant stories
# ✅ Test hover states
# ✅ Test keyboard navigation (Tab through all interactive)
# ✅ Verify focus indicators visible
# ✅ Resize to mobile (test responsive)
# ✅ DevTools: Inspect tokens (all defined, none unset)
```

**Conformity score: 100% required**

---

## 📋 PHASE 8: DELIVERABLES

### 8.1 Files Created/Modified

**List all files:**

- [ ] `source/patterns/components/[component-name]/[component-name].twig`
- [ ] `source/patterns/components/[component-name]/[component-name].css`
- [ ] `source/patterns/components/[component-name]/[component-name].yml`
- [ ] `source/patterns/components/[component-name]/[component-name].stories.jsx`
- [ ] `source/patterns/components/[component-name]/README.md`
- [ ] `source/patterns/components/_index.css` (import added)
- [ ] `docs/ps-design/CHANGELOG.md` (entry added)

### 8.2 Commit Message

**Generate structured commit:**

```
[feat|fix|refactor](components): [Action] [component-name] [brief description]

- [List key features/changes]
- [Implementation details]
- [Standards compliance notes]
- References spec: docs/design/[level]/[component-name].md
- [Closes #issue-number if applicable]
```

### 8.3 Summary Report

**Provide concise summary:**

```markdown
## Implementation Summary: [Component Name]

### ✅ Completed
- 5-file structure implemented
- Card inheritance pattern applied (blocks: media, body, footer)
- Atomic components reused: Image, Button
- All tokens defined in single CSS block
- WCAG 2.2 AA compliant
- Build passing, Storybook rendering correctly

### 📊 Conformity Score
**100%** (all standards met)

### 🔧 Card Configuration
- Variant: [elevated]
- Radius: [md]
- Blocks used: media (image + overlay), body (header + title + location), footer (CTA)

### 🧩 Atoms Reused
- Image (custom class: ps-[component]__image)
- Button (custom class: ps-[component]__favorite)

### 📝 Key Features
- [List 3-5 key features]

### 🐛 Issues Fixed
- [List any issues corrected during implementation]

### ⏭️ Next Steps
- [Any follow-up tasks, if applicable]
```

---

## 🎯 EXECUTION INSTRUCTIONS FOR AI AGENT

**When receiving this prompt:**

1. **READ FIRST** (30 minutes minimum):
   - All instruction files listed in Phase 1
   - Card component source files
   - Reference example (offer-card-list)
   - Design specification for target component

2. **ANALYZE** (Phase 2-3):
   - Complete architecture analysis
   - Document all findings
   - Create implementation plan
   - **DO NOT START CODING YET**

3. **VALIDATE PLAN** (checkpoint):
   - Review plan with human if unsure
   - Confirm Card block mapping makes sense
   - Verify all dependencies available

4. **IMPLEMENT** (Phase 7):
   - Follow file creation order
   - Test after each file
   - Fix issues immediately
   - Use parallel tool calls when safe (reads)
   - Use sequential for file writes

5. **AUDIT** (Phase 5-6):
   - Run full conformity check
   - Document all issues found
   - Fix all CRITICAL/HIGH issues
   - Report MEDIUM/LOW for review

6. **DELIVER** (Phase 8):
   - Generate commit message
   - Update changelog
   - Provide summary report

**CRITICAL RULES:**

- ❌ NEVER guess - read documentation first
- ❌ NEVER use hardcoded values - always use tokens
- ❌ NEVER write raw HTML for atoms - use includes
- ❌ NEVER define tokens outside main selector - scope issues
- ✅ ALWAYS test after each file
- ✅ ALWAYS fix issues before proceeding
- ✅ ALWAYS provide exact code for corrections
- ✅ ALWAYS verify build passes before declaring done

---

## 📞 SUPPORT & ESCALATION

**If stuck or unsure:**

1. Re-read relevant instruction files
2. Study reference example (offer-card-list)
3. Check Card component documentation
4. Ask human for clarification (provide context)

**Never proceed with uncertain implementation - ask first!**

---

**Version:** 1.0.0  
**Maintainers:** Design System Team  
**Last Updated:** 2025-12-10
