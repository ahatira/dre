# Dropdown (Molecule)

**Niveau Atomic Design** : Molecule / Form  
**Catégorie** : Selector  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Sélecteur d’options sous forme de menu déroulant. Combine un champ (affichage), un déclencheur et une liste d’options. Fournit un fallback `<select>` natif.

---

## 🎨 Aperçu visuel

```
[ Catégorie ▾ ]
┌─────────────────────────┐
│ Appartement             │
│ Maison                  │
│ Loft                    │
│ Villa (désactivé)       │
└─────────────────────────┘
```

---

## 🏗️ Structure BEM

```html
<div class="ps-dropdown ps-dropdown--medium">
  <button class="ps-dropdown__button" type="button" aria-haspopup="listbox" aria-expanded="false">
    <span class="ps-dropdown__label">Catégorie</span>
    <span class="ps-dropdown__icon" data-icon="chevron-down" aria-hidden="true"></span>
  </button>
  <ul class="ps-dropdown__list" role="listbox">
    <li class="ps-dropdown__option" role="option" aria-selected="true">Appartement</li>
    <li class="ps-dropdown__option" role="option">Maison</li>
    <li class="ps-dropdown__option" role="option">Loft</li>
    <li class="ps-dropdown__option ps-dropdown__option--disabled" role="option" aria-disabled="true">Villa</li>
  </ul>
  <select class="ps-dropdown__native" name="category">
    <option value="apartment" selected>Appartement</option>
    <option value="house">Maison</option>
    <option value="loft">Loft</option>
    <option value="villa" disabled>Villa</option>
  </select>
</div>
```

### Classes BEM

```
ps-dropdown                         // Block principal
  ps-dropdown__button               // Bouton déclencheur
  ps-dropdown__label                // Libellé affiché
  ps-dropdown__icon                 // Icône chevron
  ps-dropdown__list                 // Liste des options (overlay)
  ps-dropdown__option               // Option individuelle
  ps-dropdown__native               // Fallback <select>

Modificateurs :
  ps-dropdown--small|medium|large   // Tailles
  ps-dropdown--disabled             // État désactivé
  ps-dropdown__option--disabled     // Option désactivée
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Dropdown'
status: stable
group: molecules
description: 'Menu déroulant accessible avec fallback <select>.'

props:
  type: object
  properties:
    name:
      type: string
      title: Name
      description: Nom du champ de formulaire
    label:
      type: string
      title: Label visible
    placeholder:
      type: string
      title: Placeholder
    size:
      type: string
      title: Taille
      enum: ['small','medium','large']
      default: 'medium'
    disabled:
      type: boolean
      title: Désactivé
      default: false
    options:
      type: array
      title: Options
      items:
        type: object
        properties:
          label:
            type: string
          value:
            type: string
          selected:
            type: boolean
            default: false
          disabled:
            type: boolean
            default: false
        required: ['label','value']
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - name
    - options
```

---

## 🎭 Variants

- Tailles `small|medium|large`.
- `disabled: true` désactive le contrôle.

---

## 🎨 Design Tokens

- Base champ: utilise tokens de `Field` (typo, bordure, radius, fond, focus)
- Icône: taille via `--ps-icon-size-16|20`
- Couleurs: `--ps-color-text`, `--ps-color-neutral-200|300`, `--ps-color-white`, `--ps-color-primary-green` (focus)
- Ombres overlay: `--ps-shadow-md`

Si des tokens manquent (icon sizes), proposer `sizes.icon.16/20`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Dropdown molecule.
 * Variables:
 * - name: string (requis)
 * - label: string
 * - placeholder: string
 * - size: 'small'|'medium'|'large'
 * - disabled: bool
 * - options: array<{label, value, selected?, disabled?}>
 * - attributes: Attribute
 #}

{% set size = size|default('medium') %}
{% set classes = [
  'ps-dropdown',
  'ps-dropdown--' ~ size,
  disabled ? 'ps-dropdown--disabled'
] %}

<div {{ attributes.addClass(classes) }} data-dropdown>
  <button class="ps-dropdown__button" type="button" aria-haspopup="listbox" aria-expanded="false" data-dropdown-button {% if disabled %}disabled aria-disabled="true"{% endif %}>
    <span class="ps-dropdown__label">{{ label ?? placeholder ?? 'Sélectionner' }}</span>
    <span class="ps-dropdown__icon" data-icon="chevron-down" aria-hidden="true"></span>
  </button>

  <ul class="ps-dropdown__list" role="listbox" tabindex="-1" hidden data-dropdown-list>
    {% for option in options %}
      <li class="ps-dropdown__option{{ option.disabled ? ' ps-dropdown__option--disabled' }}" role="option" aria-selected="{{ option.selected ? 'true' : 'false' }}" {% if option.disabled %}aria-disabled="true"{% endif %}>{{ option.label }}</li>
    {% endfor %}
  </ul>

  <select class="ps-dropdown__native" name="{{ name }}" {% if disabled %}disabled aria-disabled="true"{% endif %}>
    {% for option in options %}
      <option value="{{ option.value }}" {% if option.selected %}selected{% endif %} {% if option.disabled %}disabled{% endif %}>{{ option.label }}</option>
    {% endfor %}
  </select>
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-dropdown {
  position: relative;
  display: inline-block;

  &__button {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--ps-spacing-2, 8px);
    width: 100%;
    min-width: 220px;
    padding: var(--ps-field-padding-vertical, 8px) var(--ps-field-padding-horizontal, 12px);
    border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
    border-radius: var(--ps-border-radius-sm, 4px);
    background: var(--ps-color-white, #FFFFFF);
    color: var(--ps-color-text, #1F2A33);
    font-family: var(--ps-font-family-primary);
    font-size: var(--ps-font-size-base, 16px);
    line-height: var(--ps-line-height-normal, 1.5);
    cursor: pointer;

    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
    &:disabled { background: var(--ps-color-neutral-100, #F2F4F5); color: var(--ps-color-text-muted, #6B7780); cursor: not-allowed; }
  }

  &--small &__button { font-size: var(--ps-font-size-sm, 14px); padding: 6px 10px; }
  &--large &__button { font-size: var(--ps-font-size-lg, 18px); padding: 10px 14px; }

  &__icon { width: 20px; height: 20px; flex-shrink: 0; }

  &__list {
    position: absolute;
    z-index: 1000;
    top: calc(100% + 4px);
    left: 0;
    min-width: 100%;
    background: var(--ps-color-white, #FFFFFF);
    border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
    border-radius: var(--ps-border-radius-sm, 4px);
    box-shadow: var(--ps-shadow-md, 0 8px 24px rgba(0,0,0,0.12));
    padding: var(--ps-spacing-2, 8px) 0;
    list-style: none;
  }

  &__option {
    padding: 8px 12px;
    cursor: pointer;
    color: var(--ps-color-text, #1F2A33);
    &:hover { background: var(--ps-color-neutral-100, #F2F4F5); }
    &[aria-selected='true'] { background: rgba(0, 145, 90, 0.08); }
    &--disabled { color: var(--ps-color-text-muted, #6B7780); cursor: not-allowed; }
  }

  &__native { display: none; }
}
```

---

## ♿ Accessibilité

- Le bouton utilise `aria-haspopup="listbox"` et `aria-expanded`; la liste a `role="listbox"` et les options `role="option"`.
- Fournit un fallback `<select>` natif visible si JS désactivé (ou activer via utilitaire si requis).
- Focus visible et contrastes conformes.

---

## 📱 Comportement responsive

- Largeur fluide; l’overlay s’aligne sous le bouton.
- Gérer la hauteur via `max-height` + scroll si liste longue (optionnel).

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-dropdown/ps-dropdown.twig' with {
  name: 'category',
  label: 'Catégorie',
  options: [
    { label: 'Appartement', value: 'apartment', selected: true },
    { label: 'Maison', value: 'house' },
    { label: 'Loft', value: 'loft' },
    { label: 'Villa', value: 'villa', disabled: true },
  ],
} %}
```

---

## 🔌 JavaScript behavior (facultatif)

- Au clic sur `__button`, toggle `hidden` + `aria-expanded`.
- Gérer navigation clavier: Up/Down, Enter, Escape; roving tabindex.

```js
// Minimal disclosure behavior (no framework)
document.querySelectorAll('[data-dropdown]').forEach((root) => {
  const btn = root.querySelector('[data-dropdown-button]');
  const list = root.querySelector('[data-dropdown-list]');
  if (!btn || !list) return;
  const close = () => { list.hidden = true; btn.setAttribute('aria-expanded', 'false'); };
  const open = () => { list.hidden = false; btn.setAttribute('aria-expanded', 'true'); };

  btn.addEventListener('click', () => {
    const expanded = btn.getAttribute('aria-expanded') === 'true';
    expanded ? close() : open();
  });

  // Optional: close on Escape and outside click
  document.addEventListener('click', (e) => { if (!root.contains(e.target)) close(); });
  root.addEventListener('keydown', (e) => { if (e.key === 'Escape') { close(); btn.focus(); } });
});
```
