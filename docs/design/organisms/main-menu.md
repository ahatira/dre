# Main Menu (Organism)

**Niveau Atomic Design** : Organism / Navigation  
**Catégorie** : Primary navigation  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Navigation principale du site. Gère l’orientation (horizontale/verticale), les sous-menus (1 niveau suffisant pour la plupart des sites), les états actifs et l’accessibilité. Sans JS, la structure reste navigable; avec JS, on peut améliorer l’ouverture/fermeture des sous-menus.

---

## 🎨 Aperçu visuel

```
[Logo]  Accueil  Biens ▼  Services ▼  Contact     [FR ▾]
                 ├─ Location
                 └─ Achat
```

---

## 🏗️ Structure BEM

```html
<nav class="ps-main-menu ps-main-menu--horizontal" aria-label="Navigation principale" data-menu>
  <button class="ps-main-menu__toggle" type="button" aria-expanded="false" aria-controls="main-menu-list" data-menu-toggle>Menu</button>
  <ul class="ps-main-menu__list ps-main-menu__list--level-1" id="main-menu-list">
    <li class="ps-menu-item ps-menu-item--level-1">
      <a class="ps-menu-item__link" href="/">Accueil</a>
    </li>
    <li class="ps-menu-item ps-menu-item--level-1 ps-menu-item--has-children">
      <button class="ps-main-menu__submenu-toggle" type="button" aria-expanded="false" data-submenu-toggle>Biens</button>
      <ul class="ps-main-menu__list ps-main-menu__list--level-2" hidden>
        <li class="ps-menu-item ps-menu-item--level-2"><a class="ps-menu-item__link" href="/location">Location</a></li>
        <li class="ps-menu-item ps-menu-item--level-2"><a class="ps-menu-item__link" href="/achat">Achat</a></li>
      </ul>
    </li>
  </ul>
</nav>
```

### Classes BEM

```
ps-main-menu                                // Bloc navigation
  ps-main-menu__toggle                      // Bouton mobile
  ps-main-menu__list                        // UL de niveau
  ps-main-menu__submenu-toggle              // Bouton pour ouvrir un sous-menu

(Modèle items)
ps-menu-item (réutilisé, voir molecule)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Main Menu'
status: stable
group: organisms
description: 'Navigation principale avec sous-menus et support mobile.'

props:
  type: object
  properties:
    items:
      type: array
      title: Éléments de menu
      items:
        type: object
        properties:
          label:
            type: string
          url:
            type: string
          active:
            type: boolean
            default: false
          disabled:
            type: boolean
            default: false
          icon:
            type: string
          children:
            type: array
            items:
              type: object
              properties:
                label:
                  type: string
                url:
                  type: string
                active:
                  type: boolean
                  default: false
                disabled:
                  type: boolean
                  default: false
                icon:
                  type: string
        required: ['label']
    orientation:
      type: string
      enum: ['horizontal','vertical']
      default: 'horizontal'
    collapsible:
      type: boolean
      title: Afficher le bouton mobile
      default: true
    toggleLabel:
      type: string
      title: Libellé du bouton mobile
      default: 'Menu'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - items
```

---

## 🎭 Variants

- Orientation: `horizontal` (desktop) ou `vertical` (sidebars, mobile).
- `collapsible: true` affiche un bouton de bascule (mobile-first).

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-base`, `--ps-font-weight-medium`
- Spacing: `--ps-spacing-3|4` pour gaps/paddings
- Couleurs liens: palette `--ps-link*`, séparateurs en option via `--ps-color-neutral-200`
- Focus: `--ps-color-interactive-focus-outline`, `--ps-border-width-focus`

---

## 🔧 Template Twig

```twig
{#
 * Template for Main Menu organism.
 * Variables: voir API YAML
 #}

{% macro render_items(items, level) %}
  <ul class="ps-main-menu__list ps-main-menu__list--level-{{ level }}" {% if level > 1 %}hidden{% endif %}>
    {% for item in items %}
      {% set has_children = item.children is defined and item.children|length > 0 %}
      {% set li_classes = ['ps-menu-item', 'ps-menu-item--level-' ~ level, has_children ? 'ps-menu-item--has-children', item.active ? 'ps-menu-item--active', item.disabled ? 'ps-menu-item--disabled'] %}
      <li class="{{ li_classes|join(' ') }}">
        {% if has_children %}
          <button class="ps-main-menu__submenu-toggle" type="button" aria-expanded="false" data-submenu-toggle>{{ item.label }}</button>
          {{ _self.render_items(item.children, level + 1) }}
        {% else %}
          <a class="ps-menu-item__link" href="{{ item.url }}" {% if item.active %}aria-current="page"{% endif %}>{{ item.label }}</a>
        {% endif %}
      </li>
    {% endfor %}
  </ul>
{% endmacro %}

{% set orientation = orientation|default('horizontal') %}
{% set classes = ['ps-main-menu', 'ps-main-menu--' ~ orientation] %}

<nav {{ attributes.addClass(classes) }} aria-label="Navigation principale">
  {% if collapsible %}
    <button class="ps-main-menu__toggle" type="button" aria-expanded="false" aria-controls="ps-main-menu-list">{{ toggleLabel|default('Menu') }}</button>
  {% endif %}
  {{ _self.render_items(items, 1) }}
</nav>
```

---

## 🎨 Styles SCSS

```scss
.ps-main-menu {
  font-family: var(--ps-font-family-primary);

  &__toggle {
    display: none; // visible via media queries pour mobile
    padding: var(--ps-spacing-3, 12px) var(--ps-spacing-4, 16px);
    border: 1px solid var(--ps-color-neutral-300, #D2D7DB);
    border-radius: var(--ps-border-radius-sm, 4px);
    background: var(--ps-color-white, #FFFFFF);
  }

  &__list {
    list-style: none; margin: 0; padding: 0;
    display: flex; gap: var(--ps-spacing-2, 8px);
  }

  &--vertical &__list { flex-direction: column; }

  // Sous-menus
  &__list--level-2 { position: absolute; background: var(--ps-color-white, #FFF); border: 1px solid var(--ps-color-neutral-300, #D2D7DB); border-radius: var(--ps-border-radius-sm, 4px); padding: var(--ps-spacing-2, 8px) 0; box-shadow: var(--ps-shadow-md, 0 8px 24px rgba(0,0,0,0.12)); }

  // Démonstration CSS-only: affichage au :focus-within du parent (progressive enhancement)
  .ps-menu-item--has-children:focus-within > &__list--level-2,
  .ps-menu-item--has-children:hover > &__list--level-2 { display: block; position: absolute; }
}

@media (max-width: 768px) {
  .ps-main-menu {
    &__toggle { display: inline-flex; }
    &__list { display: none; }
    &__toggle[aria-expanded="true"] + &__list { display: flex; flex-direction: column; }
  }
}
```

---

## ♿ Accessibilité

- `nav[aria-label]`, `aria-current="page"` pour l’élément actif.
- Boutons de sous-menus avec `aria-expanded` (géré par JS), list-level caché via `hidden`.
- Focus visible et navigation clavier (recommandé d’ajouter JS pour roving tabindex en pattern menubar si nécessaire).

---

## 📱 Comportement responsive

- `collapsible: true` affiche un bouton pour ouvrir/fermer la liste au mobile.
- Orientation contrôlable via prop; styles adaptatifs.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-main-menu/ps-main-menu.twig' with {
  orientation: 'horizontal',
  items: [
    { label: 'Accueil', url: '/', active: true },
    { label: 'Biens', url: '/biens', children: [
      { label: 'Location', url: '/location' },
      { label: 'Achat', url: '/achat' },
    ]},
    { label: 'Contact', url: '/contact' },
  ],
} %}
```

---

## 📚 Ressources

- Dépendances: Menu Item, Link, Icon
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`
- ARIA Authoring Practices: Menubar / Disclosure Navigation

---

## 🔌 JavaScript behavior (facultatif)

```js
// Mobile toggle and submenu disclosure (progressive enhancement)
document.querySelectorAll('[data-menu]').forEach((menu) => {
  const toggle = menu.querySelector('[data-menu-toggle]');
  if (toggle) {
    const list = menu.querySelector('.ps-main-menu__list--level-1');
    toggle.addEventListener('click', () => {
      const expanded = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!expanded));
      if (list) list.style.display = expanded ? 'none' : 'flex';
    });
  }

  menu.querySelectorAll('[data-submenu-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!expanded));
      const nextList = btn.nextElementSibling;
      if (nextList && nextList.matches('.ps-main-menu__list')) {
        nextList.hidden = expanded;
      }
    });
  });
});
```
