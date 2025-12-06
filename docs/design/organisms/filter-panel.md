# Filter Panel (Organism)

**Niveau Atomic Design** : Organism / Sidebar  
**Catégorie** : Filters  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Panneau de filtres latéral ou drawer. Sections repliables (accordions), cases à cocher, plages numériques (prix, surface), et actions appliquer/réinitialiser. A11y via contrôles de sections et associations de labels.

---

## 🏗️ Structure BEM

```html
<aside class="ps-filter-panel ps-filter-panel--sidebar" aria-label="Filtres">
  <div class="ps-filter-panel__section">
    <button class="ps-filter-panel__toggle" aria-expanded="true" aria-controls="fp-section-1">Type de bien</button>
    <div id="fp-section-1" class="ps-filter-panel__content">
      <label class="ps-checkbox"><input type="checkbox" /> Maison</label>
      <label class="ps-checkbox"><input type="checkbox" /> Appartement</label>
    </div>
  </div>
  <div class="ps-filter-panel__section">
    <button class="ps-filter-panel__toggle" aria-expanded="false" aria-controls="fp-section-2">Budget</button>
    <div id="fp-section-2" class="ps-filter-panel__content">
      <label class="ps-label" for="fp-budget">Max (€)</label>
      <input id="fp-budget" class="ps-field" type="number" min="0" step="1000" />
    </div>
  </div>
  <div class="ps-filter-panel__actions">
    <button class="ps-button ps-button--primary" type="button">Appliquer</button>
    <button class="ps-button ps-button--secondary" type="button">Réinitialiser</button>
  </div>
</aside>
```

### Classes BEM

```
ps-filter-panel                            // Block
  ps-filter-panel__section                 // Section group
  ps-filter-panel__toggle                  // Section toggle button
  ps-filter-panel__content                 // Collapsible content
  ps-filter-panel__actions                 // Footer actions

Modificateurs :
  ps-filter-panel--sidebar                 // Sidebar static
  ps-filter-panel--drawer                  // Drawer overlay
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Filter Panel'
status: stable
group: organisms
description: 'Filter panel with collapsible sections and actions.'

props:
  type: object
  properties:
    variant: { type: string, enum: ['sidebar','drawer'], default: 'sidebar' }
    sections:
      type: array
      items:
        type: object
        properties:
          id: { type: string }
          title: { type: string }
          expanded: { type: boolean, default: false }
          content: { type: string, description: 'Rendered HTML for fields' }
        required: ['id','title','content']
    actions:
      type: array
      items:
        type: object
        properties:
          label: { type: string }
          variant: { type: string, enum: ['primary','secondary'] }
          type: { type: string, enum: ['button','submit'], default: 'button' }
        required: ['label']
    attributes:
      type: Drupal\\Core\\Template\\Attribute
  required:
    - sections
```

---

## 🔧 Template Twig

```twig
{% set variant = variant|default('sidebar') %}
{% set classes = ['ps-filter-panel', 'ps-filter-panel--' ~ variant] %}

<aside {{ attributes.addClass(classes) }} aria-label="Filtres">
  {% for s in sections %}
    <div class="ps-filter-panel__section">
      <button class="ps-filter-panel__toggle" aria-expanded="{{ s.expanded ? 'true' : 'false' }}" aria-controls="fp-section-{{ s.id }}">
        {{ s.title }}
      </button>
      <div id="fp-section-{{ s.id }}" class="ps-filter-panel__content" {% if not s.expanded %}hidden{% endif %}>
        {{ s.content|raw }}
      </div>
    </div>
  {% endfor %}

  {% if actions %}
    <div class="ps-filter-panel__actions">
      {% for a in actions %}
        <button class="ps-button ps-button--{{ a.variant|default('primary') }}" type="{{ a.type|default('button') }}">{{ a.label }}</button>
      {% endfor %}
    </div>
  {% endif %}
</aside>
```

---

## 🎨 Styles SCSS

```scss
.ps-filter-panel {
  &__section { border-bottom: 1px solid var(--border-default); padding: var(--size-3) 0; }
  &__toggle { display: flex; width: 100%; background: none; border: none; text-align: left; font-weight: var(--font-weight-600); cursor: pointer; }
  &__content { padding-top: var(--size-2); }
  &__actions { display: flex; gap: var(--size-3); margin-top: var(--size-4); }

  &--drawer { position: fixed; inset: 0 0 0 auto; width: var(--size-80); background: var(--white); box-shadow: var(--shadow-4); }

  @media (max-width: 768px) {
    &--sidebar { position: static; }
  }
}
```

---

## ♿ Accessibilité

- Boutons toggle avec `aria-expanded` + `aria-controls`.
- `hidden` pour contenu replié (visuel + a11y).
- Sections ordonnées, labels associés pour champs inclus.

---

## 🔌 JavaScript behavior (optionnel)

```js
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('.ps-filter-panel__toggle');
  if(!btn) return;
  const id = btn.getAttribute('aria-controls');
  const content = document.getElementById(id);
  const expanded = btn.getAttribute('aria-expanded') === 'true';
  btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
  content.toggleAttribute('hidden');
});
```

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-filter-panel/ps-filter-panel.twig' with {
  variant: 'sidebar',
  sections: [
    { id: 'type', title: 'Type de bien', expanded: true, content: '<label class="ps-checkbox"><input type="checkbox" /> Maison</label>' },
    { id: 'budget', title: 'Budget', content: '<label class="ps-label" for="fp-budget">Max (€)</label><input id="fp-budget" class="ps-field" type="number" />' }
  ],
  actions: [ { label: 'Appliquer', variant: 'primary' }, { label: 'Réinitialiser', variant: 'secondary' } ]
} %}
```

---

## 📚 Ressources

- Composition: molecules/accordion, atoms/checkbox, molecules/dropdown
- A11y: Disclosure patterns
