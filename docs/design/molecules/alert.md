# Alert (Molecule)

**Niveau Atomic Design** : Molecule / Feedback  
**Catégorie** : Status & Messaging  
**Statut** : ✅ Stable  
**Version** : 1.1.0  
**Dernière mise à jour** : 3 décembre 2025

---

## 📋 Description

Semantic alert with 8 color variants (primary, secondary, success, danger, warning, info, light, dark) and optional dismissal. Supports free HTML content and contextual feedback patterns for real estate notifications.

**Implémentation** : `source/patterns/components/alert/`

---

## 🎨 Aperçu visuel

```
[PRIMARY] Brand-specific highlights, featured real estate content
[SECONDARY] Muted notices and supplementary information
[SUCCESS] ✓ Property saved successfully to your favorites
[DANGER] × Error: Unable to schedule property viewing
[WARNING] ! Your offer expires in 24 hours
[INFO] i New listings available in your saved search area
[LIGHT] General non-critical announcements
[DARK] High-contrast important notifications
```

---

## 🏗️ Structure BEM

```html
<div class="ps-alert ps-alert--success" role="status">
  <div class="ps-alert__content">
    <h4 class="ps-alert-heading">Property saved!</h4>
    <p>You can view this property in your <a href="/favorites" class="ps-alert-link">saved listings</a>.</p>
  </div>
</div>

<!-- Dismissible variant -->
<div class="ps-alert ps-alert--warning ps-alert--dismissible" role="alert">
  <div class="ps-alert__content">
    <p>Your offer expires in 24 hours.</p>
  </div>
  <button class="ps-alert__close" type="button" aria-label="Dismiss alert">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<!-- With rounded borders -->
<div class="ps-alert ps-alert--info ps-alert--rounded" role="status">
  <div class="ps-alert__content">
    5 new properties match your search criteria.
  </div>
</div>
```

### Classes BEM

```
ps-alert                           // Block principal
  ps-alert__content                // Free HTML content wrapper
  ps-alert__close                  // Close button (when dismissible)

Modificateurs :
  ps-alert--primary                // Brand green (BNP) - default
  ps-alert--secondary              // Gray muted
  ps-alert--success                // Green confirmation
  ps-alert--danger                 // Red error/critical
  ps-alert--warning                // Yellow/orange caution
  ps-alert--info                   // Blue informational
  ps-alert--light                  // Light gray
  ps-alert--dark                   // Dark high-contrast
  ps-alert--dismissible            // Has close button (adds padding-right)
  ps-alert--rounded                // Border radius applied

Utility classes:
  ps-alert-link                    // Darker/bolder links within alerts
  ps-alert-heading                 // Larger, bold headings (h4 recommended)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Alert'
status: stable
group: molecules
description: 'Message d’état sémantique avec rôle ARIA et option de fermeture.'

props:
  type: object
  properties:
    variant:
      type: string
      title: Variant
      enum: ['info','success','warning','error']
      default: 'info'
    title:
      type: string
      title: Titre
    message:
      type: string
      title: Message HTML
    dismissible:
      type: boolean
      title: Fermeture
      default: false
    compact:
      type: boolean
      title: Compact
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
  required: []

slots:
  message:
    title: Contenu HTML du message
    description: Permet d’injecter du HTML riche
```

---

## 🎭 Variants

- `info|success|warning|error` avec couleurs et icônes dédiées.
- `dismissible: true` pour afficher un bouton de fermeture.
- `compact: true` réduit padding et taille.

---

## 🎨 Design Tokens

- Couleurs sémantiques: `--primary`, `--secondary`, `--success`, `--danger`, `--warning`, `--info`, `--gray-50`, `--gray-900`
- Arrière-plans doux: `--green-50`, `--purple-50`, `--red-50`, `--yellow-50`, `--blue-50`, `--gray-50`
- Bordure: `--border-size-1`
- Typo: `--font-sans`, `--font-size-1`, `--leading-normal`
- Spacing: `--size-3`, `--size-4`, `--size-8`
- Bouton close: `--font-size-4`, `--border-size-2`

---

## 🔧 Template Twig

```twig
{#
 * Template for Alert molecule.
 * Variables:
 * - variant: 'info'|'success'|'warning'|'error'
 * - title: string
 * - message: string (HTML)
 * - dismissible: bool
 * - compact: bool
 * - attributes: Attribute
 #}

{% set variant = variant|default('info') %}
{% set role = variant == 'error' ? 'alert' : 'status' %}
{% set liveliness = variant == 'error' ? 'assertive' : 'polite' %}
{% set classes = [
  'ps-alert',
  'ps-alert--' ~ variant,
  dismissible ? 'ps-alert--dismissible',
  compact ? 'ps-alert--compact'
] %}

<div {{ attributes.addClass(classes) }} role="{{ role }}" aria-live="{{ liveliness }}">
  <span class="ps-alert__icon" aria-hidden="true">
    <svg class="ps-alert__icon-svg" focusable="false" aria-hidden="true">
      {% set icon_name = {
        'info': 'info',
        'success': 'check-circle',
        'warning': 'warning',
        'error': 'error'
      }[variant] %}
      <use href="#icon-{{ icon_name }}"></use>
    </svg>
  </span>

  <div class="ps-alert__content">
    {% if title %}<h3 class="ps-alert__title">{{ title }}</h3>{% endif %}
    <div class="ps-alert__message">
      {% if message is not empty %}{{ message|raw }}{% elseif message_slot %}{{ message_slot }}{% endif %}
    </div>
  </div>

  {% if dismissible %}
    <button class="ps-alert__close" type="button" aria-label="Fermer">×</button>
  {% endif %}
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-alert {
  /* Variables composant */
  --ps-alert-padding-y: var(--size-4);
  --ps-alert-padding-x: var(--size-4);
  --ps-alert-border-radius: 0;
  --ps-alert-border-width: var(--border-size-1);
  --ps-alert-font-family: var(--font-sans);
  --ps-alert-font-size: var(--font-size-1);
  --ps-alert-line-height: var(--leading-normal);
  --ps-alert-bg: transparent;
  --ps-alert-color: inherit;
  --ps-alert-border-color: transparent;
  --ps-alert-link-color: inherit;
  --ps-alert-close-padding: var(--size-3);
  --ps-alert-close-opacity: 0.5;
  --ps-alert-close-hover-opacity: 0.75;
  --ps-alert-close-focus-opacity: 1;
  --ps-alert-close-focus-outline-width: var(--border-size-2);
  --ps-alert-close-focus-outline-offset: calc(var(--border-size-1) * -1);

  position: relative;
  display: block;
  padding: var(--ps-alert-padding-y) var(--ps-alert-padding-x);
  border-radius: var(--ps-alert-border-radius);
  border: var(--ps-alert-border-width) solid var(--ps-alert-border-color);
  background-color: var(--ps-alert-bg);
  font-family: var(--ps-alert-font-family);
  font-size: var(--ps-alert-font-size);
  line-height: var(--ps-alert-line-height);
  color: var(--ps-alert-color);

  &--dismissible { padding-right: calc(var(--ps-alert-padding-x) + var(--size-8)); }
  &--rounded { --ps-alert-border-radius: var(--radius-2); }

  // Variantes sémantiques
  &--primary { --ps-alert-bg: var(--green-50); --ps-alert-color: var(--primary); --ps-alert-border-color: var(--primary); --ps-alert-link-color: var(--primary); }
  &--secondary { --ps-alert-bg: var(--purple-50); --ps-alert-color: var(--secondary); --ps-alert-border-color: var(--secondary); --ps-alert-link-color: var(--secondary); }
  &--success { --ps-alert-bg: var(--green-100); --ps-alert-color: var(--success); --ps-alert-border-color: var(--success); --ps-alert-link-color: var(--success); }
  &--danger { --ps-alert-bg: var(--red-50); --ps-alert-color: var(--danger); --ps-alert-border-color: var(--danger); --ps-alert-link-color: var(--danger); }
  &--warning { --ps-alert-bg: var(--yellow-50); --ps-alert-color: var(--warning-text); --ps-alert-border-color: var(--warning); --ps-alert-link-color: var(--warning); }
  &--info { --ps-alert-bg: var(--blue-50); --ps-alert-color: var(--info); --ps-alert-border-color: var(--info); --ps-alert-link-color: var(--info); }
  &--light { --ps-alert-bg: var(--gray-50); --ps-alert-color: var(--gray-900); --ps-alert-border-color: var(--gray-300); --ps-alert-link-color: var(--gray-900); }
  &--dark { --ps-alert-bg: var(--gray-900); --ps-alert-color: var(--gray-50); --ps-alert-border-color: var(--gray-800); --ps-alert-link-color: var(--gray-50); }

  // Bouton de fermeture
  &__close {
    position: absolute;
    top: 0; right: 0;
    padding: var(--ps-alert-close-padding);
    font-size: var(--font-size-4);
    color: var(--ps-alert-color);
    opacity: var(--ps-alert-close-opacity);
    cursor: pointer;
    background: transparent;
    border: none;
    line-height: 1;
    transition: opacity var(--duration-fast) var(--ease-out-2);

    &:hover { opacity: var(--ps-alert-close-hover-opacity); }
    &:focus-visible {
      opacity: var(--ps-alert-close-focus-opacity);
      outline: var(--ps-alert-close-focus-outline-width) solid currentColor;
      outline-offset: var(--ps-alert-close-focus-outline-offset);
    }
  }
}
```

---

## ♿ Accessibilité

- `role="alert"` + `aria-live="assertive"` pour erreurs; sinon `role="status"` + `polite`.
- Bouton de fermeture avec `aria-label` clair.
- Contrastes conformes via variantes sémantiques.

---

## 📱 Comportement responsive

- Grid fluide, message s’enroule naturellement.
- `compact` pour vues denses (mobile, encarts).

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-alert/ps-alert.twig' with { variant: 'info', title: 'Information', message: 'Votre session expire bientôt.' } %}
{% include '@ps_theme/ps-alert/ps-alert.twig' with { variant: 'success', message: 'Votre demande a été envoyée avec succès.', dismissible: true } %}
{% include '@ps_theme/ps-alert/ps-alert.twig' with { variant: 'error', title: 'Erreur', message: 'Une erreur est survenue.' } %}
```

---

## 📚 Ressources

- Design tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`
- WCAG: Status Messages (4.1.3), Error Identification (3.3.1)
