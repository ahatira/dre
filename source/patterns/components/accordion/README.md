# Accordion

Simplified, pixel-perfect disclosure list with default separators and optional flush layout. Uses `aria-expanded` buttons controlling panels with `role="region"`.

## Props

- items: Array `{ id?, title, content, open? }` (required fields: title, content)
- singleOpen: One section open at a time (default: true)
- flush: Remove horizontal padding (default: false)
- headingLevel: `h2|h3|h4|h5` (default `h3`)
- attributes: Attributes for root element

## BEM

- Block: `ps-accordion`
- Elements: `ps-accordion__item`, `ps-accordion__header`, `ps-accordion__trigger`, `ps-accordion__title`, `ps-accordion__icon`, `ps-accordion__panel`
- Modifiers: `ps-accordion--flush`, `ps-accordion__item--open`

## Tokens

- Typography: `--ps-font-family-primary`, `--font-size-1`
- Spacing: `--ps-spacing-2`, `--ps-spacing-3`, `--ps-spacing-4`, `--ps-spacing-5`
- Borders: `--ps-border-width-default`, `--ps-border-width-focus`, `--ps-border-radius-sm`, `--gray-300`, `--ps-color-border-focus`
- Icon: `--ps-icon-size-16`
- State: glyph swap via icon font codes (chevron right/down) — pseudo-element only

## Usage

```twig
{% include '@ps_theme/accordion/accordion.twig' with {
  singleOpen: true,
  items: [
    { id: 'faq-1', title: 'Question 1', content: '<p>Réponse...</p>', open: true },
    { id: 'faq-2', title: 'Question 2', content: '<p>Réponse...</p>' }
  ]
} %}
```

## Accessibility

- Trigger toggles `aria-expanded`; panel toggles `hidden`.
- Panel has `role="region"` + `aria-labelledby`.
- Keyboard: Enter/Space toggles; focus-visible outline uses tokens.

## Real cases

- FAQ sections, collapsible info blocks, mobile content groups.
