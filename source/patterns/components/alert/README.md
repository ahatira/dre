# Alert Component

A semantic status message component with variant-specific colors, icons, and optional dismissal functionality.

## Component Props

| Prop | Type | Options | Default | Description |
|------|------|---------|---------|-------------|
| `variant` | string | `'info'`, `'success'`, `'warning'`, `'error'` | `'info'` | Semantic variant with appropriate color and icon |
| `title` | string | any | `''` | Optional title text |
| `message` | string | any (HTML supported) | `''` | Message content |
| `dismissible` | boolean | `true`, `false` | `false` | Show close button |
| `compact` | boolean | `true`, `false` | `false` | Reduced padding for dense layouts |
| `attributes` | object | any | - | Additional HTML attributes |

## BEM Structure

```
ps-alert                         # Root element
├── ps-alert--info               # Info variant (default)
├── ps-alert--success            # Success variant
├── ps-alert--warning            # Warning variant
├── ps-alert--error              # Error variant
├── ps-alert--dismissible        # Has close button
├── ps-alert--compact            # Compact spacing
│
├── ps-alert__icon               # Icon container (CSS pseudo-element)
├── ps-alert__content            # Content wrapper
│   ├── ps-alert__title          # Title element (optional)
│   └── ps-alert__message        # Message element
└── ps-alert__close              # Close button (when dismissible)
```

## Semantic Variants

| Variant | Use Case | Background | Border | Icon |
|---------|----------|------------|--------|------|
| **info** (default) | General information, tips | `--blue-50` | `--btn-info` | info (ⓘ) |
| **success** | Confirmations, completed actions | `--green-50` | `--btn-success` | check (✓) |
| **warning** | Cautions, attention needed | `--yellow-50` | `--btn-warning` | warning (!) |
| **error** | Errors, critical issues | `--red-50` | `--btn-danger` | error (×) |

## Design Tokens Used

### Colors
**Backgrounds:**
- `--blue-50` - Info variant background
- `--green-50` - Success variant background
- `--yellow-50` - Warning variant background
- `--red-50` - Danger variant background

**Borders & Icons:**
- `--btn-info` (fallback `--blue-600`) - Info border/icon color
- `--btn-success` (fallback `--green-600`) - Success border/icon color
- `--btn-warning` (fallback `--yellow-500`) - Warning border/icon color
- `--btn-danger` (fallback `--red-600`) - Danger border/icon color

**Text:**
- `--gray-900` - Text color
- `--brand-primary` - Link color

### Typography
- `--font-sans` - Font family (BNPP Sans)
- `--font-size-0` (14px) - Compact text size
- `--font-size-1` (16px) - Default text size
- `--font-size-3` (20px) - Icon size (default)
- `--font-size-4` (24px) - Close button size
- `--font-weight-600` - Title weight
- `--leading-normal` (1.5) - Message line height
- `--leading-snug` (1.375) - Title line height

### Spacing
- `--size-1` (4px) - Title/message gap, close button padding
- `--size-2` (8px) - Compact gap, paragraph spacing
- `--size-3` (12px) - Default grid gap, compact padding
- `--size-4` (16px) - Default padding, icon size (compact)
- `--size-5` (20px) - Icon size (default), close button size (compact)
- `--size-6` (24px) - Close button size (default)

### Visual
- `--radius-1` (2px) - Close button border radius
- `--radius-2` (4px) - Alert border radius
- `--border-size-1` (1px) - Alert border width
- `--border-size-2` (2px) - Focus outline width

### Icons System
- Icons rendered via centralized `[data-icon]` system in `icons.css`
- Icon name matches variant: `data-icon="info"`, `data-icon="success"` (uses check), `data-icon="warning"`, `data-icon="error"`
- Font: bnpre-icons
- No component-specific icon mappings (centralized in `source/props/icons.css`)

## Usage Examples

### Basic Info Alert
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'info',
  title: 'Information',
  message: 'Your session will expire in 5 minutes.'
} %}
```

### Success Alert (No Title)
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  message: 'Your changes have been saved successfully.'
} %}
```

### Warning Alert (Dismissible)
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  title: 'Action Required',
  message: 'Please verify your email address to continue.',
  dismissible: true
} %}
```

### Danger Alert
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'error',
  title: 'Error',
  message: 'An error occurred while processing your request. Please try again.'
} %}
```

### Compact Alert
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'info',
  message: 'Tip: Use Ctrl+S to save your work.',
  compact: true,
  dismissible: true
} %}
```

### Alert with HTML Content
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  title: 'Subscription Expiring',
  message: '<p>Your subscription expires in 3 days.</p><p><a href="/renew">Renew now</a> to avoid interruption.</p>',
  dismissible: true
} %}
```

## Common Use Cases

### 1. Form Submission Success
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'success',
  title: 'Form Submitted',
  message: 'Thank you for your submission. We will contact you within 5 business days.',
  dismissible: true
} %}
```

### 2. Session Expiration Warning
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'warning',
  title: 'Session Expiring Soon',
  message: 'Your session will expire in 2 minutes. Click anywhere to stay logged in.'
} %}
```

### 3. System Error
```twig
{% include '@components/alert/alert.twig' with {
  variant: 'error',
  title: 'Connection Error',
  message: 'Unable to connect to the server. Please check your internet connection.'
} %}
```

### 4. Inline Help (Compact)
```twig
<div class="sidebar">
  {% include '@components/alert/alert.twig' with {
    variant: 'info',
    message: 'Pro tip: Use keyboard shortcuts to navigate faster.',
    compact: true,
    dismissible: true
  } %}
</div>
```

### 5. Multiple Alerts Container
```twig
<div class="alerts-container" style="display: flex; flex-direction: column; gap: var(--size-4);">
  {% include '@components/alert/alert.twig' with {
    variant: 'success',
    message: 'Document uploaded successfully.'
  } %}
  
  {% include '@components/alert/alert.twig' with {
    variant: 'warning',
    message: 'Remember to submit your report by Friday.',
    dismissible: true
  } %}
</div>
```

## Accessibility

### ARIA Roles & Live Regions
- **Error alerts**: `role="alert"` with `aria-live="assertive"` for immediate announcement to screen readers
- **Other variants**: `role="status"` with `aria-live="polite"` for non-intrusive updates
- Screen readers announce content automatically when alert appears

### Keyboard & Focus
- Close button is keyboard accessible (Tab to focus, Enter/Space to activate)
- Focus indicator visible with `outline` on `:focus-visible` state
- Tab order: Close button is last in alert (natural grid order)

### Visual Accessibility
- **Color contrast**: Text meets WCAG AA standards (4.5:1 minimum against background)
- **Icons**: Rendered via `[data-icon]` attribute, marked `aria-hidden="true"` (color and text already convey meaning)
- **Close button**: Descriptive `aria-label="Close alert"` for screen readers
- **Don't rely on color alone**: Icons + text reinforce meaning

### Best Practices
- Critical error alerts should NOT be dismissible (users must address them)
- Include descriptive titles for context
- Keep messages concise and actionable
- Use semantic HTML in message content (proper headings, paragraphs, links)

## Responsive Behavior

### Layout
- **Grid layout**: Icon, content, close button in 3-column grid
- **Content wrapping**: Message text wraps naturally in middle column
- **Minimum width**: 280px recommended for mobile displays

### Compact Mode
- Reduced padding: `--size-3` instead of `--size-4`
- Smaller icon: `--size-4` instead of `--size-5`
- Smaller text: `--font-size-0` instead of `--font-size-1`
- Ideal for sidebars, mobile views, inline tips

## Interactive Behavior

### Close Button (JavaScript Required)
The close button is markup-only. You need to add JavaScript to handle dismissal:

```javascript
document.querySelectorAll('.ps-alert__close').forEach(button => {
  button.addEventListener('click', function() {
    const alert = this.closest('.ps-alert');
    alert.remove(); // or alert.style.display = 'none';
  });
});
```

### Animation (Optional)
Consider adding fade-out animation on dismissal:

```css
@keyframes fadeOut {
  from { opacity: 1; }
  to { opacity: 0; }
}

.ps-alert.is-closing {
  animation: fadeOut 200ms ease-out forwards;
}
```

## Best Practices

### DO ✅
- Use semantic variants that match the message intent
- Include descriptive titles for context
- Keep messages concise and actionable
- Use HTML content for formatting (links, bold, paragraphs)
- Make non-critical alerts dismissible
- Use compact mode in space-constrained layouts
- Stack multiple alerts with appropriate spacing
- Always use design tokens for colors and sizing

### DON'T ❌
- Don't use alerts for promotional content—use banners instead
- Don't make critical error alerts dismissible—users must address them
- Don't use alerts for field validation—use inline feedback
- Don't stack alerts without spacing between them
- Don't hardcode colors or sizes—always use tokens
- Don't rely on color alone—icons + text required
- Don't overwhelm users with too many alerts at once
- Don't use alerts for content that should be persistent (use cards instead)

## Component Audit Checklist

- [x] Uses semantic HTML with proper ARIA roles and live regions
- [x] All colors use design tokens (no hardcoded values)
- [x] All spacing uses design tokens (`--size-*`)
- [x] All typography uses design tokens (`--font-*`)
- [x] BEM methodology with `ps-` prefix
- [x] CSS uses PostCSS nesting syntax
- [x] Minimal markup (default info requires no modifier class except base)
- [x] Supports 4 semantic variants (info, success, warning, error)
- [x] Icons rendered via centralized `[data-icon]` system
- [x] Optional title and dismissible functionality
- [x] Compact mode for dense layouts
- [x] HTML content support in messages
- [x] Accessible close button with aria-label
- [x] Focus indicators visible (keyboard navigation)
- [x] Color contrast meets WCAG AA standards
- [x] Grid layout adapts to content
- [x] Storybook documentation complete with structured sections
- [x] All content in English
- [x] 5 required files present (.twig, .css, .yml, .stories.jsx, README.md)
