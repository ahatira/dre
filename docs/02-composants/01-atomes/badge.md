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

## 🎨 Design Tokens (réels)

- Typo : `--font-body`, `--font-size-0|1`, `--font-weight-600`, `--leading-tight`
- Couleurs (bg/text/border) en sémantique existante :
  - Default/neutral : `--neutral`, `--neutral-hover`, `--neutral-text`, `--border-default`
  - Primary : `--primary`, `--primary-hover`, `--primary-active`, `--primary-text`, `--primary-border`
  - Secondary : `--secondary`, `--secondary-hover`, `--secondary-active`, `--secondary-text`, `--secondary-border`
  - Info : `--info`, `--info-hover`, `--info-active`, `--info-text`, `--info-border`
  - Success : `--success`, `--success-hover`, `--success-active`, `--success-text`, `--success-border`
  - Warning : `--warning`, `--warning-hover`, `--warning-active`, `--warning-text`, `--warning-border`
  - Danger : `--danger`, `--danger-hover`, `--danger-active`, `--danger-text`, `--danger-border`
- Rayon : `--radius-2` (4px), `--radius-3` (6px), `--radius-round` (pill)
- Bordure : `--border-size-1` (1px) ou `--border-size-2` (2px)
- Espacements : `--size-1|2|3` pour les paddings internes
- Ombre optionnelle : `--shadow-1` ou `--shadow-2`
- Transition : `--duration-fast` + `--ease-3`

ℹ️ Le variant « gold » n’existe pas dans les tokens : à remplacer par `warning` ou définir un token approuvé avant usage.

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
  /* Layer 2: composant (variables overridables) */
  --badge-bg: var(--gray-200);
  --badge-color: var(--gray-600);
  --badge-padding-y: var(--size-1);
  --badge-padding-x: var(--size-2);
  --badge-font-size: var(--font-size--1); // 12px
  --badge-font-weight: var(--font-weight-500);
  --badge-radius: var(--radius-2);
  --badge-icon-size: var(--font-size--1);
  --badge-gap: var(--size-1);

  display: inline-flex;
  align-items: center;
  gap: var(--badge-gap);
  padding: var(--badge-padding-y) var(--badge-padding-x);
  font-family: var(--font-sans);
  font-size: var(--badge-font-size);
  font-weight: var(--badge-font-weight);
  line-height: 1.2;
  border-radius: var(--badge-radius);
  text-decoration: none;
  white-space: nowrap;
  background: var(--badge-bg);
  color: var(--badge-color);
  transition: background var(--duration-fast) var(--ease-4);

  &__icon {
    inline-size: var(--badge-icon-size);
    block-size: var(--badge-icon-size);
    flex-shrink: 0;
  }

  // Tailles
  &--small {
    --badge-font-size: var(--font-size--2);
    --badge-padding-y: var(--size-05);
    --badge-padding-x: var(--size-1);
    --badge-icon-size: var(--font-size--2);
  }

  &--large {
    --badge-font-size: var(--font-size-0);
    --badge-padding-y: var(--size-105);
    --badge-padding-x: var(--size-3);
    --badge-icon-size: var(--font-size-0);
  }

  // Formes
  &--pill { --badge-radius: var(--radius-round); }

  // Variantes sémantiques
  &--default { --badge-bg: var(--gray-200); --badge-color: var(--gray-600); }
  &--primary { --badge-bg: var(--primary); --badge-color: var(--white); }
  &--secondary { --badge-bg: var(--secondary); --badge-color: var(--white); }
  &--info { --badge-bg: var(--blue-100); --badge-color: var(--blue-700); }
  &--success { --badge-bg: var(--green-100); --badge-color: var(--green-700); }
  &--warning { --badge-bg: var(--yellow-100); --badge-color: var(--yellow-700); }
  &--danger { --badge-bg: var(--red-100); --badge-color: var(--red-700); }

  // État cliquable (lien)
  &:is(a) {
    &:hover { filter: brightness(0.95); }
    &:focus-visible {
      outline: var(--border-size-2) solid var(--primary);
      outline-offset: var(--size-05);
    }
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
