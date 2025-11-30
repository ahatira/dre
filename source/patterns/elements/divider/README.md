# Divider Component

A flexible visual separator to structure content sections. Supports horizontal and vertical orientations with customizable styles, colors, and optional centered content.

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

## Design Tokens Used

### Colors
- `--ps-color-neutral-300` (fallback `--gray-300`) - Default neutral color
- `--brand-primary` - Primary brand color (green)
- `--brand-secondary` - Secondary brand color (purple)
- `--btn-success` (fallback `--green-600`) - Success state
- `--btn-warning` (fallback `--yellow-500`) - Warning state
- `--btn-danger` (fallback `--red-600`) - Danger state
- `--btn-info` (fallback `--blue-600`) - Info state
- `--ps-color-neutral-600` (fallback `--gray-600`) - Text color
- `--ps-color-neutral-500` (fallback `--gray-500`) - Icon color
- `--ps-color-neutral-50` (fallback `--gray-50`) - Background for content

### Spacing
- `--size-2` (8px) - Small spacing
- `--size-4` (16px) - Medium spacing (default)
- `--size-6` (24px) - Large spacing
- `--size-3` (12px) - Gap around centered content

### Typography (for centered content)
- `--font-sans` - Font family (BNPP Sans)
- `--font-size-0` (14px) - Text size
- `--font-weight-500` - Text weight (medium)
- `--font-size-1` (16px) - Icon size

### Thickness
- `1px` - Thin
- `2px` - Medium (default)
- `4px` - Thick

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
- [x] All colors use design tokens (no hardcoded values)
- [x] All spacing uses design tokens (`--size-*`)
- [x] Typography uses design tokens (`--font-*`)
- [x] BEM methodology with `ps-` prefix
- [x] CSS uses PostCSS nesting syntax
- [x] Minimal markup (default props require no modifier classes)
- [x] Supports 7 semantic colors (neutral, primary, secondary, success, warning, danger, info)
- [x] Supports 3 line styles (solid, dashed, dotted)
- [x] Supports 3 thickness levels (thin, medium, thick)
- [x] Supports 3 spacing levels (sm, md, lg)
- [x] Optional centered text/icon content
- [x] Icons use `data-icon` attribute pattern
- [x] Non-interactive (no focus states)
- [x] Accessible color contrast
- [x] Storybook documentation complete with structured sections
- [x] All content in English
