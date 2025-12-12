# Table (Molecule)

**Niveau Atomic Design** : Molecule / Data display  
**Catégorie** : Tabular data  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Tableau de données structuré pour afficher des informations tabulaires. Supporte le tri par colonnes, sélection de lignes, états hover/active, lignes zébrées, variantes responsive (scroll horizontal, stacked mobile), sticky headers, et actions par ligne. Accessible avec rôles et en-têtes appropriés.

---

## 🎨 Aperçu visuel

```
┌─────────────┬───────────┬──────────┐
│ Nom         │ Statut    │ Actions  │
├─────────────┼───────────┼──────────┤
│ Propriété 1 │ Actif     │ [Edit]   │
│ Propriété 2 │ Inactif   │ [Edit]   │
└─────────────┴───────────┴──────────┘
```

---

## 🏗️ Structure BEM

```html
<div class="ps-table-wrapper">
  <table class="ps-table ps-table--striped ps-table--hover">
    <thead class="ps-table__head">
      <tr class="ps-table__row">
        <th class="ps-table__header ps-table__header--sortable" scope="col" aria-sort="ascending" data-sort="name">
          <button class="ps-table__sort-button" type="button">
            Nom
            <span class="ps-table__sort-icon" data-icon="arrow-up" aria-hidden="true"></span>
          </button>
        </th>
        <th class="ps-table__header" scope="col">Statut</th>
        <th class="ps-table__header" scope="col">Actions</th>
      </tr>
    </thead>
    <tbody class="ps-table__body">
      <tr class="ps-table__row">
        <td class="ps-table__cell" data-label="Nom">Propriété 1</td>
        <td class="ps-table__cell" data-label="Statut">
          <span class="ps-badge ps-badge--success">Actif</span>
        </td>
        <td class="ps-table__cell ps-table__cell--actions" data-label="Actions">
          <button class="ps-button ps-button--small" type="button">Éditer</button>
        </td>
      </tr>
      <tr class="ps-table__row ps-table__row--selected">
        <td class="ps-table__cell" data-label="Nom">Propriété 2</td>
        <td class="ps-table__cell" data-label="Statut">
          <span class="ps-badge ps-badge--neutral">Inactif</span>
        </td>
        <td class="ps-table__cell ps-table__cell--actions" data-label="Actions">
          <button class="ps-button ps-button--small" type="button">Éditer</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### Classes BEM

```
ps-table-wrapper                          // Conteneur scroll
ps-table                                  // Block (<table>)
  ps-table__head                          // <thead>
  ps-table__body                          // <tbody>
  ps-table__row                           // <tr>
  ps-table__header                        // <th>
  ps-table__cell                          // <td>
  ps-table__sort-button                   // Bouton tri colonne
  ps-table__sort-icon                     // Icône tri

Modificateurs :
  ps-table--striped                       // Lignes zébrées
  ps-table--hover                         // Hover sur lignes
  ps-table--bordered                      // Bordures complètes
  ps-table--compact                       // Espacement réduit
  ps-table--responsive                    // Scroll horizontal mobile
  ps-table--stacked                       // Empilé mobile (card-like)
  
  ps-table__header--sortable              // Colonne triable
  ps-table__header--sorted-asc            // Triée croissant
  ps-table__header--sorted-desc           // Triée décroissant
  ps-table__header--sticky                // En-tête fixe
  
  ps-table__row--selected                 // Ligne sélectionnée
  ps-table__row--disabled                 // Ligne désactivée
  
  ps-table__cell--actions                 // Colonne actions
  ps-table__cell--numeric                 // Contenu numérique (aligné droite)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Table'
status: stable
group: molecules
description: 'Tableau de données avec tri, sélection, responsive, et actions.'

props:
  type: object
  properties:
    headers:
      type: array
      title: En-têtes
      items:
        type: object
        properties:
          key:
            type: string
          label:
            type: string
          sortable:
            type: boolean
            default: false
          numeric:
            type: boolean
            default: false
          sticky:
            type: boolean
            default: false
        required: ['key','label']
    rows:
      type: array
      title: Lignes
      items:
        type: object
        properties:
          id:
            type: string
          cells:
            type: array
            description: 'Valeurs des cellules (ordre = headers)'
          selected:
            type: boolean
            default: false
          disabled:
            type: boolean
            default: false
    striped:
      type: boolean
      default: true
    hover:
      type: boolean
      default: true
    bordered:
      type: boolean
      default: false
    compact:
      type: boolean
      default: false
    responsive:
      type: boolean
      default: true
      description: 'Scroll horizontal mobile'
    stacked:
      type: boolean
      default: false
      description: 'Empilé mobile (incompatible avec responsive)'
    caption:
      type: string
      description: 'Caption pour accessibilité'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - headers
    - rows
```

---

## 🎭 Variants

- **Styles** : `striped`, `hover`, `bordered`, `compact`.
- **Responsive** : `responsive` (scroll horizontal) ou `stacked` (card-like mobile).
- **Tri** : colonnes sortables avec états asc/desc.
- **Sélection** : lignes `selected`.
- **Sticky** : headers fixes au scroll.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-sm`, `--ps-font-weight-medium`
- Couleurs:
  - Header: `--ps-color-neutral-100` (bg), `--ps-color-neutral-900` (text)
  - Body: `--ps-color-neutral-0` (bg), `--ps-color-neutral-700` (text)
  - Striped: `--ps-color-neutral-50` (alternate row)
  - Hover: `--ps-color-neutral-100` (hover bg)
  - Selected: `--ps-color-primary-50` (selected bg)
  - Border: `--ps-color-neutral-300`
- Espacements:
  - Default: `--ps-spacing-3|4` (padding)
  - Compact: `--ps-spacing-2` (padding)
- Bordures: `--ps-border-width-default`, `--ps-border-radius-sm`

---

## 🔧 Template Twig

```twig
{#
 * Template for Table molecule.
 * Variables: voir API YAML
 #}

{% set striped = striped ?? true %}
{% set hover = hover ?? true %}
{% set bordered = bordered|default(false) %}
{% set compact = compact|default(false) %}
{% set responsive = responsive ?? true %}
{% set stacked = stacked|default(false) %}

{% set table_classes = [
  'ps-table',
  striped ? 'ps-table--striped',
  hover ? 'ps-table--hover',
  bordered ? 'ps-table--bordered',
  compact ? 'ps-table--compact',
  stacked ? 'ps-table--stacked'
] %}

{% set wrapper_classes = [
  'ps-table-wrapper',
  responsive and not stacked ? 'ps-table-wrapper--responsive'
] %}

<div class="{{ wrapper_classes|join(' ') }}">
  <table {{ attributes.addClass(table_classes) }}>
    {% if caption %}
      <caption class="ps-table__caption">{{ caption }}</caption>
    {% endif %}
    <thead class="ps-table__head">
      <tr class="ps-table__row">
        {% for header in headers %}
          <th class="ps-table__header {{ header.sortable ? 'ps-table__header--sortable' }} {{ header.sticky ? 'ps-table__header--sticky' }} {{ header.numeric ? 'ps-table__header--numeric' }}" scope="col" {% if header.sortable %}data-sort="{{ header.key }}"{% endif %}>
            {% if header.sortable %}
              <button class="ps-table__sort-button" type="button">
                {{ header.label }}
                <span class="ps-table__sort-icon" data-icon="arrow-up-down" aria-hidden="true"></span>
              </button>
            {% else %}
              {{ header.label }}
            {% endif %}
          </th>
        {% endfor %}
      </tr>
    </thead>
    <tbody class="ps-table__body">
      {% for row in rows %}
        <tr class="ps-table__row {{ row.selected ? 'ps-table__row--selected' }} {{ row.disabled ? 'ps-table__row--disabled' }}">
          {% for cell in row.cells %}
            <td class="ps-table__cell {{ headers[loop.index0].numeric ? 'ps-table__cell--numeric' }}" data-label="{{ headers[loop.index0].label }}">
              {{ cell|raw }}
            </td>
          {% endfor %}
        </tr>
      {% endfor %}
    </tbody>
  </table>
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-table-wrapper {
  width: 100%;
  
  &--responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
}

.ps-table {
  width: 100%;
  border-collapse: collapse;
  font-family: var(--ps-font-family-primary);
  font-size: var(--ps-font-size-sm, 14px);

  &__caption {
    padding: var(--ps-spacing-3, 12px);
    text-align: left;
    font-weight: var(--ps-font-weight-semibold, 600);
    caption-side: top;
  }

  &__head {
    background: var(--ps-color-neutral-100, #F3F6F9);
  }

  &__header {
    padding: var(--ps-spacing-3, 12px) var(--ps-spacing-4, 16px);
    text-align: left;
    font-weight: var(--ps-font-weight-semibold, 600);
    color: var(--ps-color-neutral-900, #111);
    border-bottom: 2px solid var(--ps-color-neutral-300, #D2D7DB);

    &--numeric { text-align: right; }
    
    &--sortable {
      cursor: pointer;
      user-select: none;
    }
    
    &--sticky {
      position: sticky; top: 0; z-index: 10;
      background: var(--ps-color-neutral-100, #F3F6F9);
    }
  }

  &__sort-button {
    display: inline-flex; align-items: center; gap: var(--ps-spacing-1, 4px);
    background: none; border: none; padding: 0; cursor: pointer;
    font: inherit; color: inherit;
    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }

  &__sort-icon {
    width: 14px; height: 14px;
    opacity: 0.5;
    transition: opacity var(--ps-transition-duration-fast, 0.15s);
  }

  &__header--sorted-asc .ps-table__sort-icon,
  &__header--sorted-desc .ps-table__sort-icon {
    opacity: 1;
  }
  &__header--sorted-desc .ps-table__sort-icon { transform: rotate(180deg); }

  &__body {
    background: var(--ps-color-neutral-0, #FFF);
  }

  &__row {
    border-bottom: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
    
    &--selected {
      background: var(--ps-color-primary-50, #E8FAF5);
    }
    
    &--disabled {
      opacity: 0.5;
      pointer-events: none;
    }
  }

  &__cell {
    padding: var(--ps-spacing-3, 12px) var(--ps-spacing-4, 16px);
    color: var(--ps-color-neutral-700, #3B4754);
    vertical-align: middle;

    &--numeric { text-align: right; }
    &--actions { text-align: right; white-space: nowrap; }
  }

  // Striped variant
  &--striped {
    .ps-table__body .ps-table__row:nth-child(even) {
      background: var(--ps-color-neutral-50, #F9FAFB);
    }
  }

  // Hover variant
  &--hover {
    .ps-table__body .ps-table__row:hover {
      background: var(--ps-color-neutral-100, #F3F6F9);
    }
  }

  // Bordered variant
  &--bordered {
    border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
    .ps-table__header,
    .ps-table__cell {
      border-right: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
      &:last-child { border-right: none; }
    }
  }

  // Compact variant
  &--compact {
    .ps-table__header,
    .ps-table__cell {
      padding: var(--ps-spacing-2, 8px);
    }
  }

  // Stacked mobile variant
  &--stacked {
    @media (max-width: 768px) {
      .ps-table__head { display: none; }
      .ps-table__body,
      .ps-table__row,
      .ps-table__cell {
        display: block;
      }
      .ps-table__row {
        margin-bottom: var(--ps-spacing-4, 16px);
        padding: var(--ps-spacing-3, 12px);
        border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
        border-radius: var(--ps-border-radius-sm, 4px);
      }
      .ps-table__cell {
        padding: var(--ps-spacing-2, 8px) 0;
        text-align: left !important;
        &::before {
          content: attr(data-label) ": ";
          font-weight: var(--ps-font-weight-semibold, 600);
          display: inline-block;
          margin-right: var(--ps-spacing-2, 8px);
        }
      }
    }
  }
}
```

---

## ♿ Accessibilité

- `<table>` natif avec `<thead>`, `<tbody>`, `<th scope="col">`.
- `<caption>` pour description du tableau.
- `aria-sort` sur en-têtes triables (`ascending`, `descending`, `none`).
- Boutons tri avec labels clairs et focus visible.
- `data-label` pour variante stacked mobile (contexte visuel).

---

## 📱 Comportement responsive

- **Responsive** (défaut) : scroll horizontal sur petits écrans.
- **Stacked** : transformation en cartes empilées mobile (data-label comme labels).

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-table/ps-table.twig' with {
  caption: 'Liste des propriétés',
  headers: [
    { key: 'name', label: 'Nom', sortable: true },
    { key: 'status', label: 'Statut', sortable: false },
    { key: 'price', label: 'Prix', sortable: true, numeric: true },
    { key: 'actions', label: 'Actions', sortable: false }
  ],
  rows: [
    {
      id: '1',
      cells: [
        'Appartement T3',
        '<span class="ps-badge ps-badge--success">Actif</span>',
        '250 000 €',
        '<button class="ps-button ps-button--small" type="button">Éditer</button>'
      ],
      selected: false
    },
    {
      id: '2',
      cells: [
        'Maison 4 pièces',
        '<span class="ps-badge ps-badge--neutral">Inactif</span>',
        '450 000 €',
        '<button class="ps-button ps-button--small" type="button">Éditer</button>'
      ],
      selected: true
    }
  ],
  striped: true,
  hover: true,
  bordered: false,
  compact: false,
  responsive: true
} %}
```

---

## 🔌 JavaScript behavior (facultatif)

```js
// Minimal table sort behavior
function setupTable(table) {
  const headers = table.querySelectorAll('[data-sort]');
  
  headers.forEach(header => {
    const button = header.querySelector('.ps-table__sort-button');
    if (!button) return;

    button.addEventListener('click', () => {
      const key = header.getAttribute('data-sort');
      const tbody = table.querySelector('.ps-table__body');
      const rows = Array.from(tbody.querySelectorAll('.ps-table__row'));
      const colIndex = Array.from(header.parentElement.children).indexOf(header);
      
      // Determine sort direction
      const isAsc = header.classList.contains('ps-table__header--sorted-asc');
      const isDesc = header.classList.contains('ps-table__header--sorted-desc');
      const nextDir = isAsc ? 'desc' : 'asc';
      
      // Clear all sort states
      headers.forEach(h => {
        h.classList.remove('ps-table__header--sorted-asc', 'ps-table__header--sorted-desc');
        h.removeAttribute('aria-sort');
      });
      
      // Set new sort state
      header.classList.add(`ps-table__header--sorted-${nextDir}`);
      header.setAttribute('aria-sort', nextDir === 'asc' ? 'ascending' : 'descending');
      
      // Sort rows
      rows.sort((a, b) => {
        const aText = a.children[colIndex].textContent.trim();
        const bText = b.children[colIndex].textContent.trim();
        const aVal = isNaN(aText) ? aText : parseFloat(aText);
        const bVal = isNaN(bText) ? bText : parseFloat(bText);
        
        if (nextDir === 'asc') {
          return aVal > bVal ? 1 : -1;
        } else {
          return aVal < bVal ? 1 : -1;
        }
      });
      
      // Reappend sorted rows
      rows.forEach(row => tbody.appendChild(row));
    });
  });
}

document.querySelectorAll('.ps-table').forEach(setupTable);
```

---

## 📚 Ressources

- WAI-ARIA: Table, `scope`, `aria-sort`
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`, `/design/tokens/borders.yml`
