# Accordion Component

Accessible accordion component for collapsible content sections with ARIA support, keyboard navigation, and single/multi-expansion modes.

## Component Props

| Prop | Type | Options | Default | Description |
|------|------|---------|---------|-------------|
| `items` | array | - | `[]` | Array of accordion sections (required) |
| `items[].id` | string | any | auto-generated | Unique identifier for section |
| `items[].title` | string | any | - | Section title (required) |
| `items[].content` | string | HTML | - | Section content (required) |
| `items[].open` | boolean | `true`, `false` | `false` | Initial open state |
| `singleOpen` | boolean | `true`, `false` | `true` | Only one section open at a time |
| `bordered` | boolean | `true`, `false` | `true` | Show borders between items |
| `flush` | boolean | `true`, `false` | `false` | Remove padding for dense layouts |
| `headingLevel` | string | `'h2'`, `'h3'`, `'h4'`, `'h5'` | `'h3'` | Semantic heading level |
| `attributes` | object | any | - | Additional HTML attributes |

## BEM Structure

```
ps-accordion                         // Root element (default: bordered)
├── ps-accordion--no-bordered        // Variant without borders
├── ps-accordion--flush              // Variant with minimal padding
│
├── ps-accordion__item               // Individual accordion section
│   └── ps-accordion__item--open     // Open state
│
├── ps-accordion__header             // Heading wrapper
├── ps-accordion__trigger            // Toggle button
├── ps-accordion__title              // Section title text
├── ps-accordion__icon               // Expand/collapse icon
└── ps-accordion__panel              // Content panel
```

## Variants

### Bordered (Default)
Standard accordion with borders between items.

### No Borders
Set `bordered: false` to remove borders for a cleaner appearance.

### Flush
Set `flush: true` for minimal padding, ideal for sidebars or dense layouts.

## Behavior Modes

### Single Open (Default)
- `singleOpen: true`
- Only one section can be open at a time
- Opening a new section automatically closes others
- Ideal for FAQs and wizards

### Multi Open
- `singleOpen: false`
- Multiple sections can be open simultaneously
- All sections are independent
- Ideal for product details and feature lists

## Design Tokens Used

### Typography
- `--font-sans` - Font family
- `--font-size-1` - Base font size (16px)
- `--font-weight-600` - Title font weight
- `--leading-normal` - Line height

### Spacing
- `--size-3` (12px) - Icon/title gap, paragraph spacing, flush padding
- `--size-4` (16px) - Trigger vertical padding
- `--size-5` (20px) - Trigger horizontal padding, panel padding, icon size

### Colors
- `--gray-50` - Hover background
- `--gray-100` - Active background
- `--gray-200` - Border color
- `--gray-700` - Panel text color
- `--gray-900` - Title color
- `--brand-primary` - Focus outline, links
- `--green-700` - Link hover

### Borders
- `--border-size-1` (1px) - Item borders
- `--border-size-2` (2px) - Focus outline

### Transitions
- `--ease-out-2` - Easing function for smooth animations

## Usage Examples

### Basic FAQ

```twig
{% include '@components/accordion/accordion.twig' with {
  singleOpen: true,
  bordered: true,
  items: [
    {
      title: 'How do I reset my password?',
      content: '<p>Click "Forgot Password" on the login page...</p>',
      open: true
    },
    {
      title: 'What payment methods do you accept?',
      content: '<p>We accept all major credit cards...</p>'
    }
  ]
} %}
```

### Product Details (Multi-Open)

```twig
{% include '@components/accordion/accordion.twig' with {
  singleOpen: false,
  bordered: true,
  items: [
    {
      title: 'Description',
      content: '<p>High-quality product designed...</p>',
      open: true
    },
    {
      title: 'Specifications',
      content: '<ul><li>Dimensions: 30×20×15 cm</li></ul>',
      open: true
    },
    {
      title: 'Shipping & Returns',
      content: '<p>Free shipping on orders over €50...</p>'
    }
  ]
} %}
```

### Flush Sidebar Accordion

```twig
{% include '@components/accordion/accordion.twig' with {
  singleOpen: true,
  bordered: false,
  flush: true,
  headingLevel: 'h4',
  items: [
    {
      title: 'Filter by Category',
      content: '<ul><li>Option 1</li><li>Option 2</li></ul>',
      open: true
    }
  ]
} %}
```

## Real-World Use Cases

### 1. FAQ Section
```twig
{% include '@components/accordion/accordion.twig' with {
  singleOpen: true,
  bordered: true,
  headingLevel: 'h3',
  items: faq_items
} %}
```

### 2. Product Information Tabs
```twig
{% include '@components/accordion/accordion.twig' with {
  singleOpen: false,
  bordered: true,
  items: [
    { title: 'Description', content: product.description, open: true },
    { title: 'Specifications', content: product.specs, open: true },
    { title: 'Reviews', content: product.reviews }
  ]
} %}
```

### 3. Settings Panel
```twig
{% include '@components/accordion/accordion.twig' with {
  singleOpen: true,
  bordered: true,
  items: [
    { title: 'Account Settings', content: account_form },
    { title: 'Privacy Settings', content: privacy_form },
    { title: 'Notification Settings', content: notification_form }
  ]
} %}
```

## Accessibility

### ARIA Attributes
- **Triggers**: `button[aria-expanded]` to indicate open/closed state
- **Panels**: `role="region"` with `aria-labelledby` linking to trigger ID
- **IDs**: Automatic generation ensures unique `id` and `aria-controls` pairing

### Keyboard Navigation
- **Tab**: Navigate between accordion triggers
- **Enter/Space**: Toggle current section open/closed
- **Focus visible**: Outline appears on keyboard focus

### Semantic Headings
- `headingLevel` prop ensures proper heading hierarchy in document outline
- Default `h3` suitable for most page sections
- Adjust based on page structure (h2 for main sections, h4 for nested areas)

### Screen Readers
- Button labels are section titles (clear and descriptive)
- `aria-expanded` announces state changes
- `role="region"` identifies content panels

## Implementation Notes

### JavaScript Behavior
- Drupal behavior attached via `once()` to prevent duplicate handlers
- Delegated event handling for dynamic content support
- Programmatic API: `accordion.PSAccordion.openItem(index)`, `closeAll()`

### CSS Transitions
- Icon rotation animated via CSS transform
- Background color transitions on hover/active states
- Panel height changes managed by `hidden` attribute (no complex animations)

### Performance
- Minimal reflows: `hidden` attribute for show/hide
- Delegated events reduce memory overhead
- No external dependencies (vanilla JS + Drupal core)

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11: Requires polyfills for CSS nesting (autoprefixer handles this)
- Keyboard navigation fully supported across all browsers
