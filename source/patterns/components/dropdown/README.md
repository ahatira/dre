# Dropdown

**Component type**: Molecule  
**Category**: Form / Selector  
**Status**: ✅ Stable

Accessible select dropdown with custom styling, keyboard navigation, and native `<select>` fallback.

**Note**: Current implementation is a simple single-select dropdown. Advanced variants shown in mockups (multiselect with checkboxes, grouped sections, footer buttons) are planned for future releases.

---

## Props

| Prop | Type | Default | Required | Description |
|------|------|---------|----------|-------------|
| `name` | `string` | — | ✅ | Form field name attribute |
| `label` | `string` | — | — | Visible button label (uses selected option if not provided) |
| `placeholder` | `string` | `'Select an option'` | — | Placeholder text when no selection |
| `size` | `'small' \| 'medium' \| 'large'` | `'medium'` | — | Size variant |
| `shape` | `'none' \| 'rounded' \| 'pill'` | `'rounded'` | — | Border radius style |
| `disabled` | `boolean` | `false` | — | Disable dropdown |
| `options` | `array` | `[]` | ✅ | Array of option objects (see below) |
| `attributes` | `object` | — | — | Additional HTML attributes |

### Option object structure

```yaml
label: string        # Display text (required)
value: string        # Form value (required)
selected: boolean    # Mark as selected (default: false)
disabled: boolean    # Disable option (default: false)
```

---

## BEM Structure

```
ps-dropdown                         # Block
  ps-dropdown__button               # Trigger button
  ps-dropdown__label                # Button text
  ps-dropdown__icon                 # Chevron icon
  ps-dropdown__list                 # Overlay list
  ps-dropdown__option               # Option item
  ps-dropdown__native               # Fallback <select>

Modifiers:
  ps-dropdown--small                # Small size
  ps-dropdown--medium               # Medium size (default)
  ps-dropdown--large                # Large size
  ps-dropdown--none                 # No border-radius (sharp corners)
  ps-dropdown--pill                 # Fully rounded ends
  ps-dropdown--disabled             # Disabled state
  ps-dropdown__option--disabled     # Disabled option
```

---

## Design Tokens Used

### Layout & Spacing
- `--size-1` to `--size-6` - Padding, gaps
- `--size-105` - Half-step spacing for small variant
- `--ps-icon-size-16` / `--ps-icon-size-20` - Icon sizes (small/medium-large)
- `--ps-dropdown-min-width-small` / `medium` / `large` - Min widths (180px/220px/260px)

### Typography
- `--ps-font-family-primary` - Button and option text
- `--ps-font-size-sm` / `--font-size-1` / `--font-size-2` - Size variants
- `--ps-font-weight-medium` - Selected option emphasis
- `--leading-normal` - Text line height

### Colors
- `--white` - Background
- `--ps-color-text` - Primary text
- `--ps-color-text-muted` - Disabled text
- `--gray-50` / `100` / `300` / `400` - Borders, hover, selected states
- `--blue-600` - Focus outline
- `--primary` - Selected text color (semantic)

### Effects
- `--radius-2` - Border radius (4px)
- `--shadow-4` - Dropdown overlay shadow
- `--ps-transition-duration-fast` - Hover/focus transitions (0.15s)
- `--ease-3` - Animation easing
- `--layer-40` - Stacking context (z-index)

---

## Usage Examples

### Basic dropdown

```twig
{% include '@components/dropdown/dropdown.twig' with {
  name: 'property_type',
  label: 'Property type',
  options: [
    { label: 'Apartment', value: 'apartment', selected: true },
    { label: 'House', value: 'house' },
    { label: 'Villa', value: 'villa' }
  ]
} %}
```

### With disabled options

```twig
{% include '@components/dropdown/dropdown.twig' with {
  name: 'category',
  placeholder: 'Select category',
  options: [
    { label: 'Available', value: 'available' },
    { label: 'Coming soon', value: 'coming', disabled: true },
    { label: 'Sold out', value: 'sold', disabled: true }
  ]
} %}
```

### Size variants

```twig
{# Small dropdown #}
{% include '@components/dropdown/dropdown.twig' with {
  name: 'filter',
  size: 'small',
  options: [...]
} %}

{# Large dropdown #}
{% include '@components/dropdown/dropdown.twig' with {
  name: 'main_category',
  size: 'large',
  options: [...]
} %}
```

### Disabled state

```twig
{% include '@components/dropdown/dropdown.twig' with {
  name: 'locked_field',
  disabled: true,
  options: [...]
} %}
```

---

## Accessibility

### ARIA Implementation
- Button uses `aria-haspopup="listbox"` and `aria-expanded` to indicate dropdown state
- List has `role="listbox"` for screen reader context
- Options have `role="option"` with `aria-selected` for current selection
- Disabled options use `aria-disabled="true"`

### Keyboard Navigation
- **Space/Enter** - Open dropdown
- **Arrow Down/Up** - Navigate options
- **Home/End** - Jump to first/last option
- **Escape** - Close dropdown and return focus to button
- **Tab** - Close dropdown and move to next focusable element

### Focus Management
- Visible focus indicator on button (`outline: 2px solid --primary`)
- Focus moves to first/selected option when dropdown opens
- Focus returns to button on close
- Focus trapped within dropdown during navigation

### Progressive Enhancement
- Native `<select>` fallback hidden visually but accessible
- Syncs with custom UI on selection
- Triggers native `change` event for form handling

---

## Behavior (JavaScript)

Implemented via `dropdown.js` with Drupal behaviors pattern:

```javascript
Drupal.behaviors.psDropdown = {
  attach(context) {
    // Initialize with once() to prevent re-initialization
    once('ps-dropdown', '[data-dropdown]', context).forEach((element) => {
      const wrapper = new PsDropdownWrapper(element);
      wrapper.init();
    });
  }
};
```

### Features
- Click outside to close
- Escape key closes dropdown
- Full keyboard navigation (arrows, Home, End, Enter, Space)
- Syncs custom UI with native `<select>` value
- Prevents re-initialization on AJAX updates (via `once()`)

---

## Variants

### Sizes
- **Small** (`--small`) - Compact for tight spaces (180px min-width)
- **Medium** (default) - Standard form fields (220px min-width)
- **Large** (`--large`) - Prominent selectors (260px min-width)

### Shapes
- **None** (`--none`) - Sharp corners (border-radius: 0)
- **Rounded** (default) - Standard rounded corners (4px)
- **Pill** (`--pill`) - Fully rounded ends for button-like appearance

### States
- **Default** - Interactive, ready for input
- **Hover** - Border color changes on button hover
- **Focus** - Primary color outline on keyboard focus
- **Expanded** - Chevron rotates 180°, list visible
- **Disabled** - Grayed out, non-interactive
- **Selected** - Option highlighted with primary color background

---

## Integration Notes

### Form Submission
- Native `<select>` receives updates when custom option selected
- Standard `name` attribute for form submission
- Works with Drupal Form API and standard HTML forms

### Styling Customization
- All styles use design tokens - easy to theme
- BEM modifiers for predictable overrides
- Dropdown list has `max-height: 320px` with scroll for long lists

### Performance
- Uses event delegation where possible
- Lazy initialization with `once()` prevents duplicates
- Minimal DOM queries cached in wrapper instance

---

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Keyboard navigation tested in all major browsers
- Screen reader compatible (NVDA, JAWS, VoiceOver)
- Progressive enhancement ensures `<select>` works everywhere

---

## Related Components

- **Button** - Shares focus and sizing tokens
- **Field** - Can be composed with text inputs in forms
- **Checkbox/Radio** - Alternative selection patterns
- **Autocomplete** - Enhanced search-based selection (future)

---

## Future Enhancements

Based on design mockups, the following variants are planned for future releases:

### Multiselect Dropdown
- Checkboxes for each option
- Multiple selection support
- "Select all" / "Clear all" actions
- Selected count indicator

### Grouped Dropdown (Search variant)
- Section headers (e.g., "Section 1", "Section 2")
- Optgroup-style organization
- Visual separators between groups

### Dropdown with Footer Actions
- Action button at bottom of list (e.g., "Apply", "Clear filters")
- Secondary actions in dropdown context
- Composed patterns for filter panels

### Sort Dropdown (Compact variant)
- Minimal inline format: "Sort by : [Selection]"
- Reduced visual weight for secondary controls

---

## Changelog

### 1.0.0 (2025-12-01)
- Initial implementation: Simple single-select dropdown
- Full keyboard navigation (arrows, Home, End, Escape, Enter, Space)
- ARIA compliant with listbox pattern
- Three size variants (small, medium, large)
- Disabled state and disabled options
- Native `<select>` fallback synchronized
- Drupal behaviors with `once()` integration
- Storybook stories: Default, AllSizes, WithDisabledOptions, DisabledDropdown, LongList, InForm
- Pixel perfect adjustments: radius-2, subtle selected state, optimized padding
