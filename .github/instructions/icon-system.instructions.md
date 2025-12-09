---
applyTo: source/patterns/elements/icon/**, source/props/icons.css, scripts/build-icons.mjs
---

# Icon System Architecture

**Version**: 3.1.0  
**Last Updated**: 2025-12-09

---

## 🎯 Two-Layer Architecture: Component vs System

The PS Theme icon infrastructure is **intentionally split into two complementary layers**:

### Layer 1: Icon.twig Component (Twig Wrapper)
**Purpose**: Reusable Twig atom that renders an `<i>` element with icon semantics.

**Behavior**:
- Generates: `<i data-icon="name" class="ps-icon ..."></i>`
- Accepts parameters: `icon` (required), `size`, `color`, `ariaLabel`, `ariaHidden`, `attributes`
- Does NOT accept text content (the `<i>` is purely for icon rendering)
- Does NOT support `data-icon-position` (positioning is parent's responsibility)

**Usage** (in Twig templates/stories):
```twig
{# Simple icon #}
{% include '@elements/icon/icon.twig' with { icon: 'check' } %}

{# With size #}
{% include '@elements/icon/icon.twig' with { icon: 'arrow-right', size: 'lg' } %}

{# Icon-only with aria-label #}
{% include '@elements/icon/icon.twig' with { 
  icon: 'close',
  ariaLabel: 'Close dialog'
} %}
```

**Output HTML**:
```html
<i data-icon="check" class="ps-icon" aria-hidden="true"></i>
<i data-icon="arrow-right" class="ps-icon ps-icon--lg" aria-hidden="true"></i>
<i data-icon="close" class="ps-icon" aria-label="Close dialog"></i>
```

**When to use Icon.twig**:
- ✅ Reusable icon logic (size/color/accessibility params)
- ✅ Stories/documentation where you need icon component demos
- ✅ Complex icon markup patterns requiring Twig templating
- ❌ NOT for inline icon rendering on buttons/links/etc (use `data-icon` directly instead)

---

### Layer 2: data-icon System (CSS Primitive)
**Purpose**: Low-level CSS attribute system for rendering icons on **ANY HTML element**.

**Behavior**:
- Applied as: `data-icon="name"` on any element (button, span, a, div, h1, p, etc.)
- Renders via pseudo-elements: `::before` (default, position="start") or `::after` (position="end")
- Supports positioning: `data-icon-position="start"` | `"end"`
- Inherits color from parent: Uses `currentColor` for dynamic theming

**Usage** (direct HTML attributes):
```html
<!-- Icon on button (default position: start) -->
<button data-icon="arrow-right">Next Step</button>

<!-- Icon on button with end position -->
<button data-icon="check" data-icon-position="end">Confirm</button>

<!-- Icon on span with text -->
<span data-icon="phone" data-icon-position="start">+33 1 23 45 67</span>

<!-- Icon on heading -->
<h2 data-icon="star" data-icon-position="start">Featured Properties</h2>

<!-- Icon on link -->
<a href="/search" data-icon="search">Find Properties</a>
```

**CSS Rendering** (`source/props/icons.css`):
```css
/* Base: [data-icon] container styling */
[data-icon] {
  display: inline-flex;
  align-items: center;
  gap: var(--ps-icon-gap, 0.375em);
}

/* Pseudo-element base styling */
[data-icon]::before,
[data-icon]::after {
  content: "";
  flex-shrink: 0;
  display: inline-block;
  width: 1em;
  height: 1em;
  mask-image: url("data:image/svg+xml,...");
  background-color: currentColor;
}

/* Default: show ::before (start position) */
[data-icon]::after { display: none; }

/* Position: end → show ::after instead */
[data-icon-position="end"]::before { display: none; }
[data-icon-position="end"]::after { display: inline-block; }

/* Per-icon mask-image definitions (auto-generated) */
[data-icon="check"]::before,
[data-icon="check"]::after { mask-image: url("data:image/svg+xml,...check-svg..."); }
/* ... 140 icons total ... */
```

**When to use data-icon**:
- ✅ Inline icons on native HTML elements (button, link, span)
- ✅ Icons with text content (automatic spacing via flexbox + gap)
- ✅ Color inheritance from parent (uses currentColor)
- ✅ Position control (start/end positioning via ::before/::after)
- ❌ NOT as a Twig component (use Icon.twig for that)

---

## 📊 Comparison Table

| Aspect | Icon.twig (Component) | data-icon (System) |
|--------|----------------------|-------------------|
| **Type** | Twig atom component | CSS attribute system |
| **Generates** | `<i data-icon="...">` | Pseudo-elements `::before`/`::after` |
| **Applied to** | Always `<i>` element | ANY HTML element |
| **Text content** | ❌ No | ✅ Yes (automatic spacing) |
| **Position control** | ❌ No (parent's job) | ✅ Yes (`data-icon-position`) |
| **Parameters** | ✅ icon, size, color, aria* | ✅ data-icon, data-icon-position |
| **Usage context** | Twig templates, stories | HTML templates, direct markup |
| **Example** | `{% include '@elements/icon/icon.twig' with {...} %}` | `<button data-icon="check">Action</button>` |

---

## 🏗️ Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         Icon System (Icons)                         │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  Layer 1: Twig Component (icon.twig)                                │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ Input: Parameters (icon, size, color, aria*)                │   │
│  │ Output: <i data-icon="name" class="ps-icon ..."></i>        │   │
│  │ Purpose: Reusable wrapper for icon logic                    │   │
│  └──────────────────────────────────────────────────────────────┘   │
│           ↓                                                           │
│  Layer 2: CSS Primitive (data-icon system)                          │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ Input: data-icon="name" attribute on ANY HTML element       │   │
│  │ Rendering: [data-icon]::before|::after pseudo-elements     │   │
│  │ Position: data-icon-position="start"|"end"                 │   │
│  │ Output: Rendered icon via mask-image + currentColor        │   │
│  │ Color inheritance: From parent element (currentColor)       │   │
│  └──────────────────────────────────────────────────────────────┘   │
│           ↑                                                           │
│  Direct CSS: Can skip Twig, apply data-icon directly               │
│                                                                       │
└─────────────────────────────────────────────────────────────────────┘

                            Data Flow

Twig Templates                          HTML Templates
       ↓                                      ↓
{% include icon.twig %}            <button data-icon="...">
       ↓                                      ↓
  <i data-icon="...">              data-icon system CSS
       ↓                                      ↓
Rendered by data-icon              Rendered by data-icon
     CSS system                          CSS system
       ↓                                      ↓
  Displayed Icon                       Displayed Icon
```

---

## 💡 Real-World Examples

### Example 1: Button with Trailing Icon (data-icon only)
```html
<!-- HTML: Icon on right (position="end") with text on left -->
<button class="ps-button" data-icon="arrow-right" data-icon-position="end">
  Proceed to next step
</button>

<!-- CSS: Automatically adds spacing via gap property -->
<!-- Icon rendered at END (::after) due to position="end" -->
<!-- Visual: [Text] [gap] [→] -->
```

### Example 2: Icon-Only Button (Icon.twig)
```twig
{# Twig: Semantic icon component #}
<button class="ps-button" aria-label="Close dialog">
  {% include '@elements/icon/icon.twig' with { icon: 'close' } %}
</button>

{# Output: #}
{# <button class="ps-button" aria-label="Close dialog">#}
{#   <i data-icon="close" class="ps-icon" aria-hidden="true"></i>#}
{# </button> #}
```

### Example 3: Badge with Leading Icon (data-icon direct)
```html
<!-- HTML: Icon on left (default position="start") with text -->
<span class="ps-badge ps-badge--success" data-icon="check">
  Approved
</span>

<!-- CSS: Automatically adds spacing via gap property -->
<!-- Icon rendered at START (::before) by default -->
<!-- Visual: [✓] [gap] [Approved] -->
```

### Example 4: Heading with Icon (data-icon direct)
```html
<!-- HTML: Icon can go on heading -->
<h2 class="ps-heading-2" data-icon="star" data-icon-position="start">
  Featured Listings
</h2>

<!-- CSS: Icon inherits color from h2 -->
<!-- Visual: [★] [gap] [Featured Listings] -->
```

### Example 5: Link with Icon (data-icon direct)
```html
<!-- HTML: Icon on link -->
<a href="/search" class="ps-link" data-icon="search" data-icon-position="end">
  Find properties
</a>

<!-- CSS: Icon inherits color from link (currentColor) -->
<!-- Responds to hover states automatically -->
<!-- Visual: [Find properties] [gap] [🔍] on hover: color changes -->
```

---

## 🔧 Build System Integration

**Icon generation workflow** (`scripts/build-icons.mjs`):

1. **Read SVG sources**: `source/icons-source/**/*.svg` (140 icons)
2. **Generate CSS rules**: `source/props/icons.css`
   - Base styling: `[data-icon]` container (flexbox + gap)
   - Pseudo-element base: `[data-icon]::before, [data-icon]::after` (width, height, mask-image)
   - Position rules: `[data-icon-position="end"]` swaps display
   - Per-icon masks: `[data-icon="name"]::before, [data-icon="name"]::after { mask-image: url(...) }`
3. **Generate registry**: `source/patterns/documentation/icons-registry.json`
   - Icon names, categories, metadata for Storybook discovery

**Build command**:
```bash
npm run build
# Validates icons, generates CSS, formats with Biome, bundles with Vite
```

---

## ✅ Checklist: When to Use Each

**Use Icon.twig component when**:
- [ ] Building a story or demo (Storybook)
- [ ] Need reusable Twig logic with parameters
- [ ] Want encapsulated icon markup
- [ ] Example: Icon atom story, documentation

**Use data-icon system when**:
- [ ] Applying icon to native HTML element (button, link, span)
- [ ] Icon + text content together
- [ ] Icon inherits color from parent
- [ ] Want simplest possible markup
- [ ] Example: Button with icon, badge with checkmark, link with arrow

**NEVER mix**:
- ❌ Don't use data-icon-position on Icon.twig output (Icon outputs `<i>`, position is CSS-only)
- ❌ Don't use Icon.twig to render multiple icons for different purposes (it's a single-icon component)
- ❌ Don't apply data-icon to the output of Icon.twig (already applied by component)

---

## 🔗 References

- **Icon CSS**: `source/props/icons.css` (auto-generated, 140 icons)
- **Icon Registry**: `source/patterns/documentation/icons-registry.json` (metadata)
- **Build Script**: `scripts/build-icons.mjs` (generation logic)
- **Component**: `source/patterns/elements/icon/icon.twig` (wrapper)
- **Stories**: `source/patterns/elements/icon/icon.stories.jsx` (demos)
- **README**: `source/patterns/elements/icon/README.md` (usage docs)

---

## 📝 Zero-Tolerance Rules

**These will ALWAYS be rejected**:

- ❌ Using Icon.twig with `{{ caller() }}` for text content → Icon renders `<i>` only, use data-icon instead
- ❌ Applying `data-icon-position` to Icon.twig output → Position is CSS-only, applies to data-icon system
- ❌ Arrow functions in Twig icon templates: `filter(v => v)` → Use ternary operators
- ❌ Hardcoded color values in Icon CSS → Use semantic tokens from `brand.css`
- ❌ Icon names with prefix: `icon-check` → Use `check` (system auto-prefixes)
- ❌ Missing 5-file structure for Icon component → `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
- ❌ Missing `tags: ['autodocs']` in Icon story export → Required for Storybook documentation

---

**Summary**: Two-layer architecture enables maximum flexibility:
- **Icon.twig**: For templating & reuse (Twig-level)
- **data-icon**: For styling & primitive rendering (CSS-level)

Both are complementary, not competing.
