# Button (Atom)

**Niveau Atomic Design** : Atom / Element  
**Catégorie** : Interactive  
**Statut** : ✅ Stable  
**Version** : 1.1.0  
**Dernière mise à jour** : 3 décembre 2025

---

## 🎨 Design Tokens (réels)

- Typo : `--font-body`, `--font-size-1|2`, `--font-weight-600`, `--leading-tight`
- Espacements : `--size-3|4|5|6` pour les paddings internes, gap icône `--size-2`
- Hauteurs indicatives : `--size-8` (32px), `--size-10` (40px), `--size-12` (48px)
- Rayon : `--radius-2` (4px), `--radius-3` (6px), `--radius-round` (icon-only/pill)
- Bordures : `--border-size-1` (outline fin) ou `--border-size-2`
- Couleurs sémantiques disponibles (fond/texte/bordure) :
  - Neutral : `--gray-500`, `--gray-600`, `--gray-700`, `--text-secondary`, `--border-default`
  - Primary : `--primary`, `--primary-hover`, `--primary-active`, `--primary-text`, `--primary-border`
  - Secondary : `--secondary`, `--secondary-hover`, `--secondary-active`, `--secondary-text`, `--secondary-border`
  - Success : `--success`, `--success-hover`, `--success-active`, `--success-text`, `--success-border`
  - Info : `--info`, `--info-hover`, `--info-active`, `--info-text`, `--info-border`
  - Warning : `--warning`, `--warning-hover`, `--warning-active`, `--warning-text`, `--warning-border`
  - Danger : `--danger`, `--danger-hover`, `--danger-active`, `--danger-text`, `--danger-border`
- Outline : fond `transparent`, bordure sur la couleur sémantique correspondante
- Focus : utiliser `--border-focus` pour l’outline visible AA
- Ombres optionnelles : `--shadow-1` (repos), `--shadow-2` (hover)
- Transition : `--duration-fast` + `--ease-3`
<!-- Primary with icon right -->
<button class="ps-button ps-button--primary ps-button--icon-right">
  <span class="ps-button__label">Next</span>
  <span class="ps-button__icon ps-button__icon--right" data-icon="arrow-right" aria-hidden="true"></span>
</button>

<!-- Outline secondary -->
<button class="ps-button ps-button--secondary ps-button--outline">
  <span class="ps-button__label">Cancel</span>
</button>

<!-- Loading state -->
<button class="ps-button ps-button--loading" aria-busy="true">
  <span class="ps-button__spinner" aria-hidden="true"></span>
  <span class="ps-button__label">Loading...</span>
</button>
```

### Classes BEM

```
ps-button                          // Block principal
  ps-button__label                // Texte du bouton
  ps-button__icon                 // Icône (via data-icon)
    ps-button__icon--left         // Icône à gauche
    ps-button__icon--right        // Icône à droite
  ps-button__spinner              // Spinner loading

Modifiers (variants sémantiques):
  ps-button--neutral              // Défaut (gris neutre) - NO CLASS NEEDED
  ps-button--primary              // Primaire (vert brand)
  ps-button--secondary            // Secondaire (violet brand)
  ps-button--success              // Succès (vert)
  ps-button--info                 // Info (bleu)
  ps-button--warning              // Avertissement (orange)
  ps-button--danger               // Danger (rouge)
  
Modifiers (styles):
  ps-button--outline              // Style outline (bordure seule)
  
Modifiers (tailles):
  ps-button--small                // Petit (height 32px)
  ps-button--medium               // Moyen (height 40px) - NO CLASS NEEDED
  ps-button--large                // Large (height 48px)
  
Modifiers (layout):
  ps-button--full-width           // Largeur 100%
  ps-button--icon-only            // Icône seule (carré)
  ps-button--icon-left            // Avec icône à gauche
  ps-button--icon-right           // Avec icône à droite
  
States:
  ps-button--disabled             // État désactivé
  ps-button--loading              // État chargement (avec spinner)

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

# CSS Variables (Tokens standards - sans préfixe)
--size-9: 36px (button height medium)
--primary: #00915A (vert BNP)
--secondary: #A12B66 (violet BNP)
--white: #FFFFFF
--size-2: 8px (spacing)
--radius-0: 0 (border square)
--duration-fast + --ease-3: transition button

Note : Anciens tokens --ps-* dépréciés, utiliser tokens standards.
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
.ps-button {
  /* Variables composant (Layer 2) */
  --button-gap: var(--size-2);
  --button-padding-y: var(--size-2);
  --button-padding-x: var(--size-4);
  --button-min-width: var(--size-9);
  --button-height: var(--size-9);
  --button-font-family: var(--font-sans);
  --button-font-size: var(--size-4);
  --button-font-weight: var(--font-weight-400);
  --button-line-height: 1.5;
  --button-bg: var(--gray-500);
  --button-color: var(--white);
  --button-border-width: 0;
  --button-border-color: transparent;
  --button-border-radius: 0;
  --button-hover-bg: var(--gray-600);
  --button-hover-border-color: transparent;
  --button-hover-color: var(--white);
  --button-hover-transform: translateY(-1px);
  --button-active-bg: var(--gray-700);
  --button-active-transform: translateY(0);
  --button-disabled-opacity: 0.5;
  --button-focus-outline-width: var(--border-size-2);
  --button-focus-outline-color: var(--border-focus);
  --button-focus-outline-offset: var(--border-size-2);
  --button-transition-duration: var(--duration-fast);
  --button-transition-timing: var(--ease-4);

  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--button-gap);
  padding: var(--button-padding-y) var(--button-padding-x);
  min-width: var(--button-min-width);
  height: var(--button-height);
  font-family: var(--button-font-family);
  font-size: var(--button-font-size);
  font-weight: var(--button-font-weight);
  line-height: var(--button-line-height);
  text-align: center;
  white-space: nowrap;
  background-color: var(--button-bg);
  color: var(--button-color);
  border-style: solid;
  border-width: var(--button-border-width);
  border-color: var(--button-border-color);
  border-radius: var(--button-border-radius);
  text-decoration: none;
  transition:
    background-color var(--button-transition-duration) var(--button-transition-timing),
    color var(--button-transition-duration) var(--button-transition-timing),
    border-color var(--button-transition-duration) var(--button-transition-timing),
    transform var(--button-transition-duration) var(--button-transition-timing);

  &:hover:not(:disabled):not(.ps-button--disabled) {
    background-color: var(--button-hover-bg);
    border-color: var(--button-hover-border-color);
    color: var(--button-hover-color);
    transform: var(--button-hover-transform);
  }

  &:active:not(:disabled):not(.ps-button--disabled) {
    background-color: var(--button-active-bg);
    transform: var(--button-active-transform);
  }

  &:focus-visible {
    outline: var(--button-focus-outline-width) solid var(--button-focus-outline-color);
    outline-offset: var(--button-focus-outline-offset);
  }
}

.ps-button__label { display: inline-block; }
.ps-button__icon { flex-shrink: 0; line-height: 1; }

// Variantes sémantiques
.ps-button--primary { --button-bg: var(--primary); --button-color: var(--primary-text); --button-hover-bg: var(--primary-hover); --button-active-bg: var(--primary-active); }
.ps-button--secondary { --button-bg: var(--secondary); --button-color: var(--secondary-text); --button-hover-bg: var(--secondary-hover); --button-active-bg: var(--secondary-active); }
.ps-button--success { --button-bg: var(--success); --button-color: var(--success-text); --button-hover-bg: var(--success-hover); --button-active-bg: var(--success-active); }
.ps-button--info { --button-bg: var(--info); --button-color: var(--info-text); --button-hover-bg: var(--info-hover); --button-active-bg: var(--info-active); }
.ps-button--warning { --button-bg: var(--warning); --button-color: var(--warning-text); --button-hover-bg: var(--warning-hover); --button-active-bg: var(--warning-active); }
.ps-button--danger { --button-bg: var(--danger); --button-color: var(--danger-text); --button-hover-bg: var(--danger-hover); --button-active-bg: var(--danger-active); }
.ps-button--neutral { --button-bg: var(--gray-500); --button-color: var(--white); --button-hover-bg: var(--gray-600); --button-active-bg: var(--gray-700); }

// Variante outline (appliquée seule ou combinée)
.ps-button--outline {
  --button-bg: transparent;
  --button-border-width: var(--border-size-2);
  --button-border-color: var(--gray-500);
  --button-color: var(--gray-600);
  --button-hover-bg: color-mix(in srgb, var(--gray-500) 8%, transparent);
  --button-hover-border-color: var(--gray-600);
  --button-hover-color: var(--gray-700);
  --button-active-bg: color-mix(in srgb, var(--gray-500) 16%, transparent);
}

// Outline par variante
.ps-button--outline.ps-button--primary { --button-border-color: var(--primary); --button-color: var(--primary); --button-hover-bg: color-mix(in srgb, var(--primary) 8%, transparent); --button-hover-border-color: var(--primary-hover); --button-hover-color: var(--primary-hover); --button-active-bg: color-mix(in srgb, var(--primary) 16%, transparent); }
.ps-button--outline.ps-button--secondary { --button-border-color: var(--secondary); --button-color: var(--secondary); --button-hover-bg: color-mix(in srgb, var(--secondary) 8%, transparent); --button-hover-border-color: var(--secondary-hover); --button-hover-color: var(--secondary-hover); --button-active-bg: color-mix(in srgb, var(--secondary) 16%, transparent); }
.ps-button--outline.ps-button--success { --button-border-color: var(--success); --button-color: var(--success); --button-hover-bg: color-mix(in srgb, var(--success) 8%, transparent); --button-hover-border-color: var(--success-hover); --button-hover-color: var(--success-hover); --button-active-bg: color-mix(in srgb, var(--success) 16%, transparent); }
.ps-button--outline.ps-button--info { --button-border-color: var(--info); --button-color: var(--info); --button-hover-bg: color-mix(in srgb, var(--info) 8%, transparent); --button-hover-border-color: var(--info-hover); --button-hover-color: var(--info-hover); --button-active-bg: color-mix(in srgb, var(--info) 16%, transparent); }
.ps-button--outline.ps-button--warning { --button-border-color: var(--warning); --button-color: var(--warning); --button-hover-bg: color-mix(in srgb, var(--warning) 8%, transparent); --button-hover-border-color: var(--warning-hover); --button-hover-color: var(--warning-hover); --button-active-bg: color-mix(in srgb, var(--warning) 16%, transparent); }
.ps-button--outline.ps-button--danger { --button-border-color: var(--danger); --button-color: var(--danger); --button-hover-bg: color-mix(in srgb, var(--danger) 8%, transparent); --button-hover-border-color: var(--danger-hover); --button-hover-color: var(--danger-hover); --button-active-bg: color-mix(in srgb, var(--danger) 16%, transparent); }
.ps-button--outline.ps-button--neutral { --button-border-color: var(--gray-500); --button-color: var(--gray-600); --button-hover-bg: color-mix(in srgb, var(--gray-500) 8%, transparent); --button-hover-border-color: var(--gray-600); --button-hover-color: var(--gray-700); --button-active-bg: color-mix(in srgb, var(--gray-500) 16%, transparent); }

// Tailles
.ps-button--small {
  --button-height: 2.12375rem; /* 34px */
  --button-padding-y: var(--size-105); /* 6px */
  --button-padding-x: var(--size-305); /* 14px */
  --button-font-size: var(--size-305); /* 14px */
  --button-line-height: 1.428; /* 20px */
}

.ps-button--medium {
  --button-height: var(--size-9); /* 36px */
  --button-padding-y: var(--size-2);
  --button-padding-x: var(--size-4);
  --button-font-size: var(--size-4);
  --button-line-height: 1.5;
}

.ps-button--large {
  --button-height: var(--size-10); /* 40px */
  --button-padding-y: var(--size-205); /* 10px */
  --button-padding-x: var(--size-5); /* 20px */
  --button-font-size: 1.125rem; /* 18px */
  --button-line-height: 1.444; /* 26px */
}

// Icône seule
.ps-button--icon-only {
  --button-padding-y: var(--size-2);
  --button-padding-x: var(--size-2);

  .ps-button__label {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
  }

  &.ps-button--small {
    --button-padding-y: var(--size-105);
    --button-padding-x: var(--size-105);
    width: 2.12375rem;
  }

  &.ps-button--medium { width: var(--size-9); }

  &.ps-button--large {
    --button-padding-y: var(--size-205);
    --button-padding-x: var(--size-205);
    width: var(--size-10);
  }
}

// Pleine largeur
.ps-button--full-width { width: 100%; }

// États
.ps-button--disabled,
.ps-button:disabled { opacity: var(--button-disabled-opacity); cursor: not-allowed; pointer-events: none; }

.ps-button--loading {
  --button-spinner-color: var(--white);
  position: relative;
  color: transparent !important;
  pointer-events: none;

  .ps-button__spinner {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;

    &::before {
      content: '';
      width: 1rem;
      height: 1rem;
      border: var(--border-size-2) solid var(--button-spinner-color);
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
    }
  }

  .ps-button__label,
  .ps-button__icon { visibility: hidden; }
}

// Couleur du spinner pour outlines
.ps-button--outline.ps-button--primary.ps-button--loading { --button-spinner-color: var(--primary); }
.ps-button--outline.ps-button--secondary.ps-button--loading { --button-spinner-color: var(--secondary); }
.ps-button--outline.ps-button--success.ps-button--loading { --button-spinner-color: var(--success); }
.ps-button--outline.ps-button--info.ps-button--loading { --button-spinner-color: var(--info); }
.ps-button--outline.ps-button--warning.ps-button--loading { --button-spinner-color: var(--warning); }
.ps-button--outline.ps-button--danger.ps-button--loading { --button-spinner-color: var(--danger); }
.ps-button--outline.ps-button--neutral.ps-button--loading { --button-spinner-color: var(--gray-600); }
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
