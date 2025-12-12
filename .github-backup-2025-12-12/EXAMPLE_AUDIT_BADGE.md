# 📋 Example: Audit Badge Component

## 🎯 Copier-coller ce texte pour auditer Badge

```
Audit the atom component: badge located in source/patterns/elements/badge/

Verify STRICT compliance with ALL rules below. Return FAIL immediately if ANY rule is violated.
Score 100/100 points only if ALL checks pass (no partial credit).

=== FILE STRUCTURE (MANDATORY - 0 TOLERANCE) ===
☐ 5 files exist: badge.twig, badge.css, badge.yml, badge.stories.jsx, README.md
☐ All files named exactly: badge.{ext} (lowercase, kebab-case)
☐ NO badge.js present (unless documented behavior required)

=== TWIG TEMPLATE ===

☐ Header comment present with:
  - Component purpose/description
  - @param entries for all props (type, required/optional, description)

☐ ALL default values use: {%- set prop = prop|default('value') -%}
  - NO unset variables without defaults
  - Format MUST be: varname|default('defaultvalue')

☐ Classes construction uses ternary + null:
  {%- set classes = [
    'ps-component',
    condition ? 'ps-component--modifier' : null,
    another ? 'ps-component--state' : null
  ] -%}
  - NEVER: filter(v => v), map(), includes(), .filter()
  - NEVER: if-else blocks for classes
  - MUST: ternary: condition ? 'class' : null

☐ Markup principle - MINIMAL:
  - Default values = NO modifier classes in output
  - Example: <button class="ps-button"> (not <button class="ps-button ps-button--md">)

☐ Composition uses ONLY attributes.addClass():
  {%- include '@elements/element/element.twig' with {
    prop: value,
    attributes: create_attribute().addClass('ps-parent__element')
  } only -%}
  - FORBIDDEN: baseClass parameter (REMOVED in v4.0.0)
  - REQUIRED: only keyword at end

☐ Real Estate context:
  - Property-related content (addresses, property types, prices, contact info)
  - NOT generic placeholder text
  - Example: "Luxury apartment in Marais" (NOT "Some text here")

=== CSS STYLES ===

☐ ALL values from tokens (ZERO hardcoded values):
  ✅ background: var(--primary); font-size: var(--font-size-1);
  ❌ background: #00915A; font-size: 16px; color: green;
  - Check EVERY value: colors, sizes, durations, shadows, borders, spacing

☐ Nesting syntax with & (postcss-nested required):
  .ps-component {
    color: var(--text-primary);
    
    &__element {
      padding: var(--size-2);
    }
    
    &--modifier {
      background: var(--primary);
    }
    
    &:hover {
      color: var(--primary-hover);
    }
  }
  - NEVER flat CSS without nesting

☐ Cascade order (CRITICAL):
  1. Block base
  2. Elements (__element)
  3. Modifiers (--variant)
  4. States (:hover, :focus-visible)

☐ Semantic colors ONLY:
  ✅ var(--primary), var(--secondary), var(--success), var(--warning), var(--danger), var(--info)
  ❌ var(--green-600), #00915A, green

☐ Focus-visible for ALL interactives:
  &:focus-visible {
    outline: var(--border-focus);
    outline-offset: 2px;
  }

☐ Component-scoped variables (Layer 2 system):
  --ps-badge-size: var(--size-4);
  --ps-badge-color: var(--primary);

☐ Modifier independence:
  - Each modifier works ALONE on base class

=== STORYBOOK STORIES ===

☐ Import syntax EXACT:
  import badgeTwig from './badge.twig';

☐ Export default with tags MANDATORY:
  export default {
    title: 'Elements/Badge',
    render: (args) => badgeTwig(args),
    tags: ['autodocs'],
    parameters: { ... },
    argTypes: { ... }
  }

☐ NO React/JSX:
  ❌ <Badge text="Value" />
  ✅ render: (args) => badgeTwig(args)

☐ ArgTypes structured by CATEGORY:
  Content | Appearance | Behavior | Link | Accessibility | Layout
  - text: category: 'Content'
  - color: category: 'Appearance'
  - size: category: 'Appearance'
  - pill: category: 'Appearance'
  - icon: category: 'Content'
  - url: category: 'Link'

☐ Description ≤ 2 lines in parameters.docs.description.component

☐ Stories structure - ONLY 2 types:
  1. Default: Shows standard usage
  2. Showcase stories: AllColors, AllSizes, AllIcons, UseCases, Combinations
  - FORBIDDEN: Individual variant stories (Primary, Secondary, Small, etc.)

=== YAML DATA ===

☐ File format valid YAML

☐ Real Estate context:
  - Property-realistic values
  - Examples: "Luxury property", "New listing", "Featured", "Verified seller"

☐ ALL required props defined with meaningful values

=== README.md ===

☐ Section: **# Badge**

☐ Section: **## Props**
  | Prop | Type | Default | Description |
  | text | string | '' | Badge text content |
  | color | string | default | Semantic color |
  | size | string | medium | Badge size (small|medium|large) |
  | pill | boolean | false | Fully rounded pill shape |
  | icon | string | null | Optional icon name |
  | url | string | null | Optional link URL |
  | attributes | object | null | Additional HTML attributes |

☐ Section: **## BEM Structure**
  ```
  .ps-badge
    .ps-badge__icon
    .ps-badge__text
    .ps-badge--primary (color modifier)
    .ps-badge--secondary
    .ps-badge--success
    .ps-badge--warning
    .ps-badge--danger
    .ps-badge--info
    .ps-badge--gold
    .ps-badge--small
    .ps-badge--large
    .ps-badge--pill
  ```

☐ Section: **## Usage**
  Example:
  ```twig
  {% include '@elements/badge/badge.twig' with {
    text: 'Luxury Property',
    color: 'success',
    icon: 'star',
    pill: false
  } only %}
  ```

☐ Section: **## Design Tokens**
  - var(--primary), var(--secondary), var(--success), var(--warning), var(--danger), var(--info), var(--gold)
  - var(--size-2), var(--size-3), var(--font-size-0)
  - var(--text-primary), var(--border-default)

☐ Section: **## Accessibility**
  - [ ] Contrast: 4.5:1 text, 3:1 UI components
  - [ ] Focus-visible indicator (if clickable/link)
  - [ ] ARIA: aria-label for icon-only badges
  - [ ] Keyboard: Tab to focus, Enter/Space to activate (if link)

☐ Section: **## Examples**
  Real Estate use cases:
  - Featured property indicator
  - Verified seller badge
  - Property type label
  - New listing indicator

=== BEM NAMING ===

☐ Prefix **ps-** mandatory on ALL classes:
  ✅ .ps-badge, .ps-badge__icon, .ps-badge__text, .ps-badge--primary
  ❌ .badge, .badge-icon, .badge_primary

☐ Format: .ps-badge__{element}--{modifier}

☐ NO nested elements:
  ✅ .ps-badge__icon
  ❌ .ps-badge__container__icon

☐ Modifiers logical + independent:
  ✅ .ps-badge--primary, .ps-badge--large, .ps-badge--pill
  ❌ .ps-badge--primary-large

=== ACCESSIBILITY (WCAG 2.2 AA) ===

☐ Contrast ratios:
  - Badge text on background: minimum 4.5:1
  - Badge with icon: minimum 3:1 for icon

☐ Focus-visible for clickable badges (with url):
  a.ps-badge:focus-visible { outline: ... }

☐ ARIA attributes:
  - Icon-only badges: aria-label="..."
  - Decorative icons: aria-hidden="true"

☐ Semantic HTML:
  - <span> for non-clickable badges
  - <a> for clickable badges (url provided)

=== FINAL VALIDATION ===

☐ npm run build: PASS
☐ Storybook: http://localhost:6006 → Elements/Badge renders correctly
☐ Visual: All color variants visible and readable
☐ Responsive: Badge readable on mobile/tablet/desktop

=== SCORING ===

**90-100 points**: ✅ PRODUCTION READY
**75-89 points**: ⚠️  MINOR FIXES REQUIRED
**Below 75 points**: ❌ MAJOR REFACTORING NEEDED

=== AUDIT REPORT ===

## Audit Report: Badge

**Score**: {TOTAL}/100

### ✅ Passed Checks ({COUNT})
- [List all PASSED checks]

### ❌ Failed Checks ({COUNT})
- **[Failed check name]** (Category): Description
  - File: source/patterns/elements/badge/badge.{ext}
  - Required action: What needs to be fixed

### 🔧 Recommended Fixes (Priority)
1. [CRITICAL] Fix violation
2. [MAJOR] Fix violation
3. [MINOR] Fix violation

### 📝 Notes
- Overall assessment
- Critical issues blocking production
```

---

## 📝 Copier ce prompt + remplacer `badge` par ton composant

Si tu veux auditer **Button** par exemple :

```
Audit the atom component: button located in source/patterns/elements/button/

[Même prompt ci-dessus, remplacer "badge" par "button"]
```

Si tu veux auditer **Avatar** (molecule) :

```
Audit the molecule component: avatar located in source/patterns/components/avatar/

[Même prompt ci-dessus, remplacer "badge" par "avatar"]
```

---

## 🎯 Workflow concret

### 1️⃣ Après avoir créé/modifié Badge

```bash
# Ouvrir le prompt et copier le texte
# Remplacer "badge" partout
# Coller dans Copilot/Claude/ChatGPT
```

### 2️⃣ Recopier le prompt avec Badge spécifique

```
Audit the atom component: badge located in source/patterns/elements/badge/

[PASTE PROMPT ABOVE]
```

### 3️⃣ AI retourne un rapport comme :

```
## Audit Report: Badge

**Score**: 95/100 ✅ PRODUCTION READY

### ✅ Passed Checks (76)
- File structure: ✓ (5 files present)
- Twig header comment: ✓
- Default values: ✓
- Classes with ternary: ✓
- No arrow functions: ✓
- attributes.addClass() for icon: ✓
- CSS tokens: ✓ (all vars)
- Nesting with &: ✓
- Semantic colors: ✓ (--primary, --success, etc)
- Focus-visible: ✓ (on links)
- Storybook autodocs: ✓
- NO React/JSX: ✓
- BEM naming: ✓
- README sections: ✓
- Accessibility: ✓ (WCAG AA)
- Build passes: ✓

### ❌ Failed Checks (1)
- **Component-scoped variables**: Missing --ps-badge-size in CSS
  - File: source/patterns/elements/badge/badge.css
  - Required action: Add --ps-badge-size: var(--size-3) at top of .ps-badge block

### 🔧 Recommended Fixes (Priority)
1. [MINOR] Add component-scoped variables for size modifiers

### 📝 Notes
Component is nearly production-ready. Add CSS variables for consistency
with Layer 2 system. All other checks pass perfectly.
```

### 4️⃣ Tu fixes et re-valides

```bash
npm run build  # Vérifier build
# Coller le prompt encore une fois
# Vérifier le score = 100/100
```

---

## 💡 Tips d'utilisation

✅ **Avant chaque commit** : Lance l'audit  
✅ **Pendant code review** : Partage le rapport  
✅ **Nouveau team member** : Montre l'exemple Badge  
✅ **Refactoring** : Vérifie avant/après conformité  

---

## 🚀 Raccourci : Version simplifiée

Si tu veux juste une **vérification rapide** (pas complète) :

```
Quick conformity check for badge component:
- Does it have 5 files? (twig, css, yml, stories, README)
- Does badge.twig use attributes.addClass() for icon (NOT baseClass)?
- Does badge.css use ONLY tokens (NO #00915A, 16px)?
- Does badge.stories.jsx have tags: ['autodocs']?
- Does badge.css have &--primary, &--success, etc modifiers?
- Does README have Props, BEM, Usage, Tokens, Accessibility sections?
- Does npm run build pass?

Report findings for each.
```

Beaucoup plus rapide pour des check-ins réguliers ! 🏃
