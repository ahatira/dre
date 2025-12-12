# Toast (Molecule)

**Niveau Atomic Design** : Molecule / Notification  
**Catégorie** : Feedback  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Notification temporaire non-intrusive affichée en overlay (généralement coin supérieur droit ou inférieur). Supporte des variantes sémantiques (info, success, warning, error), auto-dismiss avec timer, fermeture manuelle, et accessibilité via `role="status"` ou `role="alert"`. Empilable pour plusieurs notifications simultanées.

---

## 🎨 Aperçu visuel

```
┌──────────────────────────────┐
│ ✓ Enregistrement réussi   [×]│
└──────────────────────────────┘
```

---

## 🏗️ Structure BEM

```html
<div class="ps-toast ps-toast--success" role="status" aria-live="polite" aria-atomic="true" data-toast data-duration="5000">
  <div class="ps-toast__content">
    <svg class="ps-toast__icon" aria-hidden="true"><use href="#icon-check-circle"></use></svg>
    <div class="ps-toast__message">Enregistrement réussi</div>
  </div>
  <button class="ps-toast__close" type="button" aria-label="Fermer" data-toast-close>
    <svg class="ps-toast__close-icon" aria-hidden="true"><use href="#icon-close"></use></svg>
  </button>
</div>
```

### Classes BEM

```
ps-toast                                  // Block
  ps-toast__content                       // Conteneur message + icône
  ps-toast__icon                          // Icône sémantique
  ps-toast__message                       // Texte
  ps-toast__close                         // Bouton fermeture
  ps-toast__close-icon                    // Icône fermeture

Modificateurs :
  ps-toast--info                          // Variante info (bleu)
  ps-toast--success                       // Variante succès (vert)
  ps-toast--warning                       // Variante avertissement (orange)
  ps-toast--error                         // Variante erreur (rouge)
  ps-toast--dismissible                   // Peut être fermé manuellement
  is-visible                              // État visible (animation)
  is-exiting                              // État sortie (animation)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Toast'
status: stable
group: molecules
description: 'Notification temporaire non-intrusive avec auto-dismiss et variantes sémantiques.'

props:
  type: object
  properties:
    message:
      type: string
      title: Message
    variant:
      type: string
      enum: ['info','success','warning','error']
      default: 'info'
    icon:
      type: string
      description: 'Nom d'icône optionnel (ex: check-circle, info-circle, alert-triangle, alert-circle)'
    duration:
      type: integer
      default: 5000
      description: 'Durée en ms avant auto-dismiss (0 = pas d'auto-dismiss)'
    dismissible:
      type: boolean
      default: true
    position:
      type: string
      enum: ['top-right','top-left','bottom-right','bottom-left','top-center','bottom-center']
      default: 'top-right'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - message
```

---

## 🎭 Variants

- `variant`: `info`|`success`|`warning`|`error` (couleurs et icônes par défaut).
- `dismissible`: afficher le bouton fermer.
- `duration`: 0 = reste jusqu'à fermeture manuelle; >0 = auto-dismiss après X ms.
- `position`: placement (géré par conteneur global, voir JS).

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-sm`, `--ps-font-weight-medium`
- Couleurs par variante:
  - Info: `--ps-color-info-50` (bg), `--ps-color-info-700` (text/icon), `--ps-color-info-600` (border)
  - Success: `--ps-color-success-50`, `--ps-color-success-700`, `--ps-color-success-600`
  - Warning: `--ps-color-warning-50`, `--ps-color-warning-800`, `--ps-color-warning-600`
  - Error: `--ps-color-error-50`, `--ps-color-error-700`, `--ps-color-error-600`
- Bordures: `--ps-border-width-default`, `--ps-border-radius-md`
- Espacements: `--ps-spacing-3|4`
- Ombres: `--ps-shadow-md`
- Transitions: `--ps-transition-duration-normal`, `--ps-transition-easing-default`

Proposition si manquant: couleurs info/success/warning/error (50, 600, 700, 800).

---

## 🔧 Template Twig

```twig
{#
 * Template for Toast molecule.
 * Variables: voir API YAML
 #}

{% set variant = variant|default('info') %}
{% set duration = duration|default(5000) %}
{% set dismissible = dismissible ?? true %}
{% set position = position|default('top-right') %}

{% set icon_map = {
  'info': 'info-circle',
  'success': 'check-circle',
  'warning': 'alert-triangle',
  'error': 'alert-circle'
} %}
{% set toast_icon = icon ?? icon_map[variant] %}

{% set role = (variant == 'error' or variant == 'warning') ? 'alert' : 'status' %}
{% set aria_live = (variant == 'error' or variant == 'warning') ? 'assertive' : 'polite' %}

{% set root_classes = [
  'ps-toast',
  'ps-toast--' ~ variant,
  dismissible ? 'ps-toast--dismissible'
] %}

<div {{ attributes.addClass(root_classes) }} role="{{ role }}" aria-live="{{ aria_live }}" aria-atomic="true" data-toast data-duration="{{ duration }}" data-position="{{ position }}">
  <div class="ps-toast__content">
    {% if toast_icon %}
      <svg class="ps-toast__icon" aria-hidden="true"><use href="#icon-{{ toast_icon }}"></use></svg>
    {% endif %}
    <div class="ps-toast__message">{{ message }}</div>
  </div>
  {% if dismissible %}
    <button class="ps-toast__close" type="button" aria-label="Fermer" data-toast-close>
      <svg class="ps-toast__close-icon" aria-hidden="true"><use href="#icon-close"></use></svg>
    </button>
  {% endif %}
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-toast {
  position: fixed;
  z-index: var(--ps-z-index-toast, 1100);
  display: flex; align-items: center; gap: var(--ps-spacing-3, 12px);
  min-width: 300px; max-width: 500px;
  padding: var(--ps-spacing-3, 12px) var(--ps-spacing-4, 16px);
  font-family: var(--ps-font-family-primary);
  font-size: var(--ps-font-size-sm, 14px);
  border-radius: var(--ps-border-radius-md, 8px);
  box-shadow: var(--ps-shadow-md, 0 4px 12px rgba(0, 0, 0, 0.15));
  border: var(--ps-border-width-default, 1px) solid;
  opacity: 0;
  transform: translateY(-20px);
  transition: opacity var(--ps-transition-duration-normal, 0.3s) var(--ps-transition-easing-default, ease),
              transform var(--ps-transition-duration-normal, 0.3s) var(--ps-transition-easing-default, ease);

  &.is-visible { opacity: 1; transform: translateY(0); }
  &.is-exiting { opacity: 0; transform: translateY(-20px); }

  &__content {
    display: flex; align-items: center; gap: var(--ps-spacing-2, 8px); flex: 1;
  }
  &__icon { width: 20px; height: 20px; flex-shrink: 0; }
  &__message { flex: 1; }

  &__close {
    background: none; border: none; cursor: pointer; padding: var(--ps-spacing-1, 4px); line-height: 1; flex-shrink: 0;
    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid currentColor; outline-offset: 2px; }
  }
  &__close-icon { width: 16px; height: 16px; }

  // Variants
  &--info {
    background: var(--ps-color-info-50, #E3F2FD);
    color: var(--ps-color-info-700, #0277BD);
    border-color: var(--ps-color-info-600, #039BE5);
  }
  &--success {
    background: var(--ps-color-success-50, #E8FAF5);
    color: var(--ps-color-success-700, #0E7A5F);
    border-color: var(--ps-color-success-600, #0DB089);
  }
  &--warning {
    background: var(--ps-color-warning-50, #FFF3E0);
    color: var(--ps-color-warning-800, #E65100);
    border-color: var(--ps-color-warning-600, #FB8C00);
  }
  &--error {
    background: var(--ps-color-error-50, #FFEBEE);
    color: var(--ps-color-error-700, #C62828);
    border-color: var(--ps-color-error-600, #E53935);
  }

  // Positions (handled by container, fallback here)
  &[data-position="top-right"] { top: var(--ps-spacing-4, 16px); right: var(--ps-spacing-4, 16px); }
  &[data-position="top-left"] { top: var(--ps-spacing-4, 16px); left: var(--ps-spacing-4, 16px); }
  &[data-position="bottom-right"] { bottom: var(--ps-spacing-4, 16px); right: var(--ps-spacing-4, 16px); }
  &[data-position="bottom-left"] { bottom: var(--ps-spacing-4, 16px); left: var(--ps-spacing-4, 16px); }
  &[data-position="top-center"] { top: var(--ps-spacing-4, 16px); left: 50%; transform: translateX(-50%) translateY(-20px); }
  &[data-position="bottom-center"] { bottom: var(--ps-spacing-4, 16px); left: 50%; transform: translateX(-50%) translateY(-20px); }
}
```

---

## ♿ Accessibilité

- `role="status"` (info/success) ou `role="alert"` (warning/error).
- `aria-live="polite"` (status) ou `aria-live="assertive"` (alert).
- `aria-atomic="true"` : annonce le message complet.
- Bouton fermer avec `aria-label="Fermer"`.
- Pas de focus automatique (non-intrusif).

---

## 📱 Comportement responsive

- Position fixe avec `z-index` élevé.
- Largeur adaptative (min 300px, max 500px).
- Empilable : conteneur gère l'espacement vertical entre toasts multiples (voir JS).

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-toast/ps-toast.twig' with {
  message: 'Votre annonce a été publiée avec succès.',
  variant: 'success',
  duration: 5000,
  dismissible: true,
  position: 'top-right'
} %}
```

---

## 🔌 JavaScript behavior (facultatif)

```js
// Toast system with container management
class ToastManager {
  constructor() {
    this.containers = {};
    this.initContainers();
  }

  initContainers() {
    const positions = ['top-right', 'top-left', 'bottom-right', 'bottom-left', 'top-center', 'bottom-center'];
    positions.forEach(pos => {
      const container = document.createElement('div');
      container.className = 'ps-toast-container';
      container.setAttribute('data-position', pos);
      container.style.cssText = `
        position: fixed; z-index: 1100; display: flex; flex-direction: column; gap: 12px;
        ${pos.includes('top') ? 'top: 16px;' : 'bottom: 16px;'}
        ${pos.includes('right') ? 'right: 16px;' : ''}
        ${pos.includes('left') ? 'left: 16px;' : ''}
        ${pos.includes('center') ? 'left: 50%; transform: translateX(-50%);' : ''}
      `;
      document.body.appendChild(container);
      this.containers[pos] = container;
    });
  }

  show(options) {
    const { message, variant = 'info', duration = 5000, dismissible = true, position = 'top-right' } = options;
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `ps-toast ps-toast--${variant}`;
    toast.setAttribute('role', (variant === 'error' || variant === 'warning') ? 'alert' : 'status');
    toast.setAttribute('aria-live', (variant === 'error' || variant === 'warning') ? 'assertive' : 'polite');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-toast', '');
    toast.setAttribute('data-duration', duration);

    const iconMap = { info: 'info-circle', success: 'check-circle', warning: 'alert-triangle', error: 'alert-circle' };
    const icon = iconMap[variant];

    toast.innerHTML = `
      <div class="ps-toast__content">
        <svg class="ps-toast__icon" aria-hidden="true"><use href="#icon-${icon}"></use></svg>
        <div class="ps-toast__message">${message}</div>
      </div>
      ${dismissible ? '<button class="ps-toast__close" type="button" aria-label="Fermer" data-toast-close><svg class="ps-toast__close-icon" aria-hidden="true"><use href="#icon-close"></use></svg></button>' : ''}
    `;

    // Append to container
    const container = this.containers[position];
    container.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(() => toast.classList.add('is-visible'));

    // Close handler
    const close = () => {
      toast.classList.remove('is-visible');
      toast.classList.add('is-exiting');
      setTimeout(() => toast.remove(), 300);
    };

    if (dismissible) {
      const closeBtn = toast.querySelector('[data-toast-close]');
      closeBtn.addEventListener('click', close);
    }

    // Auto-dismiss
    if (duration > 0) {
      setTimeout(close, duration);
    }
  }
}

// Global instance
window.toastManager = new ToastManager();

// Setup existing toasts (server-rendered)
document.querySelectorAll('[data-toast]').forEach(toast => {
  const duration = parseInt(toast.getAttribute('data-duration')) || 5000;
  const closeBtn = toast.querySelector('[data-toast-close]');
  
  requestAnimationFrame(() => toast.classList.add('is-visible'));

  const close = () => {
    toast.classList.remove('is-visible');
    toast.classList.add('is-exiting');
    setTimeout(() => toast.remove(), 300);
  };

  if (closeBtn) closeBtn.addEventListener('click', close);
  if (duration > 0) setTimeout(close, duration);
});

// Usage: window.toastManager.show({ message: 'Success!', variant: 'success' });
```

---

## 📚 Ressources

- WAI-ARIA: `role="status"` and `role="alert"`
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`, `/design/tokens/shadows.yml`, `/design/tokens/transitions.yml`
