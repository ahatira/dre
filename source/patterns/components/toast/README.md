# Toast

Temporary notification message that appears at screen edge to provide feedback on user actions. Auto-dismisses after specified duration or can be manually closed.

## Usage

```twig
{% include '@components/toast/toast.twig' with {
  message: 'Property added to favorites!',
  type: 'success',
} only %}
```

## Props

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `message` | `string` | required | Notification message content |
| `type` | `string` | `'info'` | Semantic type: `success`, `error`, `warning`, `info` |
| `dismissible` | `boolean` | `true` | Show close button for manual dismissal |
| `position` | `string` | `'bottom-right'` | Screen position: `bottom-right`, `bottom-left`, `top-right`, `top-left` |
| `duration` | `number` | `4000` | Auto-dismiss duration in milliseconds |
| `attributes` | `object` | - | Additional HTML attributes |

## BEM Structure

```
.ps-toast
├── .ps-toast__content
├── .ps-toast__close
├── .ps-toast--success
├── .ps-toast--error
├── .ps-toast--warning
├── .ps-toast--info
├── .ps-toast--top-right
├── .ps-toast--top-left
├── .ps-toast--bottom-left
└── [data-toast-dismissing] (state)
```

## Design Tokens (Layer 2)

### Base Variables
- `--ps-toast-padding-x`, `--ps-toast-padding-y` - Internal spacing
- `--ps-toast-gap` - Space between content and close button
- `--ps-toast-radius` - Border radius
- `--ps-toast-shadow` - Drop shadow
- `--ps-toast-max-width` - Maximum width (400px)
- `--ps-toast-offset` - Distance from screen edge

### Color Variables (Semantic)
- `--ps-toast-bg` - Background color (uses semantic tokens)
- `--ps-toast-color` - Text color (uses semantic tokens)
- `--ps-toast-border-color` - Border color (uses semantic tokens)

## Accessibility

- ✅ ARIA attributes: `role="status"`, `aria-live="polite"`, `aria-atomic="true"`
- ✅ Keyboard support: Close with Escape key
- ✅ Focus management: Close button has visible focus indicator
- ✅ Screen reader friendly: Non-disruptive announcements via `polite`
- ✅ WCAG 2.2 AA: All semantic variants meet contrast requirements (4.5:1)

## JavaScript Behavior

The Toast component includes behavior management that works in both **Drupal** and **standalone (Storybook)** contexts:

### Features
- **Auto-dismiss**: Automatically removes after `duration` milliseconds
- **Manual dismiss**: Click close button to dismiss immediately
- **Keyboard support**: Press Escape key to dismiss focused toast
- **Animations**: Slide-in on show, slide-out on dismiss with smooth transitions
- **Duplicate prevention**: Uses WeakSet to avoid processing toasts multiple times
- **Context-aware**: Works with or without Drupal/once() dependency

### Programmatic Usage (Drupal Context)

```javascript
// Show a success toast
Drupal.toast({
  message: 'Property saved successfully!',
  type: 'success',
  duration: 5000
});

// Show an error toast without dismiss button
Drupal.toast({
  message: 'Unable to connect to server',
  type: 'error',
  dismissible: false,
  position: 'top-right'
});
```

### Programmatic Usage (Standalone/Storybook)

```javascript
// Show a toast in Storybook or vanilla JS
window.Toast.create({
  message: 'Property added to favorites!',
  type: 'success',
  duration: 4000,
  position: 'bottom-right'
});

// Initialize existing toasts in context
window.Toast.init(document);
```

### Auto-Initialization

The script automatically initializes all `.ps-toast` elements on page load:
- In Drupal: Via `Drupal.behaviors.toast`
- In standalone: Via `DOMContentLoaded` event listener

## Real Estate Examples

### Success Actions
```twig
{# Property added to favorites #}
{% include '@components/toast/toast.twig' with {
  message: 'Property added to favorites!',
  type: 'success',
} only %}

{# Search alert created #}
{% include '@components/toast/toast.twig' with {
  message: 'Search alert created successfully!',
  type: 'success',
} only %}
```

### Error States
```twig
{# Unable to schedule tour #}
{% include '@components/toast/toast.twig' with {
  message: 'Unable to schedule tour. Contact agent directly.',
  type: 'error',
} only %}
```

### Warnings
```twig
{# Property may be under offer #}
{% include '@components/toast/toast.twig' with {
  message: 'This property may be under offer.',
  type: 'warning',
} only %}
```

### Information
```twig
{# New matching properties #}
{% include '@components/toast/toast.twig' with {
  message: '3 new properties match your criteria.',
  type: 'info',
} only %}
```

## Migration from Old System

### Token Changes (3-Layer System)

**OLD** (flat variables):
```css
--ps-toast-border: var(--gray-200);
--ps-toast-bg: var(--success-50);
--ps-toast-color: var(--success-900);
```

**NEW** (semantic tokens):
```css
--ps-toast-border-color: var(--success-border);
--ps-toast-bg: var(--success-bg-subtle);
--ps-toast-color: var(--success-text-emphasis);
```

### Removed Features
- ❌ `show` parameter removed - Visibility managed by JavaScript
- ❌ Inline SVG removed - Uses `data-icon` system

### New Features
- ✅ Position variants (4 screen corners)
- ✅ JavaScript auto-dismiss behavior
- ✅ Programmatic API
- ✅ Keyboard support (Escape key)
- ✅ `attributes` parameter for Drupal integration

## Technical Notes

- **Z-index**: Uses `--z-toast` (1080) from global token system
- **Animations**: 
  - Slide-in: `var(--duration-normal)` with `var(--ease-out-cubic)`
  - Slide-out: `var(--duration-fast)` with `var(--ease-out-cubic)`
- **Icon System**: Close button uses `data-icon="close"` (automatic sprite loading)
- **Stacking**: Multiple toasts will overlap - consider implementing toast container in future

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS Grid and Flexbox required
- CSS Custom Properties required
- `animation` and `@keyframes` required
