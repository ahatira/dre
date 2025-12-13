# Avatar (Molecule)

**Niveau Atomic Design** : Molecule / Component  
**Catégorie** : Identity  
**Statut** : ✅ Stable  
**Version** : 1.1.0  
**Dernière mise à jour** : 3 décembre 2025

---

## 📋 Description

User or entity visual representation composed of three atomic elements: image, text (initials), and status badge. Features automatic fallback hierarchy (image → initials → gender icon) and interactive states.

**Implémentation** : `source/patterns/components/avatar/`

---

## 🎨 Aperçu visuel

```
┌──────┐   ╭──────╮   ╭──────╮
│ AB   │   │ [Img]│   │  👤  │
└──────┘   ╰──────╯   ╰──────╯
Square     Circle     Fallback
```

---

## 🏗️ Structure BEM

```html
<!-- Image avatar -->
<div class="ps-avatar ps-avatar--medium ps-avatar--circle">
  <img class="ps-avatar__image" src="/path/to/photo.jpg" alt="John Doe" />
</div>

<!-- Initials avatar -->
<div class="ps-avatar ps-avatar--medium ps-avatar--circle ps-avatar--initials">
  <span class="ps-avatar__text">JD</span>
</div>

<!-- Icon fallback avatar -->
<div class="ps-avatar ps-avatar--medium ps-avatar--circle ps-avatar--icon">
  <span class="ps-avatar__icon" data-icon="user" aria-hidden="true"></span>
</div>

<!-- With status badge -->
<div class="ps-avatar ps-avatar--medium ps-avatar--circle ps-avatar--has-status">
  <img class="ps-avatar__image" src="/path/to/photo.jpg" alt="John Doe" />
  <span class="ps-avatar__status ps-avatar__status--online" aria-label="En ligne"></span>
</div>
```

### Classes BEM

```
ps-avatar                                 // Block
  ps-avatar__image                        // Image
  ps-avatar__text                         // Initiales
  ps-avatar__icon                         // Icône par défaut
  ps-avatar__status                       // Badge de statut

Modificateurs :
  ps-avatar--small                        // 32px
  ps-avatar--medium                       // 40px (défaut)
  ps-avatar--large                        // 56px
  
  ps-avatar--circle                       // Forme ronde (défaut)
  ps-avatar--square                       // Forme carrée
  ps-avatar--rounded                      // Coins arrondis
  
  ps-avatar--initials                     // Type initiales
  ps-avatar--icon                         // Type icône
  ps-avatar--bordered                     // Bordure
  ps-avatar--clickable                    // Cliquable (hover/focus)
  ps-avatar--has-status                   // Avec badge de statut
  
  ps-avatar__status--online               // Statut en ligne (vert)
  ps-avatar__status--offline              // Statut hors ligne (gris)
  ps-avatar__status--busy                 // Statut occupé (rouge)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Avatar'
status: stable
group: atoms
description: 'Avatar utilisateur avec image, initiales, ou icône par défaut et statut optionnel.'

props:
  type: object
  properties:
    src:
      type: string
      title: URL de l'image
    alt:
      type: string
      title: Texte alternatif
    initials:
      type: string
      title: Initiales (ex "JD")
      description: 'Utilisé si pas d'image'
    size:
      type: string
      enum: ['small','medium','large']
      default: 'medium'
      description: 'Taille de l'avatar. Système standardisé : small (32px), medium (40px), large (56px)'
    shape:
      type: string
      enum: ['circle','square','rounded']
      default: 'circle'
    status:
      type: string
      enum: ['online','offline','busy']
      description: 'Badge de statut optionnel'
    bordered:
      type: boolean
      default: false
    clickable:
      type: boolean
      default: false
    href:
      type: string
      description: 'URL si cliquable'
    attributes:
      type: Drupal\Core\Template\Attribute
```

---

## 🎭 Variants

- **Tailles** : `small` (32px)|`medium` (40px)|`large` (56px).
- **Formes** : `circle`|`square`|`rounded`.
- **Types** : image, initiales, icône (fallback automatique).
- **Statut** : `online`|`offline`|`busy` (badge).
- **Bordure** : `bordered` (contour blanc/neutre).
- **Cliquable** : `clickable` avec hover/focus.

---

## 🎨 Design Tokens

- Tailles:
  - xs: 24px
  - sm: 32px
  - md: 40px
  - lg: 56px
  - xl: 80px
- Couleurs initiales: `--primary` (bg), `--white` (text)
- Couleurs icône: `--gray-200` (bg), `--gray-600` (icon)
- Bordure: `--white`, `--border-size-1`
- Statut:
  - online: `--success`
  - offline: `--gray-400`
  - busy: `--danger`
- Bordures: `--radius-round`, `--radius-2`, `--radius-1`
- Transitions: `--duration-fast`

---

## 🔧 Template Twig

```twig
{#
 * Template for Avatar atom.
 * Variables: voir API YAML
 #}

{% set size = size|default('md') %}
{% set shape = shape|default('circle') %}
{% set bordered = bordered|default(false) %}
{% set clickable = clickable|default(false) %}

{% set has_image = src %}
{% set has_initials = initials and not has_image %}
{% set has_icon = not has_image and not has_initials %}

{% set root_classes = [
  'ps-avatar',
  'ps-avatar--' ~ size,
  'ps-avatar--' ~ shape,
  has_initials ? 'ps-avatar--initials',
  has_icon ? 'ps-avatar--icon',
  bordered ? 'ps-avatar--bordered',
  clickable ? 'ps-avatar--clickable',
  status ? 'ps-avatar--has-status'
] %}

{% set tag = href ? 'a' : 'div' %}

<{{ tag }} {{ attributes.addClass(root_classes) }}{% if href %} href="{{ href }}"{% endif %}>
  {% if has_image %}
    <img class="ps-avatar__image" src="{{ src }}" alt="{{ alt|default('') }}" loading="lazy" />
  {% elseif has_initials %}
    <span class="ps-avatar__text">{{ initials }}</span>
  {% else %}
    <span class="ps-avatar__icon" data-icon="user" aria-hidden="true"></span>
  {% endif %}
  
  {% if status %}
    <span class="ps-avatar__status ps-avatar__status--{{ status }}" aria-label="{% if status == 'online' %}En ligne{% elseif status == 'busy' %}Occupé{% else %}Hors ligne{% endif %}"></span>
  {% endif %}
</{{ tag }}>
```

---

## 🎨 Styles SCSS

```scss
.ps-avatar {
  position: relative; display: inline-flex; align-items: center; justify-content: center;
  overflow: hidden; flex-shrink: 0;
  background: var(--gray-200);
  text-decoration: none;

  &__image {
    width: 100%; height: 100%; object-fit: cover;
  }

  &__text {
    font-family: var(--ps-font-family-primary);
    font-weight: var(--ps-font-weight-semibold, 600);
    color: var(--ps-color-neutral-0, #FFF);
    text-transform: uppercase;
    user-select: none;
  }

  &__icon {
    color: var(--ps-color-neutral-600, #54636F);
  }

  &__status {
    position: absolute;
    width: 25%; height: 25%;
    border-radius: var(--ps-border-radius-full, 50%);
    border: 2px solid var(--ps-color-neutral-0, #FFF);
    bottom: 0; right: 0;
  }
  &__status--online { background: var(--ps-color-success-600, #0DB089); }
  &__status--offline { background: var(--ps-color-neutral-400, #9AA6B2); }
  &__status--busy { background: var(--ps-color-error-600, #E53935); }

  // Sizes (standardized)
  &--small {
    width: 32px; height: 32px;
    .ps-avatar__text { font-size: 12px; }
    .ps-avatar__icon { width: 16px; height: 16px; }
  }
  &--medium {
    width: 40px; height: 40px;
    .ps-avatar__text { font-size: 14px; }
    .ps-avatar__icon { width: 20px; height: 20px; }
  }
  &--large {
    width: 56px; height: 56px;
    .ps-avatar__text { font-size: 18px; }
    .ps-avatar__icon { width: 28px; height: 28px; }
  }

  // Shapes
  &--circle { border-radius: var(--ps-border-radius-full, 50%); }
  &--square { border-radius: 0; }
  &--rounded { border-radius: var(--ps-border-radius-sm, 4px); }

  // Types
  &--initials {
    background: var(--ps-color-primary-600, #0DB089);
  }
  &--icon {
    background: var(--ps-color-neutral-100, #F3F6F9);
  }

  // Bordered
  &--bordered {
    border: var(--ps-border-width-default, 2px) solid var(--ps-color-neutral-0, #FFF);
  }

  // Clickable
  &--clickable {
    cursor: pointer;
    transition: transform var(--ps-transition-duration-fast, 0.15s) var(--ps-transition-easing-default, ease);
    &:hover { transform: scale(1.05); }
    &:focus-visible {
      outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF);
      outline-offset: 2px;
    }
  }
}
```

---

## ♿ Accessibilité

- Image avatar : `alt` descriptif (nom utilisateur).
- Initiales/icône : texte visible suffit ou `aria-label` sur le conteneur si cliquable.
- Badge de statut : `aria-label` pour annoncer l'état (en ligne, occupé, hors ligne).
- Focus visible si cliquable.

---

## 📱 Comportement responsive

- Tailles fixes adaptées au contexte.
- Inline-flex : s'intègre dans des layouts variés (listes, headers, etc.).

---

## 🧪 Exemples d'usage

```twig
{# Image avatar #}
{% include '@ps_theme/ps-avatar/ps-avatar.twig' with {
  src: '/images/users/john-doe.jpg',
  alt: 'John Doe',
  size: 'md',
  shape: 'circle',
  status: 'online'
} %}

{# Initials avatar #}
{% include '@ps_theme/ps-avatar/ps-avatar.twig' with {
  initials: 'JD',
  size: 'lg',
  shape: 'rounded',
  bordered: true
} %}

{# Icon fallback #}
{% include '@ps_theme/ps-avatar/ps-avatar.twig' with {
  size: 'sm',
  shape: 'circle'
} %}

{# Clickable avatar #}
{% include '@ps_theme/ps-avatar/ps-avatar.twig' with {
  src: '/images/users/jane-smith.jpg',
  alt: 'Jane Smith',
  size: 'md',
  clickable: true,
  href: '/user/jane-smith'
} %}
```

---

## 📚 Ressources

- Figma: Partial occurrences (user representations)
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/borders.yml`, `/design/tokens/transitions.yml`
