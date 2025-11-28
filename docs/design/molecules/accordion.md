# Accordion (Molecule)

**Niveau Atomic Design** : Molecule / Disclosure  
**Catégorie** : Content reveal  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Bloc d'accordéon pour afficher/masquer des sections de contenu. Utilise des boutons avec `aria-expanded` et des régions `role="region"` liées via `aria-controls`/`aria-labelledby`. Option `singleOpen` pour n'avoir qu'une seule section ouverte à la fois.

---

## 🎨 Aperçu visuel

```
[+] Question 1
    Réponse détaillée...
[–] Question 2
    Réponse détaillée...
```

---

## 🏗️ Structure BEM

```html
<div class="ps-accordion ps-accordion--bordered" data-accordion data-single-open="true">
  <div class="ps-accordion__item ps-accordion__item--open">
    <h3 class="ps-accordion__header">
      <button class="ps-accordion__trigger" type="button" aria-expanded="true" aria-controls="acc-1" id="acc-1-label" data-accordion-trigger>
        <svg class="ps-accordion__icon" aria-hidden="true"><use href="#icon-plus"></use></svg>
        <span class="ps-accordion__title">Question 1</span>
      </button>
    </h3>
    <div class="ps-accordion__panel" id="acc-1" role="region" aria-labelledby="acc-1-label" data-accordion-panel>
      Réponse détaillée...
    </div>
  </div>
  <div class="ps-accordion__item">
    <h3 class="ps-accordion__header">
      <button class="ps-accordion__trigger" type="button" aria-expanded="false" aria-controls="acc-2" id="acc-2-label" data-accordion-trigger>
        <svg class="ps-accordion__icon" aria-hidden="true"><use href="#icon-plus"></use></svg>
        <span class="ps-accordion__title">Question 2</span>
      </button>
    </h3>
    <div class="ps-accordion__panel" id="acc-2" role="region" aria-labelledby="acc-2-label" data-accordion-panel hidden>
      Réponse détaillée...
    </div>
  </div>
</div>
```

### Classes BEM

```
ps-accordion                              // Block
  ps-accordion__item                      // Élément
  ps-accordion__header                    // En-tête (heading)
  ps-accordion__trigger                   // Bouton de bascule
  ps-accordion__icon                      // Icône (plus/chevron)
  ps-accordion__title                     // Texte
  ps-accordion__panel                     // Contenu masqué/visible

Modificateurs :
  ps-accordion--bordered                  // Variante avec bordures
  ps-accordion__item--open                // Item ouvert
  ps-accordion--flush                     // Sans bordures/padding
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Accordion'
status: stable
group: molecules
description: 'Accordéon accessible avec boutons aria-expanded et panneaux region.'

props:
  type: object
  properties:
    items:
      type: array
      title: Sections
      items:
        type: object
        properties:
          id:
            type: string
          title:
            type: string
          content:
            type: string
          open:
            type: boolean
            default: false
          icon:
            type: string
        required: ['title','content']
    singleOpen:
      type: boolean
      title: Une seule section ouverte
      default: true
    bordered:
      type: boolean
      title: Bordures
      default: true
    flush:
      type: boolean
      title: Sans bordures/padding
      default: false
    headingLevel:
      type: string
      enum: ['h2','h3','h4','h5']
      default: 'h3'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - items
```

---

## 🎭 Variants

- `bordered: true|false` et `flush: true|false` (visuel).
- `singleOpen: true` (accordéon strict) ou `false` (multiexpansion).
- `headingLevel`: h2–h5 pour la sémantique.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-base`, `--ps-font-weight-medium`
- Spacing: `--ps-spacing-3|4` (padding), `--ps-spacing-2` (gaps)
- Bordures: `--ps-color-neutral-300`, `--ps-border-width-default`, `--ps-border-radius-sm`
- Icône: 16–20px

Proposition si manquant: `--ps-icon-size-16`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Accordion molecule.
 * Variables: voir API YAML
 #}

{% set heading = headingLevel|default('h3') %}
{% set root_classes = [ 'ps-accordion', bordered ? 'ps-accordion--bordered', flush ? 'ps-accordion--flush' ] %}

<div {{ attributes.addClass(root_classes) }} data-accordion data-single-open="{{ singleOpen ? 'true' : 'false' }}">
  {% for item in items %}
    {% set panel_id = item.id ?? ('acc-' ~ loop.index) %}
    {% set label_id = panel_id ~ '-label' %}
    {% set is_open = item.open ?? false %}
    <div class="ps-accordion__item {{ is_open ? 'ps-accordion__item--open' }}">
      <{{ heading }} class="ps-accordion__header">
        <button class="ps-accordion__trigger" type="button" aria-expanded="{{ is_open ? 'true' : 'false' }}" aria-controls="{{ panel_id }}" id="{{ label_id }}" data-accordion-trigger>
          {% if item.icon %}<svg class="ps-accordion__icon" aria-hidden="true"><use href="#icon-{{ item.icon }}"></use></svg>{% endif %}
          <span class="ps-accordion__title">{{ item.title }}</span>
        </button>
      </{{ heading }}>
      <div class="ps-accordion__panel" id="{{ panel_id }}" role="region" aria-labelledby="{{ label_id }}" data-accordion-panel {% if not is_open %}hidden{% endif %}>
        {{ item.content|raw }}
      </div>
    </div>
  {% endfor %}
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-accordion {
  font-family: var(--ps-font-family-primary);

  &__header { margin: 0; }
  &__trigger {
    width: 100%;
    display: flex; align-items: center; justify-content: space-between; gap: var(--ps-spacing-2, 8px);
    padding: var(--ps-spacing-3, 12px) var(--ps-spacing-4, 16px);
    border: none; background: none; text-align: left; cursor: pointer;
    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }
  &__icon { width: 16px; height: 16px; }
  &__panel { padding: 0 var(--ps-spacing-4, 16px) var(--ps-spacing-4, 16px); }

  &--bordered {
    .ps-accordion__item { border-bottom: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB); }
    .ps-accordion__item:first-child { border-top: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB); }
  }
  &--flush {
    .ps-accordion__trigger { padding: var(--ps-spacing-2, 8px) 0; }
    .ps-accordion__panel { padding: 0 0 var(--ps-spacing-2, 8px); }
  }
}
```

---

## ♿ Accessibilité

- `button[aria-expanded]` + `role="region"` pour chaque panneau avec `aria-labelledby`.
- `headingLevel` permet de respecter la hiérarchie des titres.
- Focus visible et commandes clavier (Enter/Space pour basculer).

---

## 📱 Comportement responsive

- Largeur fluide; padding adaptable via tokens.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-accordion/ps-accordion.twig' with {
  singleOpen: true,
  bordered: true,
  items: [
    { id: 'faq-1', title: 'Question 1', content: '<p>Réponse...</p>', open: true, icon: 'plus' },
    { id: 'faq-2', title: 'Question 2', content: '<p>Réponse...</p>' },
  ]
} %}
```

---

## 🔌 JavaScript behavior (facultatif)

```js
// Minimal accordion script (singleOpen supported)
function setupAccordion(root) {
  const single = root.getAttribute('data-single-open') === 'true';
  const triggers = root.querySelectorAll('[data-accordion-trigger]');
  const panels = root.querySelectorAll('[data-accordion-panel]');

  triggers.forEach((btn) => {
    btn.addEventListener('click', () => {
      const panel = root.querySelector('#' + btn.getAttribute('aria-controls'));
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      if (single && !expanded) {
        // Close others
        triggers.forEach((b) => b.setAttribute('aria-expanded', 'false'));
        panels.forEach((p) => p.hidden = true);
        root.querySelectorAll('.ps-accordion__item').forEach((it) => it.classList.remove('ps-accordion__item--open'));
      }
      btn.setAttribute('aria-expanded', String(!expanded));
      panel.hidden = expanded;
      btn.closest('.ps-accordion__item').classList.toggle('ps-accordion__item--open', !expanded);
    });
  });
}

document.querySelectorAll('[data-accordion]').forEach(setupAccordion);
```

---

## 📚 Ressources

- WAI-ARIA Authoring Practices: Accordion
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`
