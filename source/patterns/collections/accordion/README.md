# Accordion

**Type**: Collection (Organism)

## Purpose

Orchestrates multiple Collapse elements with optional single-open coordination. Manages group behavior when only one item should be expanded at a time.

## Usage

**When to use:**
- FAQ lists with automatic single-open behavior
- Progressive disclosure of multiple related sections
- Grouped content where only one section should be visible at once

**When not to use:**
- Single collapsible item (use Collapse atom directly)
- Navigation (use menu/tabs components)
- When all items should be independently toggleable without coordination (use multiple Collapse atoms)

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | `array` | required | Array of collapse items: `[{ id?, title, content?, text?, expanded? }]` |
| `single_open` | `boolean` | `true` | Only one item open at a time (coordination) |
| `variant` | `string` | `'default'` | Visual style variant (`default`, `flush`) |
| `attributes` | `object` | `{}` | Drupal attributes for root element |

### Item Props (passed to Collapse)

Each item supports all Collapse props:

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `id` | `string` | optional | Unique ID (auto-generated if omitted) |
| `title` | `string` | **required** | Section heading text |
| `content` | `string` | optional | Raw HTML content (backward-compat) |
| `text` | `string` | optional | Text rendered via text atom (atomic composition) |
| `text_format` | `string` | optional | Format variant for text atom |
| `expanded` | `boolean` | optional | Initial expanded state (default: `false`) |

## BEM Structure

```
.ps-accordion                 # Root container
  .ps-accordion__item         # Collapse element wrapper (with data-accordion-item)
    .ps-collapse              # Collapse atom (see Collapse README for structure)

Modifiers:
  .ps-accordion--flush        # Reduced padding variant
```

## Design Tokens

### Accordion-Level (Layer 2)

```css
/* No accordion-specific tokens - orchestration layer only */
/* Individual items styled via Collapse component tokens */
```

## Accessibility

- Accordion itself is a coordination container
- All accessibility handled by Collapse atom (see Collapse README)
- Ensure `single_open` behavior announced clearly in surrounding context when needed

## Atomic Composition

**Accordion = Collection/Organism**

Composes:
- `@elements/collapse/collapse.twig` (required, multiple instances)

Collapse composes:
- `@elements/text/text.twig` (optional, for panel content)

## JavaScript Behavior

**File:** `accordion.js`

**Functionality:**
- Listens for `collapse:show` events from child Collapse elements
- When `single_open` is true, dispatches `collapse:external-toggle` to close other items
- No direct DOM manipulation - delegates to Collapse behaviors

**Integration:**
- Drupal behavior pattern with `once()`
- Event-driven coordination (loose coupling)

## Examples

### Basic Usage

```twig
{% include '@collections/accordion/accordion.twig' with {
  items: [
    { title: 'Section 1', text: 'Content here' },
    { title: 'Section 2', text: 'More content' }
  ],
  single_open: true
} only %}
```

### Multiple Open (No Coordination)

```twig
{% include '@collections/accordion/accordion.twig' with {
  items: [
    { title: 'Feature A', text: 'Details...' },
    { title: 'Feature B', text: 'Details...' }
  ],
  single_open: false
} only %}
```

### Flush Variant (Reduced Padding)

```twig
{% include '@collections/accordion/accordion.twig' with {
  items: faqs,
  variant: 'flush'
} only %}
```

## Architecture Notes

This component represents the **separation of concerns** in Atomic Design:

- **Collapse (Atom):** Single-item expand/collapse behavior + visual styling
- **Accordion (Collection):** Multi-item coordination + group orchestration

This enables:
- Collapse to be used independently (single disclosures)
- Accordion to focus solely on coordination logic
- Easier testing and maintenance (single responsibility)

## Notes

- Accordion is a **thin orchestration layer** - minimal CSS/markup
- All visual styling delegated to Collapse component
- Event-driven coordination allows Collapse to remain decoupled
- Backward compatible with legacy `content` prop via Collapse support
