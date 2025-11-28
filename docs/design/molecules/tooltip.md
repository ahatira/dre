# Tooltip (Molecule)

**Niveau Atomic Design** : Molecule / Feedback  
**Catégorie** : Hint / Helper  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Infobulle accessible apportant une aide contextuelle. Déclenchement au focus/hover/clic avec gestion d'`aria-describedby` et `role="tooltip"`. Reste utilisable au clavier et compatible mobile via déclenchement au clic.

---

## 🎨 Aperçu visuel

```
[?]  Survolez / focus → « Plus d'informations sur ce champ »
```

---

## 🏗️ Structure BEM

```html
<span class="ps-tooltip ps-tooltip--top ps-tooltip--medium" data-tooltip>
  <button class="ps-tooltip__trigger" type="button" aria-describedby="tt-1" data-tooltip-trigger>
    <svg class="ps-tooltip__icon" aria-hidden="true"><use href="#icon-info"></use></svg>
    <span class="visually-hidden">Plus d'informations</span>
  </button>
  <span class="ps-tooltip__content" id="tt-1" role="tooltip" data-tooltip-content hidden>
    Plus d'informations sur ce champ.
  </span>
</span>
```

### Classes BEM

```
ps-tooltip                          // Block
  ps-tooltip__trigger               // Déclencheur (bouton ou élément focusable)
  ps-tooltip__icon                  // Icône optionnelle
  ps-tooltip__content               // Contenu de l'infobulle

Modificateurs :
  ps-tooltip--top|right|bottom|left // Position de la bulle
  ps-tooltip--small|medium          // Tailles
  ps-tooltip--open                  // État ouvert (JS)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Tooltip'
status: stable
group: molecules
description: 'Infobulle accessible (role=tooltip) avec déclenchement focus/hover/clic.'

props:
  type: object
  properties:
    id:
      type: string
      title: ID du tooltip
      description: Identifiant unique pour aria-describedby
    label:
      type: string
      title: Libellé screen-reader du trigger
      default: 'Plus d\'informations'
    content:
      type: string
      title: Contenu
      description: Texte HTML de l'infobulle
    position:
      type: string
      enum: ['top','right','bottom','left']
      default: 'top'
    size:
      type: string
      enum: ['small','medium']
      default: 'medium'
    trigger:
      type: string
      enum: ['hover','click','focus']
      default: 'hover'
    disabled:
      type: boolean
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - content
```

---

## 🎭 Variants

- Positions: `top|right|bottom|left`.
- Tailles: `small|medium`.
- Déclenchement: `hover|click|focus`.

---

## 🎨 Design Tokens

- Couleurs: `--ps-color-neutral-900` (texte), `--ps-color-neutral-800` (fond), `--ps-color-white` pour inverse si nécessaire.
- Ombre: `--ps-shadow-md`
- Rayon: `--ps-border-radius-sm`
- Spacing: `--ps-spacing-2|3`
- Transition: `--ps-transition-standard`

Propositions si manquants: `colors.neutral.800|900`, `--ps-transition-standard`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Tooltip molecule.
 * Variables: voir API YAML
 #}

{% set id = id|default('tt-' ~ random()) %}
{% set position = position|default('top') %}
{% set size = size|default('medium') %}
{% set classes = [ 'ps-tooltip', 'ps-tooltip--' ~ position, 'ps-tooltip--' ~ size, disabled ? 'ps-tooltip--disabled' ] %}

<span {{ attributes.addClass(classes) }} data-tooltip data-trigger="{{ trigger|default('hover') }}">
  <button class="ps-tooltip__trigger" type="button" aria-describedby="{{ id }}" data-tooltip-trigger {% if disabled %}disabled aria-disabled="true"{% endif %}>
    <svg class="ps-tooltip__icon" aria-hidden="true"><use href="#icon-info"></use></svg>
    <span class="visually-hidden">{{ label|default("Plus d'informations") }}</span>
  </button>
  <span class="ps-tooltip__content" id="{{ id }}" role="tooltip" data-tooltip-content hidden>
    {{ content|raw }}
  </span>
</span>
```

---

## 🎨 Styles SCSS

```scss
.ps-tooltip {
  position: relative;
  display: inline-flex;
  align-items: center;

  &__trigger { display: inline-flex; align-items: center; justify-content: center; }
  &__icon { width: 20px; height: 20px; }

  &__content {
    position: absolute;
    min-width: 180px;
    max-width: 260px;
    padding: var(--ps-spacing-2, 8px) var(--ps-spacing-3, 12px);
    color: var(--ps-color-white, #FFF);
    background: var(--ps-color-neutral-900, #1F2A33);
    border-radius: var(--ps-border-radius-sm, 4px);
    box-shadow: var(--ps-shadow-md, 0 8px 24px rgba(0,0,0,0.12));
    z-index: 1000;
  }

  &--small &__content { min-width: 140px; max-width: 220px; }

  &--top &__content { bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%); }
  &--bottom &__content { top: calc(100% + 8px); left: 50%; transform: translateX(-50%); }
  &--left &__content { right: calc(100% + 8px); top: 50%; transform: translateY(-50%); }
  &--right &__content { left: calc(100% + 8px); top: 50%; transform: translateY(-50%); }

  &--open &__content { display: block; }
}
```

---

## ♿ Accessibilité

- `role="tooltip"` et association via `aria-describedby`.
- Accessible au clavier (focus sur trigger). Prévoir le déclenchement au clic sur mobile.
- Éviter d'utiliser uniquement le `title` natif (non stylable, moins accessible).

---

## 📱 Comportement responsive

- Largeur max contrôlée; se repositionne en fonction de la place disponible (améliorable via JS si nécessaire).

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-tooltip/ps-tooltip.twig' with { content: 'Plus d\'informations sur ce champ', position: 'top', trigger: 'hover' } %}
{% include '@ps_theme/ps-tooltip/ps-tooltip.twig' with { content: 'Cliquez pour voir l\'aide', position: 'right', trigger: 'click' } %}
```

---

## 🔌 JavaScript behavior (facultatif)

```js
// Minimal tooltip script
function setupTooltip(root) {
  const trigger = root.querySelector('[data-tooltip-trigger]');
  const content = root.querySelector('[data-tooltip-content]');
  if (!trigger || !content) return;
  const type = root.getAttribute('data-trigger') || 'hover';
  const open = () => { root.classList.add('ps-tooltip--open'); content.hidden = false; };
  const close = () => { root.classList.remove('ps-tooltip--open'); content.hidden = true; };

  if (type === 'click') {
    trigger.addEventListener('click', () => {
      const isOpen = root.classList.contains('ps-tooltip--open');
      isOpen ? close() : open();
    });
    document.addEventListener('click', (e) => { if (!root.contains(e.target)) close(); });
  } else {
    trigger.addEventListener('focus', open);
    trigger.addEventListener('blur', close);
    trigger.addEventListener('mouseenter', open);
    root.addEventListener('mouseleave', close);
  }
}

document.querySelectorAll('[data-tooltip]').forEach(setupTooltip);
```

---

## 📚 Ressources

- WAI-ARIA Authoring Practices: Tooltip
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/shadows.yml`, `/design/tokens/spacing.yml`
