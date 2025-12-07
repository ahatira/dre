# Search Bar (Molecule)

Search input component with icon and optional suggestions dropdown. Accessible and keyboard-navigable.

## Usage

```twig
{% include '@components/search-bar/search-bar.twig' with {
  placeholder: 'Search properties...',
  show_icon: true,
} only %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `placeholder` | string | 'Search...' | Input placeholder text |
| `search_text` | string | '' | Current search value |
| `size` | string | 'md' | Size variant (xs, sm, md, lg, xl, xxl) |
| `show_icon` | boolean | true | Display search icon |
| `has_suggestions` | boolean | false | Show suggestions dropdown |
| `suggestions` | array | [] | Array of suggestion strings |
| `attributes` | Drupal Attribute | {} | Additional HTML attributes |

## BEM Structure

```
ps-search-bar
├── ps-search-bar__form
├── ps-search-bar__input-wrapper
│   ├── ps-search-bar__icon (SVG sprite)
│   └── ps-search-bar__input
└── ps-search-bar__suggestions
    └── ps-search-bar__suggestion
        └── ps-search-bar__suggestion-link

Modifiers:
  ps-search-bar--xs                # Extra small size
  ps-search-bar--sm                # Small size
  ps-search-bar--md                # Medium size (default)
  ps-search-bar--lg                # Large size
  ps-search-bar--xl                # Extra large size
  ps-search-bar--xxl               # Extra extra large size
```

## CSS Variables (Layer 2)

Component-scoped variables that can be overridden:

```css
/* Layout */
--ps-search-bar-input-height: var(--size-10);        /* 40px */
--ps-search-bar-input-padding-y: var(--size-2);      /* 8px */
--ps-search-bar-input-padding-x: var(--size-3);      /* 12px */
--ps-search-bar-icon-size: var(--size-5);            /* 20px */
--ps-search-bar-icon-spacing: var(--size-2);         /* 8px */

/* Colors */
--ps-search-bar-bg: var(--white);
--ps-search-bar-border-color: var(--gray-300);
--ps-search-bar-border-color-hover: var(--gray-400);
--ps-search-bar-border-color-focus: var(--primary);
--ps-search-bar-text-color: var(--text-primary);
--ps-search-bar-placeholder-color: var(--gray-500);
--ps-search-bar-icon-color: var(--gray-500);

/* Borders */
--ps-search-bar-border-width: var(--border-size-1);
--ps-search-bar-border-radius: var(--radius-3);
--ps-search-bar-focus-ring-width: var(--border-size-2);

/* Suggestions dropdown */
--ps-search-bar-suggestions-bg: var(--white);
--ps-search-bar-suggestions-border: var(--gray-300);
--ps-search-bar-suggestions-shadow: var(--shadow-3);
--ps-search-bar-suggestion-hover-bg: var(--gray-100);
--ps-search-bar-suggestions-z-index: var(--layer-40);

/* Transitions */
--ps-search-bar-transition-duration: var(--duration-fast);
--ps-search-bar-transition-timing: var(--ease-3);
```

## Icon System

Search icon is rendered using the SVG sprite system (`icon-search`).

```twig
<svg class="ps-search-bar__icon" aria-hidden="true" focusable="false">
  <use xlink:href="#icon-search" />
</svg>
```

## States

- **Default**: Gray border, white background
- **Hover**: Darker gray border
- **Focus**: Primary color border with subtle shadow ring
- **With suggestions**: Dropdown appears below input

## Variants

### Size Variants

The `size` prop adjusts the search bar height and input dimensions:

- **xs**: Extra small (height: 28px)
- **sm**: Small (height: 32px)
- **md** (default): Medium (height: 40px)
- **lg**: Large (height: 48px)
- **xl**: Extra large (height: 56px)
- **xxl**: Extra extra large (height: 64px)

Size variants harmonize with Input component heights for consistent form layouts.

## Accessibility

- **ARIA**: `role="search"` on form, `role="listbox"` on suggestions, `role="option"` on items
- **Keyboard**: Tab to focus, type to search, Arrow keys to navigate suggestions (if implemented)
- **Focus-visible**: 2px primary outline on input and suggestion links
- **Contrast**: All text meets WCAG 2.2 AA (4.5:1 minimum)
- **Screen readers**: `aria-label="Search"` on input, icons have `aria-hidden="true"`

## Examples

### Basic Search

```twig
{% include '@components/search-bar/search-bar.twig' with {
  placeholder: 'Search for properties...',
} only %}
```

### With Pre-filled Value

```twig
{% include '@components/search-bar/search-bar.twig' with {
  placeholder: 'Search...',
  search_text: 'Paris apartments',
} only %}
```

### Without Icon

```twig
{% include '@components/search-bar/search-bar.twig' with {
  placeholder: 'Type to search...',
  show_icon: false,
} only %}
```

### With Suggestions (Static)

```twig
{% include '@components/search-bar/search-bar.twig' with {
  placeholder: 'Search cities...',
  search_text: 'Par',
  has_suggestions: true,
  suggestions: [
    'Paris',
    'Parma',
    'Paramaribo',
  ],
} only %}
```

## Real Estate Context

Commonly used for:
- Property search in header
- Location search in filters
- Quick search in admin interfaces
- Market/city search in location selectors

## Design Tokens Used

| Token | Value | Usage |
|-------|-------|-------|
| `--size-10` | 40px | Input height |
| `--size-5` | 20px | Icon size |
| `--gray-300` | #D1D5DB | Default border |
| `--primary` | #00915A | Focus border (BNP green) |
| `--shadow-3` | 0 4px 6px rgba(...) | Suggestions dropdown |
| `--radius-3` | 8px | Border radius |
| `--duration-fast` | 150ms | Transition speed |

## Browser Compatibility

- ✅ Modern browsers (Chrome, Firefox, Safari, Edge)
- ✅ `type="search"` input with native clear button (most browsers)
- ✅ SVG sprite icons with fallback support

## Notes

- Maximum width: 400px (responsive)
- Icon is positioned absolutely within input wrapper
- Suggestions dropdown requires JavaScript for dynamic behavior
- Focus ring uses `color-mix()` for transparency (modern CSS)
- Search input has native clear button in most browsers (`type="search"`)

## Related Components

- **Input** (`@elements/input/input.twig`) - Base input atom
- **Dropdown** (`@components/dropdown/dropdown.twig`) - Custom select dropdown
- **Form-Element** (`@components/form-element/form-element.twig`) - Full form field with label

## Migration Notes

**v3.0.0 (2025-12-07)**:
- ✅ Migrated to SVG sprite for icon (was inline SVG)
- ✅ Corrected all tokens to project standards
- ✅ Added Layer 2 component-scoped variables
- ✅ Added header comment with full documentation
- ✅ Removed `@layer components` (not needed with modern PostCSS)
