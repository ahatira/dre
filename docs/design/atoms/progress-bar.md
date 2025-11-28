# Progress Bar (Atom)

**Niveau Atomic Design** : Atom / Feedback  
**Catégorie** : Progress indicator  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Indicateur de progression pour des tâches déterminées ou indéterminées (upload, téléchargement, formulaire multi-étapes). Disponible en variantes linéaire (barre horizontale) et circulaire (anneau). Supporte les valeurs min/max, label, couleurs sémantiques, et états indéterminés (animation infinie). Accessible via `role="progressbar"` et attributs ARIA.

---

## 🎨 Aperçu visuel

```
[████████░░░░░░] 60%     Linéaire

    ◐                     Circulaire
   60%
```

---

## 🏗️ Structure BEM

```html
<!-- Linear progress bar -->
<div class="ps-progress ps-progress--linear ps-progress--primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" aria-label="Upload en cours">
  <div class="ps-progress__track">
    <div class="ps-progress__fill" style="width: 60%;"></div>
  </div>
  <span class="ps-progress__label">60%</span>
</div>

<!-- Circular progress bar -->
<div class="ps-progress ps-progress--circular ps-progress--primary" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" aria-label="Chargement">
  <svg class="ps-progress__svg" viewBox="0 0 100 100">
    <circle class="ps-progress__track-circle" cx="50" cy="50" r="45" fill="none" stroke-width="8"></circle>
    <circle class="ps-progress__fill-circle" cx="50" cy="50" r="45" fill="none" stroke-width="8" stroke-dasharray="282.743" stroke-dashoffset="70.686"></circle>
  </svg>
  <span class="ps-progress__label">75%</span>
</div>

<!-- Indeterminate linear -->
<div class="ps-progress ps-progress--linear ps-progress--indeterminate" role="progressbar" aria-label="Chargement en cours">
  <div class="ps-progress__track">
    <div class="ps-progress__fill"></div>
  </div>
</div>
```

### Classes BEM

```
ps-progress                               // Block
  ps-progress__track                      // Piste (linéaire)
  ps-progress__fill                       // Remplissage (linéaire)
  ps-progress__svg                        // SVG conteneur (circulaire)
  ps-progress__track-circle               // Cercle de fond (circulaire)
  ps-progress__fill-circle                // Cercle de progression (circulaire)
  ps-progress__label                      // Label texte (pourcentage)

Modificateurs :
  ps-progress--linear                     // Variante linéaire (défaut)
  ps-progress--circular                   // Variante circulaire
  
  ps-progress--primary                    // Couleur primaire (vert)
  ps-progress--secondary                  // Couleur secondaire (gris)
  ps-progress--info                       // Info (bleu)
  ps-progress--success                    // Succès (vert)
  ps-progress--warning                    // Avertissement (orange)
  ps-progress--error                      // Erreur (rouge)
  
  ps-progress--small                      // Petite taille
  ps-progress--medium                     // Taille moyenne (défaut)
  ps-progress--large                      // Grande taille
  
  ps-progress--indeterminate              // Animation indéterminée
  ps-progress--striped                    // Rayures animées (linéaire)
  ps-progress--with-label                 // Avec label texte
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Progress Bar'
status: stable
group: atoms
description: 'Indicateur de progression linéaire ou circulaire avec valeurs déterminées/indéterminées.'

props:
  type: object
  properties:
    value:
      type: number
      title: Valeur actuelle
      description: '0-100 (omis si indeterminate)'
    min:
      type: number
      default: 0
    max:
      type: number
      default: 100
    variant:
      type: string
      enum: ['linear','circular']
      default: 'linear'
    color:
      type: string
      enum: ['primary','secondary','info','success','warning','error']
      default: 'primary'
    size:
      type: string
      enum: ['small','medium','large']
      default: 'medium'
    indeterminate:
      type: boolean
      default: false
      description: 'Animation indéterminée (pas de valeur)'
    striped:
      type: boolean
      default: false
      description: 'Rayures animées (linéaire uniquement)'
    showLabel:
      type: boolean
      default: false
      description: 'Afficher le pourcentage'
    label:
      type: string
      description: 'Label pour lecteurs d'écran'
    attributes:
      type: Drupal\Core\Template\Attribute
```

---

## 🎭 Variants

- **Types** : `linear`|`circular`.
- **Couleurs** : `primary`|`secondary`|`info`|`success`|`warning`|`error`.
- **Tailles** : `small`|`medium`|`large`.
- **États** : `indeterminate` (animation infinie), `striped` (rayures animées pour linear).
- **Label** : `showLabel` (affiche pourcentage).

---

## 🎨 Design Tokens

- Couleurs par variante:
  - Primary: `--ps-color-primary-600`
  - Secondary: `--ps-color-neutral-500`
  - Info: `--ps-color-info-600`
  - Success: `--ps-color-success-600`
  - Warning: `--ps-color-warning-600`
  - Error: `--ps-color-error-600`
- Track (fond): `--ps-color-neutral-200`
- Hauteurs linéaires:
  - Small: 4px
  - Medium: 8px
  - Large: 12px
- Tailles circulaires:
  - Small: 40px
  - Medium: 64px
  - Large: 96px
- Bordures: `--ps-border-radius-full` (linéaire arrondi)
- Transitions: `--ps-transition-duration-normal`

---

## 🔧 Template Twig

```twig
{#
 * Template for Progress Bar atom.
 * Variables: voir API YAML
 #}

{% set variant = variant|default('linear') %}
{% set color = color|default('primary') %}
{% set size = size|default('medium') %}
{% set indeterminate = indeterminate|default(false) %}
{% set striped = striped|default(false) %}
{% set showLabel = showLabel|default(false) %}
{% set value = indeterminate ? null : (value ?? 0) %}
{% set min = min|default(0) %}
{% set max = max|default(100) %}
{% set percent = value is not null ? ((value - min) / (max - min) * 100)|round : 0 %}

{% set root_classes = [
  'ps-progress',
  'ps-progress--' ~ variant,
  'ps-progress--' ~ color,
  'ps-progress--' ~ size,
  indeterminate ? 'ps-progress--indeterminate',
  striped and variant == 'linear' ? 'ps-progress--striped',
  showLabel ? 'ps-progress--with-label'
] %}

{% if variant == 'linear' %}
  <div {{ attributes.addClass(root_classes) }} role="progressbar" {% if not indeterminate %}aria-valuenow="{{ value }}" aria-valuemin="{{ min }}" aria-valuemax="{{ max }}"{% endif %} {% if label %}aria-label="{{ label }}"{% endif %}>
    <div class="ps-progress__track">
      <div class="ps-progress__fill" {% if not indeterminate %}style="width: {{ percent }}%;"{% endif %}></div>
    </div>
    {% if showLabel and not indeterminate %}
      <span class="ps-progress__label">{{ percent }}%</span>
    {% endif %}
  </div>
{% else %}
  {% set circumference = 2 * 3.14159 * 45 %}
  {% set offset = indeterminate ? 0 : (circumference * (1 - percent / 100)) %}
  <div {{ attributes.addClass(root_classes) }} role="progressbar" {% if not indeterminate %}aria-valuenow="{{ value }}" aria-valuemin="{{ min }}" aria-valuemax="{{ max }}"{% endif %} {% if label %}aria-label="{{ label }}"{% endif %}>
    <svg class="ps-progress__svg" viewBox="0 0 100 100">
      <circle class="ps-progress__track-circle" cx="50" cy="50" r="45" fill="none" stroke-width="8"></circle>
      <circle class="ps-progress__fill-circle" cx="50" cy="50" r="45" fill="none" stroke-width="8" stroke-dasharray="{{ circumference }}" stroke-dashoffset="{{ offset }}" {% if indeterminate %}class="ps-progress__fill-circle--indeterminate"{% endif %}></circle>
    </svg>
    {% if showLabel and not indeterminate %}
      <span class="ps-progress__label">{{ percent }}%</span>
    {% endif %}
  </div>
{% endif %}
```

---

## 🎨 Styles SCSS

```scss
.ps-progress {
  position: relative;
  font-family: var(--ps-font-family-primary);

  // Linear variant
  &--linear {
    display: flex; align-items: center; gap: var(--ps-spacing-2, 8px);
    
    .ps-progress__track {
      flex: 1; height: 8px;
      background: var(--ps-color-neutral-200, #E8EBEF);
      border-radius: var(--ps-border-radius-full, 999px);
      overflow: hidden;
    }

    .ps-progress__fill {
      height: 100%;
      background: var(--ps-color-primary-600, #0DB089);
      border-radius: var(--ps-border-radius-full, 999px);
      transition: width var(--ps-transition-duration-normal, 0.3s) ease;
    }

    &.ps-progress--small .ps-progress__track { height: 4px; }
    &.ps-progress--medium .ps-progress__track { height: 8px; }
    &.ps-progress--large .ps-progress__track { height: 12px; }
  }

  // Circular variant
  &--circular {
    display: inline-flex; align-items: center; justify-content: center;
    position: relative;
    width: 64px; height: 64px;

    .ps-progress__svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .ps-progress__track-circle { stroke: var(--ps-color-neutral-200, #E8EBEF); }
    .ps-progress__fill-circle {
      stroke: var(--ps-color-primary-600, #0DB089);
      stroke-linecap: round;
      transition: stroke-dashoffset var(--ps-transition-duration-normal, 0.3s) ease;
    }

    .ps-progress__label {
      position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    }

    &.ps-progress--small { width: 40px; height: 40px; }
    &.ps-progress--medium { width: 64px; height: 64px; }
    &.ps-progress--large { width: 96px; height: 96px; }
  }

  // Colors
  &--primary .ps-progress__fill { background: var(--ps-color-primary-600, #0DB089); }
  &--primary .ps-progress__fill-circle { stroke: var(--ps-color-primary-600, #0DB089); }
  
  &--secondary .ps-progress__fill { background: var(--ps-color-neutral-500, #6E7C89); }
  &--secondary .ps-progress__fill-circle { stroke: var(--ps-color-neutral-500, #6E7C89); }
  
  &--info .ps-progress__fill { background: var(--ps-color-info-600, #039BE5); }
  &--info .ps-progress__fill-circle { stroke: var(--ps-color-info-600, #039BE5); }
  
  &--success .ps-progress__fill { background: var(--ps-color-success-600, #0DB089); }
  &--success .ps-progress__fill-circle { stroke: var(--ps-color-success-600, #0DB089); }
  
  &--warning .ps-progress__fill { background: var(--ps-color-warning-600, #FB8C00); }
  &--warning .ps-progress__fill-circle { stroke: var(--ps-color-warning-600, #FB8C00); }
  
  &--error .ps-progress__fill { background: var(--ps-color-error-600, #E53935); }
  &--error .ps-progress__fill-circle { stroke: var(--ps-color-error-600, #E53935); }

  // Indeterminate state
  &--indeterminate.ps-progress--linear .ps-progress__fill {
    width: 40%;
    animation: ps-progress-indeterminate 1.5s ease-in-out infinite;
  }

  &--indeterminate.ps-progress--circular .ps-progress__fill-circle {
    animation: ps-progress-circular-rotate 2s linear infinite;
    stroke-dasharray: 80, 200;
    stroke-dashoffset: 0;
  }

  // Striped (linear only)
  &--striped.ps-progress--linear .ps-progress__fill {
    background-image: linear-gradient(
      45deg,
      rgba(255, 255, 255, 0.15) 25%,
      transparent 25%,
      transparent 50%,
      rgba(255, 255, 255, 0.15) 50%,
      rgba(255, 255, 255, 0.15) 75%,
      transparent 75%,
      transparent
    );
    background-size: 1rem 1rem;
    animation: ps-progress-stripes 1s linear infinite;
  }

  &__label {
    font-size: var(--ps-font-size-sm, 14px);
    font-weight: var(--ps-font-weight-medium, 500);
    color: var(--ps-color-neutral-700, #3B4754);
    white-space: nowrap;
  }
}

// Animations
@keyframes ps-progress-indeterminate {
  0% { left: -40%; }
  100% { left: 100%; }
}

@keyframes ps-progress-circular-rotate {
  0% { transform: rotate(0deg); stroke-dashoffset: 0; }
  50% { stroke-dashoffset: -70; }
  100% { transform: rotate(360deg); stroke-dashoffset: -140; }
}

@keyframes ps-progress-stripes {
  0% { background-position: 0 0; }
  100% { background-position: 1rem 0; }
}
```

---

## ♿ Accessibilité

- `role="progressbar"` : identifie l'indicateur de progression.
- `aria-valuenow`, `aria-valuemin`, `aria-valuemax` : valeurs actuelles pour états déterminés.
- `aria-label` : description du contexte (ex: "Upload en cours").
- Pas de focus : élément non-interactif.
- Contraste suffisant entre track et fill.

---

## 📱 Comportement responsive

- Linéaire : largeur fluide (flex: 1).
- Circulaire : taille fixe adaptée via modificateurs.

---

## 🧪 Exemples d'usage

```twig
{# Linear progress bar #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: 60,
  color: 'primary',
  size: 'medium',
  showLabel: true,
  label: 'Upload en cours'
} %}

{# Circular progress bar #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'circular',
  value: 75,
  color: 'success',
  size: 'large',
  showLabel: true
} %}

{# Indeterminate linear #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  indeterminate: true,
  color: 'info',
  label: 'Chargement en cours'
} %}

{# Striped animated #}
{% include '@ps_theme/ps-progress/ps-progress.twig' with {
  variant: 'linear',
  value: 45,
  color: 'warning',
  striped: true,
  showLabel: true
} %}
```

---

## 📚 Ressources

- WAI-ARIA: `role="progressbar"`, `aria-valuenow`, `aria-valuemin`, `aria-valuemax`
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/transitions.yml`, `/design/tokens/borders.yml`
