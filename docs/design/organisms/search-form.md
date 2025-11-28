# Search Form (Organism)

**Niveau Atomic Design** : Organism / Form  
**Catégorie** : Search & Filters  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Formulaire de recherche immobilière avec champs principaux (lieu, type, prix, surface), actions, et liens avancés. Conçu pour l'accessibilité, incluant labels visibles, messages d'erreur, et navigation clavier. Variantes compact/expanded, inline/stacked, avec slot pour filtres avancés.

---

## 🏗️ Structure BEM

```html
<form class="ps-search-form ps-search-form--inline" action="/search" method="get" novalidate>
  <div class="ps-search-form__fields">
    <div class="ps-form-field">
      <label class="ps-label" for="sf-location">Localisation</label>
      <input class="ps-field" id="sf-location" name="location" type="text" placeholder="Ville, code postal" />
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="sf-type">Type</label>
      <select class="ps-field" id="sf-type" name="type">
        <option value="house">Maison</option>
        <option value="apartment">Appartement</option>
      </select>
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="sf-price">Budget max</label>
      <input class="ps-field" id="sf-price" name="price_max" type="number" min="0" step="1000" />
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="sf-area">Surface min (m²)</label>
      <input class="ps-field" id="sf-area" name="area_min" type="number" min="0" step="1" />
    </div>
  </div>
  <div class="ps-search-form__actions">
    <button class="ps-button ps-button--primary" type="submit">Rechercher</button>
    <button class="ps-button ps-button--secondary" type="button">Filtres avancés</button>
  </div>
</form>
```

### Classes BEM

```
ps-search-form                             // Block (form)
  ps-search-form__fields                   // Fields group
  ps-search-form__actions                  // Buttons group

Modificateurs :
  ps-search-form--inline                   // Inline layout
  ps-search-form--stacked                  // Stacked layout
  ps-search-form--compact                  // Reduced spacing
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Search Form'
status: stable
group: organisms
description: 'Property search form with primary fields and actions.'

props:
  type: object
  properties:
    action: { type: string }
    method: { type: string, enum: ['get','post'], default: 'get' }
    layout: { type: string, enum: ['inline','stacked'], default: 'inline' }
    compact: { type: boolean, default: false }
    fields:
      type: array
      items:
        type: object
        properties:
          id: { type: string }
          name: { type: string }
          label: { type: string }
          type: { type: string, enum: ['text','select','number'] }
          placeholder: { type: string }
          options:
            type: array
            items:
              type: object
              properties:
                label: { type: string }
                value: { type: string }
        required: ['id','name','label','type']
    actions:
      type: array
      items:
        type: object
        properties:
          label: { type: string }
          type: { type: string, enum: ['submit','button'], default: 'submit' }
          variant: { type: string, enum: ['primary','secondary'] }
        required: ['label']
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set layout = layout|default('inline') %}
{% set compact = compact|default(false) %}
{% set classes = ['ps-search-form', 'ps-search-form--' ~ layout, compact ? 'ps-search-form--compact'] %}

<form {{ attributes.addClass(classes) }} action="{{ action }}" method="{{ method|default('get') }}" novalidate>
  <div class="ps-search-form__fields">
    {% for f in fields %}
      <div class="ps-form-field">
        <label class="ps-label" for="sf-{{ f.id }}">{{ f.label }}</label>
        {% if f.type == 'text' %}
          <input class="ps-field" id="sf-{{ f.id }}" name="{{ f.name }}" type="text" placeholder="{{ f.placeholder }}" />
        {% elseif f.type == 'number' %}
          <input class="ps-field" id="sf-{{ f.id }}" name="{{ f.name }}" type="number" placeholder="{{ f.placeholder }}" />
        {% elseif f.type == 'select' %}
          <select class="ps-field" id="sf-{{ f.id }}" name="{{ f.name }}">
            {% for opt in f.options %}
              <option value="{{ opt.value }}">{{ opt.label }}</option>
            {% endfor %}
          </select>
        {% endif %}
      </div>
    {% endfor %}
  </div>
  {% if actions %}
  <div class="ps-search-form__actions">
    {% for a in actions %}
      <button class="ps-button ps-button--{{ a.variant|default('primary') }}" type="{{ a.type|default('submit') }}">{{ a.label }}</button>
    {% endfor %}
  </div>
  {% endif %}
</form>
```

---

## 🎨 Styles SCSS

```scss
.ps-search-form {
  &__fields { display: grid; gap: var(--ps-spacing-3, 12px); grid-template-columns: repeat(4, 1fr); }
  &__actions { display: flex; gap: var(--ps-spacing-3, 12px); margin-top: var(--ps-spacing-3, 12px); }

  &--inline {
    & .ps-form-field { display: flex; flex-direction: column; }
  }
  &--stacked {
    &__fields { grid-template-columns: 1fr; }
  }
  &--compact {
    &__fields { gap: var(--ps-spacing-2, 8px); }
    &__actions { gap: var(--ps-spacing-2, 8px); }
  }

  @media (max-width: 992px) {
    &__fields { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 600px) {
    &__fields { grid-template-columns: 1fr; }
  }
}
```

---

## ♿ Accessibilité

- Labels associés par `for`/`id`.
- Champs groupés logiquement; erreurs à prévoir dans intégration.
- Touches Tab/Shift+Tab pour navigation.

---

## 🔌 JavaScript validation (optionnel)

```js
// Minimal client-side validation: required fields, numeric ranges, aria-live errors
(function(){
  const form = document.querySelector('.ps-search-form');
  if(!form) return;
  const fields = Array.from(form.querySelectorAll('.ps-form-field'));

  function setError(fieldEl, msg){
    let err = fieldEl.querySelector('.ps-field__error');
    if(!err){ err = document.createElement('div'); err.className = 'ps-field__error'; err.setAttribute('role','alert'); err.setAttribute('aria-live','polite'); fieldEl.appendChild(err); }
    err.textContent = msg || '';
  }

  form.addEventListener('submit', (e)=>{
    let hasError = false;
    fields.forEach((wrap)=>{
      const input = wrap.querySelector('.ps-field');
      if(!input) return;
      const val = input.value?.trim();
      let msg = '';
      // Examples: require location; validate positive numbers
      if(input.id.includes('location') && !val){ msg = 'Veuillez renseigner la localisation.'; }
      if(input.type === 'number'){
        const num = Number(val);
        if(val && (Number.isNaN(num) || num < 0)) msg = 'Veuillez saisir un nombre positif.';
      }
      setError(wrap, msg);
      hasError = hasError || !!msg;
    });
    if(hasError){ e.preventDefault(); }
  });
})();
```

```scss
// Optional style hook for error messages inside search-form
.ps-field__error { margin-top: 4px; color: var(--ps-color-error-700, #C62828); font-size: var(--ps-font-size-xs, 12px); }
```

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-search-form/ps-search-form.twig' with {
  action: '/search',
  method: 'get',
  layout: 'inline',
  fields: [
    { id: 'location', name: 'location', label: 'Localisation', type: 'text', placeholder: 'Ville, code postal' },
    { id: 'type', name: 'type', label: 'Type', type: 'select', options: [ {label:'Maison', value:'house'}, {label:'Appartement', value:'apartment'} ] },
    { id: 'price_max', name: 'price_max', label: 'Budget max', type: 'number' },
    { id: 'area_min', name: 'area_min', label: 'Surface min', type: 'number' }
  ],
  actions: [ { label: 'Rechercher', type: 'submit', variant: 'primary' }, { label: 'Filtres avancés', type: 'button', variant: 'secondary' } ]
} %}
```

---

## 📚 Ressources

- Composition: molecules (form-field, dropdown), atoms (label, field, button)
- A11y: WCAG forms
