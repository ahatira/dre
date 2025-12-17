# Table (Molecule)

Data display table with sorting, selection, and responsive variants for structured real estate information.

## Overview

The Table molecule provides a flexible and accessible way to display tabular data with support for:

- **Sorting**: Clickable column headers (requires JavaScript)
- **Selection**: Visual indication of selected rows
- **Responsive**: Horizontal scroll or stacked mobile layout
- **Variants**: Striped rows, hover effects, borders, compact spacing
- **Accessibility**: Proper semantic HTML (`<table>`, `<thead>`, `<tbody>`, `scope`, `aria-sort`)

## Usage

### Basic Table

```twig
{% include '@components/table/table.twig' with {
  caption: 'Commercial Properties',
  headers: [
    { key: 'property', label: 'Property Name', sortable: true },
    { key: 'location', label: 'Location', sortable: true },
    { key: 'price', label: 'Price', sortable: true, numeric: true }
  ],
  rows: [
    {
      id: '1',
      cells: ['Central Office', 'Paris 8e', '€4.2M'],
      selected: false,
      disabled: false
    }
  ]
} only %}
```

### Surface Table (Property Detail)

```twig
{% include '@components/table/table.twig' with {
  caption: 'Available Units',
  headers: [
    { key: 'lot', label: 'Lot', sortable: true },
    { key: 'floor', label: 'Floor', sortable: true, numeric: true },
    { key: 'surface', label: 'Surface (m²)', sortable: true, numeric: true },
    { key: 'status', label: 'Status', sortable: false }
  ],
  rows: [...],
  striped: true,
  hover: true
} only %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `headers` | `array` | `[]` | Column headers with `{key, label, sortable, numeric, sticky}` |
| `rows` | `array` | `[]` | Data rows with `{id, cells, selected, disabled}` |
| `caption` | `string` | `null` | Table caption for accessibility |
| `striped` | `boolean` | `true` | Alternate row background colors |
| `hover` | `boolean` | `true` | Show hover effect on rows |
| `bordered` | `boolean` | `false` | Add borders to all cells |
| `compact` | `boolean` | `false` | Reduce padding for denser display |
| `responsive` | `boolean` | `true` | Horizontal scroll on mobile |
| `stacked` | `boolean` | `false` | Card-like layout on mobile (incompatible with responsive) |
| `attributes` | `object` | - | Drupal attributes object |

## Variants

### Striped (`striped: true`)

Alternating row backgrounds for improved readability.

### Hover (`hover: true`)

Highlight rows on mouse hover for better interactivity.

### Bordered (`bordered: true`)

Add vertical borders between cells for clear separation.

### Compact (`compact: true`)

Reduce padding for displaying more data in less space.

### Responsive (`responsive: true`)

Enable horizontal scrolling on mobile devices to preserve tabular layout. Best for tables with many columns.

### Stacked (`stacked: true`)

Transform into card-like stacked layout on mobile with `data-label` prefixes. Best for tables with few columns where row context is important.

## Accessibility

- ✅ Semantic HTML: `<table>`, `<thead>`, `<tbody>`, `<th>`, `<td>`
- ✅ `scope="col"` on all header cells
- ✅ `<caption>` for table description
- ✅ `aria-sort` on sortable columns (ascending/descending/none)
- ✅ Keyboard navigable sort buttons with `aria-label`
- ✅ Focus-visible indicators on interactive elements
- ✅ `data-label` for mobile stacked variant (screen reader context)

## JavaScript Behavior (Optional)

The table includes sortable column headers with `data-sort` attributes. Implement sorting with:

```js
document.querySelectorAll('.ps-table__sort-button').forEach(button => {
  button.addEventListener('click', (e) => {
    const header = e.target.closest('.ps-table__header');
    const key = header.getAttribute('data-sort');
    // Implement sorting logic
    // Update aria-sort attribute
    // Toggle --sorted-asc / --sorted-desc classes
  });
});
```

## Design Tokens Used

### Typography

- `--font-sans`, `--size-305` (14px), `--font-weight-400/600/700`

### Colors

- Headers: `--gray-50` (background), `--gray-900` (text)
- Body: `--white` (background), `--gray-700` (text)
- Borders: `--gray-300`
- States: `--gray-50` (hover/striped), `--primary-subtle` (selected)

### Spacing

- Default: `--size-3` (12px) padding-y, `--size-4` (16px) padding-x
- Compact: `--size-2` (8px) padding-y, `--size-3` (12px) padding-x

### Borders

- `--border-size-1` (1px) row borders
- `--border-size-2` (2px) header bottom border

### Focus

- `--border-size-2` outline width
- `--secondary` outline color

## Related Components

- **Badge**: For status indicators in cells
- **Button**: For action buttons in cells
- **Surface Table Row** (organism): Specialized row component for property units

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile responsive with `-webkit-overflow-scrolling: touch`
- CSS nesting support (PostCSS transforms for older browsers)

## Notes

- **Responsive vs Stacked**: These variants are mutually exclusive. Use `responsive: true` (default) for horizontal scroll, or `stacked: true` for mobile cards.
- **Numeric Columns**: Set `numeric: true` in header to right-align cells.
- **Sorting**: Visual states (icons, classes) are provided, but JavaScript implementation is required for actual sorting logic.
- **Row States**: Use `selected: true` for highlighted rows, `disabled: true` for inactive rows.
- **Border Radius**: Table uses sharp corners by default (`border-radius: 0`) following project standards.

## References

- [WAI-ARIA: Table](https://www.w3.org/WAI/ARIA/apg/patterns/table/)
- [MDN: HTML Table](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/table)
- Specs: `docs/design/pages/property-detail/surface-table.md`
