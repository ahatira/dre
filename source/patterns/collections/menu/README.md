# Menu Component

**Component Type**: Collection (Organism)  
**Location**: `source/patterns/collections/menu/`  
**Status**: ✅ Complete

---

## Overview

Multi-level navigation menu for primary site navigation. Supports up to 3 levels of nesting with responsive behavior: horizontal layout with dropdown on desktop, vertical accordion on mobile.

---

## Features

### Desktop (≥768px)
- Horizontal layout with 64px item height
- Dropdown submenus on hover/click
- Green underline (2px) for active/selected items
- Hover state with color change
- Nested dropdowns for multi-level menus
- Box shadow and border radius on dropdown panels

### Mobile (<768px)
- Vertical accordion layout
- One submenu open at a time (accordion behavior)
- Chevron icon rotates 180deg when expanded
- Indented submenu levels (24px, 36px, 48px)
- Active item shows green color

### Accessibility
- Full ARIA support (`aria-expanded`, `aria-haspopup`, `role="menubar"`)
- Keyboard navigation (Arrow keys, Enter, Space, Escape)
- Focus-visible indicators
- Screen reader compatible

---

## Files

- **menu.twig** - Template with recursive macro for multi-level rendering
- **menu.css** - Token-based styles with mobile-first approach
- **menu.js** - Interactive behavior (accordion, keyboard navigation, ARIA)
- **menu.yml** - Sample data with real estate menu items
- **menu.stories.jsx** - Storybook documentation with 5 stories
- **README.md** - This file

---

## Usage

### Drupal Template Override

Use the primary navigation override in `templates/navigation/menu--primary.html.twig`:

```twig
{% if items %}
  {% include '@collections/menu/menu.twig' with {
    'menu_name': menu_name,
    'items': items,
    'attributes': attributes,
    'modifier': 'primary'
  } only %}
{% endif %}
```

### Standalone (Storybook/Pattern Lab)

```twig
{% include '@collections/menu/menu.twig' with {
  menu_name: 'primary',
  modifier: 'primary',
  items: [
    {
      title: 'Find a property',
      url: '/find-property',
      is_expanded: true,
      below: [
        { title: 'Buy', url: '/find-property/buy' },
        { title: 'Rent', url: '/find-property/rent' },
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
- **Required**: Yes
- **Description**: Machine name of the menu (e.g., 'primary', 'footer')
- **Example**: `'primary'`

### modifier
- **Type**: String
- **Required**: No
- **Description**: Optional modifier class for styling variations
- **Example**: `'primary'` → `.menu--primary`

### items
- **Type**: Array
- **Required**: Yes
- **Description**: Nested array of menu items

**Item structure**:
```yaml
- title: 'Menu Item'          # Link text (required)
  url: '/path'                # URL string or Drupal\Core\Url object (required)
  attributes: {}              # Drupal Attribute object (optional)
  is_expanded: true           # Show submenu (optional)
  is_collapsed: false         # Hide submenu (optional)
  in_active_trail: true       # Mark as active/selected (optional)
  below:                      # Child items array (optional)
    - title: 'Submenu Item'
      url: '/path/subitem'
```

### attributes
- **Type**: Drupal Attribute object
- **Required**: No
- **Description**: Additional HTML attributes for the root `<ul>` element
- **Default**: `create_attribute()` (empty)

---

## Design Tokens

All styling uses design tokens from `source/props/`:

### Colors
- `--gray-700` - Default link color (#333333)
- `--gray-500` - Hover link color (#777E83)
- `--primary` - Active/selected color (#00915A)
- `--white` - Submenu background
- `--border-focus` - Focus outline color

### Spacing
- `--size-1` (4px) - Icon spacing
- `--size-2` (8px) - Item gap, submenu padding vertical
- `--size-3` (12px) - Submenu padding horizontal (desktop)
- `--size-4` (16px) - Icon size, link padding
- `--size-6` (24px) - Submenu indent (mobile)

### Typography
- `--font-size-3` (16px) - Link text size
- `--font-weight-400` - Normal weight
- `--line-height-2` - Line height

### Effects
- `--shadow-2` - Dropdown box shadow (desktop)
- `--radius-2` - Dropdown border radius (desktop)
- `--duration-200` - Transition duration (200ms)
- `--ease-2` - Easing function

### Layout
- `--z-dropdown` - Z-index for dropdown positioning

---

## States

### Default
- Gray text (`--gray-700`)
- No underline
- Transparent border-bottom

### Hover
- Gray text (`--gray-500`)
- No underline
- Maintains transparency

### Active/Selected
- Green text (`--primary`)
- Green underline 2px (desktop only)
- Chevron matches green color

### Focus
- 2px solid outline (`--border-focus`)
- 2px offset
- Color changes to hover state

### Expanded (has submenu open)
- Chevron rotates 180deg (mobile)
- Submenu visible
- `aria-expanded="true"`

---

## JavaScript Behavior

### Accordion (Mobile)
- Click/tap to toggle submenu
- Only one submenu open at a time per level
- Chevron rotates on expand/collapse

### Dropdown (Desktop)
- Hover to show submenu
- Click outside to close
- Nested dropdowns positioned to the right

### Keyboard Navigation
- **Enter/Space** - Toggle submenu
- **Escape** - Close submenu and return focus
- **Arrow Down** - Next item or enter submenu
- **Arrow Up** - Previous item
- **Arrow Right** - Expand submenu
- **Arrow Left** - Collapse submenu

### Window Resize
- Closes all submenus on viewport change (prevents state issues)
- Debounced 250ms

---

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11 not supported (uses CSS custom properties)
- Mobile Safari iOS 12+
- Chrome Android 80+

---

## Accessibility Checklist

✅ ARIA attributes (`aria-expanded`, `aria-haspopup`, `role`)  
✅ Keyboard navigation (all arrow keys, Enter, Escape)  
✅ Focus indicators (outline with offset)  
✅ Screen reader announcements (via ARIA)  
✅ Color contrast WCAG AA (4.5:1 minimum)  
✅ Touch targets 44x44px minimum  
✅ No keyboard traps  
✅ Skip to content link (handled by header)

---

## Examples

### Three-level Menu

```yaml
items:
  - title: 'Find a property'
    url: '/find-property'
    is_expanded: true
    below:
      - title: 'Invest'
        url: '/find-property/invest'
        is_expanded: true
        below:
          - title: 'Residential'
            url: '/find-property/invest/residential'
          - title: 'Commercial'
            url: '/find-property/invest/commercial'
```

### Flat Menu (no submenus)

```yaml
items:
  - title: 'Home'
    url: '/'
  - title: 'About'
    url: '/about'
    in_active_trail: true
  - title: 'Contact'
    url: '/contact'
```

---

## Customization

### Override Colors

```css
.menu--custom {
  --ps-menu-link-color: var(--gray-900);
  --ps-menu-link-color-active: var(--secondary);
  --ps-menu-border-active-color: var(--secondary);
}
```

### Override Spacing

```css
.menu--compact {
  --ps-menu-item-height: 48px;
  --ps-menu-item-gap: var(--size-1);
}
```

---

## Related Components

- **Header** (`collections/header/`) - Contains the primary menu
- **Breadcrumb** (`elements/breadcrumb/`) - Secondary navigation
- **Icon** (`elements/icon/`) - Chevron icons

---

## References

- [Figma Design](https://www.figma.com/file/...) - Desktop/Mobile menu states
- [Drupal Menu System](https://www.drupal.org/docs/theming-drupal/twig-in-drupal/twig-template-naming-conventions#menu) - Menu template naming
- [ARIA Authoring Practices Guide - Navigation Menu](https://www.w3.org/WAI/ARIA/apg/patterns/menubar/) - Accessibility patterns

---

**Last Updated**: 2025-12-29  
**Maintainer**: Design System Team
