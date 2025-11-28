# Divider (Atom)

**Niveau Atomic Design** : Atom / Layout  
**Catégorie** : Separator  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Séparateur visuel pour délimiter des sections de contenu. Disponible en orientations horizontale et verticale, avec variantes de style (solid, dashed, dotted), épaisseur, espacement, et couleur. Peut inclure du texte ou une icône au centre. Rôle sémantique via `role="separator"` ou simple élément `<hr>`.

---

## 🎨 Aperçu visuel

```
──────────────────────  Horizontal
│                       Vertical
│
──── Texte ────         Avec label
```

---

## 🏗️ Structure BEM

```html
<!-- Simple horizontal divider -->
<hr class="ps-divider ps-divider--horizontal ps-divider--solid" />

<!-- Vertical divider -->
<span class="ps-divider ps-divider--vertical ps-divider--solid" role="separator" aria-orientation="vertical"></span>

<!-- Divider with text -->
<div class="ps-divider ps-divider--horizontal ps-divider--with-text">
  <span class="ps-divider__line"></span>
  <span class="ps-divider__text">ou</span>
  <span class="ps-divider__line"></span>
</div>

<!-- Divider with icon -->
<div class="ps-divider ps-divider--horizontal ps-divider--with-icon">
  <span class="ps-divider__line"></span>
  <svg class="ps-divider__icon" aria-hidden="true"><use href="#icon-star"></use></svg>
  <span class="ps-divider__line"></span>
</div>
```

### Classes BEM

```
ps-divider                                // Block
  ps-divider__line                        // Ligne (si avec texte/icône)
  ps-divider__text                        // Texte central
  ps-divider__icon                        // Icône centrale

Modificateurs :
  ps-divider--horizontal                  // Horizontal (défaut)
  ps-divider--vertical                    // Vertical
  
  ps-divider--solid                       // Ligne pleine (défaut)
  ps-divider--dashed                      // Ligne pointillée
  ps-divider--dotted                      // Ligne en pointillés
  
  ps-divider--thin                        // 1px
  ps-divider--medium                      // 2px (défaut)
  ps-divider--thick                       // 4px
  
  ps-divider--primary                     // Couleur primaire
  ps-divider--secondary                   // Couleur secondaire
  ps-divider--neutral                     // Couleur neutre (défaut)
  
  ps-divider--with-text                   // Avec texte central
  ps-divider--with-icon                   // Avec icône centrale
  
  ps-divider--spacing-sm                  // Espacement réduit
  ps-divider--spacing-md                  // Espacement moyen (défaut)
  ps-divider--spacing-lg                  // Espacement large
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Divider'
status: stable
group: atoms
description: 'Séparateur visuel horizontal ou vertical avec variantes de style, couleur, et contenu central optionnel.'

props:
  type: object
  properties:
    orientation:
      type: string
      enum: ['horizontal','vertical']
      default: 'horizontal'
    style:
      type: string
      enum: ['solid','dashed','dotted']
      default: 'solid'
    thickness:
      type: string
      enum: ['thin','medium','thick']
      default: 'medium'
    color:
      type: string
      enum: ['primary','secondary','neutral']
      default: 'neutral'
    spacing:
      type: string
      enum: ['sm','md','lg']
      default: 'md'
    text:
      type: string
      description: 'Texte central optionnel'
    icon:
      type: string
      description: 'Nom d'icône centrale optionnelle'
    attributes:
      type: Drupal\Core\Template\Attribute
```

---

## 🎭 Variants

- **Orientations** : `horizontal`|`vertical`.
- **Styles** : `solid`|`dashed`|`dotted`.
- **Épaisseurs** : `thin`|`medium`|`thick`.
- **Couleurs** : `primary`|`secondary`|`neutral`.
- **Espacements** : `sm`|`md`|`lg` (margin vertical/horizontal).
- **Contenu central** : texte ou icône.

---

## 🎨 Design Tokens

- Couleurs:
  - Primary: `--ps-color-primary-600`
  - Secondary: `--ps-color-neutral-500`
  - Neutral: `--ps-color-neutral-300` (défaut)
- Épaisseurs:
  - Thin: `1px` ou `--ps-border-width-thin`
  - Medium: `2px` ou `--ps-border-width-default`
  - Thick: `4px` ou `--ps-border-width-thick`
- Espacements:
  - sm: `--ps-spacing-2` (8px)
  - md: `--ps-spacing-4` (16px)
  - lg: `--ps-spacing-6` (24px)
- Typo (texte central): `--ps-font-size-sm`, `--ps-font-weight-medium`, `--ps-color-neutral-600`

---

## 🔧 Template Twig

```twig
{#
 * Template for Divider atom.
 * Variables: voir API YAML
 #}

{% set orientation = orientation|default('horizontal') %}
{% set style = style|default('solid') %}
{% set thickness = thickness|default('medium') %}
{% set color = color|default('neutral') %}
{% set spacing = spacing|default('md') %}
{% set has_content = text or icon %}

{% set root_classes = [
  'ps-divider',
  'ps-divider--' ~ orientation,
  'ps-divider--' ~ style,
  'ps-divider--' ~ thickness,
  'ps-divider--' ~ color,
  'ps-divider--spacing-' ~ spacing,
  text ? 'ps-divider--with-text',
  icon ? 'ps-divider--with-icon'
] %}

{% if has_content %}
  <div {{ attributes.addClass(root_classes) }}>
    <span class="ps-divider__line"></span>
    {% if text %}
      <span class="ps-divider__text">{{ text }}</span>
    {% elseif icon %}
      <svg class="ps-divider__icon" aria-hidden="true"><use href="#icon-{{ icon }}"></use></svg>
    {% endif %}
    <span class="ps-divider__line"></span>
  </div>
{% else %}
  {% if orientation == 'horizontal' %}
    <hr {{ attributes.addClass(root_classes) }} />
  {% else %}
    <span {{ attributes.addClass(root_classes) }} role="separator" aria-orientation="vertical"></span>
  {% endif %}
{% endif %}
```

---

## 🎨 Styles SCSS

```scss
.ps-divider {
  border: none;
  background: none;
  
  // Horizontal (default)
  &--horizontal {
    display: block;
    height: 0;
    border-top-style: solid;
    border-top-width: 2px;
    border-top-color: var(--ps-color-neutral-300, #D2D7DB);
    
    &.ps-divider--spacing-sm { margin: var(--ps-spacing-2, 8px) 0; }
    &.ps-divider--spacing-md { margin: var(--ps-spacing-4, 16px) 0; }
    &.ps-divider--spacing-lg { margin: var(--ps-spacing-6, 24px) 0; }
  }

  // Vertical
  &--vertical {
    display: inline-block;
    width: 0;
    height: 100%;
    border-left-style: solid;
    border-left-width: 2px;
    border-left-color: var(--ps-color-neutral-300, #D2D7DB);
    vertical-align: middle;
    
    &.ps-divider--spacing-sm { margin: 0 var(--ps-spacing-2, 8px); }
    &.ps-divider--spacing-md { margin: 0 var(--ps-spacing-4, 16px); }
    &.ps-divider--spacing-lg { margin: 0 var(--ps-spacing-6, 24px); }
  }

  // Styles
  &--dashed {
    &.ps-divider--horizontal { border-top-style: dashed; }
    &.ps-divider--vertical { border-left-style: dashed; }
  }
  &--dotted {
    &.ps-divider--horizontal { border-top-style: dotted; }
    &.ps-divider--vertical { border-left-style: dotted; }
  }

  // Thickness
  &--thin {
    &.ps-divider--horizontal { border-top-width: 1px; }
    &.ps-divider--vertical { border-left-width: 1px; }
  }
  &--medium {
    &.ps-divider--horizontal { border-top-width: 2px; }
    &.ps-divider--vertical { border-left-width: 2px; }
  }
  &--thick {
    &.ps-divider--horizontal { border-top-width: 4px; }
    &.ps-divider--vertical { border-left-width: 4px; }
  }

  // Colors
  &--primary {
    &.ps-divider--horizontal { border-top-color: var(--ps-color-primary-600, #0DB089); }
    &.ps-divider--vertical { border-left-color: var(--ps-color-primary-600, #0DB089); }
  }
  &--secondary {
    &.ps-divider--horizontal { border-top-color: var(--ps-color-neutral-500, #6E7C89); }
    &.ps-divider--vertical { border-left-color: var(--ps-color-neutral-500, #6E7C89); }
  }
  &--neutral {
    &.ps-divider--horizontal { border-top-color: var(--ps-color-neutral-300, #D2D7DB); }
    &.ps-divider--vertical { border-left-color: var(--ps-color-neutral-300, #D2D7DB); }
  }

  // With text/icon
  &--with-text, &--with-icon {
    display: flex; align-items: center; gap: var(--ps-spacing-3, 12px);
    border: none;
  }

  &__line {
    flex: 1; height: 0;
    border-top: 2px solid var(--ps-color-neutral-300, #D2D7DB);
  }

  &__text {
    font-family: var(--ps-font-family-primary);
    font-size: var(--ps-font-size-sm, 14px);
    font-weight: var(--ps-font-weight-medium, 500);
    color: var(--ps-color-neutral-600, #54636F);
    white-space: nowrap;
  }

  &__icon {
    width: 16px; height: 16px;
    color: var(--ps-color-neutral-500, #6E7C89);
    flex-shrink: 0;
  }

  // Adjust line styles for with-text/icon variants
  &--solid.ps-divider--with-text .ps-divider__line,
  &--solid.ps-divider--with-icon .ps-divider__line {
    border-top-style: solid;
  }
  &--dashed.ps-divider--with-text .ps-divider__line,
  &--dashed.ps-divider--with-icon .ps-divider__line {
    border-top-style: dashed;
  }
  &--dotted.ps-divider--with-text .ps-divider__line,
  &--dotted.ps-divider--with-icon .ps-divider__line {
    border-top-style: dotted;
  }

  // Color for lines in with-text/icon variants
  &--primary.ps-divider--with-text .ps-divider__line,
  &--primary.ps-divider--with-icon .ps-divider__line {
    border-top-color: var(--ps-color-primary-600, #0DB089);
  }
  &--secondary.ps-divider--with-text .ps-divider__line,
  &--secondary.ps-divider--with-icon .ps-divider__line {
    border-top-color: var(--ps-color-neutral-500, #6E7C89);
  }
}
```

---

## ♿ Accessibilité

- `<hr>` natif pour séparateurs horizontaux simples (sémantique).
- `role="separator"` avec `aria-orientation="vertical"` pour verticaux.
- Pas de focus : élément non-interactif.
- Texte/icône central décoratif : pas d'`aria-label` requis.

---

## 📱 Comportement responsive

- Horizontal : largeur 100% (block).
- Vertical : hauteur héritée du conteneur parent (inline-block ou flex).
- Espacement adapté via modificateurs.

---

## 🧪 Exemples d'usage

```twig
{# Simple horizontal divider #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  orientation: 'horizontal',
  style: 'solid',
  thickness: 'medium',
  color: 'neutral',
  spacing: 'md'
} %}

{# Vertical divider #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  orientation: 'vertical',
  style: 'solid',
  thickness: 'thin',
  color: 'neutral',
  spacing: 'sm'
} %}

{# Divider with text #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  orientation: 'horizontal',
  text: 'ou',
  style: 'solid',
  color: 'secondary'
} %}

{# Divider with icon #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  orientation: 'horizontal',
  icon: 'star',
  style: 'dashed',
  color: 'primary'
} %}

{# Thick primary divider #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  thickness: 'thick',
  color: 'primary',
  spacing: 'lg'
} %}
```

---

## 📚 Ressources

- Figma: Extensive occurrences (section separators)
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/borders.yml`, `/design/tokens/typography.yml`
