# Button (Atom)

**Niveau Atomic Design** : Atom / Element  
**Catégorie** : Interactive  
**Statut** : ✅ Stable  
**Version** : 2.0.0 (Breaking changes: tailles standardisées, HTML simplifié)  
**Dernière mise à jour** : 13 décembre 2025

---

## 📋 Vue d'ensemble

Bouton d'action interactif avec variantes sémantiques, support d'icônes via `data-icon`, états disabled/loading, style outline, et layout full-width.

**Caractéristiques** :
- **7 variantes sémantiques** : primary, secondary, success, info, warning, danger, gold (neutral = omission)
- **3 tailles standardisées** : small (32px), medium (36px défaut), large (40px)
- **Structure HTML simplifiée** : Icônes via attribut `data-icon`, texte direct dans bouton
- **Responsive** : Touch targets adaptés tablet+, optimisations desktop
- **Accessibilité** : WCAG 2.2 AA, focus-visible, ARIA states

---

## 🎨 Design Tokens

### Couleurs sémantiques (utilisées par les variantes)

| Variant | Token Base | Hover Token | Active Token | Text Token |
|---------|-----------|-------------|--------------|------------|
| **Neutral** (défaut) | `--gray-500` | `--gray-600` | `--gray-700` | `--white` |
| **Primary** | `--primary` | `--primary-hover` | `--primary-active` | `--primary-text` |
| **Secondary** | `--secondary` | `--secondary-hover` | `--secondary-active` | `--secondary-text` |
| **Success** | `--success` | `--success-hover` | `--success-active` | `--success-text` |
| **Info** | `--info` | `--info-hover` | `--info-active` | `--info-text` |
| **Warning** | `--warning` | `--warning-hover` | `--warning-active` | `--warning-text` |
| **Danger** | `--danger` | `--danger-hover` | `--danger-active` | `--danger-text` |
| **Gold** | `--gold` | `--gold-hover` | `--gold-active` | `--gold-text` |

### Tailles (3 tailles standard)

| Size | Height Token | Padding Y | Padding X | Font Size | Icon Size |
|------|-------------|-----------|-----------|-----------|-----------|
| **Small** | `--size-8` (32px) | `--size-105` | `--size-305` | `--size-305` (14px) | `--size-4` |
| **Medium** (défaut) | `--size-9` (36px) | `--size-2` | `--size-4` | `--size-4` (16px) | `--size-5` |
| **Large** | `--size-10` (40px) | `--size-205` | `--size-5` | `--size-5` (18px) | `--size-6` |

### Autres tokens

- **Gap icône** : `--size-2` (8px)
- **Border radius** : `0` (design carré)
- **Focus outline** : `--border-size-2` width, `--secondary` color, `--border-size-2` offset
- **Transition** : `--duration-fast` + `--ease-4`
- **Disabled opacity** : `0.5`
- **Hover transform** : `translateY(-1px)`

---

## 🏗️ Structure HTML

### Structure simplifiée (v2.0+)

```html
<!-- Texte seul (neutral default) -->
<button class="ps-button">
  Submit
</button>

<!-- Avec variante primary -->
<button class="ps-button ps-button--primary">
  Submit
</button>

<!-- Avec icône (data-icon attribute) -->
<button class="ps-button ps-button--primary" data-icon="check">
  Valider
</button>

<!-- Icône à la fin -->
<button class="ps-button ps-button--primary" data-icon="arrow-right" data-icon-position="end">
  Suivant
</button>

<!-- Outline secondary -->
<button class="ps-button ps-button--secondary ps-button--outline">
  Annuler
</button>

<!-- Loading state (spinner nécessite child element) -->
<button class="ps-button ps-button--loading" aria-busy="true">
  <span class="ps-button__spinner" aria-hidden="true"></span>
  Loading...
</button>

<!-- Icon-only (label pour accessibilité) -->
<button class="ps-button ps-button--primary ps-button--icon-only" data-icon="close" aria-label="Fermer">
  Fermer
</button>
```

**Note importante** : Le composant Button conserve l'élément `<span class="ps-button__spinner">` uniquement pour l'état loading car le spinner nécessite un child element pour le positionnement absolu et l'animation CSS.

### Classes BEM

```
ps-button                          // Block principal
  ps-button__spinner              // Spinner (loading state only)

Modifiers (variants sémantiques):
  ps-button--primary              // Primaire (vert brand)
  ps-button--secondary            // Secondaire (violet brand)
  ps-button--success              // Succès (vert)
  ps-button--info                 // Info (bleu)
  ps-button--warning              // Avertissement (orange)
  ps-button--danger               // Danger (rouge)
  ps-button--gold                 // Premium (or)
  (omission)                      // Neutral (gris) - état par défaut
  
Modifiers (styles):
  ps-button--outline              // Style outline (bordure seule)
  
Modifiers (tailles):
  ps-button--small                // Petit (height 32px)
  (omission)                      // Medium (height 36px) - taille par défaut
  ps-button--large                // Large (height 40px)
  
Modifiers (layout):
  ps-button--full-width           // Largeur 100%
  ps-button--icon-only            // Icône seule (carré aspect-ratio)
  
States:
  ps-button--disabled             // État désactivé
  ps-button--loading              // État chargement (avec spinner)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Button'
status: stable
group: atoms
description: 'Bouton d\'action avec variantes sémantiques et icônes via data-icon'

props:
  type: object
  properties:
    label:
      type: string
      title: Label du bouton
      description: Texte affiché dans le bouton (requis)
      
    variant:
      type: string
      title: Variant sémantique
      description: Type de bouton (omission = neutral/gray)
      enum: ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'gold']
      
    outline:
      type: boolean
      title: Style outline
      description: Bordure seule avec fond transparent
      default: false
      
    size:
      type: string
      title: Taille
      description: Taille du bouton (omission = medium/défaut)
      enum: ['small', 'large']
      
    url:
      type: string
      title: URL
      description: Lien de destination (transforme en <a>)
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
      description: Nom de l'icône pour attribut data-icon (e.g., 'check', 'arrow-right')
      
    iconPosition:
      type: string
      title: Position de l'icône
      enum: ['start', 'end']
      default: 'start'
      
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
      
    toggle:
      type: boolean
      title: Toggle behavior
      description: Active le comportement toggle via data-ps-toggle="button"
      default: false
      
    active:
      type: boolean
      title: État pré-toggle
      description: Utilisé avec toggle=true pour définir l'état initial
      default: false
      
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML additionnels
      
  required:
    - label
```

---

## 🎭 Variants

### 1. Neutral (default - omission)

**Usage** : Actions standards, options génériques

```html
<!-- Neutral = pas de classe variant -->
<button class="ps-button">
  Continuer
</button>
```

**Couleurs** : `--gray-500` (fond), `--white` (texte)

### 2. Primary

**Usage** : Actions principales, CTA, soumission de formulaires

```html
<button class="ps-button ps-button--primary">
  Rechercher un bien
</button>
```

**Couleurs** : `--primary`

### 3. Secondary

**Usage** : Actions secondaires, navigation, options alternatives

```html
<button class="ps-button ps-button--secondary">
  Contacter l'agence
</button>
```

**Couleurs** : `--secondary`

### 4. Success, Info, Warning, Danger

**Usage** : Feedback contextuel, actions à connotation spécifique

```html
<button class="ps-button ps-button--success">
  Valider la demande
</button>

<button class="ps-button ps-button--danger">
  Supprimer l'annonce
</button>
```

### 5. Gold

**Usage** : Features premium, mise en avant spéciale

```html
<button class="ps-button ps-button--gold">
  Passer à Premium
</button>
```

### 6. Outline

**Usage** : Variante secondaire de toute couleur sémantique

```html
<!-- Outline primary -->
<button class="ps-button ps-button--primary ps-button--outline">
  En savoir plus
</button>

<!-- Outline neutral (si pas de variant spécifié) -->
<button class="ps-button ps-button--outline">
  Annuler
</button>
```

**Caractéristiques** :
- Fond transparent
- Bordure 2px couleur sémantique
- Texte couleur sémantique
- Hover : fond semi-transparent (8% color-mix)

---

## 📏 Tailles

### Small (32px height)

```html
<button class="ps-button ps-button--primary ps-button--small">
  Action compacte
</button>
```

**Specs** : Height 32px, padding 6px/14px, font 14px

### Medium (36px height - défaut)

```html
<!-- Pas de classe --medium, c'est le défaut -->
<button class="ps-button ps-button--primary">
  Action standard
</button>
```

**Specs** : Height 36px, padding 8px/16px, font 16px

### Large (40px height)

```html
<button class="ps-button ps-button--primary ps-button--large">
  Action importante
</button>
```

**Specs** : Height 40px, padding 10px/20px, font 18px

---

## 🔧 Template Twig

```twig
{#
 * Button atom
 * @param string label - Button text (required)
 * @param string variant - primary|secondary|gold|success|info|warning|danger (omit for neutral/gray)
 * @param boolean outline - Outline style (default: false)
 * @param string size - small|large (omit for medium/default)
 * @param string url - Destination URL (optional)
 * @param string target - _self|_blank (default: _self)
 * @param string icon - Icon name for data-icon attribute (e.g., 'check', 'arrow-right')
 * @param string iconPosition - start|end (default: start)
 * @param boolean disabled - Disabled state (default: false)
 * @param boolean loading - Loading state (default: false)
 * @param boolean fullWidth - Full width (default: false)
 * @param boolean toggle - Enable toggle state behavior via data-ps-toggle="button" (default: false)
 * @param boolean active - Pre-toggle button (only used with toggle=true; requires .active class + aria-pressed="true")
 #}

{%- set variant = variant|default(null) -%}
{%- set outline = outline|default(false) -%}
{%- set size = size|default(null) -%}
{%- set icon = icon|default(null) -%}
{%- set iconPosition = iconPosition|default('start') -%}
{%- set target = target|default('_self') -%}
{%- set disabled = disabled|default(false) -%}
{%- set loading = loading|default(false) -%}
{%- set fullWidth = fullWidth|default(false) -%}
{%- set toggle = toggle|default(false) -%}
{%- set active = active|default(false) -%}
{%- set baseClass = baseClass|default('ps-button') -%}
{%- set el_spinner = baseClass ~ '__spinner' -%}
{%- set class = class|default(null) -%}

{%- set classes = [
  baseClass,
  variant ? baseClass ~ '--' ~ variant : null,
  size ? baseClass ~ '--' ~ size : null,
  outline ? baseClass ~ '--outline' : null,
  (not label) ? baseClass ~ '--icon-only' : null,
  disabled ? baseClass ~ '--disabled' : null,
  loading ? baseClass ~ '--loading' : null,
  fullWidth ? baseClass ~ '--full-width' : null,
  (toggle and active) ? 'active' : null,
  class ? class : null
] -%}

{%- set tag = url ? 'a' : 'button' -%}

<{{ tag }} class="{{ classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes|without('class') }}{% endif -%}
  {%- if url %} href="{{ url }}"{% endif -%}
  {%- if target == '_blank' %} target="_blank" rel="noopener noreferrer"{% endif -%}
  {%- if disabled and tag == 'button' %} disabled aria-disabled="true"{% endif -%}
  {%- if loading %} aria-busy="true"{% endif -%}
  {%- if toggle %} data-ps-toggle="button" aria-pressed="{{ active ? 'true' : 'false' }}"{% endif -%}
  {%- if icon %} data-icon="{{ icon }}"{% endif -%}
  {%- if icon and iconPosition != 'start' %} data-icon-position="{{ iconPosition }}"{% endif -%}
>
  {%- if loading -%}
    <span class="{{ el_spinner }}" aria-hidden="true"></span>
  {%- endif -%}
  {{- label -}}
</{{ tag }}>
```

---

## ♿ Accessibilité

### Conformité WCAG 2.2 AA

✅ **Contraste de couleur**
- Primary (green) : 7.8:1 ratio (AAA ✓)
- Secondary (violet) : 6.9:1 ratio (AAA ✓)
- Success, info, warning, danger : Tous > 4.5:1 (AA ✓)

✅ **Touch target**
- Minimum 32px (small), recommandé 36px (medium), optimisé 40px (large)
- Responsive : Touch target 40px+ sur tablet (768px+)
- Spacing minimum 8px entre boutons adjacents

✅ **Navigation clavier**
- **Tab** : Focus sur le bouton
- **Enter/Space** : Activation
- **Focus visible** : Outline 2px `--secondary` avec offset 2px

✅ **Attributs ARIA**

```html
<!-- Bouton désactivé -->
<button class="ps-button" disabled aria-disabled="true">
  Action indisponible
</button>

<!-- Bouton chargement -->
<button class="ps-button ps-button--loading" aria-busy="true">
  <span class="ps-button__spinner" aria-hidden="true"></span>
  Envoi en cours...
</button>

<!-- Bouton icône seule (label obligatoire) -->
<button class="ps-button ps-button--icon-only" data-icon="close" aria-label="Fermer">
  Fermer
</button>

<!-- Lien externe -->
<a class="ps-button ps-button--primary" href="https://example.com" target="_blank" rel="noopener noreferrer">
  Site externe
  <span class="visually-hidden">(ouvre dans un nouvel onglet)</span>
</a>

<!-- Toggle button -->
<button class="ps-button" data-ps-toggle="button" aria-pressed="false">
  Activer les notifications
</button>
```

### États visuels

| État | Visual feedback | CSS Property |
|------|-----------------|--------------|
| **Default** | Style de base | Base variables |
| **Hover** | Lift + darkened background | `transform: translateY(-1px)` + `--hover-bg` |
| **Active** | Pressed down | `transform: translateY(0)` + `--active-bg` |
| **Focus** | Outline violet 2px | `outline: 2px solid var(--secondary)` + `offset: 2px` |
| **Disabled** | Demi-transparent, no interaction | `opacity: 0.5` + `pointer-events: none` |
| **Loading** | Spinner + transparent text | `.ps-button--loading` + `color: transparent` |

---

## 📱 Comportement responsive

**Mobile-first** : Les styles de base correspondent au mobile (320px+).

### Breakpoints définis

```css
/* Base (mobile - no media query) */
.ps-button {
  --ps-button-height: var(--size-9); /* 36px */
  --ps-button-padding-y: var(--size-2);
  --ps-button-padding-x: var(--size-4);
}

/* Mobile-sm (400px+) */
@media (--mobile-sm) {
  /* No adjustments needed */
}

/* Mobile (640px+) */
@media (--mobile) {
  /* No adjustments needed */
}

/* Tablet (768px+) */
@media (--tablet) {
  .ps-button {
    /* Increase touch target for tablet */
    --ps-button-height: var(--size-10); /* 40px */
    --ps-button-padding-y: var(--size-205);
    --ps-button-padding-x: var(--size-5);
  }

  .ps-button--small {
    --ps-button-height: var(--size-9); /* 36px */
    --ps-button-padding-y: var(--size-2);
    --ps-button-padding-x: var(--size-4);
  }

  .ps-button--large {
    --ps-button-height: var(--size-11); /* 44px */
    --ps-button-padding-y: var(--size-305);
    --ps-button-padding-x: var(--size-6);
  }
}

/* Laptop (1024px+) */
@media (--laptop) {
  /* Tablet styles continue (no additional adjustments) */
}

/* Desktop (1280px+) */
@media (--desktop) {
  /* Optimized for mouse interaction (tablet sizes maintained) */
}

/* Desktop-large (1440px+) */
@media (--desktop-large) {
  /* No adjustments needed at largest breakpoint */
}
```

**Rationale** :
- **Mobile (base)** : Touch targets 36px minimum (acceptable WCAG AA)
- **Tablet (768px+)** : Touch targets augmentés à 40px+ (recommandation WCAG AAA)
- **Desktop** : Sizes maintenues de tablet (mouse interaction plus précise)

---

## 🧪 Exemples d'usage

### Drupal Twig

```twig
{# Bouton simple primary #}
{% include '@elements/button/button.twig' with {
  label: 'Rechercher un bien',
  variant: 'primary',
} only %}

{# Bouton avec lien et icône #}
{% include '@elements/button/button.twig' with {
  label: 'Découvrir nos biens',
  variant: 'secondary',
  url: '/properties',
  icon: 'arrow-right',
  iconPosition: 'end',
} only %}

{# Bouton outline small #}
{% include '@elements/button/button.twig' with {
  label: 'Annuler',
  outline: true,
  size: 'small',
} only %}

{# Bouton loading #}
{% include '@elements/button/button.twig' with {
  label: 'Envoi en cours...',
  loading: true,
  disabled: true,
} only %}

{# Bouton full-width large #}
{% include '@elements/button/button.twig' with {
  label: 'Soumettre le formulaire',
  variant: 'success',
  size: 'large',
  fullWidth: true,
} only %}

{# Bouton danger avec icône #}
{% include '@elements/button/button.twig' with {
  label: 'Supprimer l\'annonce',
  variant: 'danger',
  icon: 'trash',
} only %}
```

### Formulaire Drupal

```php
// Simple submit button
$form['submit'] = [
  '#type' => 'submit',
  '#value' => $this->t('Rechercher'),
  '#attributes' => [
    'class' => ['ps-button', 'ps-button--primary'],
  ],
];

// Button with size modifier
$form['cancel'] = [
  '#type' => 'button',
  '#value' => $this->t('Annuler'),
  '#attributes' => [
    'class' => ['ps-button', 'ps-button--outline', 'ps-button--small'],
  ],
];
```

### HTML statique

```html
<!-- CTA primaire -->
<button class="ps-button ps-button--primary" data-icon="search">
  Trouver mon bien
</button>

<!-- Lien secondaire -->
<a href="/contact" class="ps-button ps-button--secondary ps-button--outline">
  Nous contacter
</a>

<!-- Action destructive -->
<button class="ps-button ps-button--danger" data-icon="trash">
  Supprimer
</button>

<!-- Full-width mobile CTA -->
<button class="ps-button ps-button--success ps-button--large ps-button--full-width">
  Valider ma réservation
</button>
```

---

## 🔗 Composants liés

- **Icon** : Icônes via système `data-icon` (voir `source/props/icons.css`)
- **Spinner** : Animation loading intégrée via `.ps-button__spinner`
- **Link** : Alternative textuelle sans styles bouton (voir `elements/link/`)

---

## 📚 Ressources

- **Storybook** : [Voir Button dans Storybook](#)
- **Figma** : 298+ instances détectées dans les maquettes
- **Design tokens** : `source/props/brand.css`, `source/props/sizes.css`
- **CSS source** : `source/patterns/elements/button/button.css`

---

## 🚨 Breaking Changes (v2.0.0)

### Tailles (BREAKING)

❌ **Supprimé** : `--xs`, `--sm`, `--md`, `--xl`, `--xxl`  
✅ **Nouveau** : `--small`, (omission = medium), `--large`

**Migration** :
```html
<!-- Avant (v1.x) -->
<button class="ps-button ps-button--sm">Small</button>
<button class="ps-button ps-button--md">Medium</button>
<button class="ps-button ps-button--lg">Large</button>

<!-- Après (v2.0+) -->
<button class="ps-button ps-button--small">Small</button>
<button class="ps-button">Medium</button>
<button class="ps-button ps-button--large">Large</button>
```

### Variantes (BREAKING)

❌ **Supprimé** : `--dark`, `--light` (non-sémantiques)  
❌ **Supprimé** : Classe explicite `--neutral` (maintenant = omission)  
✅ **Conservé** : `--primary`, `--secondary`, `--success`, `--info`, `--warning`, `--danger`, `--gold`

**Migration** :
```html
<!-- Avant (v1.x) -->
<button class="ps-button">Neutral</button>
<button class="ps-button ps-button--dark">Dark</button>
<button class="ps-button ps-button--light">Light</button>

<!-- Après (v2.0+) -->
<button class="ps-button">Neutral</button>
<!-- Utiliser primary/secondary avec outline ou custom CSS variables -->
```

### Structure HTML (BREAKING)

❌ **Supprimé** : Element `<span class="ps-button__label">` obligatoire  
✅ **Nouveau** : Texte direct dans `<button>`, icônes via `data-icon`

**Migration** :
```html
<!-- Avant (v1.x) -->
<button class="ps-button ps-button--primary">
  <span class="ps-button__label">Submit</span>
  <span class="ps-button__icon" data-icon="check"></span>
</button>

<!-- Après (v2.0+) -->
<button class="ps-button ps-button--primary" data-icon="check">
  Submit
</button>
```

**Exception** : `.ps-button__spinner` conservé pour état loading (nécessite child element).

---

**Dernière mise à jour** : 13 décembre 2025  
**Contributeurs** : Design System Team
