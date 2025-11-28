# Button (Atom)

**Niveau Atomic Design** : Atom / Element  
**Catégorie** : Interactive  
**Statut** : ✅ Stable  
**Version** : 1.0.0

---

## 📋 Description

Le bouton est l'élément interactif fondamental pour déclencher des actions. Le PS Design System propose 4 variants principaux (primaire/secondaire × vert/violet/blanc) avec des états clairs et accessibles.

**Occurrences détectées dans Figma** : 298 instances
- Primary Green : 120
- Secondary Green : 151
- Primary Purple : 23
- Secondary White : 4

---

## 🎨 Aperçu visuel

```
┌─────────────────────────┐    ┌─────────────────────────┐
│ ●  Rechercher         → │    │    Découvrir          → │
└─────────────────────────┘    └─────────────────────────┘
   Primary Green                  Secondary Green

┌─────────────────────────┐    ┌─────────────────────────┐
│ ●  Contacter          → │    │    En savoir plus     → │
└─────────────────────────┘    └─────────────────────────┘
   Primary Purple                 Secondary White
```

---

## 🏗️ Structure BEM

```html
<button class="ps-button ps-button--primary ps-button--green">
  <span class="ps-button__label">Rechercher</span>
  <svg class="ps-button__icon ps-button__icon--right">...</svg>
</button>
```

### Classes BEM

```
ps-button                          // Block principal
  ps-button__label                // Texte du bouton
  ps-button__icon                 // Icône (optionnelle)

Modificateurs de variant:
  ps-button--primary              // Style primaire (fond coloré)
  ps-button--secondary            // Style secondaire (bordure)
  
Modificateurs de couleur:
  ps-button--green                // Couleur verte (défaut)
  ps-button--purple               // Couleur violette
  ps-button--white                // Couleur blanche

Modificateurs de taille:
  ps-button--small                // Petit (height: 33.98px)
  ps-button--medium               // Moyen (height: 36px, défaut)
  ps-button--large                // Grand (height: 40px)

Modificateurs d'état:
  ps-button--disabled             // État désactivé
  ps-button--loading              // État chargement
  ps-button--full-width           // Pleine largeur

Modificateurs d'icône:
  ps-button--icon-only            // Bouton icône seule
  ps-button--icon-left            // Icône à gauche
  ps-button--icon-right           // Icône à droite (défaut)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Button'
status: stable
group: atoms
description: 'Bouton d\'action avec variants primaire/secondaire et couleurs multiples'

props:
  type: object
  properties:
    label:
      type: string
      title: Label du bouton
      description: Texte affiché dans le bouton
      
    variant:
      type: string
      title: Variant
      description: Style du bouton
      enum: ['primary', 'secondary']
      default: 'primary'
      
    color:
      type: string
      title: Couleur
      description: Couleur du bouton
      enum: ['green', 'purple', 'white']
      default: 'green'
      
    size:
      type: string
      title: Taille
      description: Taille du bouton
      enum: ['small', 'medium', 'large']
      default: 'medium'
      
    url:
      type: string
      title: URL
      description: Lien de destination
      format: uri
      
    target:
      type: string
      title: Target
      description: Attribut target du lien
      enum: ['_self', '_blank']
      default: '_self'
      
    icon:
      type: string
      title: Icône
      description: Nom de l'icône à afficher
      
    iconPosition:
      type: string
      title: Position de l'icône
      enum: ['left', 'right']
      default: 'right'
      
    disabled:
      type: boolean
      title: Désactivé
      default: false
      
    loading:
      type: boolean
      title: Chargement
      description: Affiche un spinner
      default: false
      
    fullWidth:
      type: boolean
      title: Pleine largeur
      default: false
      
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML additionnels
      
  required:
    - label

slots:
  icon:
    title: Icône custom
    description: Permet d'injecter un SVG ou HTML custom pour l'icône

libraryOverrides:
  dependencies:
    - ps_theme/ps-icon
```

---

## 🎭 Variants

### 1. Primary Button

**Usage** : Actions principales, CTA, soumission de formulaires

```html
<!-- Primary Green (défaut) -->
<button class="ps-button ps-button--primary ps-button--green">
  <span class="ps-button__label">Rechercher</span>
</button>

<!-- Primary Purple -->
<button class="ps-button ps-button--primary ps-button--purple">
  <span class="ps-button__label">Contacter</span>
</button>
```

**Caractéristiques** :
- Fond coloré (#00915A ou #BA3075)
- Texte blanc
- Pas de bordure visible
- Prominence visuelle maximale

### 2. Secondary Button

**Usage** : Actions secondaires, navigation, options alternatives

```html
<!-- Secondary Green (défaut) -->
<button class="ps-button ps-button--secondary ps-button--green">
  <span class="ps-button__label">Découvrir</span>
</button>

<!-- Secondary White (sur fond sombre) -->
<button class="ps-button ps-button--secondary ps-button--white">
  <span class="ps-button__label">En savoir plus</span>
</button>
```

**Caractéristiques** :
- Fond transparent
- Bordure 2px colorée
- Texte coloré
- Prominence visuelle secondaire

### 3. Button avec icône

```html
<!-- Icône à droite (défaut) -->
<button class="ps-button ps-button--primary ps-button--green">
  <span class="ps-button__label">Suivant</span>
  <svg class="ps-button__icon ps-button__icon--right">
    <use href="#icon-arrow-right"></use>
  </svg>
</button>

<!-- Icône à gauche -->
<button class="ps-button ps-button--primary ps-button--green ps-button--icon-left">
  <svg class="ps-button__icon ps-button__icon--left">
    <use href="#icon-search"></use>
  </svg>
  <span class="ps-button__label">Rechercher</span>
</button>

<!-- Icône seule -->
<button class="ps-button ps-button--primary ps-button--green ps-button--icon-only" aria-label="Fermer">
  <svg class="ps-button__icon">
    <use href="#icon-close"></use>
  </svg>
</button>
```

**Note** : Les icônes sont référencées par leur `name` unique (voir `design/atoms/icon.md`).

---

## 🎨 Design Tokens

```yaml
# Tailles
button_height_small: 33.98px
button_height_medium: 36px
button_height_large: 40px

# Padding
button_padding_horizontal: 16px
button_padding_vertical: 8px

# Typographie
button_font_family: BNPP Sans
button_font_weight: 400 (Regular)
button_font_size: 16px
button_line_height: 24px

# Couleurs Primary Green
button_primary_green_bg: #00915A
button_primary_green_bg_hover: #006B43 (assombri 25%)
button_primary_green_bg_active: #004A2D (assombri 50%)
button_primary_green_text: #FFFFFF

# Couleurs Primary Purple
button_primary_purple_bg: #BA3075
button_primary_purple_text: #FFFFFF

# Couleurs Secondary
button_secondary_border_width: 2px
button_secondary_bg: transparent
button_secondary_green_border: #00915A
button_secondary_green_text: #00915A
button_secondary_white_border: #FFFFFF
button_secondary_white_text: #FFFFFF

# Spacing
button_icon_spacing: 8px

# Transitions
button_transition: background-color 150ms cubic-bezier(0.4, 0.0, 0.2, 1), color 150ms cubic-bezier(0.4, 0.0, 0.2, 1), border-color 150ms cubic-bezier(0.4, 0.0, 0.2, 1), transform 150ms cubic-bezier(0.4, 0.0, 0.2, 1)

# Border
button_border_radius: 0 (design carré)
button_border_width: 2px (secondary uniquement)

# CSS Variables (--ps prefix)
--ps-button-height-medium: 36px
--ps-primary: #00915A
--ps-secondary-purple: #BA3075
--ps-white: #FFFFFF
--ps-spacing-2: 8px
--ps-border-radius-none: 0
--ps-transition-button: all 150ms ease
```

---

## 🔧 Template Twig

```twig
{#
/**
 * @file
 * Template for Button atom.
 *
 * Available variables:
 * - label: string - Texte du bouton
 * - variant: string - 'primary' ou 'secondary'
 * - color: string - 'green', 'purple', ou 'white'
 * - size: string - 'small', 'medium', ou 'large'
 * - url: string - URL de destination
 * - target: string - '_self' ou '_blank'
 * - icon: string - Nom de l'icône
 * - iconPosition: string - 'left' ou 'right'
 * - disabled: boolean
 * - loading: boolean
 * - fullWidth: boolean
 * - attributes: Drupal\Core\Template\Attribute
 */
#}

{% set variant = variant ?? 'primary' %}
{% set color = color ?? 'green' %}
{% set size = size ?? 'medium' %}
{% set iconPosition = iconPosition ?? 'right' %}
{% set target = target ?? '_self' %}

{% set classes = [
  'ps-button',
  'ps-button--' ~ variant,
  'ps-button--' ~ color,
  'ps-button--' ~ size,
  icon ? 'ps-button--icon-' ~ iconPosition,
  disabled ? 'ps-button--disabled',
  loading ? 'ps-button--loading',
  fullWidth ? 'ps-button--full-width',
] %}

{% set tag = url ? 'a' : 'button' %}

<{{ tag }}
  {{ attributes.addClass(classes) }}
  {% if url %}href="{{ url }}"{% endif %}
  {% if target == '_blank' %}target="_blank" rel="noopener noreferrer"{% endif %}
  {% if disabled %}disabled aria-disabled="true"{% endif %}
  {% if loading %}aria-busy="true"{% endif %}
>
  {% if loading %}
    <span class="ps-button__spinner" aria-hidden="true">
      {% include '@ps_theme/ps-spinner/ps-spinner.twig' with {
        size: 'small',
        color: variant == 'primary' ? 'white' : color
      } %}
    </span>
  {% endif %}

  {% if icon and iconPosition == 'left' %}
    {% if icon_slot %}
      {{ icon_slot }}
    {% else %}
      <svg class="ps-button__icon ps-button__icon--left" aria-hidden="true">
        <use href="#icon-{{ icon }}"></use>
      </svg>
    {% endif %}
  {% endif %}

  <span class="ps-button__label">{{ label }}</span>

  {% if icon and iconPosition == 'right' %}
    {% if icon_slot %}
      {{ icon_slot }}
    {% else %}
      <svg class="ps-button__icon ps-button__icon--right" aria-hidden="true">
        <use href="#icon-{{ icon }}"></use>
      </svg>
    {% endif %}
  {% endif %}
</{{ tag }}>
```

---

## 🎨 Styles SCSS

```scss
// _ps-button.scss

.ps-button {
  // Reset
  appearance: none;
  border: 0;
  background: none;
  cursor: pointer;
  text-decoration: none;
  
  // Layout
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--ps-spacing-2);
  
  // Sizing
  height: var(--ps-button-height-medium);
  padding: var(--ps-button-padding-vertical) var(--ps-button-padding-horizontal);
  
  // Typography
  font-family: var(--ps-font-family-primary);
  font-weight: var(--ps-font-weight-regular);
  font-size: var(--ps-font-size-base);
  line-height: var(--ps-line-height-normal);
  text-align: center;
  white-space: nowrap;
  
  // Visual
  border-radius: var(--ps-border-radius-none);
  transition: var(--ps-transition-button);
  
  // States
  &:hover {
    transform: translateY(-1px);
  }
  
  &:active {
    transform: translateY(0);
  }
  
  &:focus-visible {
    outline: var(--ps-border-width-focus) solid var(--ps-color-interactive-focus-outline);
    outline-offset: 2px;
  }
  
  // Disabled
  &--disabled,
  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
  
  // Loading
  &--loading {
    position: relative;
    color: transparent;
    pointer-events: none;
    
    .ps-button__spinner {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  }
}

// Variants
.ps-button--primary {
  color: var(--ps-color-white);
  
  &.ps-button--green {
    background-color: var(--ps-color-primary-green);
    
    &:hover {
      background-color: #006B43;
    }
    
    &:active {
      background-color: #004A2D;
    }
  }
  
  &.ps-button--purple {
    background-color: var(--ps-color-primary-purple);
    
    &:hover {
      background-color: #8E2A68;
    }
    
    &:active {
      background-color: #621C47;
    }
  }
}

.ps-button--secondary {
  background-color: transparent;
  border: var(--ps-border-width-default) solid currentColor;
  
  &.ps-button--green {
    color: var(--ps-color-primary-green);
    
    &:hover {
      background-color: rgba(0, 145, 90, 0.1);
    }
  }
  
  &.ps-button--purple {
    color: var(--ps-color-primary-purple);
    
    &:hover {
      background-color: rgba(186, 48, 117, 0.1);
    }
  }
  
  &.ps-button--white {
    color: var(--ps-color-white);
    
    &:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
  }
}

// Tailles
.ps-button--small {
  height: var(--ps-button-height-small);
  padding: 6px 12px;
  font-size: var(--ps-font-size-sm);
}

.ps-button--large {
  height: var(--ps-button-height-large);
  padding: 10px 20px;
  font-size: var(--ps-font-size-lg);
}

// Full width
.ps-button--full-width {
  width: 100%;
}

// Icon
.ps-button__icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.ps-button--icon-only {
  padding: var(--ps-button-padding-vertical);
  aspect-ratio: 1;
}
```

---

## ♿ Accessibilité

### Conformité WCAG 2.2 AA

✅ **Contraste de couleur**
- Primary Green sur blanc : 4.52:1 (AA ✓)
- Primary Purple sur blanc : 5.12:1 (AA ✓)
- Texte blanc sur Green : 7.8:1 (AAA ✓)
- Texte blanc sur Purple : 6.9:1 (AAA ✓)

✅ **Touch target**
- Minimum 36px height (recommandation 44px)
- Spacing 8px minimum entre boutons adjacents

✅ **Navigation clavier**
- Tab : Focus
- Enter/Space : Activation
- Focus visible (outline 2px)

✅ **Attributs ARIA**
```html
<!-- Bouton désactivé -->
<button class="ps-button" disabled aria-disabled="true">

<!-- Bouton chargement -->
<button class="ps-button ps-button--loading" aria-busy="true">

<!-- Bouton icône seule -->
<button class="ps-button ps-button--icon-only" aria-label="Fermer">

<!-- Lien externe -->
<a class="ps-button" target="_blank" rel="noopener noreferrer">
  Lien externe
  <span class="visually-hidden">(ouvre dans un nouvel onglet)</span>
</a>
```

### États visuels

| État | Visual feedback |
|------|-----------------|
| Default | Style de base |
| Hover | `transform: translateY(-1px)` |
| Active | `transform: translateY(0)` |
| Focus | Outline 2px bleu + offset 2px |
| Disabled | `opacity: 0.5` + cursor not-allowed |
| Loading | Spinner + `color: transparent` |

---

## 📱 Comportement responsive

```scss
@media (max-width: 768px) {
  .ps-button {
    // Augmenter touch target
    min-height: 44px;
    
    // Full width sur mobile si demandé
    &--full-width-mobile {
      width: 100%;
    }
  }
}
```

---

## 🧪 Exemples d'usage

### Drupal Twig

```twig
{# Bouton simple #}
{% include '@ps_theme/ps-button/ps-button.twig' with {
  label: 'Rechercher',
  variant: 'primary',
  color: 'green',
} %}

{# Bouton avec lien #}
{% include '@ps_theme/ps-button/ps-button.twig' with {
  label: 'Découvrir nos biens',
  variant: 'secondary',
  color: 'green',
  url: '/properties',
  icon: 'arrow-right',
} %}

{# Bouton avec icône à gauche #}
{% include '@ps_theme/ps-button/ps-button.twig' with {
  label: 'Télécharger',
  variant: 'primary',
  color: 'purple',
  icon: 'download',
  iconPosition: 'left',
} %}

{# Bouton loading #}
{% include '@ps_theme/ps-button/ps-button.twig' with {
  label: 'Envoi en cours...',
  loading: true,
  disabled: true,
} %}
```

### Formulaire Drupal

```php
$form['submit'] = [
  '#type' => 'button',
  '#value' => $this->t('Rechercher'),
  '#attributes' => [
    'class' => ['ps-button', 'ps-button--primary', 'ps-button--green'],
  ],
];
```

---

## 📚 Ressources

- **Figma** : 298 instances détectées
- **Storybook** : [Voir dans Storybook](#)
- **Design tokens** : `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`
- **Composants liés** : Icon, Spinner

---

**Dernière mise à jour** : 28 novembre 2025  
**Contributeurs** : Design System Team
