# Collapse

Collapsible disclosure element with trigger button and expandable panel. Provides single-item show/hide behavior with ARIA support.

## Purpose

The Collapse component is an atom-level disclosure pattern that manages the visibility of a single content panel. It's the foundational building block for accordion and other collapsible UI patterns.

## Usage

**When to use:**
- Single expandable/collapsible content sections
- FAQs, help text, or optional details
- Building block for accordion collections (multiple collapses orchestrated together)
- Progressive disclosure patterns

**When not to use:**
- For tabs or other mutually exclusive content switching (use tabs component)
- For dropdown menus (use dropdown component)
- For modal dialogs (use modal component)

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string` | required | Unique identifier for panel/trigger ARIA linkage |
| `title` | `string` | required | Trigger button text |
| `content` | `string` | `null` | Raw HTML content for panel |
| `expanded` | `boolean` | `false` | Initial expanded state |
| `variant` | `string` | `null` | Visual style variant (`primary`, `secondary`, `success`, `warning`, `danger`, `info`, `dark`, `light`) |
| `trigger_tag` | `string` | `'button'` | HTML tag for trigger (e.g., `'h3'` for semantic heading) |
| `classes` | `array` | `[]` | Additional CSS classes |
| `attributes` | `object` | `{}` | Additional HTML attributes |

## BEM Structure

```
.ps-collapse                # Root container
  .ps-collapse__trigger     # Button/trigger element
  .ps-collapse__title       # Title text
  .ps-collapse__icon        # Chevron icon (CSS-generated)
  .ps-collapse__panel       # Collapsible panel (role="region")
  .ps-collapse__content     # Panel content wrapper

State Classes (Bootstrap-inspired):
  .is-collapsing            # Transition state (opening or closing)
  .is-expanded              # Fully expanded state (open)
```

## Design Tokens

### Three-Layer CSS Variables System

**PS Theme** uses a Bootstrap 5-inspired three-layer variable system for maximum flexibility:

#### Layer 1: Root Primitives (Global)

Defined in `source/props/*.css`:

```css
/* Base values (rarely change) */
--border-size-15: 1.5px;
--size-4: 1rem;
--size-6: 1.5rem;
--size-8: 2rem;
--font-size-2: 1rem;
--font-size-3: 1.25rem;
--gray-300: hsl(216, 12%, 84%);
--gray-600: hsl(215, 14%, 34%);
--gray-900: hsl(221, 39%, 11%);
--primary: var(--green-600);
--secondary: var(--purple-600);
```

#### Layer 2: Component-Scoped Variables (Defaults)

Defined in `collapse.css`:

```css
/* Container */
--ps-collapse-bg: transparent;
--ps-collapse-border-width: var(--border-size-15);
--ps-collapse-border-color: var(--gray-300);

/* Trigger */
--ps-collapse-trigger-padding-y: var(--size-6);
--ps-collapse-trigger-padding-x: 0;
--ps-collapse-trigger-bg: transparent;
--ps-collapse-trigger-bg-hover: var(--gray-100);

/* Title */
--ps-collapse-title-font-family: var(--font-sans);
--ps-collapse-title-font-weight: var(--font-weight-400);
--ps-collapse-title-font-size: var(--font-size-3);
--ps-collapse-title-line-height: var(--leading-6);
--ps-collapse-title-color: var(--gray-900);

/* Icon */
--ps-collapse-icon-size: var(--size-8);
--ps-collapse-icon-spacing: var(--size-4);

/* Content */
--ps-collapse-content-padding-top: var(--size-4);
--ps-collapse-content-padding-bottom: var(--size-6);
--ps-collapse-content-font-size: var(--font-size-2);
--ps-collapse-content-line-height: var(--leading-6);
--ps-collapse-content-color: var(--gray-600);

/* Focus */
--ps-collapse-focus-ring-width: var(--focus-ring-width);
--ps-collapse-focus-ring-color: var(--focus-ring-color);

/* Transitions */
--ps-collapse-transition-duration: var(--duration-fast);
--ps-collapse-transition-timing: var(--ease-out-3);
--ps-collapse-panel-transition-duration: var(--duration-normal);

#### Layer 3: Context Overrides (Runtime Customization)

Override at any level without modifying CSS:

```css
/* Example: Custom collapse in sidebar */
.sidebar .ps-collapse {
  --ps-collapse-title-color: var(--purple-600);
  --ps-collapse-bg: var(--gray-50);
}
```

```javascript
// Runtime override via JavaScript
document.querySelector('.ps-collapse').style.setProperty('--ps-collapse-title-font-size', '1.5rem');
```

### Benefits

- **Cascade Control**: Override variables at any level (root, component, context)
- **No Specificity Wars**: No `!important` needed
- **Easy Theming**: Change one variable to affect all instances
- **DevTools Friendly**: Inspect computed values in browser
- **Performance**: CSS variables resolve at paint time, not parse time
```

## Accessibility

- **Keyboard Navigation:**
  - `Enter` or `Space`: Toggle expanded/collapsed state
  - `Tab`: Focus moves to next focusable element

- **Screen Readers:**
  - `aria-expanded`: Announces current state (true/false)
  - `aria-controls`: Links trigger to panel via ID
  - `role="region"`: Identifies panel as landmark
  - `aria-labelledby`: Links panel back to trigger for context
  - `hidden` attribute: Properly hides collapsed content from AT

- **Focus Management:**
  - Visible focus indicator on trigger (`:focus-visible`)
  - Focus remains on trigger after toggle (no focus trap)

## Atomic Composition

**Collapse = Element (Atom)**

Used by:
- `@collections/accordion/accordion.twig` (orchestrates multiple collapses with single-open behavior)

## JavaScript Behavior

**File:** `collapse.js`

**Bootstrap-Inspired State Management:**
- Uses `.is-collapsing` class during transition (CSS animates height)
- Uses `.is-expanded` class when fully open
- Measures `scrollHeight` for smooth height transitions
- `transitionend` event triggers state class updates

**Functionality:**
- Click and keyboard toggle (Enter/Space)
- ARIA attribute management (`aria-expanded`, `hidden`)
- Custom events for external coordination:
  - `collapse:show` - Dispatched immediately when opening starts (Bootstrap pattern)
  - `collapse:shown` - Dispatched when opening completes (after transition)
  - `collapse:hide` - Dispatched immediately when closing starts (Bootstrap pattern)
  - `collapse:hidden` - Dispatched when closing completes (after transition)
  - `collapse:external-toggle` - Listens for external control (e.g., from accordion)

**Integration:**
- Drupal behavior pattern with `once()` for re-initialization prevention
- Event bubbling allows parent components (accordion) to coordinate multiple collapses
- Respects `prefers-reduced-motion` (skips animations when requested)

## Examples

### Basic Usage

```twig
{% include '@elements/collapse/collapse.twig' with {
  id: 'faq-1',
  title: 'What are your office hours?',
  content: 'Our real estate offices are open Monday through Friday, 9 AM to 6 PM, and Saturday 10 AM to 4 PM.',
  expanded: false
} only %}
```

### Multiple Items (Raw HTML)

```twig
{% for item in faqs %}
  {% include '@elements/collapse/collapse.twig' with {
    id: 'faq-' ~ loop.index,
    title: item.question,
    content: item.answer|raw,
    expanded: loop.first
  } only %}
{% endfor %}
```

### Rich HTML Content

```twig
{% include '@elements/collapse/collapse.twig' with {
  id: 'property-details',
  title: 'Property Features',
  content: '<p>This luxury apartment includes <strong>3 bedrooms</strong>, 2 bathrooms, modern kitchen, and private balcony.</p>',
} only %}
```

## Notes

- **Single Responsibility:** Collapse manages ONE item's expand/collapse behavior
- **Coordination:** For single-open (accordion) behavior, use the Accordion collection which orchestrates multiple Collapse instances
- **Chevron Icon:** Generated via CSS (data URI SVG) for performance and theming simplicity
- **Content Input:** Supports `content` (raw HTML) only
