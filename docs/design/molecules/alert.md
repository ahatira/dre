# Alert (Molecule)

**Niveau Atomic Design** : Molecule / Feedback  
**Catégorie** : Status & Messaging  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Message d’état avec variants sémantiques (info, succès, avertissement, erreur). Option de fermeture. Rôles ARIA adaptés (`status` ou `alert`).

---

## 🎨 Aperçu visuel

```
[ i ] Information utile à l’utilisateur.
[ ✓ ] Opération réalisée avec succès.
[ ! ] Attention : action requise.
[ × ] Une erreur est survenue.
```

---

## 🏗️ Structure BEM

```html
<div class="ps-alert ps-alert--info" role="status" aria-live="polite">
  <span class="ps-alert__icon" aria-hidden="true"></span>
  <div class="ps-alert__content">
    <h3 class="ps-alert__title">Information</h3>
    <div class="ps-alert__message">Détails du message.</div>
  </div>
  <button class="ps-alert__close" type="button" aria-label="Fermer">×</button>
</div>
```

### Classes BEM

```
ps-alert                           // Block principal
  ps-alert__icon                   // Icône contextuelle
  ps-alert__content                // Wrapper textuel
  ps-alert__title                  // Titre (optionnel)
  ps-alert__message                // Message
  ps-alert__close                  // Bouton de fermeture (optionnel)

Modificateurs :
  ps-alert--info|success|warning|error
  ps-alert--dismissible
  ps-alert--compact
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

- Couleurs sémantiques: `--ps-color-info`, `--ps-color-success`, `--ps-color-warning`, `--ps-color-error`
- Arrière-plans doux: `--ps-color-info-soft`, `--ps-color-success-soft`, `--ps-color-warning-soft`, `--ps-color-error-soft`
- Typo: `--ps-font-family-primary`, `--ps-font-size-base`, `--ps-font-weight-medium`
- Spacing: `--ps-spacing-3`, `--ps-spacing-4`
- Icone: `--ps-icon-size-20`

Si les variantes "soft" manquent, proposer `colors.feedback.<variant>.soft`.

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
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: start;
  gap: var(--ps-spacing-3, 12px);
  padding: var(--ps-spacing-4, 16px);
  border-radius: var(--ps-border-radius-sm, 4px);
  font-family: var(--ps-font-family-primary);
  color: var(--ps-color-text, #1F2A33);

  &--compact { padding: var(--ps-spacing-3, 12px); }

  &__icon-svg { width: 20px; height: 20px; }
  &__title { margin: 0 0 var(--ps-spacing-1, 4px); font-size: var(--ps-font-size-base, 16px); font-weight: var(--ps-font-weight-medium, 500); }
  &__message { font-size: var(--ps-font-size-base, 16px); }

  &--info { background: var(--ps-color-info-soft, #E6F3FF); border: 1px solid var(--ps-color-info, #0B5FFF); }
  &--success { background: var(--ps-color-success-soft, #E8F6EF); border: 1px solid var(--ps-color-success, #2DBE6C); }
  &--warning { background: var(--ps-color-warning-soft, #FFF4E5); border: 1px solid var(--ps-color-warning, #FF9800); }
  &--error { background: var(--ps-color-error-soft, #FDEBEC); border: 1px solid var(--ps-color-error, #EB3636); }

  &__close {
    align-self: start;
    appearance: none;
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
    line-height: 1;
    padding: 0 4px;
    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
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
