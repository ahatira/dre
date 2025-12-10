# Textarea (Atom)

Native `<textarea>` without label. External labels are handled upstream via `for`/`id` and this atom exposes validation states and semantic form attributes.

## Usage

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'property_details',
  id: 'property-details',
  placeholder: 'Decrivez la propriete (localisation, surface, prix)...',
  rows: 6,
  state: null,
  required: true
} only %}
```

Example with external label and error handling:

```twig
<label for="property-details">Votre recherche immobiliere</label>
{% include '@elements/textarea/textarea.twig' with {
  name: 'property_details',
  id: 'property-details',
  value: 'Maison, 200m2, region de Loire, budget 400k',
  rows: 5,
  state: 'success',
  required: true
} only %}
```

## Props

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | string | `message` | Name attribute (form submission) |
| `id` | string | Auto-generated | ID attribute (pair with external label) |
| `value` | string | `''` | Initial textarea content |
| `placeholder` | string | `Decrivez votre besoin immobilier...` | Placeholder text |
| `rows` | number | `4` | Native `rows` attribute (controls height) |
| `disabled` | boolean | `false` | Disabled state (adds `aria-disabled="true"`) |
| `required` | boolean | `false` | Required state (adds `aria-required="true"`) |
| `state` | string \| null | `null` | Validation state: `error`, `success`, `warning` |
| `attributes` | Drupal Attribute | `create_attribute()` | Attributes object for advanced Drupal integration |

## BEM

- Block: `ps-textarea`
- Modifiers:
  - `ps-textarea--disabled` (read-only state)
  - `ps-textarea--error` (validation failure)
  - `ps-textarea--success` (validation success)
  - `ps-textarea--warning` (warning message)

## CSS Variables (Layer Architecture)

### Layer 1: Global Tokens (from `source/props/`)

Inherited from global design tokens:
- **Colors**: `--text-primary`, `--text-secondary`, `--text-disabled`, `--danger`, `--success`, `--warning`
- **Typography**: `--font-sans`, `--font-size-1`, `--font-weight-400`, `--leading-6`
- **Spacing**: `--size-2`, `--size-4`, `--size-12`
- **Borders**: `--border-default`, `--border-disabled`, `--border-size-2`
- **Animations**: `--duration-fast`, `--ease-3`

### Layer 2: Component-Scoped Defaults

Component-level CSS variables that control appearance:

| Variable | Default | Purpose |
| --- | --- | --- |
| `--ps-textarea-width` | `100%` | Container width |
| `--ps-textarea-min-height` | `var(--size-12)` | Minimum height (4 rows) |
| `--ps-textarea-padding-block` | `var(--size-2)` | Vertical padding |
| `--ps-textarea-padding-inline` | `var(--size-4)` | Horizontal padding |
| `--ps-textarea-font-family` | `var(--font-sans)` | Textarea font |
| `--ps-textarea-font-size` | `var(--font-size-1)` | Base font size |
| `--ps-textarea-bg` | `var(--white)` | Background color |
| `--ps-textarea-color` | `var(--text-primary)` | Text color |
| `--ps-textarea-border-color` | `var(--border-default)` | Border color |
| `--ps-textarea-border-radius` | `0` | Radius (per maquette) |

### Layer 3: Context Overrides (Modifiers)

Modifiers override Layer 2 variables:

```css
/* Error state */
.ps-textarea--error {
  --ps-textarea-border-color: var(--danger);
  --ps-textarea-focus-border-color: var(--danger);
}

/* Success state */
.ps-textarea--success {
  --ps-textarea-border-color: var(--success);
  --ps-textarea-focus-border-color: var(--success);
}

/* Warning state */
.ps-textarea--warning {
  --ps-textarea-border-color: var(--warning);
  --ps-textarea-focus-border-color: var(--warning);
}
```

## States

### Validation States

- **`state: null`** (default): Standard border, focus-visible changes to text-primary
- **`state: 'error'`**: Border changes to danger red (validates form failure)
- **`state: 'success'`**: Border changes to success teal (validates form success)
- **`state: 'warning'`**: Border changes to warning yellow (alerts user to caution)

### Interactive States

- **`:hover`**: Border color softens (when not disabled)
- **`:focus-visible`**: Border color changes based on validation state (WCAG 2.2 AA)
- **`:disabled` or `.ps-textarea--disabled`**: Grayed out, opacity 0.7, cursor: not-allowed

## Accessibility

- Always pair with external `<label for="id">` element (WCAG required)
- `disabled` state adds `aria-disabled="true"`
- `required` state adds `aria-required="true"`
- `state: 'error'` adds `aria-invalid="true"`
- Focus visible is clearly visible (border color change, no shadow)
- Placeholder is NOT a substitute for label

**Keyboard Navigation**:
- Tab: Focus/unfocus textarea
- Shift+Tab: Navigate backward
- Arrow keys: Move within content
- Ctrl+A: Select all

## Design Notes

**Per Maquette**:
- Border-radius: `0` (sharp corners)
- Border: `2px` solid (single border, no double outline)
- Box-shadow: `none` (clean, minimal look)
- Focus indicator: Border color changes only
- Resize: Vertical only (user can adjust height, not width)

**Real Estate Context**:
Use in property search forms, agent notes, buyer/seller communication:
- "Property details" (agent listing notes)
- "Search criteria" (buyer preferences)
- "Special requests" (financing, timeline)

## Examples

### Minimal

```twig
{% include '@elements/textarea/textarea.twig' only %}
```

### With All Options

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'request',
  id: 'client-request',
  placeholder: 'Expliquez votre besoin...',
  value: '',
  required: true,
  rows: 6,
  state: null
} only %}
```

### With Error Message

```twig
<div class="form-field">
  <label for="request-id">Your Request *</label>
  {% include '@elements/textarea/textarea.twig' with {
    id: 'request-id',
    name: 'request',
    state: form.errors.request ? 'error' : null
  } only %}
  {% if form.errors.request %}
    <span class="error-message" role="alert">{{ form.errors.request }}</span>
  {% endif %}
</div>
```

## Testing

### Visual Testing
- Default state (focus, hover, blur)
- Validation states (error, success, warning)
- Disabled state
- Different `rows` values (2, 4, 6, 10)

### Accessibility Testing
- Keyboard navigation (Tab, Shift+Tab, arrows)
- Focus visible indicator
- Screen reader (label association, aria-invalid, aria-required)
- Contrast (WCAG AA minimum 4.5:1)
