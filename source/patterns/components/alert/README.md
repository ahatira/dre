# Alert Component

Semantic alert with 8 color variants and optional dismissal.

## Purpose

The Alert component is a molecule-level feedback pattern for displaying important messages, notifications, warnings, or confirmations to users. It supports free HTML content and optional dismiss functionality.

## Usage

**When to use:**
- System notifications (success, error, warning, info)
- Contextual feedback after user actions
- Important announcements or status updates
- Real estate notifications (new listings, offer status, payment reminders)

**When not to use:**
- Promotional banners (use dedicated banner component)
- Form field validation (use inline feedback)
- Persistent navigation or UI elements
- Modal dialogs requiring user interaction (use modal component)

## Component Props

| Prop | Type | Options | Default | Description |
|------|------|---------|---------|-------------|
| `variant` | `string` | `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark` | `primary` | Semantic variant with appropriate color scheme |
| `content` | `string` | any (HTML) | `''` | Free HTML content for alert body |
| `dismissible` | `boolean` | `true`, `false` | `false` | Show close button with dismiss behavior |
| `rounded` | `boolean` | `true`, `false` | `false` | Apply border radius |
| `attributes` | `object` | any | `{}` | Drupal attributes for root element |

## BEM Structure

```
ps-alert                         # Root element (single wrapper)
├── ps-alert--primary            # Primary variant
├── ps-alert--secondary          # Secondary variant
├── ps-alert--success            # Success variant
├── ps-alert--danger             # Danger variant
├── ps-alert--warning            # Warning variant
├── ps-alert--info               # Info variant
├── ps-alert--light              # Light variant
├── ps-alert--dark               # Dark variant
├── ps-alert--dismissible        # Has close button (adds right padding)
│
└── ps-alert__close              # Close button (when dismissible)
```

## Utility Classes

```
ps-alert-link                    # Darker/bolder links within alerts
ps-alert-heading                 # Larger, bold headings within alerts (h4)
```

## 8 Semantic Variants

| Variant | Use Case | Color Scheme |
|---------|----------|--------------|
| **primary** (default) | Brand-specific highlights, featured content | Light green background (BNP brand), green border, dark text |
| **secondary** | Secondary brand messaging, promotions | Light pink/magenta background (BNP brand), magenta border, dark text |
| **success** | Confirmations, completed actions | Light green background, green border, dark text |
| **danger** | Errors, critical issues (assertive ARIA) | Light red background, red border, dark text |
| **warning** | Cautions, attention needed (assertive ARIA) | Light yellow background, yellow border, dark text |
| **info** | General information, tips, notifications | Light blue background, blue border, dark text |
| **light** | Subtle alerts, light backgrounds | White background, light gray border, dark text |
| **dark** | Dark theme alerts, high contrast | Dark gray background, darker border, white text |

## Design Tokens

### Component-Scoped Variables (Layer 2)

This component uses the **Three-Layer CSS Variables System**:

```css
/* Layout & Spacing */
--ps-alert-padding-y: var(--size-4);
--ps-alert-padding-x: var(--size-4);
--ps-alert-margin-bottom: var(--size-4);
--ps-alert-border-radius: var(--radius-2);
--ps-alert-border-width: var(--border-size-1);

/* Typography */
--ps-alert-font-family: var(--font-sans);
--ps-alert-font-size: var(--font-size-1);
--ps-alert-line-height: var(--leading-normal);

/* Colors (transparent defaults, overridden by variants) */
--ps-alert-bg: transparent;
--ps-alert-color: inherit;
--ps-alert-border-color: transparent;
--ps-alert-link-color: inherit;

/* Close Button */
--ps-alert-close-padding: var(--size-3);
--ps-alert-close-color: inherit;
--ps-alert-close-opacity: 0.5;
--ps-alert-close-hover-opacity: 0.75;
--ps-alert-close-focus-opacity: 1;

/* Transitions */
--ps-alert-transition: opacity var(--duration-fast) var(--ease-out-2);
--ps-alert-animation-duration: var(--duration-fast);
```

### Referenced Primitives (Layer 1)

From `source/props/*.css`:

- **Typography**: `--font-sans`, `--font-size-1`, `--font-size-3`, `--font-size-4`, `--font-weight-600`, `--leading-normal`, `--leading-snug`
- **Colors**: 
  - Semantic: `--primary`, `--secondary`, `--success`, `--danger`, `--warning`, `--info`
  - Backgrounds: `--green-50`, `--green-100`, `--purple-50`, `--red-50`, `--yellow-50`, `--blue-50`, `--gray-50`, `--gray-900`
  - Borders: `--gray-300`, `--gray-800`
  - Special: `--warning-text` (for warning variant text contrast)
- **Spacing**: `--size-2`, `--size-3`, `--size-4`, `--size-8`
- **Borders**: `--radius-2`, `--border-size-1`, `--border-size-2`
- **Transitions**: `--duration-fast`, `--ease-out-2`

## Atomic Composition

**Alert = Component (Molecule)**

Composes (optional):
- `@elements/button/button.twig` (for close button when dismissible)

**Content model:**
- Free HTML content via `content` prop
- Users can include any HTML: headings, paragraphs, links, icons, lists, etc.
- Use utility classes (`.ps-alert-link`, `.ps-alert-heading`) for styling

## Usage Examples

### Basic Alerts

```twig
{# Simple primary alert #}
{% include '@components/alert/alert.twig' with {
  variant: 'primary',
  content: 'A simple <strong>primary</strong> alert—check it out!'
} %}

{# Success alert with link #}
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  content: 'Offer accepted! <a href="#" class="ps-alert-link">View contract</a> for details.'
} %}

{# Warning alert with dismissible #}
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  content: '<strong>Holy guacamole!</strong> You should check in on some of those fields below.',
  dismissible: true
} %}
```

### Real Estate Alerts

```twig
{# Property notification #}
{% include '@components/alert/alert.twig' with {
  variant: 'info',
  content: 'A new commercial property matching your criteria has been added in downtown. <a href="#" class="ps-alert-link">View listing</a>'
} %}

{# Offer accepted #}
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  content: '<strong>Offer Accepted!</strong> Your offer on 123 Main Street has been accepted. Contract details will arrive within 24 hours.',
  dismissible: true
} %}

{# Insurance expiring #}
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  content: '<strong>Insurance Renewal:</strong> Your property insurance expires in 30 days. <a href="#" class="ps-alert-link">Renew now</a> to maintain coverage.'
} %}

{# Payment failed #}
{% include '@components/alert/alert.twig' with {
  variant: 'danger',
  content: '<strong>Payment Failed:</strong> Monthly mortgage payment could not be processed. <a href="#" class="ps-alert-link">Update payment method</a> immediately.'
} %}
```

### Alerts with Headings

```twig
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  content: '
    <h4 class="ps-alert-heading">Property Inspection Complete!</h4>
    <p>Aww yeah, you successfully completed the inspection for 123 Main Street. The detailed report has been uploaded to your dashboard.</p>
    <hr>
    <p style="margin-bottom: 0;">Whenever you need to, proceed with the final offer or schedule a follow-up viewing.</p>
  '
} %}
```

### Alerts with Icons (Optional)

```twig
{# Icon as part of free content #}
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  content: '<span data-icon="check" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Viewing confirmed for tomorrow at 2 PM'
} %}

{# Multiple icons/content #}
{% include '@components/alert/alert.twig' with {
  variant: 'primary',
  content: '<span data-icon="infos" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span><strong>BNP Paribas RealEstate</strong> Featured announcement'
} %}
```

### Contextual Theming (Layer 3)

Override component variables for specific contexts:

```css
/* Dark mode adjustments */
.dark-theme .ps-alert {
  --ps-alert-close-opacity: 0.7;
  --ps-alert-close-hover-opacity: 1;
}

/* Compact mobile alerts */
@media (max-width: 640px) {
  .mobile-alerts .ps-alert {
    --ps-alert-padding-y: var(--size-3);
    --ps-alert-padding-x: var(--size-3);
    --ps-alert-font-size: var(--font-size-0);
  }
}

/* Sidebar alerts with custom colors */
.sidebar .ps-alert--info {
  --ps-alert-bg: var(--info-subtle);
  --ps-alert-color: var(--info-text);
}
```

## Accessibility

### ARIA Roles & Live Regions
- **Danger/Warning alerts**: `role="alert"` with `aria-live="assertive"` for immediate announcement (critical errors)
- **Other variants**: `role="status"` with `aria-live="polite"` for non-intrusive updates
- Screen readers announce content automatically when alert appears

### Keyboard & Focus
- Close button keyboard accessible (Tab to focus, Enter/Space to activate)
- Focus indicator visible with `outline` on `:focus-visible`
- Close button positioned absolutely in top-right corner

### Visual Accessibility
- **Color contrast**: All variants meet WCAG AA standards (4.5:1 minimum for text)
- **Don't rely on color alone**: Use descriptive text, headings, and icons (aria-hidden) to convey meaning
- **Close button**: Descriptive `aria-label="Close alert"`
- **Light variant**: Tested for sufficient contrast with dark text

### Best Practices
- Critical danger/warning alerts should NOT be dismissible (users must address them)
- Include descriptive headings (`.ps-alert-heading`) for context
- Keep messages concise and actionable
- Use `.ps-alert-link` class for emphasized links

## Interactive Behavior

The close button functionality is handled by `alert.js` (Drupal behaviors):
- Fade-out animation on dismissal (`.is-closing` class applied)
- Element removal after animation completes
- `once()` prevents multiple event bindings

## Browser Support

Modern browsers supporting:
- CSS Nesting (`&` syntax via PostCSS)
- CSS Custom Properties (CSS Variables)
- Flexbox & absolute positioning
- `aria-live` & `role` attributes

## Best Practices

### DO ✅
- Use semantic variants matching message intent
- Include descriptive headings (`.ps-alert-heading`) for complex alerts
- Use `.ps-alert-link` class for emphasized links
- Keep messages concise and actionable
- Make non-critical alerts dismissible
- Use HTML content for rich formatting (paragraphs, lists, headings)
- Always use design tokens (no hardcoded values)
- Test color contrast for accessibility

### DON'T ❌
- Don't use alerts for promotional banners (use dedicated banner component)
- Don't make critical danger/warning alerts dismissible
- Don't use alerts for form field validation (use inline feedback)
- Don't hardcode colors or sizes
- Don't rely on color alone (use text + headings)
- Don't overwhelm users with too many alerts simultaneously
