# Language Selector (Molecule)

**Niveau Atomic Design** : Molecule / Navigation  
**Catégorie** : Locale switcher  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Composant de changement de langue/pays. Combine un déclencheur (bouton), une liste d’options et l’affichage d’un drapeau (Flag) avec libellé. Fallback `<select>` natif pour accessibilité et non-JS.

Normalisation (Option C):
- Accepte des tags BCP 47 (ex: `fr-FR`, `en-GB`) via `locale`. Le Flag dérive le code pays pour les assets.
- Un `code` ISO peut aussi être fourni; si les deux sont présents, `code` prime.

---

## 🎨 Aperçu visuel

```
[ FR  Français  ▾ ]
┌─────────────────────┐
│ FR  Français        │
│ GB  English (UK)    │
│ DE  Deutsch         │
│ ES  Español         │
└─────────────────────┘
```

---

## 🏗️ Structure BEM

```html
<nav class="ps-language-selector" aria-label="Sélecteur de langue">
  <div class="ps-language-selector__control">
    <button class="ps-language-selector__button" type="button" aria-haspopup="listbox" aria-expanded="false">
      <span class="ps-language-selector__current">
        <span class="ps-language-selector__flag">FR</span>
        <span class="ps-language-selector__label">Français</span>
      </span>
      <svg class="ps-language-selector__icon" aria-hidden="true"><use href="#icon-chevron-down"></use></svg>
    </button>
    <ul class="ps-language-selector__list" role="listbox" hidden>
      <li class="ps-language-selector__option" role="option" aria-selected="true">
        <span class="ps-language-selector__flag">FR</span><span class="ps-language-selector__label">Français</span>
      </li>
      <li class="ps-language-selector__option" role="option"><span class="ps-language-selector__flag">GB</span><span class="ps-language-selector__label">English (UK)</span></li>
      <li class="ps-language-selector__option" role="option"><span class="ps-language-selector__flag">DE</span><span class="ps-language-selector__label">Deutsch</span></li>
    </ul>
    <select class="ps-language-selector__native" name="lang">
      <option value="fr" selected>Français</option>
      <option value="en-gb">English (UK)</option>
      <option value="de">Deutsch</option>
    </select>
  </div>
</nav>
```

### Classes BEM

```
ps-language-selector                         // Block principal
  ps-language-selector__control              // Conteneur contrôle
  ps-language-selector__button               // Bouton déclencheur
  ps-language-selector__current              // Affichage langue courante
  ps-language-selector__icon                 // Chevron
  ps-language-selector__list                 // Liste des options
  ps-language-selector__option               // Option
  ps-language-selector__flag                 // Zone drapeau (compose avec Flag)
  ps-language-selector__label                // Libellé langue
  ps-language-selector__native               // Fallback <select>

Modificateurs :
  ps-language-selector--small|medium        // Tailles
  ps-language-selector--disabled            // État désactivé
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Language Selector'
status: stable
group: molecules
description: 'Sélecteur de langue accessible avec drapeaux et fallback natif.'

props:
  type: object
  properties:
    name:
      type: string
      title: Name
      default: 'lang'
    size:
      type: string
      title: Taille
      enum: ['small','medium']
      default: 'medium'
    disabled:
      type: boolean
      title: Désactivé
      default: false
    current:
      type: object
      title: Langue courante
      properties:
        code:
          type: string
        label:
          type: string
        locale:
          type: string
      required: ['code','label']
    options:
      type: array
      title: Options
      items:
        type: object
        properties:
          code:
            type: string
          label:
            type: string
          value:
            type: string
          url:
            type: string
          locale:
            type: string
          selected:
            type: boolean
            default: false
          disabled:
            type: boolean
            default: false
        required: ['code','label']
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - current
    - options
```

---

## 🎭 Variants

- Tailles: `small|medium`.
- Désactivation globale: `disabled: true`.

---

## 🎨 Design Tokens

- Reprend tokens de `Dropdown` pour bouton/liste: bordure, radius, typo, focus.
- Espace: `--ps-spacing-2|3`, icône `20px`.
- Couleurs texte: `--ps-color-text`, neutres `--ps-color-neutral-100|300`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Language Selector molecule.
 * Variables:
 * - name: string
 * - size: 'small'|'medium'
 * - disabled: bool
 * - current: { code: string, label: string }
 * - options: array<{ code, label, value?, url?, selected?, disabled? }>
 * - attributes: Attribute
 #}

{% set size = size|default('medium') %}
{% set classes = [
  'ps-language-selector',
  'ps-language-selector--' ~ size,
  disabled ? 'ps-language-selector--disabled'
] %}

<nav {{ attributes.addClass(classes) }} aria-label="Sélecteur de langue" data-lang-selector>
  <div class="ps-language-selector__control">
    <button class="ps-language-selector__button" type="button" aria-haspopup="listbox" aria-expanded="false" data-lang-button {% if disabled %}disabled aria-disabled="true"{% endif %}>
      <span class="ps-language-selector__current">
        {% include '@ps_theme/ps-flag/ps-flag.twig' with { code: current.code, locale: current.locale, label: current.label, size: 'sm', shape: 'circle' } %}
        <span class="ps-language-selector__label">{{ current.label }}</span>
      </span>
      <svg class="ps-language-selector__icon" aria-hidden="true"><use href="#icon-chevron-down"></use></svg>
    </button>

    <ul class="ps-language-selector__list" role="listbox" hidden data-lang-list>
      {% for opt in options %}
        <li class="ps-language-selector__option" role="option" aria-selected="{{ opt.selected ? 'true' : 'false' }}" {% if opt.disabled %}aria-disabled="true"{% endif %}>
          {% include '@ps_theme/ps-flag/ps-flag.twig' with { code: opt.code, locale: opt.locale, label: opt.label, size: 'sm', shape: 'circle', decorative: true } %}
          <span class="ps-language-selector__label">{{ opt.label }}</span>
        </li>
      {% endfor %}
    </ul>

    <select class="ps-language-selector__native" name="{{ name|default('lang') }}" {% if disabled %}disabled aria-disabled="true"{% endif %}>
      {% for opt in options %}
        <option value="{{ opt.value ?? opt.code|lower }}" {% if opt.selected %}selected{% endif %} {% if opt.disabled %}disabled{% endif %}>{{ opt.label }}</option>
      {% endfor %}
    </select>
  </div>
</nav>
```

---

## 🎨 Styles SCSS

```scss
.ps-language-selector {
  font-family: var(--ps-font-family-primary);
  color: var(--ps-color-text, #1F2A33);

  &__button {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--ps-spacing-2, 8px);
    min-width: 200px;
    padding: var(--ps-field-padding-vertical, 8px) var(--ps-field-padding-horizontal, 12px);
    border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
    border-radius: var(--ps-border-radius-sm, 4px);
    background: var(--ps-color-white, #FFFFFF);

    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }

  &__current { display: inline-flex; align-items: center; gap: var(--ps-spacing-2, 8px); }
  &__icon { width: 20px; height: 20px; }

  &__list {
    position: absolute;
    z-index: 1000;
    margin-top: 4px;
    min-width: 100%;
    background: var(--ps-color-white, #FFFFFF);
    border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
    border-radius: var(--ps-border-radius-sm, 4px);
    box-shadow: var(--ps-shadow-md, 0 8px 24px rgba(0,0,0,0.12));
    padding: var(--ps-spacing-2, 8px) 0;
    list-style: none;
  }

  &__option { display: flex; align-items: center; gap: var(--ps-spacing-2, 8px); padding: 8px 12px; cursor: pointer; &:hover { background: var(--ps-color-neutral-100, #F2F4F5); } }

  &__native { display: none; }

  &--small &__button { font-size: var(--ps-font-size-sm, 14px); padding: 6px 10px; }
}
```

---

## ♿ Accessibilité

- Bouton avec `aria-haspopup=listbox`, `aria-expanded`; options avec `role=option`.
- Préférer afficher le libellé de langue; le drapeau est un complément visuel.
- Fallback `<select>` maintient l’accessibilité et le support sans JS.

---

## 📱 Comportement responsive

- Largeur fluide, liste positionnée sous le bouton.
- Support des contenus longs par retour à la ligne.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-language-selector/ps-language-selector.twig' with {
  current: { code: 'FR', label: 'Français' },
  options: [
    { code: 'FR', label: 'Français', value: 'fr', selected: true },
    { code: 'GB', label: 'English (UK)', value: 'en-gb' },
    { code: 'DE', label: 'Deutsch', value: 'de' },
  ],
} %}
```

---

## 📚 Ressources

- Dépendances: Flag, Dropdown
- Drupal: Language switcher patterns

---

## 🔌 JavaScript behavior (facultatif)

```js
document.querySelectorAll('[data-lang-selector]').forEach((root) => {
  const btn = root.querySelector('[data-lang-button]');
  const list = root.querySelector('[data-lang-list]');
  if (!btn || !list) return;
  const close = () => { list.hidden = true; btn.setAttribute('aria-expanded', 'false'); };
  const open = () => { list.hidden = false; btn.setAttribute('aria-expanded', 'true'); };
  btn.addEventListener('click', () => {
    const expanded = btn.getAttribute('aria-expanded') === 'true';
    expanded ? close() : open();
  });
  document.addEventListener('click', (e) => { if (!root.contains(e.target)) close(); });
  root.addEventListener('keydown', (e) => { if (e.key === 'Escape') { close(); btn.focus(); } });
});
```
