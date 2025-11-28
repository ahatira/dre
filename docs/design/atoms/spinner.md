# Spinner (Atom)

**Niveau Atomic Design** : Atom / Feedback  
**Catégorie** : Loading indicator  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Indicateur de chargement animé pour les états asynchrones (chargement de données, soumission de formulaire, etc.). Disponible en plusieurs variantes (circular, dots, bars), tailles, et couleurs. Inclut `role="status"` et `aria-live` pour accessibilité, avec texte de chargement masqué visuellement mais annoncé aux lecteurs d'écran.

---

## 🎨 Aperçu visuel

```
  ⟳  Circular
  ⋯  Dots
  |||  Bars
```

---

## 🏗️ Structure BEM

```html
<!-- Circular spinner -->
<div class="ps-spinner ps-spinner--circular ps-spinner--medium ps-spinner--primary" role="status" aria-live="polite">
  <svg class="ps-spinner__svg" viewBox="0 0 50 50">
    <circle class="ps-spinner__circle" cx="25" cy="25" r="20" fill="none" stroke-width="4"></circle>
  </svg>
  <span class="ps-spinner__text">Chargement en cours...</span>
</div>

<!-- Dots spinner -->
<div class="ps-spinner ps-spinner--dots ps-spinner--medium ps-spinner--primary" role="status" aria-live="polite">
  <span class="ps-spinner__dot"></span>
  <span class="ps-spinner__dot"></span>
  <span class="ps-spinner__dot"></span>
  <span class="ps-spinner__text">Chargement en cours...</span>
</div>

<!-- Bars spinner -->
<div class="ps-spinner ps-spinner--bars ps-spinner--medium ps-spinner--primary" role="status" aria-live="polite">
  <span class="ps-spinner__bar"></span>
  <span class="ps-spinner__bar"></span>
  <span class="ps-spinner__bar"></span>
  <span class="ps-spinner__text">Chargement en cours...</span>
</div>
```

### Classes BEM

```
ps-spinner                                // Block
  ps-spinner__svg                         // SVG conteneur (circular)
  ps-spinner__circle                      // Cercle animé (circular)
  ps-spinner__dot                         // Point animé (dots)
  ps-spinner__bar                         // Barre animée (bars)
  ps-spinner__text                        // Texte masqué visuellement (a11y)

Modificateurs :
  ps-spinner--circular                    // Variante cercle rotatif (défaut)
  ps-spinner--dots                        // Variante 3 points
  ps-spinner--bars                        // Variante 3 barres
  
  ps-spinner--xs                          // 16px
  ps-spinner--sm                          // 24px
  ps-spinner--md                          // 32px (défaut)
  ps-spinner--lg                          // 48px
  ps-spinner--xl                          // 64px
  
  ps-spinner--primary                     // Couleur primaire (vert)
  ps-spinner--secondary                   // Couleur secondaire (gris)
  ps-spinner--white                       // Blanc (sur fond sombre)
  ps-spinner--neutral                     // Neutre (gris moyen)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Spinner'
status: stable
group: atoms
description: 'Indicateur de chargement animé avec rôle ARIA et variantes visuelles.'

props:
  type: object
  properties:
    variant:
      type: string
      enum: ['circular','dots','bars']
      default: 'circular'
    size:
      type: string
      enum: ['xs','sm','md','lg','xl']
      default: 'md'
    color:
      type: string
      enum: ['primary','secondary','white','neutral']
      default: 'primary'
    text:
      type: string
      default: 'Chargement en cours...'
      description: 'Texte pour lecteurs d'écran'
    centered:
      type: boolean
      default: false
      description: 'Centrer dans le conteneur parent'
    attributes:
      type: Drupal\Core\Template\Attribute
```

---

## 🎭 Variants

- **Styles** : `circular`|`dots`|`bars`.
- **Tailles** : `xs`|`sm`|`md`|`lg`|`xl`.
- **Couleurs** : `primary`|`secondary`|`white`|`neutral`.
- **Centré** : `centered` (centrage absolu ou flex).

---

## 🎨 Design Tokens

- Couleurs par variante:
  - Primary: `--ps-color-primary-600`
  - Secondary: `--ps-color-neutral-500`
  - White: `--ps-color-neutral-0`
  - Neutral: `--ps-color-neutral-400`
- Tailles:
  - xs: 16px
  - sm: 24px
  - md: 32px
  - lg: 48px
  - xl: 64px
- Transitions/animations: `--ps-transition-duration-normal`, durée rotation 1s

---

## 🔧 Template Twig

```twig
{#
 * Template for Spinner atom.
 * Variables: voir API YAML
 #}

{% set variant = variant|default('circular') %}
{% set size = size|default('md') %}
{% set color = color|default('primary') %}
{% set text = text|default('Chargement en cours...') %}
{% set centered = centered|default(false) %}

{% set root_classes = [
  'ps-spinner',
  'ps-spinner--' ~ variant,
  'ps-spinner--' ~ size,
  'ps-spinner--' ~ color,
  centered ? 'ps-spinner--centered'
] %}

<div {{ attributes.addClass(root_classes) }} role="status" aria-live="polite">
  {% if variant == 'circular' %}
    <svg class="ps-spinner__svg" viewBox="0 0 50 50">
      <circle class="ps-spinner__circle" cx="25" cy="25" r="20" fill="none" stroke-width="4"></circle>
    </svg>
  {% elseif variant == 'dots' %}
    <span class="ps-spinner__dot"></span>
    <span class="ps-spinner__dot"></span>
    <span class="ps-spinner__dot"></span>
  {% elseif variant == 'bars' %}
    <span class="ps-spinner__bar"></span>
    <span class="ps-spinner__bar"></span>
    <span class="ps-spinner__bar"></span>
  {% endif %}
  <span class="ps-spinner__text">{{ text }}</span>
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-spinner {
  display: inline-flex; align-items: center; justify-content: center;
  
  &__text {
    position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
    overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0;
  }

  // Centered variant
  &--centered {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
  }

  // Sizes
  &--xs { width: 16px; height: 16px; }
  &--sm { width: 24px; height: 24px; }
  &--md { width: 32px; height: 32px; }
  &--lg { width: 48px; height: 48px; }
  &--xl { width: 64px; height: 64px; }

  // Circular variant
  &--circular {
    .ps-spinner__svg {
      width: 100%; height: 100%;
      animation: ps-spinner-rotate 1s linear infinite;
    }
    .ps-spinner__circle {
      stroke: currentColor;
      stroke-linecap: round;
      stroke-dasharray: 90, 150;
      stroke-dashoffset: 0;
      animation: ps-spinner-dash 1.5s ease-in-out infinite;
    }
  }

  // Dots variant
  &--dots {
    gap: 4px;
    .ps-spinner__dot {
      width: 25%; height: 25%;
      background: currentColor;
      border-radius: 50%;
      animation: ps-spinner-bounce 1.4s infinite ease-in-out both;
      &:nth-child(1) { animation-delay: -0.32s; }
      &:nth-child(2) { animation-delay: -0.16s; }
    }
  }

  // Bars variant
  &--bars {
    gap: 3px;
    .ps-spinner__bar {
      width: 20%; height: 100%;
      background: currentColor;
      animation: ps-spinner-stretch 1.2s infinite ease-in-out;
      &:nth-child(1) { animation-delay: -0.24s; }
      &:nth-child(2) { animation-delay: -0.12s; }
    }
  }

  // Colors
  &--primary { color: var(--ps-color-primary-600, #0DB089); }
  &--secondary { color: var(--ps-color-neutral-500, #6E7C89); }
  &--white { color: var(--ps-color-neutral-0, #FFF); }
  &--neutral { color: var(--ps-color-neutral-400, #9AA6B2); }
}

// Animations
@keyframes ps-spinner-rotate {
  to { transform: rotate(360deg); }
}

@keyframes ps-spinner-dash {
  0% { stroke-dasharray: 1, 150; stroke-dashoffset: 0; }
  50% { stroke-dasharray: 90, 150; stroke-dashoffset: -35; }
  100% { stroke-dasharray: 90, 150; stroke-dashoffset: -124; }
}

@keyframes ps-spinner-bounce {
  0%, 80%, 100% { transform: scale(0); }
  40% { transform: scale(1); }
}

@keyframes ps-spinner-stretch {
  0%, 40%, 100% { transform: scaleY(0.4); }
  20% { transform: scaleY(1); }
}
```

---

## ♿ Accessibilité

- `role="status"` : annonce les changements d'état.
- `aria-live="polite"` : annonce non-intrusive.
- Texte masqué visuellement mais accessible aux lecteurs d'écran.
- Pas de focus : spinner non-interactif.
- Contraste suffisant pour tous les variants de couleur.

---

## 📱 Comportement responsive

- Inline-flex : s'adapte au contexte (inline ou block).
- Option `centered` pour centrage absolu dans conteneur.

---

## 🧪 Exemples d'usage

```twig
{# Circular spinner (default) #}
{% include '@ps_theme/ps-spinner/ps-spinner.twig' with {
  variant: 'circular',
  size: 'md',
  color: 'primary',
  text: 'Chargement...'
} %}

{# Dots spinner #}
{% include '@ps_theme/ps-spinner/ps-spinner.twig' with {
  variant: 'dots',
  size: 'sm',
  color: 'secondary'
} %}

{# Bars spinner (white on dark bg) #}
{% include '@ps_theme/ps-spinner/ps-spinner.twig' with {
  variant: 'bars',
  size: 'lg',
  color: 'white'
} %}

{# Centered in container #}
<div style="position: relative; height: 200px;">
  {% include '@ps_theme/ps-spinner/ps-spinner.twig' with {
    variant: 'circular',
    size: 'xl',
    centered: true
  } %}
</div>

{# Inline with button #}
<button class="ps-button ps-button--primary" disabled>
  {% include '@ps_theme/ps-spinner/ps-spinner.twig' with {
    variant: 'circular',
    size: 'xs',
    color: 'white'
  } %}
  Envoi en cours...
</button>
```

---

## 📚 Ressources

- WAI-ARIA: `role="status"`, `aria-live="polite"`
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/transitions.yml`
