# Accordion (Molecule)

**Niveau Atomic Design** : Molecule / Disclosure  
**Catégorie** : Content reveal  
**Statut** : ✅ Pixel Perfect  
**Version** : 1.1.0

---

## 📋 Description

Accordéon simplifié et pixel‑perfect d’après la maquette : séparateurs par défaut entre items, variante `flush` pour retirer le padding horizontal. Boutons avec `aria-expanded` contrôlant des panneaux `role="region"`.

---

## 🎨 Aperçu visuel

```
▾ How to buy business premises ?
   Le Lorem Ipsum est simplement du faux texte...
▸ How to buy business premises
▸ How to buy business premises
```

---

## 🏗️ Structure BEM

```html
<div class="ps-accordion" data-accordion data-single-open="true">
  <div class="ps-accordion__item ps-accordion__item--open">
    <h3 class="ps-accordion__header">
      <button class="ps-accordion__trigger" type="button" aria-expanded="true" aria-controls="acc-1" id="acc-1-label" data-accordion-trigger>
        <span class="ps-accordion__title">How to buy business premises ?</span>
        <span class="ps-accordion__icon" aria-hidden="true"></span>
      </button>
    </h3>
    <div class="ps-accordion__panel" id="acc-1" role="region" aria-labelledby="acc-1-label" data-accordion-panel>
      Le Lorem Ipsum est simplement du faux texte...
    </div>
  </div>
  <div class="ps-accordion__item">
    <h3 class="ps-accordion__header">
      <button class="ps-accordion__trigger" type="button" aria-expanded="false" aria-controls="acc-2" id="acc-2-label" data-accordion-trigger>
        <span class="ps-accordion__title">How to buy business premises</span>
        <span class="ps-accordion__icon" aria-hidden="true"></span>
      </button>
    </h3>
    <div class="ps-accordion__panel" id="acc-2" role="region" aria-labelledby="acc-2-label" data-accordion-panel hidden></div>
  </div>
</div>
```

### Classes BEM

```
ps-accordion                              // Block
  ps-accordion__item                      // Élément
  ps-accordion__header                    // Heading wrapper
  ps-accordion__trigger                   // Bouton accessible
  ps-accordion__icon                      // Icône (CSS pseudo)
  ps-accordion__title                     // Texte
  ps-accordion__panel                     // Contenu

Modificateurs :
  ps-accordion__item--open                // Item ouvert (chevron bas)
  ps-accordion--flush                     // Sans padding horizontal
```

---

## 📐 Props (Component API)

```yaml
items[]: { id?, title, content, open? }
singleOpen: boolean (default true)
flush: boolean (default false)
headingLevel: 'h2'|'h3'|'h4'|'h5' (default 'h3')
attributes: Drupal attributes
```

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--font-size-1`
- Spacing: `--ps-spacing-2` (gaps), `--ps-spacing-3` `--ps-spacing-4` (padding)
- Bordures: `--gray-300`, `--ps-border-width-default`, `--ps-border-width-focus`, `--ps-border-radius-sm`
- Icône: `--ps-icon-size-16` (chevron right/down via font)

---

## 🔧 Template Twig

Voir `source/patterns/components/accordion/accordion.twig`.

---

## 🎨 Styles

Voir `source/patterns/components/accordion/accordion.css`.

---

## ♿ Accessibilité

- `button[aria-expanded]` + `role="region"` avec `aria-labelledby`.
- Clavier: Enter/Espace basculent l’état.
- `focus-visible` tokenisé.

---

## 🧪 Exemples d’usage

```twig
{% include '@ps_theme/accordion/accordion.twig' with {
  singleOpen: true,
  items: [
    { id: 'faq-1', title: 'How to buy business premises ?', content: '<p>Le Lorem Ipsum ...</p>', open: true },
    { id: 'faq-2', title: 'How to buy business premises', content: '<p>Le Lorem Ipsum ...</p>' },
    { id: 'faq-3', title: 'How to buy business premises', content: '<p>Le Lorem Ipsum ...</p>' },
  ]
} %}
```

---

## 🔌 JavaScript (comportement minimal)

Voir `source/patterns/components/accordion/accordion.js`.

---

## 📚 Ressources

- WAI-ARIA Authoring Practices: Accordion
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`
