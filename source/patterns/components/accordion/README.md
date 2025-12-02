# Accordion

Collapsible disclosure list with bordered separators, optional flush layout, and accessible ARIA controls. Uses `aria-expanded` buttons controlling panels with `role="region"`.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | `Array<{ id?, title, content, open? }>` | Required | Accordion sections (title and content required) |
| `singleOpen` | `boolean` | `true` | Only one section can be expanded at a time |
| `flush` | `boolean` | `false` | Remove vertical padding for compact layout |
| `headingLevel` | `'h2'|'h3'|'h4'|'h5'` | `'h3'` | Semantic heading level for accessibility |
| `attributes` | `Drupal.Attribute` | — | Drupal attributes object for root element |

## BEM Classes

**Block**: `ps-accordion`

**Elements**:
- `ps-accordion__item` - Individual accordion section
- `ps-accordion__header` - Heading wrapper (semantic heading)
- `ps-accordion__trigger` - Interactive button
- `ps-accordion__title` - Title text
- `ps-accordion__icon` - Chevron indicator (CSS pseudo-elements)
- `ps-accordion__panel` - Collapsible content region

**Modifiers**:
- `ps-accordion--flush` - Compact padding variant
- `ps-accordion__item--open` - Expanded state

## Design Tokens

### Component-Scoped Variables (Layer 2)

This component uses the **Three-Layer CSS Variables System**:

```css
/* Typography */
--ps-accordion-font-family: var(--font-sans);
--ps-accordion-title-font-size: var(--font-size-3);
--ps-accordion-panel-font-size: var(--font-size-1);

/* Colors */
--ps-accordion-border-color: var(--border-default);
--ps-accordion-title-color: var(--text-primary);
--ps-accordion-panel-color: var(--text-secondary);

/* Spacing */
--ps-accordion-trigger-padding-y: var(--size-4);
--ps-accordion-flush-padding-y: var(--size-2);

/* Borders */
--ps-accordion-border-width: var(--border-size-1);

/* Transitions */
--ps-accordion-trigger-transition: color var(--duration-fast) var(--ease-4);
--ps-accordion-panel-transition-duration: var(--duration-normal);
```

### Referenced Primitives (Layer 1)

- **Typography**: `--font-sans`, `--font-size-1`, `--font-size-3`, `--font-weight-400`, `--leading-normal`, `--leading-6`
- **Colors**: `--border-default`, `--text-primary`, `--text-secondary`, `--border-focus`
- **Spacing**: `--size-2`, `--size-3`, `--size-4`, `--size-5`
- **Borders**: `--border-size-1`, `--border-size-2`
- **Transitions**: `--duration-fast`, `--duration-normal`, `--ease-4`

## Usage

```twig
{% include '@ps_theme/accordion/accordion.twig' with {
  singleOpen: true,
  flush: false,
  headingLevel: 'h3',
  items: [
    {
      id: 'property-faq-1',
      title: 'How to buy commercial real estate?',
      content: '<p>Commercial property acquisition requires...</p>',
      open: true
    },
    {
      id: 'property-faq-2',
      title: 'What are the financing options?',
      content: '<p>Multiple financing solutions are available...</p>'
    },
    {
      id: 'property-faq-3',
      title: 'How long does the process take?',
      content: '<p>Typical acquisition timeline ranges from...</p>'
    }
  ]
} %}
```

### Contextual Theming (Layer 3)

Override component variables for specific contexts:

```css
/* Dark sidebar theme */
.sidebar-dark .ps-accordion {
  --ps-accordion-border-color: var(--gray-700);
  --ps-accordion-title-color: var(--text-inverse);
  --ps-accordion-panel-color: var(--gray-300);
}

/* Compact footer variant */
.footer .ps-accordion {
  --ps-accordion-trigger-padding-y: var(--size-2);
  --ps-accordion-title-font-size: var(--font-size-1);
}
```

## Accessibility

- **ARIA Patterns**: Follows [WAI-ARIA Accordion Pattern](https://www.w3.org/WAI/ARIA/apg/patterns/accordion/)
- **Button Controls**: `aria-expanded` attribute toggles between `"true"` and `"false"`
- **Panel Regions**: `role="region"` with `aria-labelledby` pointing to trigger ID
- **Keyboard Navigation**: Enter/Space keys toggle expanded state
- **Focus Management**: `:focus-visible` outline using `--border-focus` token (WCAG 2.4.7)
- **Hidden Attribute**: Collapsed panels use `hidden` attribute for screen readers
- **Semantic Headings**: Configurable heading levels (`h2`-`h5`) for proper document outline

## Real-World Use Cases

- **Property FAQ Sections** - "How to buy/sell commercial real estate"
- **Service Details** - Expandable property management services
- **Financial Information** - Mortgage options, payment schedules
- **Legal Documents** - Terms, conditions, disclosures (mobile-friendly)
- **Property Features** - Collapsible amenities, specifications lists

## Browser Support

Modern browsers supporting:
- CSS Nesting (`&` syntax via PostCSS)
- CSS Custom Properties
- CSS Grid & Flexbox
- `aria-expanded` & `hidden` attributes
