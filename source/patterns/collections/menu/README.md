# Menu Component

Responsive multi-level navigation menu component for PS Theme. Based on Drupal core menu template with Token-First styling and full accessibility support.

## Overview

- **Type**: Collection/Organism
- **Location**: `source/patterns/collections/menu/`
- **Dependencies**: 
  - Uses icon system for toggle buttons (`chevron-right` icon)
  - Drupal `link()` function for rendering links
  - Drupal `create_attribute()` for attribute handling
- **Accessibility**: Full WCAG 2.1 AAA compliance (keyboard navigation, ARIA attributes, focus-visible states)

## Features

- **Multi-level navigation** - Supports unlimited nesting depth
- **Responsive design** - Mobile-first approach with desktop hover effects
- **Mobile menu** - Toggle buttons for small screens
- **Active state tracking** - Highlights active trail and current page
- **Keyboard accessible** - Full keyboard navigation support
- **Semantic HTML** - Proper nav, ul, li structure
- **Icon integration** - Chevron icons for submenu toggles
- **CSS-driven** - Minimal JavaScript requirements (optional for enhanced UX)
- **Drupal compatible** - Works with Drupal render arrays and menu system

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `menu_name` | string | `'main'` | Machine name of the menu (Drupal) |
| `items` | array | `[]` | Nested array of menu items with below/attributes/states |
| `variant` | string | `'default'` | Visual variant: `default`, `mobile`, `compact`, `dark`, `high-contrast` |
| `expanded` | boolean | `false` | Expand menu by default (mobile variant) |
| `attributes` | object | `null` | Drupal attributes object for nav root element |

## Menu Item Structure

Each menu item object contains:
- `title` (string): Menu link text
- `url` (string): Link URL (or null for disabled items)
- `below` (array, optional): Array of child menu items
- `attributes` (object, optional): Drupal attributes for the list item
- `is_expanded` (boolean, optional): Item has visible children
- `is_collapsed` (boolean, optional): Item has hidden children
- `in_active_trail` (boolean, optional): Item is in active navigation path

## CSS Variables (Layer 2 - Component-Scoped)

```css
--ps-menu-text-color: var(--text-primary)
--ps-menu-text-size: var(--font-size-3)
--ps-menu-text-weight: var(--font-weight-400)
--ps-menu-link-color: var(--text-primary)
--ps-menu-link-color-hover: var(--primary)
--ps-menu-link-color-active: var(--primary)
--ps-menu-bg-hover: color-mix(in srgb, var(--primary) 8%, transparent)
--ps-menu-bg-active: color-mix(in srgb, var(--primary) 12%, transparent)
--ps-menu-padding-y: var(--size-3)
--ps-menu-padding-x: var(--size-4)
--ps-menu-item-gap: 0
--ps-menu-border-color: var(--gray-200)
--ps-menu-submenu-bg: var(--gray-50)
--ps-menu-submenu-indent: var(--size-6)
--ps-menu-toggle-size: var(--size-8)
--ps-menu-transition-duration: var(--duration-200)
--ps-menu-transition-easing: var(--ease-2)
```

## Variants

### Default
Standard horizontal menu with hover effects for submenus (desktop) and toggle buttons (mobile).

### Mobile
Vertical layout optimized for mobile devices. Toggle buttons visible for items with children.

### Compact
Reduced spacing and font size for dense menus or secondary navigation.

### Dark
Inverted colors for use on dark backgrounds. Light text, darker background variations.

### High Contrast
Enhanced accessibility variant with underlines instead of background colors for better contrast.

## Responsive Behavior

### Mobile (max-width: 767px)
- Vertical layout (single column)
- Toggle buttons shown for items with children
- Collapsed by default (requires click to expand)
- Full-width menu items

### Desktop (min-width: 768px)
- Horizontal layout for top-level items
- Submenus appear on hover (absolute positioning)
- Toggle buttons hidden
- Compact dropdown menus

## Accessibility

- **Keyboard navigation**: Tab through links, arrow keys for navigation (with JS enhancement)
- **ARIA attributes**: `aria-expanded` on toggle buttons, semantic HTML structure
- **Focus-visible**: Clear focus indicators on all interactive elements
- **Screen reader friendly**: Semantic nav/ul/li structure, skip link compatible
- **Sufficient color contrast**: All states meet WCAG AA standards
- **Icon accessibility**: Toggle button icons hidden from screen readers (`aria-hidden="true"`)

## Usage Examples

### Basic Drupal Integration

```php
// In your module/theme file
function my_module_theme($existing, $type, $theme, $path) {
  return [
    'ps_menu' => [
      'variables' => [
        'menu_name' => 'main',
        'items' => [],
        'variant' => 'default',
        'expanded' => false,
        'attributes' => [],
      ],
      'template' => 'menu',
      'path' => $path . '/source/patterns/collections/menu',
    ],
  ];
}
```

### Render Array Usage

```php
// In your controller/hook
$build['menu'] = [
  '#theme' => 'ps_menu',
  '#menu_name' => 'main',
  '#items' => $menu_tree,
  '#variant' => 'default',
  '#cache' => [
    'tags' => ['config:system.menu.main'],
  ],
];
```

### Twig Template Usage

```twig
{% include '@collections/menu/menu.twig' with {
  menu_name: 'main',
  items: menu_items,
  variant: 'default'
} only %}
```

## JavaScript Enhancement (Optional)

For enhanced UX, JavaScript can be added to:
- Handle toggle button clicks
- Manage `aria-expanded` state
- Support arrow key navigation in submenus
- Add animations for submenu open/close
- Persist open state in mobile menus

Basic event listener pattern:
```javascript
document.querySelectorAll('[data-submenu-toggle]').forEach(button => {
  button.addEventListener('click', (e) => {
    const isExpanded = button.getAttribute('aria-expanded') === 'true';
    button.setAttribute('aria-expanded', !isExpanded);
  });
});
```

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari 14+, Chrome Android)

## Design Tokens

Uses the following design token categories:
- **Colors**: Primary, secondary, semantic, gray scale
- **Typography**: Font sizes, weights, line heights
- **Spacing**: Size tokens (--size-1 through --size-12)
- **Borders**: Border colors and widths
- **Shadows**: Drop shadows (optional in some variants)
- **Animations**: Transition durations, easing functions
- **Z-Index**: Dropdown/overlay layering

## Related Components

- **Icon** (`elements/icon`) - For toggle button icons
- **Link** (inherits from Drupal core) - For menu links

## Conformity Checklist

- ✅ Token-First architecture (Layer 2 component variables)
- ✅ Semantic HTML structure (nav/ul/li)
- ✅ Full WCAG 2.1 accessibility compliance
- ✅ Responsive design (mobile-first)
- ✅ No hardcoded values (all via tokens)
- ✅ BEM naming convention
- ✅ Drupal Attribute object usage
- ✅ Multi-level support (unlimited nesting)
- ✅ Active state tracking
- ✅ Icon system integration

## Testing

1. **Visual Testing**:
   - Run `npm run watch` → http://localhost:6006
   - Test all variants and responsive breakpoints
   - Verify hover/focus/active states

2. **Accessibility Testing**:
   - Keyboard navigation (Tab, Enter, Escape)
   - Screen reader testing (NVDA, JAWS, VoiceOver)
   - Focus visible indicators
   - Color contrast validation

3. **Drupal Integration**:
   - Test with actual Drupal menu system
   - Verify active trail highlighting
   - Test cache invalidation
   - Confirm attribute propagation

## Changelog

See `docs/ps-design/CHANGELOG.md` for version history and updates.
