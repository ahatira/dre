# Stepper (Molecule)

**Niveau Atomic Design** : Molecule / Navigation  
**Catégorie** : Progress indicator  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Indicateur de progression multi-étapes pour formulaires, wizards, ou processus séquentiels. Affiche les étapes avec numéros/icônes, états (complete, current, upcoming), labels, et descriptions optionnelles. Supporte orientations horizontale/verticale, navigation cliquable, et variantes visuelles (numbered, icon, minimal).

---

## 🎨 Aperçu visuel

```
1 ──── 2 ──── 3 ──── 4
✓      •      ○      ○
Infos  Adresse Photos Valider
```

---

## 🏗️ Structure BEM

```html
<nav class="ps-stepper ps-stepper--horizontal ps-stepper--numbered" aria-label="Formulaire multi-étapes">
  <ol class="ps-stepper__list">
    <li class="ps-stepper__item ps-stepper__item--complete">
      <a class="ps-stepper__link" href="#step-1" aria-current="false">
        <span class="ps-stepper__indicator">
          <svg class="ps-stepper__icon" aria-hidden="true"><use href="#icon-check"></use></svg>
          <span class="ps-stepper__number">1</span>
        </span>
        <span class="ps-stepper__content">
          <span class="ps-stepper__label">Informations</span>
          <span class="ps-stepper__description">Vos coordonnées</span>
        </span>
      </a>
    </li>
    <li class="ps-stepper__item ps-stepper__item--current">
      <a class="ps-stepper__link" href="#step-2" aria-current="step">
        <span class="ps-stepper__indicator">
          <span class="ps-stepper__number">2</span>
        </span>
        <span class="ps-stepper__content">
          <span class="ps-stepper__label">Adresse</span>
          <span class="ps-stepper__description">Votre localisation</span>
        </span>
      </a>
    </li>
    <li class="ps-stepper__item ps-stepper__item--upcoming">
      <span class="ps-stepper__link" aria-disabled="true">
        <span class="ps-stepper__indicator">
          <span class="ps-stepper__number">3</span>
        </span>
        <span class="ps-stepper__content">
          <span class="ps-stepper__label">Photos</span>
          <span class="ps-stepper__description">Images du bien</span>
        </span>
      </span>
    </li>
    <li class="ps-stepper__item ps-stepper__item--upcoming">
      <span class="ps-stepper__link" aria-disabled="true">
        <span class="ps-stepper__indicator">
          <span class="ps-stepper__number">4</span>
        </span>
        <span class="ps-stepper__content">
          <span class="ps-stepper__label">Validation</span>
        </span>
      </span>
    </li>
  </ol>
</nav>
```

### Classes BEM

```
ps-stepper                                // Block (nav)
  ps-stepper__list                        // Liste <ol>
  ps-stepper__item                        // Item <li>
  ps-stepper__link                        // Lien/span
  ps-stepper__indicator                   // Cercle/icône numéro
  ps-stepper__number                      // Numéro étape
  ps-stepper__icon                        // Icône (complete/custom)
  ps-stepper__content                     // Texte (label + description)
  ps-stepper__label                       // Label étape
  ps-stepper__description                 // Description optionnelle

Modificateurs :
  ps-stepper--horizontal                  // Horizontal (défaut)
  ps-stepper--vertical                    // Vertical
  
  ps-stepper--numbered                    // Numéros (défaut)
  ps-stepper--icon                        // Icônes custom
  ps-stepper--minimal                     // Minimaliste (dots)
  
  ps-stepper__item--complete              // Étape complétée
  ps-stepper__item--current               // Étape actuelle
  ps-stepper__item--upcoming              // Étape future
  ps-stepper__item--error                 // Étape en erreur
  
  ps-stepper--clickable                   // Étapes cliquables
  ps-stepper--with-descriptions           // Avec descriptions
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Stepper'
status: stable
group: molecules
description: 'Indicateur de progression multi-étapes pour wizards et formulaires.'

props:
  type: object
  properties:
    steps:
      type: array
      title: Étapes
      items:
        type: object
        properties:
          id:
            type: string
          label:
            type: string
          description:
            type: string
          status:
            type: string
            enum: ['complete','current','upcoming','error']
            default: 'upcoming'
          href:
            type: string
            description: 'URL si cliquable'
          icon:
            type: string
            description: 'Nom d'icône custom'
        required: ['label','status']
    orientation:
      type: string
      enum: ['horizontal','vertical']
      default: 'horizontal'
    variant:
      type: string
      enum: ['numbered','icon','minimal']
      default: 'numbered'
    clickable:
      type: boolean
      default: false
      description: 'Rendre les étapes cliquables'
    withDescriptions:
      type: boolean
      default: false
    ariaLabel:
      type: string
      default: 'Progression du formulaire'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - steps
```

---

## 🎭 Variants

- **Orientations** : `horizontal`|`vertical`.
- **Styles** : `numbered` (chiffres), `icon` (icônes custom), `minimal` (dots).
- **États** : `complete`|`current`|`upcoming`|`error` par étape.
- **Cliquable** : étapes avec `href` navigables.
- **Descriptions** : textes secondaires optionnels.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-sm`, `--ps-font-weight-semibold`
- Couleurs:
  - Complete: `--ps-color-success-600` (indicator), `--ps-color-success-700` (text)
  - Current: `--ps-color-primary-600` (indicator), `--ps-color-primary-700` (text)
  - Upcoming: `--ps-color-neutral-400` (indicator), `--ps-color-neutral-600` (text)
  - Error: `--ps-color-error-600` (indicator), `--ps-color-error-700` (text)
  - Connector: `--ps-color-neutral-300`
- Tailles:
  - Indicator: 32–40px (circle)
  - Icon: 16–20px
- Espacements: `--ps-spacing-2|3|4`
- Transitions: `--ps-transition-duration-fast`

---

## 🔧 Template Twig

```twig
{#
 * Template for Stepper molecule.
 * Variables: voir API YAML
 #}

{% set orientation = orientation|default('horizontal') %}
{% set variant = variant|default('numbered') %}
{% set clickable = clickable|default(false) %}
{% set withDescriptions = withDescriptions|default(false) %}
{% set ariaLabel = ariaLabel|default('Progression du formulaire') %}

{% set root_classes = [
  'ps-stepper',
  'ps-stepper--' ~ orientation,
  'ps-stepper--' ~ variant,
  clickable ? 'ps-stepper--clickable',
  withDescriptions ? 'ps-stepper--with-descriptions'
] %}

<nav {{ attributes.addClass(root_classes) }} aria-label="{{ ariaLabel }}">
  <ol class="ps-stepper__list">
    {% for step in steps %}
      {% set status = step.status|default('upcoming') %}
      {% set is_current = status == 'current' %}
      {% set is_clickable = clickable and step.href and status in ['complete','current'] %}
      {% set tag = is_clickable ? 'a' : 'span' %}
      
      <li class="ps-stepper__item ps-stepper__item--{{ status }}">
        <{{ tag }} class="ps-stepper__link" {% if is_clickable %}href="{{ step.href }}"{% endif %} {% if is_current %}aria-current="step"{% else %}aria-current="false"{% endif %} {% if not is_clickable %}aria-disabled="true"{% endif %}>
          <span class="ps-stepper__indicator">
            {% if status == 'complete' and variant != 'icon' %}
              <svg class="ps-stepper__icon" aria-hidden="true"><use href="#icon-check"></use></svg>
            {% elseif step.icon and variant == 'icon' %}
              <svg class="ps-stepper__icon" aria-hidden="true"><use href="#icon-{{ step.icon }}"></use></svg>
            {% elseif variant != 'minimal' %}
              <span class="ps-stepper__number">{{ loop.index }}</span>
            {% endif %}
          </span>
          <span class="ps-stepper__content">
            <span class="ps-stepper__label">{{ step.label }}</span>
            {% if step.description %}
              <span class="ps-stepper__description">{{ step.description }}</span>
            {% endif %}
          </span>
        </{{ tag }}>
      </li>
    {% endfor %}
  </ol>
</nav>
```

---

## 🎨 Styles SCSS

```scss
.ps-stepper {
  font-family: var(--ps-font-family-primary);

  &__list {
    display: flex; list-style: none; padding: 0; margin: 0;
  }

  // Horizontal (default)
  &--horizontal {
    .ps-stepper__list { flex-direction: row; align-items: flex-start; }
    .ps-stepper__item {
      flex: 1; position: relative;
      &:not(:last-child)::after {
        content: ""; position: absolute; top: 16px; left: 50%; right: -50%;
        height: 2px; background: var(--ps-color-neutral-300, #D2D7DB); z-index: -1;
      }
    }
    .ps-stepper__link { display: flex; flex-direction: column; align-items: center; text-align: center; gap: var(--ps-spacing-2, 8px); }
  }

  // Vertical
  &--vertical {
    .ps-stepper__list { flex-direction: column; align-items: flex-start; }
    .ps-stepper__item {
      position: relative;
      &:not(:last-child) {
        padding-bottom: var(--ps-spacing-4, 16px);
        &::after {
          content: ""; position: absolute; top: 40px; bottom: 0; left: 16px;
          width: 2px; background: var(--ps-color-neutral-300, #D2D7DB);
        }
      }
    }
    .ps-stepper__link { display: flex; flex-direction: row; align-items: flex-start; gap: var(--ps-spacing-3, 12px); }
    .ps-stepper__content { text-align: left; }
  }

  &__link {
    text-decoration: none; cursor: default;
    &[href] { cursor: pointer; }
    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 4px; border-radius: var(--ps-border-radius-sm, 4px); }
  }

  &__indicator {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--ps-color-neutral-200, #E8EBEF);
    color: var(--ps-color-neutral-600, #54636F);
    font-weight: var(--ps-font-weight-semibold, 600);
    transition: background var(--ps-transition-duration-fast, 0.15s), color var(--ps-transition-duration-fast, 0.15s);
    flex-shrink: 0;
  }

  &__number,
  &__icon {
    font-size: var(--ps-font-size-sm, 14px);
  }

  &__icon { width: 16px; height: 16px; }

  &__content {
    display: flex; flex-direction: column; gap: var(--ps-spacing-1, 4px);
  }

  &__label {
    font-size: var(--ps-font-size-sm, 14px);
    font-weight: var(--ps-font-weight-medium, 500);
    color: var(--ps-color-neutral-700, #3B4754);
  }

  &__description {
    font-size: var(--ps-font-size-xs, 12px);
    color: var(--ps-color-neutral-600, #54636F);
  }

  // States
  &__item--complete {
    .ps-stepper__indicator {
      background: var(--ps-color-success-600, #0DB089);
      color: var(--ps-color-neutral-0, #FFF);
    }
    .ps-stepper__label { color: var(--ps-color-success-700, #0E7A5F); }
    &::after { background: var(--ps-color-success-600, #0DB089); }
  }

  &__item--current {
    .ps-stepper__indicator {
      background: var(--ps-color-primary-600, #0DB089);
      color: var(--ps-color-neutral-0, #FFF);
      box-shadow: 0 0 0 4px var(--ps-color-primary-100, #C5F4E9);
    }
    .ps-stepper__label {
      color: var(--ps-color-primary-700, #0E7A5F);
      font-weight: var(--ps-font-weight-semibold, 600);
    }
  }

  &__item--upcoming {
    .ps-stepper__indicator {
      background: var(--ps-color-neutral-200, #E8EBEF);
      color: var(--ps-color-neutral-600, #54636F);
    }
    .ps-stepper__label { color: var(--ps-color-neutral-600, #54636F); }
  }

  &__item--error {
    .ps-stepper__indicator {
      background: var(--ps-color-error-600, #E53935);
      color: var(--ps-color-neutral-0, #FFF);
    }
    .ps-stepper__label { color: var(--ps-color-error-700, #C62828); }
  }

  // Clickable variant hover
  &--clickable {
    .ps-stepper__link[href]:hover .ps-stepper__label {
      text-decoration: underline;
    }
  }

  // Minimal variant
  &--minimal {
    .ps-stepper__indicator {
      width: 12px; height: 12px;
    }
    &.ps-stepper--horizontal .ps-stepper__item:not(:last-child)::after { top: 5px; }
    &.ps-stepper--vertical .ps-stepper__item:not(:last-child)::after { top: 20px; left: 5px; }
  }
}
```

---

## ♿ Accessibilité

- `<nav>` avec `aria-label` décrivant le contexte.
- Liste ordonnée `<ol>` pour sémantique séquentielle.
- `aria-current="step"` sur l'étape active.
- `aria-disabled="true"` sur étapes non cliquables.
- Focus visible sur liens cliquables.

---

## 📱 Comportement responsive

- Horizontal : peut passer en vertical sur petits écrans (media query optionnelle).
- Vertical : adapté mobile par défaut.
- Labels/descriptions adaptables (ellipsis ou wrap).

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-stepper/ps-stepper.twig' with {
  orientation: 'horizontal',
  variant: 'numbered',
  clickable: true,
  withDescriptions: true,
  steps: [
    { label: 'Informations', description: 'Vos coordonnées', status: 'complete', href: '#step-1' },
    { label: 'Adresse', description: 'Votre localisation', status: 'current', href: '#step-2' },
    { label: 'Photos', description: 'Images du bien', status: 'upcoming' },
    { label: 'Validation', status: 'upcoming' }
  ]
} %}
```

---

## 📚 Ressources

- WAI-ARIA: `aria-current`, `aria-disabled`
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`, `/design/tokens/transitions.yml`
