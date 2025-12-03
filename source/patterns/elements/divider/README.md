# Divider Component

A flexible visual separator to structure content sections with component-scoped CSS variables. Supports horizontal and vertical orientations with customizable styles, colors, and optional centered content.

## Component Props

| Prop | Type | Options | Default | Description |
|------|------|---------|---------|-------------|
| `orientation` | string | `'horizontal'`, `'vertical'` | `'horizontal'` | Orientation of the divider |
| `style` | string | `'solid'`, `'dashed'`, `'dotted'` | `'solid'` | Line style |
| `thickness` | string | `'thin'`, `'medium'`, `'thick'` | `'medium'` | Line thickness (1px/2px/4px) |
| `color` | string | `'neutral'`, `'primary'`, `'secondary'`, `'success'`, `'warning'`, `'danger'`, `'info'` | `'neutral'` | Semantic color |
| `spacing` | string | `'sm'`, `'md'`, `'lg'` | `'md'` | Spacing around divider (8px/16px/24px) |
| `text` | string | any | `''` | Optional centered text content |
| `icon` | string | icon name | `''` | Optional centered icon (without "icon-" prefix) |
| `baseClass` | string | `'ps-divider'` | — | Override root class when composing inside other components (e.g., `'ps-section__divider'`). Modifiers and elements map to `baseClass--*` and `baseClass__*`.

## BEM Structure

```
ps-divider                       # Root element (<hr> or <div>/<span>)
├── ps-divider--vertical         # Vertical orientation
├── ps-divider--dashed           # Dashed line style
├── ps-divider--dotted           # Dotted line style
├── ps-divider--thin             # Thin thickness (1px)
├── ps-divider--thick            # Thick thickness (4px)
├── ps-divider--primary          # Primary color
├── ps-divider--secondary        # Secondary color
├── ps-divider--success          # Success color
├── ps-divider--warning          # Warning color
├── ps-divider--danger           # Danger color
├── ps-divider--info             # Info color
├── ps-divider--spacing-sm       # Small spacing (8px)
├── ps-divider--spacing-lg       # Large spacing (24px)
├── ps-divider--with-text        # Contains text content
├── ps-divider--with-icon        # Contains icon content
│
├── ps-divider__line             # Line segments (when content present)
├── ps-divider__text             # Centered text element
└── ps-divider__icon             # Centered icon element
```

When `baseClass` is provided, the same structure applies with `baseClass` instead of `ps-divider` (e.g., `ps-card__divider--dashed`, `ps-card__divider__line`).

### Modifiers by Category

**Orientation**
- Default (horizontal) - No modifier class
- `ps-divider--vertical` - Vertical divider for inline layouts

**Style**
- Default (solid) - No modifier class
- `ps-divider--dashed` - Dashed line
- `ps-divider--dotted` - Dotted line

**Thickness**
- `ps-divider--thin` - 1px line
- Default (medium) - 2px line, no modifier class
- `ps-divider--thick` - 4px line

**Color**
- Default (neutral) - No modifier class
- `ps-divider--primary` - Brand primary (green #00915A)
- `ps-divider--secondary` - Brand secondary (purple #E0388C)
- `ps-divider--success` - Success green
- `ps-divider--warning` - Warning yellow
- `ps-divider--danger` - Danger red
- `ps-divider--info` - Info blue

**Spacing**
- `ps-divider--spacing-sm` - 8px spacing
- Default (md) - 16px spacing, no modifier class
- `ps-divider--spacing-lg` - 24px spacing

**Content**
- `ps-divider--with-text` - Applied when text prop is provided
- `ps-divider--with-icon` - Applied when icon prop is provided

## CSS Variables System (3-Layer Architecture)

### Layer 1: Root Primitives (Referenced by Component)

**Sizing & Spacing**
- `--border-size-1` (1px) - Thin thickness
- `--border-size-2` (2px) - Medium thickness (default)
- `--border-size-4` (4px) - Thick thickness
- `--size-2` (8px) - Small spacing
- `--size-3` (12px) - Content gap
- `--size-4` (16px) - Medium spacing (default)
- `--size-6` (24px) - Large spacing

**Colors (Semantic)**
- `--border-default` - Default neutral divider color
- `--primary` - Brand primary (#00915A)
- `--secondary` - Brand secondary (#A12B66)
- `--success` - Success green
- `--warning` - Warning yellow
- `--danger` - Danger red
- `--info` - Info blue
- `--text-secondary` - Text/icon color for centered content

**Typography (for centered content)**
- `--font-sans` - Font family
- `--font-size-0` (14px) - Text size
- `--font-size-1` (16px) - Icon size
- `--font-weight-500` - Text weight (medium)

### Layer 2: Component-Scoped Variables (Customizable)

```css
.ps-divider {
  /* Sizing & Spacing */
  --ps-divider-thickness: var(--border-size-2);
  --ps-divider-spacing-y: var(--size-4);
  --ps-divider-spacing-x: var(--size-4);
  --ps-divider-content-gap: var(--size-3);
  
  /* Colors */
  --ps-divider-color: var(--border-default);
  --ps-divider-text-color: var(--text-secondary);
  --ps-divider-icon-color: var(--text-secondary);
  
  /* Typography (for centered content) */
  --ps-divider-text-font-family: var(--font-sans);
  --ps-divider-text-font-size: var(--font-size-0);
  --ps-divider-text-font-weight: var(--font-weight-500);
  --ps-divider-icon-size: var(--font-size-1);
  
  /* Line styles */
  --ps-divider-style: solid;
}
```

### Layer 3: Context Overrides (Example)

Override component variables for specific contexts:

```css
/* Compact layout variant */
.sidebar .ps-divider {
  --ps-divider-spacing-y: var(--size-2);
  --ps-divider-thickness: var(--border-size-1);
}

/* Custom brand color */
.premium-section .ps-divider {
  --ps-divider-color: var(--secondary);
  --ps-divider-thickness: var(--border-size-4);
}

/* Dark theme override */
[data-theme="dark"] .ps-divider {
  --ps-divider-color: hsla(0, 0%, 100%, 0.2);
  --ps-divider-text-color: hsla(0, 0%, 100%, 0.7);
}
```

## Design Tokens Used (Legacy Reference)

> **Note**: This section documents the primitive tokens referenced by component variables. For customization, use component-scoped variables (`--ps-divider-*`) instead of overriding primitives directly.

### Colors
- `--border-default` - Default neutral color
- `--primary` - Primary brand color
- `--secondary` - Secondary brand color
- `--success` - Success state
- `--warning` - Warning state
- `--danger` - Danger state
- `--info` - Info state
- `--text-secondary` - Text/icon color

### Spacing
- `--size-2` - Small spacing (8px)
- `--size-3` - Content gap (12px)
- `--size-4` - Medium spacing (16px, default)
- `--size-6` - Large spacing (24px)

### Thickness
- `--border-size-1` - Thin (1px)
- `--border-size-2` - Medium (2px, default)
- `--border-size-4` - Thick (4px)

### Typography (for centered content)
- `--font-sans` - Font family (BNPP Sans)
- `--font-size-0` - Text size (14px)
- `--font-weight-500` - Text weight (medium)
- `--font-size-1` - Icon size (16px)

## Usage Examples

### Basic Horizontal Divider
```twig
{% include '@elements/divider/divider.twig' %}
```

### Styled Divider
```twig
{% include '@elements/divider/divider.twig' with {
  style: 'dashed',
  thickness: 'thin',
  color: 'neutral',
  spacing: 'sm'
} %}
```

### Divider with Color Accent
```twig
{% include '@elements/divider/divider.twig' with {
  color: 'primary',
  thickness: 'thick',
  spacing: 'lg'
} %}
```

### Divider with Text
```twig
{% include '@elements/divider/divider.twig' with {
  text: 'or',
  spacing: 'md'
} %}
```

### Divider with Text and Color
```twig
{% include '@elements/divider/divider.twig' with {
  text: 'Section Title',
  color: 'primary',
  style: 'dashed'
} %}
```

### Divider with Icon
```twig
{% include '@elements/divider/divider.twig' with {
  icon: 'star',
  color: 'secondary'
} %}
```

### Vertical Divider in Flex Container
```html
<div style="display: flex; align-items: center; height: 60px;">
  <span>Option 1</span>
  {% include '@elements/divider/divider.twig' with {
    orientation: 'vertical',
    thickness: 'thin',
    spacing: 'sm'
  } %}
  <span>Option 2</span>
</div>
```

### Multiple Vertical Dividers (Toolbar)
```html
<div style="display: flex; align-items: center; gap: 0.5rem;">
  <button>Edit</button>
  {% include '@elements/divider/divider.twig' with {
    orientation: 'vertical',
    thickness: 'thin',
    spacing: 'sm'
  } %}
  <button>Delete</button>
  {% include '@elements/divider/divider.twig' with {
    orientation: 'vertical',
    thickness: 'thin',
    spacing: 'sm'
  } %}
  <button>Share</button>
</div>
```

## Common Use Cases

### 1. Content Section Separator
```twig
<section>
  <h2>Section 1</h2>
  <p>Content for first section...</p>
</section>

{% include '@elements/divider/divider.twig' with {
  spacing: 'lg',
  thickness: 'thin'
} %}

<section>
  <h2>Section 2</h2>
  <p>Content for second section...</p>
</section>
```

### 2. Form with Alternative Login Methods
```twig
<form>
  <input type="email" placeholder="Email" />
  <input type="password" placeholder="Password" />
  <button type="submit">Sign In</button>
  
  {% include '@elements/divider/divider.twig' with {
    text: 'or',
    spacing: 'md'
  } %}
  
  <button type="button">Continue with Google</button>
  <button type="button">Continue with GitHub</button>
</form>
```

### 3. Emphasized Section Divider
```twig
<div>
  <h2>Important Section</h2>
  <p>Critical information that needs attention.</p>
</div>

{% include '@elements/divider/divider.twig' with {
  color: 'primary',
  thickness: 'thick',
  spacing: 'md'
} %}

<div>
  <h2>Regular Section</h2>
  <p>Standard content follows.</p>
</div>
```

### 4. Dashed Decorative Separator
```twig
<article>
  <p>First paragraph of content...</p>
  
  {% include '@elements/divider/divider.twig' with {
    style: 'dashed',
    color: 'neutral',
    spacing: 'md'
  } %}
  
  <p>Second paragraph continues...</p>
</article>
```

## Accessibility

- **Semantic HTML**: Uses native `<hr>` element for horizontal dividers (proper sectioning role)
- **ARIA for Vertical**: Vertical dividers use `role="separator"` with `aria-orientation="vertical"`
- **Non-interactive**: Dividers are purely decorative and never receive keyboard focus
- **Color Contrast**: All color tokens ensure minimum 3:1 contrast ratio against backgrounds
- **Icon Treatment**: Icons in centered content are marked `aria-hidden="true"` (decorative only)
- **Screen Readers**: Horizontal `<hr>` elements properly announce content breaks

### Best Practices
- Don't rely on color alone to convey meaning (use headings/labels)
- Use dividers to enhance visual hierarchy, not replace semantic structure
- Ensure sufficient contrast when using colored dividers on tinted backgrounds

## Responsive Behavior

### Horizontal Dividers
- **Width**: 100% (block-level element)
- **Adapts**: Automatically fits container width
- **Spacing**: Vertical margin controlled by `spacing` prop

### Vertical Dividers
- **Height**: Inherits from parent container (requires explicit container height)
- **Display**: `inline-block` or flex item
- **Spacing**: Horizontal margin controlled by `spacing` prop
- **Alignment**: Aligns with flex/grid parent

### Content Centering
- **Text**: Uses `white-space: nowrap` to prevent wrapping
- **Icon**: Fixed size, centered via flexbox
- **Gap**: 12px spacing between content and line segments

## Best Practices

### DO ✅
- Use horizontal dividers to separate major content sections
- Use vertical dividers in toolbars, button groups, or inline navigation
- Use text dividers in forms to indicate alternatives (e.g., "or")
- Use colored dividers to emphasize section importance
- Ensure parent containers of vertical dividers have defined height
- Use design tokens exclusively for all styling

### DON'T ❌
- Don't overuse dividers—they can fragment content unnecessarily
- Don't hardcode colors, spacing, or thickness values
- Don't use vertical dividers without container height (they will collapse)
- Don't use dividers to replace proper semantic HTML (headings, lists, sections)
- Don't combine text and icon in the same divider—choose one or neither
- Don't use dividers as interactive elements (they are purely visual)

## Component Audit Checklist

- [x] Uses semantic `<hr>` for horizontal orientation
- [x] Uses `role="separator"` with `aria-orientation="vertical"` for vertical
- [x] **CSS Variables System**: 3-layer architecture (primitives → component → context)
- [x] **All colors use semantic tokens** (`--primary`, `--success`, `--danger`, `--info`, `--warning`, `--secondary`, `--border-default`)
- [x] **All spacing uses design tokens** (`--size-*`, `--border-size-*`)
- [x] **Typography uses design tokens** (`--font-sans`, `--font-size-*`, `--font-weight-500`)
- [x] **BEM methodology with `ps-` prefix** (`ps-divider`, `ps-divider__line`, `ps-divider__text`, `ps-divider__icon`)
- [x] **CSS uses PostCSS nesting syntax** (modern `&` syntax throughout)
- [x] **Minimal markup**: Modifiers only added when value differs from default (strict conditionals)
- [x] **Cascade order correct**: Base → Modifiers (no combined class selectors)
- [x] **Supports 7 semantic colors** (neutral, primary, secondary, success, warning, danger, info)
- [x] **Supports 3 line styles** (solid, dashed, dotted)
- [x] **Supports 3 thickness levels** (thin, medium, thick)
- [x] **Supports 3 spacing levels** (sm, md, lg)
- [x] **Optional centered text/icon content**
- [x] **Icons use `data-icon` attribute** pattern
- [x] **Non-interactive** (no focus states)
- [x] **Accessible color contrast**
- [x] **Storybook documentation complete** (`tags: ['autodocs']`, showcases: AllVariants, Vertical, UseCases)
- [x] **No hardcoded values** in CSS (all values use component variables)
- [x] **All content in English**

---

Component Status: ✅ Complete  
Last Updated: December 3, 2025
