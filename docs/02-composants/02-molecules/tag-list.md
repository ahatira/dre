# Tag List (Molecule)

**Niveau Atomic Design** : Molecule / Collection  
**Catégorie** : Content display  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Liste de tags (badges) pour catégories, filtres, ou labels multiples. Composition de badges avec gestion d'overflow ("+X more"), tags removables (close icon), et variantes layout (inline, wrap, fixed-height avec scroll). Supporte semantic markup, keyboard navigation pour suppression, et callbacks JS optionnels.

---

## 🎨 Aperçu visuel

```
[Design] [UX] [Figma] [+2 more]
[React ×] [Vue ×] [Angular ×]
```

---

## 🏗️ Structure BEM

```html
<div class="ps-tag-list ps-tag-list--wrap ps-tag-list--removable">
  <ul class="ps-tag-list__list">
    <li class="ps-tag-list__item">
      <span class="ps-badge ps-badge--primary ps-badge--sm ps-badge--pill">
        <span class="ps-badge__text">Design</span>
        <button class="ps-badge__remove" aria-label="Supprimer Design" data-tag="design">
          <span class="ps-badge__icon" data-icon="x" aria-hidden="true"></span>
        </button>
      </span>
    </li>
    <li class="ps-tag-list__item">
      <span class="ps-badge ps-badge--primary ps-badge--sm ps-badge--pill">
        <span class="ps-badge__text">UX</span>
        <button class="ps-badge__remove" aria-label="Supprimer UX" data-tag="ux">
          <span class="ps-badge__icon" data-icon="x" aria-hidden="true"></span>
        </button>
      </span>
    </li>
    <li class="ps-tag-list__item ps-tag-list__item--overflow">
      <button class="ps-tag-list__overflow-button" aria-label="Afficher 2 tags supplémentaires">
        +2 more
      </button>
    </li>
  </ul>
</div>
```

### Classes BEM

```
ps-tag-list                               // Block (div)
  ps-tag-list__list                       // Liste <ul>
  ps-tag-list__item                       // Item <li>
  ps-tag-list__item--overflow             // Item overflow button
  ps-tag-list__overflow-button            // Bouton "+X more"

Modificateurs :
  ps-tag-list--inline                     // Inline (nowrap)
  ps-tag-list--wrap                       // Wrap (défaut)
  ps-tag-list--scroll                     // Fixed height + scroll
  
  ps-tag-list--removable                  // Tags avec boutons remove
  ps-tag-list--sm                         // Petite taille (badges sm)
  ps-tag-list--md                         // Taille moyenne (badges md, défaut)
  ps-tag-list--lg                         // Grande taille (badges lg)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Tag List'
status: stable
group: molecules
description: 'Liste de tags (badges) avec gestion d'overflow et suppression.'

props:
  type: object
  properties:
    tags:
      type: array
      title: Tags
      items:
        type: object
        properties:
          id:
            type: string
          label:
            type: string
          variant:
            type: string
            enum: ['primary','secondary','info','success','warning','danger']
            description: 'Couleur sémantique. Omission = état par défaut'
            default: 'primary'
          removable:
            type: boolean
            default: false
          href:
            type: string
            description: 'URL si cliquable'
          icon:
            type: string
            description: 'Icône optionnelle'
        required: ['label']
    layout:
      type: string
      enum: ['inline','wrap','scroll']
      default: 'wrap'
    size:
      type: string
      enum: ['sm','md','lg']
      default: 'md'
    maxVisible:
      type: number
      description: 'Nombre max visible avant overflow button'
    removable:
      type: boolean
      default: false
      description: 'Rendre tous les tags removables'
    ariaLabel:
      type: string
      default: 'Liste de tags'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - tags
```

---

## 🎭 Variants

- **Layouts** : `inline` (nowrap, horizontal scroll), `wrap` (multi-ligne), `scroll` (fixed height + vertical scroll).
- **Tailles** : `sm`|`md`|`lg` (appliqué aux badges).
- **Removable** : tags avec boutons `×` pour suppression.
- **Overflow** : `maxVisible` → affiche "+X more" button pour révéler tags cachés.

---

## 🎨 Design Tokens

- Typo: héritée des badges
- Couleurs: héritées des badges (variant colors)
- Espacements:
  - Gap list: `--ps-spacing-2` (8px)
  - Padding scroll: `--ps-spacing-3` (12px)
- Tailles:
  - Max height scroll: 200px
  - Badge sizes: `--ps-badge-height-{sm|md|lg}`
- Transitions: `--ps-transition-duration-fast`

---

## 🔧 Template Twig

```twig
{#
 * Template for Tag List molecule.
 * Variables: voir API YAML
 #}

{% set layout = layout|default('wrap') %}
{% set size = size|default('md') %}
{% set removable = removable|default(false) %}
{% set maxVisible = maxVisible|default(0) %}
{% set ariaLabel = ariaLabel|default('Liste de tags') %}

{% set root_classes = [
  'ps-tag-list',
  'ps-tag-list--' ~ layout,
  'ps-tag-list--' ~ size,
  removable ? 'ps-tag-list--removable'
] %}

{% set total = tags|length %}
{% set visible_tags = maxVisible > 0 and maxVisible < total ? tags|slice(0, maxVisible) : tags %}
{% set hidden_count = maxVisible > 0 and maxVisible < total ? total - maxVisible : 0 %}

<div {{ attributes.addClass(root_classes) }} aria-label="{{ ariaLabel }}" data-tag-list>
  <ul class="ps-tag-list__list">
    {% for tag in visible_tags %}
      {% set tag_removable = tag.removable ?? removable %}
      {% set badge_classes = [
        'ps-badge',
        'ps-badge--' ~ (tag.variant|default('primary')),
        'ps-badge--' ~ size,
        'ps-badge--pill'
      ] %}
      
      <li class="ps-tag-list__item" data-tag-id="{{ tag.id }}">
        {% if tag.href %}
          <a class="{{ badge_classes|join(' ') }}" href="{{ tag.href }}">
            {% if tag.icon %}
              <span class="ps-badge__icon" data-icon="{{ tag.icon }}" aria-hidden="true"></span>
            {% endif %}
            <span class="ps-badge__text">{{ tag.label }}</span>
          </a>
        {% else %}
          <span class="{{ badge_classes|join(' ') }}">
            {% if tag.icon %}
              <span class="ps-badge__icon" data-icon="{{ tag.icon }}" aria-hidden="true"></span>
            {% endif %}
            <span class="ps-badge__text">{{ tag.label }}</span>
            {% if tag_removable %}
              <button class="ps-badge__remove" type="button" aria-label="Supprimer {{ tag.label }}" data-tag-remove="{{ tag.id }}">
                <span class="ps-badge__icon" data-icon="x" aria-hidden="true"></span>
              </button>
            {% endif %}
          </span>
        {% endif %}
      </li>
    {% endfor %}
    
    {% if hidden_count > 0 %}
      <li class="ps-tag-list__item ps-tag-list__item--overflow">
        <button class="ps-tag-list__overflow-button" type="button" aria-label="Afficher {{ hidden_count }} tags supplémentaires" data-tag-list-expand>
          +{{ hidden_count }} more
        </button>
      </li>
    {% endif %}
  </ul>
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-tag-list {
  font-family: var(--ps-font-family-primary);

  &__list {
    display: flex; gap: var(--ps-spacing-2, 8px);
    list-style: none; padding: 0; margin: 0;
  }

  // Layouts
  &--wrap {
    .ps-tag-list__list { flex-wrap: wrap; }
  }

  &--inline {
    .ps-tag-list__list { flex-wrap: nowrap; overflow-x: auto; }
  }

  &--scroll {
    max-height: 200px; overflow-y: auto; padding: var(--ps-spacing-3, 12px);
    border: var(--ps-border-width-sm, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
    border-radius: var(--ps-border-radius-md, 8px);
    .ps-tag-list__list { flex-wrap: wrap; }
  }

  &__item {
    display: inline-flex; align-items: center;
  }

  &__overflow-button {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0 var(--ps-spacing-3, 12px);
    height: 28px; // Match badge md
    background: var(--ps-color-neutral-100, #F5F7F9);
    color: var(--ps-color-neutral-700, #3B4754);
    border: none;
    border-radius: var(--ps-border-radius-full, 9999px);
    font-size: var(--ps-font-size-xs, 12px);
    font-weight: var(--ps-font-weight-medium, 500);
    cursor: pointer;
    transition: background var(--ps-transition-duration-fast, 0.15s);

    &:hover {
      background: var(--ps-color-neutral-200, #E8EBEF);
    }

    &:focus-visible {
      outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF);
      outline-offset: 2px;
    }
  }

  // Size adjustments
  &--sm {
    .ps-tag-list__overflow-button { height: 24px; font-size: var(--ps-font-size-xs, 12px); }
  }

  &--lg {
    .ps-tag-list__overflow-button { height: 36px; font-size: var(--ps-font-size-sm, 14px); }
  }

  // Badge remove button styles (extends badge component)
  .ps-badge__remove {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0; margin-left: var(--ps-spacing-1, 4px);
    width: 16px; height: 16px;
    background: transparent;
    border: none;
    border-radius: var(--ps-border-radius-sm, 4px);
    color: currentColor;
    cursor: pointer;
    transition: background var(--ps-transition-duration-fast, 0.15s), color var(--ps-transition-duration-fast, 0.15s);

    &:hover {
      background: rgba(0, 0, 0, 0.1);
    }

    &:focus-visible {
      outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF);
      outline-offset: 2px;
    }

    .ps-badge__icon { width: 12px; height: 12px; }
  }
}
```

---

## ♿ Accessibilité

- `aria-label` sur conteneur décrivant la liste.
- Boutons remove avec `aria-label` incluant le label du tag.
- Focus visible sur tous les boutons interactifs.
- Navigation clavier (Tab pour focus, Enter/Space pour action).

---

## 📱 Comportement responsive

- Layout `wrap` : adapte automatiquement sur petits écrans.
- Layout `inline` : scroll horizontal sur débordement.
- Layout `scroll` : hauteur fixe avec scroll vertical.

---

## 🧪 Exemples d'usage

### Liste simple (wrap)

```twig
{% include '@ps_theme/ps-tag-list/ps-tag-list.twig' with {
  layout: 'wrap',
  size: 'md',
  tags: [
    { label: 'Design', variant: 'primary' },
    { label: 'UX', variant: 'secondary' },
    { label: 'Figma', variant: 'info' }
  ]
} %}
```

### Tags removables

```twig
{% include '@ps_theme/ps-tag-list/ps-tag-list.twig' with {
  layout: 'wrap',
  size: 'sm',
  removable: true,
  tags: [
    { id: 'react', label: 'React', variant: 'primary' },
    { id: 'vue', label: 'Vue', variant: 'secondary' },
    { id: 'angular', label: 'Angular', variant: 'info' }
  ]
} %}
```

### Overflow (max visible)

```twig
{% include '@ps_theme/ps-tag-list/ps-tag-list.twig' with {
  layout: 'wrap',
  maxVisible: 5,
  tags: [
    { label: 'Tag 1' },
    { label: 'Tag 2' },
    { label: 'Tag 3' },
    { label: 'Tag 4' },
    { label: 'Tag 5' },
    { label: 'Tag 6' },
    { label: 'Tag 7' }
  ]
} %}
```

---

## 🧩 Comportement JavaScript (optionnel)

### 1. Tag removal

```js
// data-tag-remove="tag-id"
document.addEventListener('click', e => {
  const removeBtn = e.target.closest('[data-tag-remove]');
  if (!removeBtn) return;
  
  const tagId = removeBtn.dataset.tagRemove;
  const listItem = removeBtn.closest('[data-tag-id]');
  
  // Callback custom event
  const event = new CustomEvent('ps-tag:remove', { detail: { tagId }, cancelable: true });
  if (!listItem.dispatchEvent(event)) return;
  
  // Remove DOM
  listItem.remove();
});

// Usage avec écouteur custom
document.addEventListener('ps-tag:remove', e => {
  console.log('Tag removed:', e.detail.tagId);
  // Sync avec backend, update filter state, etc.
});
```

### 2. Overflow expansion

```js
// data-tag-list-expand
document.addEventListener('click', e => {
  const expandBtn = e.target.closest('[data-tag-list-expand]');
  if (!expandBtn) return;
  
  const tagList = expandBtn.closest('[data-tag-list]');
  // Logic: reveal hidden tags (requires storing hidden tags in data attr or separate container)
  
  // Simple approach: remove maxVisible limit dynamically
  expandBtn.closest('.ps-tag-list__item--overflow').remove();
  
  // Dispatch event
  tagList.dispatchEvent(new CustomEvent('ps-tag-list:expand'));
});
```

### 3. Data attributes

```html
<div class="ps-tag-list" data-tag-list>
  <!-- Conteneur principal -->
</div>

<li class="ps-tag-list__item" data-tag-id="react">
  <!-- Item avec ID pour ciblage -->
</li>

<button data-tag-remove="react">
  <!-- Bouton remove avec tag ID -->
</button>

<button data-tag-list-expand>
  <!-- Bouton expand pour overflow -->
</button>
```

---

## 📚 Ressources

- Composition: `design/atoms/badge.md`
- Tokens: `/design/tokens/spacing.yml`, `/design/tokens/colors.yml`
- WAI-ARIA: button labels, focus management
