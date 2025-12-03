# Badge (Atom)

**Niveau Atomic Design** : Atom / Label  
**Catégorie** : Status indicator  
**Statut** : ✅ Stable  
**Version** : 1.1.0  
**Dernière mise à jour** : 3 décembre 2025

---

## 📋 Description

Indicateur visuel compact pour afficher des statuts, labels, ou compteurs. Disponible en 8 variantes de couleur sémantiques (default, primary, secondary, gold, info, success, warning, danger), 3 tailles (small/medium/large), et forme pill optionnelle. Supporte icônes décoratives et comportement link cliquable.

**Implémentation** : `source/patterns/elements/badge/`

---

## 🎨 Aperçu visuel

```
[ Badge ]  [ ✓ Verified ]  [ 3 ]  [ Learn more → ]
Default     Primary+icon    Small   Link pill
```

---

## 🏗️ Structure BEM

```html
<!-- Default badge -->
<span class="ps-badge">Default</span>

<!-- Primary badge with icon -->
<span class="ps-badge ps-badge--primary">
  <span class="ps-badge__icon" data-icon="check"></span>
  <span class="ps-badge__text">Verified</span>
</span>

<!-- Small count -->
<span class="ps-badge ps-badge--small">3</span>

<!-- Pill link badge -->
<a href="#" class="ps-badge ps-badge--info ps-badge--pill">Learn more</a>
```

### Classes BEM

```
ps-badge                                  // Block
  ps-badge__icon                          // Icône optionnelle (via data-icon)
  ps-badge__text                          // Texte du badge

Modifiers (couleurs sémantiques):
  (default - pas de classe)               // Gris neutre (--gray-200 bg)
  ps-badge--primary                       // Primaire (vert brand)
  ps-badge--secondary                     // Secondaire (violet brand)
  ps-badge--gold                          // Or/accent (--yellow-500)
  ps-badge--info                          // Info (bleu clair)
  ps-badge--success                       // Succès (vert clair)
  ps-badge--warning                       // Avertissement (jaune)
  ps-badge--danger                        // Danger (rouge clair)
  
Modifiers (tailles):
  ps-badge--small                         // Petite taille (font 10px)
  (medium - pas de classe)                // Taille moyenne (font 12px) - DEFAULT
  ps-badge--large                         // Grande taille (font 14px)
  
Modifiers (forme):
  ps-badge--pill                          // Complètement arrondi (border-radius full)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Badge'
status: stable
group: atoms
description: 'Indicateur compact pour statuts, dates, labels, ou compteurs.'

props:
  type: object
  properties:
    text:
      type: string
      title: Texte
    variant:
      type: string
      enum: ['primary','secondary','info','success','warning','error','neutral']
      default: 'neutral'
    type:
      type: string
      enum: ['date','status','label','count']
      default: 'label'
    size:
      type: string
      enum: ['small','medium','large']
      default: 'medium'
    shape:
      type: string
      enum: ['rounded','square','pill']
      default: 'rounded'
    icon:
      type: string
      description: 'Nom d'icône optionnel'
    clickable:
      type: boolean
      default: false
    href:
      type: string
      description: 'URL si le badge est un lien'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - text
```

---

## 🎭 Variants

- **Couleurs** : `primary`|`secondary`|`info`|`success`|`warning`|`error`|`neutral`.
- **Types** : `date`|`status`|`label`|`count` (affecte l'icône par défaut et le style).
- **Tailles** : `small`|`medium`|`large`.
- **Formes** : `rounded` (coins arrondis), `square`, `pill` (complètement arrondi).
- **Cliquable** : `clickable` pour hover/focus.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-xs|sm`, `--ps-font-weight-medium|semibold`
- Couleurs par variante (bg/text/border):
  - Primary: `--ps-color-primary-100`, `--ps-color-primary-700`, `--ps-color-primary-600`
  - Secondary: `--ps-color-neutral-200`, `--ps-color-neutral-700`
  - Info: `--ps-color-info-100`, `--ps-color-info-700`
  - Success: `--ps-color-success-100`, `--ps-color-success-700`
  - Warning: `--ps-color-warning-100`, `--ps-color-warning-800`
  - Error: `--ps-color-error-100`, `--ps-color-error-700`
  - Neutral: `--ps-color-neutral-100`, `--ps-color-neutral-600`
- Bordures: `--ps-border-radius-xs` (2px), `--ps-border-radius-sm` (4px), `--ps-border-radius-full` (999px)
- Espacements: `--ps-spacing-1|2|3` (padding)
- Transitions: `--ps-transition-duration-fast`, `--ps-transition-easing-default`

---

## 🔧 Template Twig

```twig
{#
 * Template for Badge atom.
 * Variables: voir API YAML
 #}

{% set variant = variant|default('neutral') %}
{% set type = type|default('label') %}
{% set size = size|default('medium') %}
{% set shape = shape|default('rounded') %}
{% set clickable = clickable|default(false) %}

{% set icon_map = {
  'date': 'calendar',
  'status': 'check-circle',
  'count': null
} %}
{% set default_icon = icon ?? icon_map[type] %}

{% set root_classes = [
  'ps-badge',
  'ps-badge--' ~ variant,
  'ps-badge--' ~ type,
  'ps-badge--' ~ size,
  'ps-badge--' ~ shape,
  clickable ? 'ps-badge--clickable'
] %}

{% set tag = href ? 'a' : 'span' %}

<{{ tag }} {{ attributes.addClass(root_classes) }}{% if href %} href="{{ href }}"{% endif %}>
  {% if default_icon and type != 'count' %}
    <svg class="ps-badge__icon" aria-hidden="true"><use href="#icon-{{ default_icon }}"></use></svg>
  {% endif %}
  {% if type == 'count' %}
    {{ text }}
  {% else %}
    <span class="ps-badge__text">{{ text }}</span>
  {% endif %}
</{{ tag }}>
```

---

## 🎨 Styles SCSS

```scss
.ps-badge {
  display: inline-flex; align-items: center; gap: var(--ps-spacing-1, 4px);
  padding: var(--ps-spacing-1, 4px) var(--ps-spacing-2, 8px);
  font-family: var(--ps-font-family-primary);
  font-size: var(--ps-font-size-xs, 12px);
  font-weight: var(--ps-font-weight-medium, 500);
  line-height: 1.2;
  border-radius: var(--ps-border-radius-sm, 4px);
  text-decoration: none;
  white-space: nowrap;
  transition: background var(--ps-transition-duration-fast, 0.15s) var(--ps-transition-easing-default, ease);

  &__icon { width: 12px; height: 12px; flex-shrink: 0; }

  // Sizes
  &--small {
    font-size: var(--ps-font-size-xs, 11px);
    padding: 2px var(--ps-spacing-1, 6px);
    .ps-badge__icon { width: 10px; height: 10px; }
  }
  &--medium {
    font-size: var(--ps-font-size-xs, 12px);
    padding: var(--ps-spacing-1, 4px) var(--ps-spacing-2, 8px);
  }
  &--large {
    font-size: var(--ps-font-size-sm, 14px);
    padding: var(--ps-spacing-2, 6px) var(--ps-spacing-3, 12px);
    .ps-badge__icon { width: 14px; height: 14px; }
  }

  // Shapes
  &--rounded { border-radius: var(--ps-border-radius-sm, 4px); }
  &--square { border-radius: var(--ps-border-radius-xs, 2px); }
  &--pill, &--count { border-radius: var(--ps-border-radius-full, 999px); }

  // Count type: compact circular
  &--count {
    min-width: 20px; height: 20px;
    padding: 0 var(--ps-spacing-1, 6px);
    justify-content: center;
    font-weight: var(--ps-font-weight-semibold, 600);
  }

  // Variants
  &--primary {
    background: var(--ps-color-primary-100, #C5F4E9);
    color: var(--ps-color-primary-700, #0E7A5F);
  }
  &--secondary {
    background: var(--ps-color-neutral-200, #E8EBEF);
    color: var(--ps-color-neutral-700, #3B4754);
  }
  &--info {
    background: var(--ps-color-info-100, #B3E5FC);
    color: var(--ps-color-info-700, #0277BD);
  }
  &--success {
    background: var(--ps-color-success-100, #C5F4E9);
    color: var(--ps-color-success-700, #0E7A5F);
  }
  &--warning {
    background: var(--ps-color-warning-100, #FFE0B2);
    color: var(--ps-color-warning-800, #E65100);
  }
  &--error {
    background: var(--ps-color-error-100, #FFCDD2);
    color: var(--ps-color-error-700, #C62828);
  }
  &--neutral {
    background: var(--ps-color-neutral-100, #F3F6F9);
    color: var(--ps-color-neutral-600, #54636F);
  }

  // Clickable state
  &--clickable {
    cursor: pointer;
    &:hover { filter: brightness(0.95); }
    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }
}
```

---

## ♿ Accessibilité

- Texte lisible avec contraste suffisant (WCAG AA).
- Si cliquable : `<a>` avec `href` ou `<button>` avec `type="button"`.
- Si décoratif (compteur) : texte visible suffit; pas d'`aria-label` nécessaire sauf contexte ambigu.
- Focus visible pour variantes cliquables.

---

## 📱 Comportement responsive

- Inline-flex : s'adapte au contenu parent.
- Taille fixe pour `count` (min-width) pour assurer un cercle régulier.

---

## 🧪 Exemples d'usage

```twig
{# Status badge #}
{% include '@ps_theme/ps-badge/ps-badge.twig' with {
  text: 'Actif',
  variant: 'success',
  type: 'status',
  size: 'medium',
  icon: 'check-circle'
} %}

{# Date badge #}
{% include '@ps_theme/ps-badge/ps-badge.twig' with {
  text: '15 Jan 2025',
  variant: 'neutral',
  type: 'date',
  icon: 'calendar'
} %}

{# Count badge #}
{% include '@ps_theme/ps-badge/ps-badge.twig' with {
  text: '3',
  variant: 'primary',
  type: 'count',
  size: 'small'
} %}

{# Clickable label #}
{% include '@ps_theme/ps-badge/ps-badge.twig' with {
  text: 'Immobilier',
  variant: 'info',
  type: 'label',
  clickable: true,
  href: '/category/immobilier'
} %}
```

---

## 📚 Ressources

- Figma: 27 occurrences détectées (date, status, label variants)
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`, `/design/tokens/borders.yml`
