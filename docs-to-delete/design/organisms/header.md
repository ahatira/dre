# Header (Organism)

Type: Organism / Collection
Rôle: En-tête du site avec logo, navigation, actions, langue.
Statut: ✅ Stable
Version: 1.0.0

---

## BEM
```
ps-header
  ps-header__top
  ps-header__logo
  ps-header__menu
  ps-header__actions
  ps-header__language
  ps-header__search

Modificateurs:
  ps-header--sticky | --transparent | --with-submenu | --logged-in | --logged-out
```

## API
```yaml
name: 'PS Header'
status: stable
group: organisms
props:
  type: object
  properties:
    logo: { type: object, properties: { url: { type: string }, alt: { type: string }, link: { type: string } } }
    menuItems: { type: array, items: { type: object, properties: { text: { type: string }, url: { type: string }, submenu: { type: array } } } }
    actions: { type: array, items: { type: object, properties: { type: { type: string, enum: ['button','icon','dropdown'] }, label: { type: string }, icon: { type: string }, url: { type: string } } } }
    language: { type: object, description: 'Props pour ps-language-selector' }
    sticky: { type: boolean, default: true }
    transparent: { type: boolean, default: false }
```

## Twig
```twig
<header class="ps-header {{ sticky ? 'ps-header--sticky' }} {{ transparent ? 'ps-header--transparent' }}" role="banner">
  <div class="ps-header__top">
    <a class="ps-header__logo" href="{{ logo.link }}" aria-label="Accueil">
      <img src="{{ logo.url }}" alt="{{ logo.alt }}" />
    </a>
    <nav class="ps-header__menu" role="navigation" aria-label="Navigation principale">
      <ul>
      {% for item in menuItems %}
        <li>
          {% include '@ps_theme/ps-menu-item/ps-menu-item.twig' with item %}
        </li>
      {% endfor %}
      </ul>
    </nav>
    <div class="ps-header__actions">
      {% for action in actions %}
        {% if action.type == 'button' %}
          {% include '@ps_theme/ps-button/ps-button.twig' with { label: action.label, url: action.url, icon: action.icon } %}
        {% elseif action.type == 'icon' %}
          {% include '@ps_theme/ps-icon/ps-icon.twig' with { name: action.icon, ariaLabel: action.label } %}
        {% endif %}
      {% endfor %}
    </div>
    <div class="ps-header__language">
      {% include '@ps_theme/ps-language-selector/ps-language-selector.twig' with language %}
    </div>
  </div>
</header>
```

## Tokens
- Layout.header, Spacing.inline.menu_item_to_item

## A11y
- `role="banner"`, `nav` avec `aria-label`
- Focus management pour sous-menus
