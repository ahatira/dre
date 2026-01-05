# Menu Primary Component

**Component Type**: Collection (Organism)  
**Location**: `source/patterns/collections/menu-primary/`  
**Status**: ✅ Complete

---

## Overview

Primary navigation menu for main site navigation. Clean and simple implementation with multi-level support (up to 3 levels).

**Key Features**:
- ✅ Chevrons managed via CSS pseudo-elements (no span)
- ✅ Desktop: horizontal layout with hover dropdowns
- ✅ Mobile: vertical accordion with click toggle
- ✅ 100% design tokens (no hardcoded values)
- ✅ Simple Twig template (recursive macro)
- ✅ Active state with green underline (root level only)

---

## Files

- **menu-primary.twig** - Simple template with recursive macro
- **menu-primary.css** - Token-based styles, chevrons in CSS
- **menu-primary.js** - Mobile accordion behavior only
- **menu-primary.yml** - Sample data (Find a property, About us, Solutions, Latest News)
- **menu-primary.stories.jsx** - Storybook documentation (5 stories)
- **README.md** - This file

---

## Usage

### Basic Implementation

```twig
{% include '@collections/menu-primary/menu-primary.twig' with {
  menu_name: 'primary',
  items: [
    {
      title: 'Find a property',
      url: '/find-property',
      below: [
        { title: 'Buy', url: '/find-property/buy' },
        { title: 'Rent', url: '/find-property/rent' }
      ]
    },
    {
      title: 'About us',
      url: '/about-us',
      in_active_trail: true
    }
  ]
} only %}
```

---

## Props

### menu_name
- **Type**: String
- **Required**: No
- **Description**: Machine name of the menu
- **Example**: `'primary'`

### items
- **Type**: Array
- **Required**: Yes
- **Description**: Nested array of menu items

**Item structure**:
```yaml
- title: 'Menu Item'        # Link text (required)
  url: '/path'              # URL (required)
  below: []                 # Child items (optional)
  in_active_trail: false    # Active state (optional)
```

---

## Design Tokens

All styling uses tokens from `source/props/`:

### Colors
- `--gray-700` (#333333) - Default link color
- `--gray-500` (#777E83) - Hover link color
- `--primary` (#00915A) - Active/selected color
- `--white` - Submenu background
- `--border-focus` - Focus outline

### Spacing
- `--size-1` (4px) - Nested dropdown offset
- `--size-2` (8px) - Chevron spacing, submenu padding
- `--size-3` (12px) - Submenu link padding (desktop)
- `--size-4` (16px) - Link padding, chevron size
- `--size-6` (24px) - Submenu indent (mobile)

### Typography
- `--font-size-5` (16px) - Link text size
- `--font-weight-400` - Normal weight
- `--line-height-2` - Line height

### Effects
- `--shadow-2` - Dropdown shadow (desktop)
- `--radius-2` - Dropdown border radius
- `--duration-200` - Transition duration
- `--ease-2` - Easing function

---

## States

### Default
- Gray text (`--gray-700`)
- No underline
- Transparent border

### Hover
- Gray text (`--gray-500`)
- Desktop: show dropdown

### Active (root level only)
- Green text (`--primary`)
- Green underline 2px (desktop only)
- No underline on submenu active items

### Focus
- 2px solid outline (`--border-focus`)
- 2px offset

---

## Chevron Implementation

**CSS Pseudo-element (::after)** - No HTML span needed:

```css
.menu-primary__item--has-children > .menu-primary__link::after {
  content: '';
  width: var(--menu-primary-chevron-size);
  height: var(--menu-primary-chevron-size);
  background-color: currentColor;
  mask-image: url('/icons/icons-sprite.svg#icon-chevron-down');
  /* ... mask properties */
}
```

**Rotation (mobile only)**:
- Mobile: 180deg when `.is-open` class present
- Desktop: no rotation (dropdown opens below)

---

## JavaScript Behavior

### Mobile Accordion
- Click link to toggle submenu (< 768px)
- One submenu open at a time per level
- Adds/removes `.is-open` class

### Desktop Hover
- Pure CSS hover (`:hover` pseudo-class)
- No JavaScript needed
- Instant dropdown on hover

### Window Resize
- Closes all submenus on viewport change
- Debounced 250ms

---

## Responsive Breakpoints

- **Mobile**: < 768px (vertical accordion)
- **Desktop**: ≥ 768px (horizontal with hover)

---

## Accessibility

- ✅ `role="menubar"` on root `<ul>`
- ✅ Semantic HTML (`<nav>`, `<ul>`, `<li>`, `<a>`)
- ✅ Focus indicators (outline with offset)
- ✅ Color contrast WCAG AA (4.5:1)
- ✅ Touch targets 44x44px minimum
- ✅ Keyboard navigation (native link behavior)

**Note**: For full ARIA support, consider adding:
- `aria-haspopup="true"` on links with submenus
- `aria-expanded` state management
- Arrow key navigation

---

## Examples

### Three Levels

```yaml
items:
  - title: 'Find a property'
    url: '/find-property'
    below:
      - title: 'Invest'
        url: '/find-property/invest'
        below:
          - title: 'Residential'
            url: '/find-property/invest/residential'
```

### Flat Menu

```yaml
items:
  - title: 'Home'
    url: '/'
  - title: 'About'
    url: '/about'
    in_active_trail: true
```

---

## Customization

### Override Colors

```css
.menu-primary {
  --menu-primary-color: var(--gray-900);
  --menu-primary-color-active: var(--secondary);
  --menu-primary-border-active: 2px solid var(--secondary);
}
```

### Override Spacing

```css
.menu-primary {
  --menu-primary-height: 56px;
  --menu-primary-padding-x: var(--size-3);
}
```

---

## Differences from `menu` Component

1. **Chevrons**: CSS pseudo-elements (vs span elements)
2. **Simplicity**: Cleaner Twig template (less complexity)
3. **JavaScript**: Mobile-only accordion (vs full interaction)
4. **Naming**: BEM with `menu-primary` prefix (vs `menu`)
5. **Purpose**: Specifically for primary navigation

---

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11 not supported (CSS custom properties)
- Mobile Safari iOS 12+
- Chrome Android 80+

---

## Related Components

- **Menu** (`collections/menu/`) - Generic menu component
- **Header** (`collections/header/`) - Contains primary menu
- **Breadcrumb** (`elements/breadcrumb/`) - Secondary navigation

---

**Last Updated**: 2025-12-29  
**Maintainer**: Design System Team
