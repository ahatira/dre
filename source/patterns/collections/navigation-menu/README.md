# Navigation Menu Component

Responsive multi-level navigation menu component for header with full accessibility support and configurable behavior.

## Overview

- **Type**: Collection/Organism
- **Location**: `source/patterns/collections/navigation-menu/`
- **Dependencies**: 
  - Icon atom (chevron-down icon)
  - Drupal `link()` function for rendering links
  - Drupal `create_attribute()` for attribute handling
- **Accessibility**: Full WCAG 2.2 AA compliance (keyboard navigation, ARIA attributes, focus management)
- **JavaScript**: Progressive enhancement with Drupal behaviors

## Features

### Desktop
- **Horizontal layout** - Menu items displayed in a row
- **Configurable behavior**:
  - `hover` (default) - Submenus open on mouse hover
  - `click` - Submenus require click on toggle button
- **Active state** - Underline on current page link
- **Dropdown positioning** - Absolute positioned submenus with shadow
- **Focus-within fallback** - Keyboard navigation works without JS

### Mobile
- **Vertical layout** - Stacked menu items for small screens
- **Toggle buttons** - Chevron icons to expand/collapse submenus
- **Accordion mode** - Optional: only one submenu open at a time
- **Fullscreen drawer** - Optional variant for mobile navigation
- **Animated chevrons** - Rotate 180° when submenu is open

### Accessibility
- **Keyboard navigation** - Full support for Tab, Enter, Space, Escape, Arrow keys
- **ARIA attributes** - `aria-expanded`, `aria-label`, `role="navigation"`
- **Focus management** - Focus moves to first submenu link on open
- **Screen reader** - Proper announcements for menu state changes
- **Focus indicators** - Visible outline on all interactive elements

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `[]` | **Required.** Nested array of menu items |
| `menu_name` | string | `'main'` | Machine name of the menu (Drupal) |
| `variant` | string | `'default'` | Visual variant: `default`, `dark`, `mobile` |
| `behavior` | string | `'hover'` | Desktop submenu behavior: `hover`, `click` |
| `accordion` | boolean | `false` | Mobile accordion mode (one item open at a time) |
| `attributes` | object | `null` | Drupal attributes object for nav root element |
| `modifier_class` | string | `''` | Additional CSS classes |

## Menu Item Structure

Each item in the `items` array must have:

```yaml
- title: "Menu Item Label"      # Required - Link text
  url: "/path"                   # Required - Link URL (string or Drupal\Core\Url)
  below:                         # Optional - Array of child items
    - title: "Submenu Item"
      url: "/path/child"
  attributes: {}                 # Optional - Drupal Attribute object
  is_expanded: false             # Optional - Has visible children
  is_collapsed: false            # Optional - Has hidden children
  in_active_trail: false         # Optional - Is in active navigation path
```

## CSS Variables (Layer 2 - Component-Scoped)

Override these variables in consuming components to customize appearance:

```css
.your-header {
  --ps-navigation-menu-bg: var(--white);
  --ps-navigation-menu-text-color: var(--gray-700);
  --ps-navigation-menu-text-hover: var(--gray-500);
  --ps-navigation-menu-text-active: var(--primary);
  --ps-navigation-menu-border-active: var(--primary);
  --ps-navigation-menu-border-width: 2px;
  --ps-navigation-menu-item-height: 64px;
  --ps-navigation-menu-item-padding-x: var(--size-6);
  --ps-navigation-menu-item-gap: var(--size-2);
  --ps-navigation-menu-font-size: var(--font-size-3);
  --ps-navigation-menu-font-weight: var(--font-weight-400);
  --ps-navigation-menu-submenu-bg: var(--white);
  --ps-navigation-menu-submenu-shadow: var(--shadow-3);
  --ps-navigation-menu-submenu-min-width: 200px;
  --ps-navigation-menu-submenu-padding: var(--size-2) 0;
  --ps-navigation-menu-transition-duration: var(--duration-200);
  --ps-navigation-menu-transition-easing: var(--ease-2);
  --ps-navigation-menu-chevron-size: var(--size-4);
  --ps-navigation-menu-chevron-rotation: 180deg;
  --ps-navigation-menu-toggle-size: var(--size-8);
}
```

## BEM Structure

```
.ps-navigation-menu                          # Block (root nav element)
├── .ps-navigation-menu--dark                # Modifier: dark variant
├── .ps-navigation-menu--mobile              # Modifier: mobile variant
├── .ps-navigation-menu--click               # Modifier: click behavior
├── .ps-navigation-menu--accordion           # Modifier: accordion mode
├── .ps-navigation-menu__list                # Element: list container
│   ├── .ps-navigation-menu__list--root      # Modifier: root level
│   └── .ps-navigation-menu__list--submenu   # Modifier: submenu level
├── .ps-navigation-menu__item                # Element: menu item
│   ├── .ps-navigation-menu__item--expanded  # Modifier: submenu open
│   ├── .ps-navigation-menu__item--collapsed # Modifier: submenu closed
│   ├── .ps-navigation-menu__item--active-trail # Modifier: in active path
│   └── .ps-navigation-menu__item--has-children # Modifier: has submenu
├── .ps-navigation-menu__item-wrapper        # Element: link + toggle wrapper
├── .ps-navigation-menu__link                # Element: menu link
│   ├── .ps-navigation-menu__link--active    # Modifier: active page
│   └── .ps-navigation-menu__link--disabled  # Modifier: disabled link
├── .ps-navigation-menu__toggle              # Element: toggle button
└── .ps-navigation-menu__chevron             # Element: chevron icon
```

## Usage Examples

### Basic Menu (Hover Behavior)

```twig
{% include '@collections/navigation-menu/navigation-menu.twig' with {
  items: menu_items,
  menu_name: 'main'
} only %}
```

### Click Behavior (Touch-Friendly)

```twig
{% include '@collections/navigation-menu/navigation-menu.twig' with {
  items: menu_items,
  behavior: 'click'
} only %}
```

### Dark Variant

```twig
{% include '@collections/navigation-menu/navigation-menu.twig' with {
  items: menu_items,
  variant: 'dark'
} only %}
```

### Mobile Fullscreen Drawer

```twig
{% include '@collections/navigation-menu/navigation-menu.twig' with {
  items: menu_items,
  variant: 'mobile',
  accordion: true
} only %}
```

### Real Estate Context

```twig
{# Drupal preprocess or controller #}
{% set main_menu_items = [
  {
    title: 'Rechercher un bien',
    url: path('view.property_search.page'),
    in_active_trail: true,
    below: [
      { title: 'Location', url: path('view.property_search.rent') },
      { title: 'Vente', url: path('view.property_search.sale') },
      { title: 'Programmes neufs', url: path('view.property_search.new') }
    ]
  },
  {
    title: 'Nos services',
    url: path('page.services'),
    below: [
      { title: 'Estimation', url: path('page.services.valuation') },
      { title: 'Gestion locative', url: path('page.services.management') }
    ]
  }
] %}

{% include '@collections/navigation-menu/navigation-menu.twig' with {
  items: main_menu_items,
  menu_name: 'main',
  behavior: 'click'
} only %}
```

## JavaScript API

The component uses Drupal behaviors for progressive enhancement. The behavior is automatically initialized on page load and AJAX requests.

### Manual Initialization

```javascript
// Drupal behavior handles initialization automatically
// No manual initialization needed

// Access menu instance
const menu = document.querySelector('.ps-navigation-menu');
const behavior = menu.dataset.behavior; // 'hover' or 'click'
const isAccordion = menu.dataset.accordion === 'true';
```

### Events

The component dispatches custom events for integration:

```javascript
// Listen for submenu open
document.addEventListener('ps-navigation-menu:submenu-open', (event) => {
  console.log('Submenu opened:', event.detail.submenu);
});

// Listen for submenu close
document.addEventListener('ps-navigation-menu:submenu-close', (event) => {
  console.log('Submenu closed:', event.detail.submenu);
});
```

## Drupal Integration

### Theme Hook

Register the theme hook in `ps.theme`:

```php
/**
 * Implements hook_theme().
 */
function ps_theme() {
  return [
    'navigation_menu' => [
      'variables' => [
        'items' => [],
        'menu_name' => 'main',
        'variant' => 'default',
        'behavior' => 'hover',
        'accordion' => FALSE,
        'attributes' => NULL,
        'modifier_class' => '',
      ],
    ],
  ];
}
```

### Preprocess Function

```php
/**
 * Implements template_preprocess_navigation_menu().
 */
function ps_preprocess_navigation_menu(&$variables) {
  // Attach JavaScript library
  $variables['#attached']['library'][] = 'ps/navigation-menu';
  
  // Convert menu tree to renderable array
  $menu_name = $variables['menu_name'];
  $menu_tree = \Drupal::menuTree();
  $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
  $tree = $menu_tree->load($menu_name, $parameters);
  $manipulators = [
    ['callable' => 'menu.default_tree_manipulators:checkAccess'],
    ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
  ];
  $tree = $menu_tree->transform($tree, $manipulators);
  $variables['items'] = $menu_tree->build($tree);
}
```

### Library Definition

Add to `ps.libraries.yml`:

```yaml
navigation-menu:
  version: 1.0.0
  css:
    component:
      source/patterns/collections/navigation-menu/navigation-menu.css: {}
  js:
    source/patterns/collections/navigation-menu/navigation-menu.js: {}
  dependencies:
    - core/drupal
    - core/once
```

## Testing

### Accessibility Testing

- [x] Keyboard navigation (Tab, Enter, Space, Escape, Arrows)
- [x] Screen reader announcements (NVDA, JAWS, VoiceOver)
- [x] Focus indicators visible on all interactive elements
- [x] Color contrast 4.5:1 for text, 3:1 for UI components
- [x] Touch target size minimum 44x44px

### Responsive Testing

- [x] Mobile portrait (320px - 639px)
- [x] Mobile landscape (640px - 767px)
- [x] Tablet (768px - 1023px)
- [x] Desktop (1024px+)

### Cross-Browser Testing

- [x] Chrome (latest)
- [x] Firefox (latest)
- [x] Safari (latest)
- [x] Edge (latest)

## Related Components

- **Menu** (`source/patterns/collections/menu/`) - Base Drupal menu template
- **Icon** (`source/patterns/elements/icon/`) - Icon atom for chevrons
- **Link** (`source/patterns/elements/link/`) - Link atom (implicit via Drupal)

## Changelog

See `docs/ps-design/CHANGELOG.md` for component history.

## Maintainers

Design System Team - PS Theme

---

**Last Updated**: 2025-12-28  
**Version**: 1.0.0
