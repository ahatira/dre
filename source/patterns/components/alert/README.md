# Alert Component

Semantic alert component with 10 color variants for displaying important feedback messages.

## Purpose

The Alert component is a molecule-level feedback pattern for displaying important messages, notifications, warnings, or confirmations to users. It supports free HTML content, optional leading icons, and dismissal functionality.

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

| Prop         | Type      | Options                                                                 | Default   | Description                                      |
|--------------|-----------|------------------------------------------------------------------------|-----------|--------------------------------------------------|
| `variant`    | `string`  | `default`, `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `gold`, `light`, `dark` | `default` | Semantic variant with appropriate color scheme   |
| `content`    | `string`  | any (HTML)                                                            | `''`      | Free HTML content for alert body                 |
| `icon`       | `string`  | any icon name (without `icon-` prefix)                                | `null`    | Leading icon displayed before content            |
| `dismissible`| `boolean` | `true`, `false`                                                        | `false`   | Show close button with dismiss behavior          |
| `rounded`    | `boolean` | `true`, `false`                                                        | `false`   | Apply border radius                              |
| `attributes` | `object`  | any                                                                   | `{}`      | Drupal attributes for root element               |

## BEM Structure

```
ps-alert                         # Root element (with optional data-icon attribute)
├── ps-alert--default            # Default neutral variant
├── ps-alert--primary            # Primary brand variant (green)
├── ps-alert--secondary          # Secondary brand variant (pink)
├── ps-alert--success            # Success variant (teal)
├── ps-alert--danger             # Danger/error variant (red)
├── ps-alert--warning            # Warning variant (yellow)
├── ps-alert--info               # Info variant (blue)
├── ps-alert--gold               # Gold/premium variant
├── ps-alert--light              # Light variant
├── ps-alert--dark               # Dark variant
├── ps-alert--with-icon          # Flexbox layout when icon present (automatic)
├── ps-alert--dismissible        # Has close button (adds right padding)
├── ps-alert--rounded            # Border radius applied
│
├── ps-alert__content            # Content wrapper
└── ps-alert__close              # Close button (when dismissible)
```

**Note**: Icon is rendered via `data-icon` attribute on root `.ps-alert` element. The `--with-icon` modifier is automatically applied when an icon is provided to activate flexbox layout and style the icon pseudo-element.

## Utility Classes

```
ps-alert-link                    # Darker/bolder links within alerts
ps-alert-heading                 # Larger, bold headings within alerts (h4)
```

## 10 Semantic Variants

| Variant | Use Case | Color Scheme |
|---------|----------|--------------|
| **default** | Neutral messages, general information | Light gray background, gray border, dark text |
| **primary** | Brand-specific highlights, featured content | Light green background (BNP brand), green border, dark text |
| **secondary** | Secondary brand messaging, promotions | Light pink/magenta background (BNP brand), magenta border, dark text |
| **success** | Confirmations, completed actions | Light teal background, teal border, dark text |
| **danger** | Errors, critical issues (assertive ARIA) | Light red background, red border, dark text |
| **warning** | Cautions, attention needed (assertive ARIA) | Light yellow background, yellow border, dark text |
| **info** | General information, tips, notifications | Light blue background, blue border, dark text |
| **gold** | Premium features, exclusive content | Light gold background, gold border, dark text |
| **light** | Subtle alerts, light backgrounds | White background, light gray border, dark text |
| **dark** | Dark theme alerts, high contrast | Dark gray background, darker border, white text |

## Design Tokens

### Component-Scoped Variables (Layer 2)

This component uses the **Three-Layer CSS Variables System**:

```css
/* Layout & Spacing */
--ps-alert-padding-y: var(--size-4);         /* Vertical padding */
--ps-alert-padding-x: var(--size-4);         /* Horizontal padding */
--ps-alert-margin-bottom: var(--size-4);     /* Bottom margin */
--ps-alert-border-radius: 0;                 /* Border radius (0 by default, var(--radius-2) when rounded) */
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

**Typography**: `--font-sans`, `--font-size-0` through `--font-size-4`, `--font-weight-600`, `--leading-normal`, `--leading-snug`

**Semantic Colors** (each with 9 states):
- `--primary-*` (green), `--secondary-*` (pink), `--success-*` (teal)
- `--danger-*` (red), `--warning-*` (yellow), `--info-*` (blue)
- `--gold-*`, `--light-*`, `--dark-*`
- States: base, hover, active, text, border, subtle, bg-subtle, border-subtle, text-emphasis

**Spacing**: `--size-2`, `--size-3`, `--size-4`, `--size-5`, `--size-6`, `--size-8`

**Borders**: `--radius-2`, `--border-size-1`, `--border-size-2`, `--border-default`

**Transitions**: `--duration-fast`, `--ease-out-2`

## Atomic Composition

**Alert = Component (Molecule)**

Composes (optional):
- `@elements/button/button.twig` (for close button when dismissible)

**Content model:**
- Free HTML content via `content` prop
- Users can include any HTML: headings, paragraphs, links, lists, etc.
- Use utility classes (`.ps-alert-link`, `.ps-alert-heading`) for styling

## Usage Examples

### Basic Alerts

```twig
{# Default neutral alert #}
{% include '@components/alert/alert.twig' with {
  variant: 'default',
  content: 'Ceci est une information générale.'
} %}

{# Success alert with link #}
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  content: 'Offre acceptée ! <a href="#" class="ps-alert-link">Voir le contrat</a> pour les détails.'
} %}

{# Warning alert with dismissible #}
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  content: '<strong>Attention !</strong> Votre offre expire dans 24 heures.',
  dismissible: true
} %}
```

### Size Variations

```twig
{# Extra small alert #}
{% include '@components/alert/alert.twig' with {
  variant: 'info',
  size: 'xs',
  content: 'Notification compacte'
} %}

{# Large alert #}
{% include '@components/alert/alert.twig' with {
  variant: 'primary',
  size: 'lg',
  content: '<h4 class="ps-alert-heading">Annonce importante</h4><p>Contenu détaillé...</p>'
} %}

{# Extra extra large alert #}
{% include '@components/alert/alert.twig' with {
  variant: 'gold',
  size: 'xxl',
  rounded: true,
  content: '<h4 class="ps-alert-heading">✨ Fonctionnalité Premium</h4><p>Accès exclusif débloqué.</p>'
} %}
```

### Real Estate Alerts

```twig
{# Property notification #}
{% include '@components/alert/alert.twig' with {
  variant: 'info',
  content: 'Nouveau bien commercial correspondant à vos critères ajouté au centre-ville. <a href="#" class="ps-alert-link">Voir l'annonce</a>'
} %}

{# Offer accepted #}
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  size: 'lg',
  dismissible: true,
  content: '<h4 class="ps-alert-heading">Offre acceptée !</h4><p>Votre offre sur le 123 Rue de la République a été acceptée. Les détails du contrat arriveront sous 24 heures.</p>'
} %}

{# Insurance expiring #}
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  content: '<strong>Renouvellement d'assurance :</strong> Votre assurance immobilière expire dans 30 jours. <a href="#" class="ps-alert-link">Renouveler maintenant</a> pour maintenir votre couverture.'
} %}

{# Payment failed #}
{% include '@components/alert/alert.twig' with {
  variant: 'danger',
  content: '<strong>Paiement refusé :</strong> Le paiement mensuel de votre local commercial n'a pas pu être traité. <a href="#" class="ps-alert-link">Mettre à jour le moyen de paiement</a> immédiatement.'
} %}

{# Premium feature #}
{% include '@components/alert/alert.twig' with {
  variant: 'gold',
  size: 'lg',
  rounded: true,
  dismissible: true,
  content: '<h4 class="ps-alert-heading">✨ Accès Premium Débloqué</h4><p>Votre compte a été mis à niveau. Profitez de l'accès prioritaire aux nouvelles annonces.</p>'
} %}
```

### Alerts with Headings

```twig
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  rounded: true,
  content: '
    <h4 class="ps-alert-heading">Inspection du bien terminée !</h4>
    <p>L'inspection du 123 Avenue des Champs-Élysées a été complétée avec succès. Le rapport détaillé a été téléversé dans votre tableau de bord.</p>
    <hr>
    <p style="margin-bottom: 0;">Vous pouvez maintenant <a href="#" class="ps-alert-link">finaliser l'offre</a> ou <a href="#" class="ps-alert-link">planifier une seconde visite</a>.</p>
  '
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
- **Don't rely on color alone**: Use descriptive text, headings, and semantic HTML to convey meaning
- **Close button**: Descriptive `aria-label="Fermer l'alerte"`
- **Light/Dark variants**: Tested for sufficient contrast

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
- Exposed global API: `window.PSAlert.dismiss(element)` / `dismissAll()`

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
- Choose appropriate size for context (xs for inline, lg/xl for hero sections)

### DON'T ❌
- Don't use alerts for promotional banners (use dedicated banner component)
- Don't make critical danger/warning alerts dismissible
- Don't use alerts for form field validation (use inline feedback)
- Don't hardcode colors or sizes
- Don't rely on color alone (use text + headings)
- Don't overwhelm users with too many alerts simultaneously
- Don't use default variant for critical messages (use danger/warning)

## Migration Notes

**Breaking changes from previous version:**
- Removed `icon` and `iconPosition` props (not part of core design spec)
- Changed default variant from `primary` to `default` (neutral gray)
- Added `size` prop with 6 variations (xs, sm, md, lg, xl, xxl)
- Added `gold` variant for premium content
- Removed `error` variant alias (use `danger` instead)
- Fully migrated to 3-layer CSS variables system

**Migration guide:**
```twig
{# OLD (v1.0) #}
{% include '@components/alert/alert.twig' with {
  variant: 'error',
  icon: 'warning'
} %}

{# NEW (v2.0) #}
{% include '@components/alert/alert.twig' with {
  variant: 'danger',
  size: 'md'
  {# Icons now embedded in content if needed #}
} %}
```
