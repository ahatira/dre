# Tabs (Molecule)

**Niveau Atomic Design** : Molecule / Navigation  
**Catégorie** : Content switcher  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Système d'onglets accessible permettant de regrouper plusieurs contenus et de basculer entre eux via des onglets. Conforme aux APG WAI-ARIA (tablist, tab, tabpanel) avec gestion clavier et focus. Supporte les orientations horizontale/verticale, activation au focus ou au clic, et variantes visuelles.

---

## 🎨 Aperçu visuel

```
| Onglet A | Onglet B | Onglet C |
[Contenu de l'onglet sélectionné]
```

---

## 🏗️ Structure BEM

```html
<div class="ps-tabs ps-tabs--underline" data-tabs data-activation="auto" data-orientation="horizontal">
  <div class="ps-tabs__list" role="tablist" aria-orientation="horizontal" data-tablist>
    <button class="ps-tabs__tab is-selected" id="tab-1" role="tab" aria-selected="true" tabindex="0" aria-controls="panel-1" data-tab>
      <span class="ps-tabs__label">Onglet A</span>
    </button>
    <button class="ps-tabs__tab" id="tab-2" role="tab" aria-selected="false" tabindex="-1" aria-controls="panel-2" data-tab>
      <span class="ps-tabs__label">Onglet B</span>
    </button>
  </div>
  <div class="ps-tabs__panel" id="panel-1" role="tabpanel" aria-labelledby="tab-1" data-tabpanel>
    Contenu A…
  </div>
  <div class="ps-tabs__panel" id="panel-2" role="tabpanel" aria-labelledby="tab-2" hidden data-tabpanel>
    Contenu B…
  </div>
</div>
```

### Classes BEM

```
ps-tabs                                // Block
  ps-tabs__list                        // Conteneur d'onglets (tablist)
  ps-tabs__tab                         // Bouton d'onglet (tab)
  ps-tabs__label                       // Libellé de l'onglet
  ps-tabs__panel                       // Panneau de contenu (tabpanel)

Modificateurs :
  ps-tabs--underline                   // Ligne active sous l'onglet (par défaut)
  ps-tabs--boxed                       // Onglets encadrés
  ps-tabs--pill                        // Onglets pastilles
  ps-tabs--vertical                    // Orientation verticale
  is-selected                          // État d'onglet actif
  is-disabled                          // État désactivé
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Tabs'
status: stable
group: molecules
description: 'Onglets accessibles (tablist/tab/tabpanel) avec gestion clavier et options.'

props:
  type: object
  properties:
    tabs:
      type: array
      title: Onglets
      items:
        type: object
        properties:
          id:
            type: string
            description: 'Identifiant unique (sinon auto: tab-{index})'
          label:
            type: string
          panel:
            type: string
            description: 'Contenu HTML du panneau'
          icon:
            type: string
            description: 'Nom d’icône optionnel (utilise atoms/icon)'
          disabled:
            type: boolean
            default: false
        required: ['label','panel']
    selectedIndex:
      type: integer
      default: 0
    orientation:
      type: string
      enum: ['horizontal','vertical']
      default: 'horizontal'
    activation:
      type: string
      enum: ['auto','manual']
      default: 'auto'
    variant:
      type: string
      enum: ['underline','boxed','pill']
      default: 'underline'
    equalWidth:
      type: boolean
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - tabs
```

---

## 🎭 Variants

- `variant`: `underline` (par défaut), `boxed`, `pill`.
- `orientation`: `horizontal` ou `vertical` + modificateur `ps-tabs--vertical`.
- `activation`: `auto` (actif au focus ou clic) ou `manual` (nécessite Entrée/Espace).
- `equalWidth`: uniformise la largeur des onglets.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-base`, `--ps-font-weight-medium`
- Couleurs: `--ps-color-interactive-text`, `--ps-color-neutral-500`, `--ps-color-primary-600`, `--ps-color-neutral-300`
- État actif/hover: `--ps-color-interactive-hover-bg`, `--ps-color-interactive-focus-outline`
- Bordures: `--ps-border-width-default`, `--ps-border-radius-sm`
- Espacements: `--ps-spacing-2|3|4`

---

## 🔧 Template Twig

```twig
{#
 * Template for Tabs molecule.
 * Variables: voir API YAML
 #}

{% set variant = variant|default('underline') %}
{% set orientation = orientation|default('horizontal') %}
{% set activation = activation|default('auto') %}
{% set selected = selectedIndex is defined ? selectedIndex|default(0) : 0 %}

{% set root_classes = [
  'ps-tabs',
  variant ? 'ps-tabs--' ~ variant,
  orientation == 'vertical' ? 'ps-tabs--vertical'
] %}

<div {{ attributes.addClass(root_classes) }} data-tabs data-activation="{{ activation }}" data-orientation="{{ orientation }}">
  <div class="ps-tabs__list" role="tablist" aria-orientation="{{ orientation }}" data-tablist>
    {% for tab in tabs %}
      {% set tid = tab.id ?? ('tab-' ~ loop.index) %}
      {% set pid = 'panel-' ~ (tab.id ?? loop.index) %}
      {% set is_selected = (loop.index0 == selected) and not tab.disabled %}
      <button
        class="ps-tabs__tab {{ is_selected ? 'is-selected' }} {{ tab.disabled ? 'is-disabled' }}"
        id="{{ tid }}"
        role="tab"
        aria-selected="{{ is_selected ? 'true' : 'false' }}"
        tabindex="{{ is_selected ? '0' : '-1' }}"
        aria-controls="{{ pid }}"
        {% if tab.disabled %}disabled aria-disabled="true"{% endif %}
        data-tab>
        {% if tab.icon %}<svg class="ps-tabs__icon" aria-hidden="true"><use href="#icon-{{ tab.icon }}"></use></svg>{% endif %}
        <span class="ps-tabs__label">{{ tab.label }}</span>
      </button>
    {% endfor %}
  </div>

  {% for tab in tabs %}
    {% set tid = tab.id ?? ('tab-' ~ loop.index) %}
    {% set pid = 'panel-' ~ (tab.id ?? loop.index) %}
    {% set is_selected = (loop.index0 == selected) and not tab.disabled %}
    <div class="ps-tabs__panel" id="{{ pid }}" role="tabpanel" aria-labelledby="{{ tid }}" data-tabpanel {% if not is_selected %}hidden{% endif %}>
      {{ tab.panel|raw }}
    </div>
  {% endfor %}
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-tabs {
  font-family: var(--ps-font-family-primary);
  color: var(--ps-color-interactive-text, #111);

  &__list {
    display: flex; gap: var(--ps-spacing-2, 8px);
    border-bottom: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
  }
  &--vertical {
    .ps-tabs__list { flex-direction: column; border-bottom: none; }
  }

  &__tab {
    position: relative;
    display: inline-flex; align-items: center; gap: var(--ps-spacing-2, 8px);
    padding: var(--ps-spacing-3, 12px) var(--ps-spacing-4, 16px);
    background: none; border: none; cursor: pointer;
    color: var(--ps-color-neutral-600, #3B4754);

    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
    &.is-disabled { opacity: .5; cursor: not-allowed; }
  }

  // Variants
  &--underline {
    .ps-tabs__tab.is-selected { color: var(--ps-color-primary-700, #0E7A5F); }
    .ps-tabs__tab.is-selected::after {
      content: ""; position: absolute; left: 0; right: 0; bottom: -1px; height: 2px;
      background: var(--ps-color-primary-600, #0DB089);
    }
  }
  &--boxed {
    .ps-tabs__tab { border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB); border-radius: var(--ps-border-radius-sm, 4px); }
    .ps-tabs__tab.is-selected { background: var(--ps-color-interactive-hover-bg, #F3F6F9); }
  }
  &--pill {
    .ps-tabs__tab { border-radius: 999px; background: transparent; }
    .ps-tabs__tab.is-selected { background: var(--ps-color-primary-50, #E8FAF5); color: var(--ps-color-primary-700, #0E7A5F); }
  }

  &__panel { padding: var(--ps-spacing-4, 16px) 0; }

  // Equal width option
  &.is-equal {
    .ps-tabs__list { display: grid; grid-auto-flow: column; grid-auto-columns: 1fr; gap: var(--ps-spacing-2, 8px); }
    .ps-tabs__tab { width: 100%; justify-content: center; }
  }
}
```

---

## ♿ Accessibilité

- Rôles: `tablist` (container), `tab` (boutons), `tabpanel` (panneaux).
- Relations: `aria-controls`/`aria-labelledby` + `id` uniques synchronisés.
- Focus: roving tabindex (un seul onglet `tabindex=0`), `aria-selected` vrai pour l’actif.
- Clavier: `ArrowLeft/Right` (ou `Up/Down` en vertical), `Home/End`, `Enter`/`Space` selon `activation`.
- Onglets désactivés: `disabled` + `aria-disabled` et ignorés par la navigation.

---

## 📱 Comportement responsive

- La liste s’enroule si trop d’onglets; `equalWidth` disponible pour mise en page régulière.
- Orientation verticale possible via `ps-tabs--vertical`.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-tabs/ps-tabs.twig' with {
  variant: 'underline',
  orientation: 'horizontal',
  activation: 'auto',
  selectedIndex: 0,
  equalWidth: false,
  tabs: [
    { id: 'a', label: 'Détails', panel: '<p>Contenu A</p>' },
    { id: 'b', label: 'Caractéristiques', panel: '<p>Contenu B</p>' },
    { id: 'c', label: 'Avis', panel: '<p>Contenu C</p>', disabled: true }
  ]
} %}
```

---

## 🔌 JavaScript behavior (facultatif)

```js
// Minimal accessible Tabs behavior (APG compliant)
function setupTabs(root) {
  const activation = root.getAttribute('data-activation') || 'auto';
  const orientation = root.getAttribute('data-orientation') || 'horizontal';
  const tabs = Array.from(root.querySelectorAll('[data-tab]'));
  const panels = Array.from(root.querySelectorAll('[data-tabpanel]'));

  function select(i, focus = true) {
    tabs.forEach((t, idx) => {
      const selected = idx === i && !t.hasAttribute('disabled');
      t.classList.toggle('is-selected', selected);
      t.setAttribute('aria-selected', selected ? 'true' : 'false');
      t.setAttribute('tabindex', selected ? '0' : '-1');
      if (focus && selected) t.focus();
      const pid = t.getAttribute('aria-controls');
      const panel = root.querySelector('#' + pid);
      if (panel) panel.hidden = !selected;
    });
  }

  // Initial selected index
  const initial = tabs.findIndex(t => t.getAttribute('aria-selected') === 'true' && !t.hasAttribute('disabled'));
  if (initial >= 0) select(initial, false);

  tabs.forEach((tab, idx) => {
    tab.addEventListener('click', () => {
      if (tab.hasAttribute('disabled')) return;
      select(idx, true);
    });

    tab.addEventListener('keydown', (e) => {
      const key = e.key;
      const horiz = orientation !== 'vertical';
      const enabledTabs = tabs.filter(t => !t.hasAttribute('disabled'));
      const currentIndex = tabs.indexOf(document.activeElement);
      if (key === 'Home') { e.preventDefault(); select(tabs.indexOf(enabledTabs[0])); }
      else if (key === 'End') { e.preventDefault(); select(tabs.indexOf(enabledTabs[enabledTabs.length - 1])); }
      else if ((horiz && (key === 'ArrowRight' || key === 'ArrowLeft')) || (!horiz && (key === 'ArrowDown' || key === 'ArrowUp'))) {
        e.preventDefault();
        const dir = (key === 'ArrowRight' || key === 'ArrowDown') ? 1 : -1;
        let next = currentIndex;
        do { next = (next + dir + tabs.length) % tabs.length; } while (tabs[next].hasAttribute('disabled'));
        const shouldActivate = activation === 'auto';
        if (shouldActivate) select(next, true); else tabs[next].focus();
      } else if (activation === 'manual' && (key === 'Enter' || key === ' ')) {
        e.preventDefault(); select(idx, true);
      }
    });
  });
}

document.querySelectorAll('[data-tabs]').forEach(setupTabs);
```

---

## 📚 Ressources

- WAI-ARIA Authoring Practices 1.2 — Tabs
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`, `/design/tokens/borders.yml`
```
