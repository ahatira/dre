# Calculator (Organism)

**Niveau Atomic Design** : Organism / Tool  
**Catégorie** : Finance  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Calculateur hypothécaire / budget: champs pour prix, apport, durée, taux; affiche mensualité estimée et coût total. Validation de base et a11y. Variantes compact/panel.

---

## 🏗️ Structure BEM

```html
<section class="ps-calculator ps-calculator--panel" aria-labelledby="calc-title">
  <h2 id="calc-title" class="ps-calculator__title">Calculateur de mensualité</h2>
  <form class="ps-calculator__form" novalidate>
    <div class="ps-form-field">
      <label class="ps-label" for="calc-price">Prix (€)</label>
      <input class="ps-field" id="calc-price" type="number" min="0" step="1000" />
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="calc-down">Apport (€)</label>
      <input class="ps-field" id="calc-down" type="number" min="0" step="1000" />
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="calc-years">Durée (années)</label>
      <input class="ps-field" id="calc-years" type="number" min="1" step="1" />
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="calc-rate">Taux (%)</label>
      <input class="ps-field" id="calc-rate" type="number" min="0" step="0.01" />
    </div>
    <div class="ps-calculator__actions">
      <button class="ps-button ps-button--primary" type="button" id="calc-run">Calculer</button>
    </div>
  </form>
  <div class="ps-calculator__results" aria-live="polite" aria-atomic="true">
    <div class="ps-calculator__result-line"><span>Mensualité :</span> <strong id="calc-monthly">—</strong></div>
    <div class="ps-calculator__result-line"><span>Coût total :</span> <strong id="calc-total">—</strong></div>
  </div>
</section>
```

### Classes BEM

```
ps-calculator                               // Block
  ps-calculator__title                      // Heading
  ps-calculator__form                       // Form wrapper
  ps-calculator__actions                    // Buttons
  ps-calculator__results                    // Results live region
  ps-calculator__result-line                // Line item

Modificateurs :
  ps-calculator--compact                    // Small spacing
  ps-calculator--panel                      // Card-like panel
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Calculator'
status: stable
group: organisms
description: 'Mortgage/payment calculator with validation and results.'

props:
  type: object
  properties:
    title: { type: string, default: 'Calculateur de mensualité' }
    compact: { type: boolean, default: false }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set compact = compact|default(false) %}
{% set classes = ['ps-calculator', compact ? 'ps-calculator--compact', 'ps-calculator--panel'] %}

<section {{ attributes.addClass(classes) }} aria-labelledby="calc-title">
  <h2 id="calc-title" class="ps-calculator__title">{{ title|default('Calculateur de mensualité') }}</h2>
  <form class="ps-calculator__form" novalidate>
    {# Field markup can be rendered via molecules/form-field in integration #}
    <div class="ps-form-field">
      <label class="ps-label" for="calc-price">Prix (€)</label>
      <input class="ps-field" id="calc-price" type="number" min="0" step="1000" />
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="calc-down">Apport (€)</label>
      <input class="ps-field" id="calc-down" type="number" min="0" step="1000" />
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="calc-years">Durée (années)</label>
      <input class="ps-field" id="calc-years" type="number" min="1" step="1" />
    </div>
    <div class="ps-form-field">
      <label class="ps-label" for="calc-rate">Taux (%)</label>
      <input class="ps-field" id="calc-rate" type="number" min="0" step="0.01" />
    </div>
    <div class="ps-calculator__actions">
      <button class="ps-button ps-button--primary" type="button" id="calc-run">Calculer</button>
    </div>
  </form>
  <div class="ps-calculator__results" aria-live="polite" aria-atomic="true">
    <div class="ps-calculator__result-line"><span>Mensualité :</span> <strong id="calc-monthly">—</strong></div>
    <div class="ps-calculator__result-line"><span>Coût total :</span> <strong id="calc-total">—</strong></div>
  </div>
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-calculator {
  &__title { font-size: var(--font-size-5); margin-bottom: var(--size-3); }
  &__form { display: grid; gap: var(--size-3); grid-template-columns: repeat(2, 1fr); }
  &__actions { grid-column: 1 / -1; display: flex; justify-content: flex-start; }
  &__results { margin-top: var(--size-4); }
  &__result-line { display: flex; justify-content: space-between; padding: var(--size-2) 0; }

  &--compact { &__form { gap: var(--size-2); } }

  &--panel { padding: var(--size-4); border: var(--border-size-1) solid var(--border-default); border-radius: var(--radius-4); box-shadow: var(--shadow-2); }

  @media (max-width: 768px) { &__form { grid-template-columns: 1fr; } }
}
```

---

## 🔌 JavaScript behavior (optionnel)

```js
(function(){
  const price = document.getElementById('calc-price');
  const down = document.getElementById('calc-down');
  const years = document.getElementById('calc-years');
  const rate = document.getElementById('calc-rate');
  const run = document.getElementById('calc-run');
  const outMonthly = document.getElementById('calc-monthly');
  const outTotal = document.getElementById('calc-total');
  if(!run) return;

  function formatEUR(n){ return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(n); }
  function calc(){
    const P = Math.max(0, Number(price.value||0) - Number(down.value||0));
    const i = (Number(rate.value||0)/100)/12; // monthly interest
    const n = Number(years.value||0)*12;
    if (P<=0 || i<=0 || n<=0) { outMonthly.textContent = '—'; outTotal.textContent = '—'; return; }
    const m = P * (i * Math.pow(1+i, n)) / (Math.pow(1+i, n) - 1); // annuity formula
    outMonthly.textContent = formatEUR(m);
    outTotal.textContent = formatEUR(m*n);
  }

  run.addEventListener('click', calc);
})();
```

---

## ♿ Accessibilité

- Labels visibles pour tous les champs, champs numeriques avec `min`/`step`.
- Live region pour mise à jour des résultats.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-calculator/ps-calculator.twig' with {
  title: 'Calculateur de mensualité'
} %}
```

---

## 📚 Ressources

- Form patterns, financial calculation formula
