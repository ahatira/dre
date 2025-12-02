# Alert Component

Semantic status message component with variant-specific colors, icons, and optional dismissal functionality using the Three-Layer CSS Variables System.

## Component Props

| Prop | Type | Options | Default | Description |
|------|------|---------|---------|-------------|
| `variant` | string | `'info'`, `'success'`, `'warning'`, `'danger'`, `'primary'`, `'secondary'` | `'info'` | Semantic variant with appropriate color and icon |
| `title` | string | any | `''` | Optional title text |
| `message` | string | any (HTML supported) | `''` | Message content |
| `icon` | boolean | `true`, `false` | `true` | Show icon |
| `dismissible` | boolean | `true`, `false` | `false` | Show close button |
| `compact` | boolean | `true`, `false` | `false` | Reduced padding for dense layouts |
| `attributes` | object | any | - | Additional HTML attributes |

## BEM Structure

```
ps-alert                         # Root element
├── ps-alert--info               # Info variant (default)
├── ps-alert--success            # Success variant
├── ps-alert--warning            # Warning variant
├── ps-alert--danger             # Danger variant
├── ps-alert--primary            # Primary brand variant
├── ps-alert--secondary          # Secondary brand variant
├── ps-alert--dismissible        # Has close button
├── ps-alert--compact            # Compact spacing
│
├── ps-alert__icon               # Icon container (data-icon)
├── ps-alert__content            # Content wrapper
│   ├── ps-alert__title          # Title element (optional)
│   └── ps-alert__message        # Message element
└── ps-alert__close              # Close button (when dismissible)
```

## Semantic Variants

| Variant | Use Case | Icon |
|---------|----------|------|
| **info** (default) | General information, tips, notifications | info (ⓘ) |
| **success** | Confirmations, completed actions | check (✓) |
| **warning** | Cautions, attention needed | warning (!) |
| **danger** | Errors, critical issues | error (×) |
| **primary** | Brand-specific highlights | info (ⓘ) |
| **secondary** | Secondary brand messaging | info (ⓘ) |

## Design Tokens

### Component-Scoped Variables (Layer 2)

This component uses the **Three-Layer CSS Variables System**:

```css
/* Layout & Spacing */
--ps-alert-padding: var(--size-4);
--ps-alert-gap: var(--size-3);
--ps-alert-border-radius: var(--radius-2);
--ps-alert-border-width: var(--size-1);

/* Typography */
--ps-alert-font-family: var(--font-sans);
--ps-alert-font-size: var(--font-size-1);
--ps-alert-line-height: var(--leading-normal);
--ps-alert-title-font-weight: var(--font-weight-600);

/* Colors (default: info) */
--ps-alert-bg: var(--info);
--ps-alert-color: var(--text-inverse);
--ps-alert-border-color: var(--info-hover);
--ps-alert-link-color: var(--text-inverse);

/* Icon */
--ps-alert-icon-size: var(--size-5);
--ps-alert-icon-font-size: var(--font-size-3);
--ps-alert-icon-color: var(--text-inverse);

/* Close Button */
--ps-alert-close-size: var(--size-6);
--ps-alert-close-color: var(--text-inverse);
--ps-alert-close-hover-bg: rgba(0, 0, 0, 0.1);
--ps-alert-close-active-bg: rgba(0, 0, 0, 0.2);

/* Transitions */
--ps-alert-transition: background-color var(--duration-fast) var(--ease-out-2);
--ps-alert-animation-duration: var(--duration-fast);

/* Compact Modifiers */
--ps-alert-compact-padding: var(--size-3);
--ps-alert-compact-gap: var(--size-2);
--ps-alert-compact-icon-size: var(--size-4);
```

### Referenced Primitives (Layer 1)

- **Typography**: `--font-sans`, `--font-size-0`, `--font-size-1`, `--font-size-3`, `--font-size-4`, `--font-weight-600`, `--leading-normal`, `--leading-snug`
- **Colors**: `--info`, `--info-hover`, `--success`, `--success-hover`, `--warning`, `--warning-hover`, `--warning-text`, `--danger`, `--danger-hover`, `--primary`, `--primary-hover`, `--secondary`, `--secondary-hover`, `--text-inverse`
- **Spacing**: `--size-1`, `--size-2`, `--size-3`, `--size-4`, `--size-5`, `--size-6`
- **Borders**: `--radius-1`, `--radius-2`, `--border-size-1`, `--border-size-2`
- **Transitions**: `--duration-fast`, `--ease-out-2`

### Icons System
- Icons rendered via centralized `[data-icon]` system in `icons.css`
- Icon mapping: `info` → infos, `success` → check, `warning` → help, `danger` → close
- No component-specific icon mappings (centralized system)

## Usage Examples

### Basic Real Estate Alerts

```twig
{# Property notification #}
{% include '@components/alert/alert.twig' with {
  variant: 'info',
  title: 'New Property Listing',
  message: 'A commercial property matching your criteria has been added in downtown.'
} %}

{# Offer accepted #}
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  title: 'Offer Accepted',
  message: 'Your offer on 123 Main Street has been accepted by the seller.',
  dismissible: true
} %}

{# Document expiring #}
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  title: 'Insurance Expiring',
  message: 'Your property insurance expires in 30 days. Please renew to avoid gaps.',
  dismissible: true
} %}

{# Payment issue #}
{% include '@components/alert/alert.twig' with {
  variant: 'danger',
  title: 'Payment Failed',
  message: 'Monthly payment could not be processed. Update your payment method.',
} %}
```

### Compact Alerts (Sidebars)

```twig
<div class="sidebar">
  {% include '@components/alert/alert.twig' with {
    variant: 'info',
    message: 'New viewing request received.',
    compact: true,
    dismissible: true
  } %}
</div>
```

### Alerts with HTML Content

```twig
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  title: 'Mortgage Pre-Approval Expiring',
  message: '<p>Your pre-approval expires in 7 days.</p><p><a href="/renew">Renew now</a> to maintain your rate lock.</p>',
  dismissible: true
} %}
```

### Contextual Theming (Layer 3)

Override component variables for specific contexts:

```css
/* Dark theme */
.dark-theme .ps-alert {
  --ps-alert-close-hover-bg: rgba(255, 255, 255, 0.1);
  --ps-alert-close-active-bg: rgba(255, 255, 255, 0.2);
}

/* Compact mobile alerts */
@media (max-width: 640px) {
  .mobile-alerts .ps-alert {
    --ps-alert-padding: var(--size-3);
    --ps-alert-gap: var(--size-2);
  }
}

/* Sidebar alerts */
.sidebar .ps-alert {
  --ps-alert-font-size: var(--font-size-0);
  --ps-alert-icon-size: var(--size-4);
}
```

## Accessibility

### ARIA Roles & Live Regions
- **Danger alerts**: `role="alert"` with `aria-live="assertive"` for immediate announcement
- **Other variants**: `role="status"` with `aria-live="polite"` for non-intrusive updates
- Screen readers announce content automatically when alert appears

### Keyboard & Focus
- Close button keyboard accessible (Tab to focus, Enter/Space to activate)
- Focus indicator visible with `outline` on `:focus-visible`
- Tab order: Close button is last (natural grid order)

### Visual Accessibility
- **Color contrast**: Text meets WCAG AA standards (4.5:1 minimum)
- **Icons**: Marked `aria-hidden="true"` (color and text convey meaning)
- **Close button**: Descriptive `aria-label="Close alert"`
- **Don't rely on color alone**: Icons + text reinforce meaning

### Best Practices
- Critical danger alerts should NOT be dismissible (users must address them)
- Include descriptive titles for context
- Keep messages concise and actionable
- Use semantic HTML in message content

## Interactive Behavior

The close button functionality is handled by `alert.js` (Drupal behaviors):
- Fade-out animation on dismissal
- Element removal after animation completes
- `once()` prevents multiple bindings

## Best Practices

### DO ✅
- Use semantic variants matching message intent
- Include descriptive titles for context
- Keep messages concise and actionable
- Use HTML content for formatting (links, bold, paragraphs)
- Make non-critical alerts dismissible
- Use compact mode in space-constrained layouts
- Always use design tokens (no hardcoded values)

### DON'T ❌
- Don't use alerts for promotional content (use banners)
- Don't make critical danger alerts dismissible
- Don't use alerts for field validation (use inline feedback)
- Don't hardcode colors or sizes
- Don't rely on color alone (icons + text required)
- Don't overwhelm users with too many alerts

## Browser Support

Modern browsers supporting:
- CSS Nesting (`&` syntax via PostCSS)
- CSS Custom Properties (CSS Variables)
- CSS Grid & Flexbox
- `aria-live` & `role` attributes
- `has()` pseudo-class (layout without icon)
