# Accordion (Organism)

**Niveau Atomic Design** : Organism / Collection  
**Catégorie** : Progressive disclosure  
**Statut** : ✅ Stable  
**Version** : 1.2.0  
**Dernière mise à jour** : 3 décembre 2025

---

## 📋 Description

Orchestrateur de multiples éléments Collapse avec coordination optionnelle single-open. Gère le comportement de groupe quand un seul item doit être ouvert à la fois. Utilise pattern de composition atomique (Accordion = Collection d'atoms Collapse).

**Implémentation** : `source/patterns/collections/accordion/`

**Architecture** : Thin orchestration layer qui compose `@elements/collapse` atoms avec JavaScript event-driven coordination.

---

## 🎯 Quand utiliser

**Utiliser Accordion quand :**
- Listes FAQ avec comportement automatic single-open
- Divulgation progressive de sections liées multiples
- Contenu groupé où un seul item visible à la fois

**NE PAS utiliser quand :**
- Item pliable unique (utiliser Collapse atom directement)
- Navigation (utiliser menu/tabs components)
- Tous items indépendants toggleables (utiliser plusieurs Collapse atoms)

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
<div class="ps-accordion" data-accordion>
  <!-- Item 1: Collapse element -->
  <div class="ps-accordion__item" data-accordion-item>
    <div class="ps-collapse is-expanded">
      <button class="ps-collapse__trigger" 
              type="button" 
              aria-expanded="true" 
              aria-controls="collapse-1"
              data-collapse-trigger>
        <span class="ps-collapse__title">How to buy business premises?</span>
        <span class="ps-collapse__icon" aria-hidden="true"></span>
      </button>
      <div class="ps-collapse__panel" 
           id="collapse-1" 
           role="region" 
           data-collapse-panel>
        <div class="ps-collapse__content">
          Content here...
        </div>
      </div>
    </div>
  </div>
  
  <!-- Item 2: Another Collapse element -->
  <div class="ps-accordion__item" data-accordion-item>
    <div class="ps-collapse">
      <!-- Collapse structure repeated -->
    </div>
  </div>
</div>
```

### Classes BEM

```
ps-accordion                              // Block (root container)
  ps-accordion__item                      // Wrapper for each Collapse atom
    (+ all .ps-collapse classes)         // Collapse atom structure

Modifiers:
  ps-accordion--flush                     // Reduced padding variant

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Accordion'
status: stable
group: collections
description: 'Orchestrator for multiple Collapse elements with single-open coordination.'

props:
  type: object
  required:
    - items
  properties:
    items:
      type: array
      title: Collapse items array
      description: 'Array of objects passed to Collapse: [{ id?, title, content?, expanded? }]'
      items:
        type: object
        required: ['title']
        properties:
          id:
            type: string
            description: 'Unique ID (auto-generated if omitted)'
          title:
            type: string
            description: 'Section heading text'
          content:
            type: string
            description: 'Raw HTML content for panel'
          expanded:
            type: boolean
            default: false
            description: 'Initial expanded state'
    single_open:
      type: boolean
      default: true
      title: Single-open coordination
      description: 'Only one item open at a time (closes others when opening)'
    variant:
      type: string
      enum: ['default','flush']
      default: 'default'
      title: Visual style variant
    attributes:
      type: Drupal\Core\Template\Attribute
```

---

## 🎨 Design Tokens

### Accordion-Level (Layer 2 CSS Variables)

Accordion définit des variables qui influencent les Collapse enfants :

```css
/* Variables CSS scoped à .ps-accordion */
--ps-accordion-trigger-padding-y: var(--size-6);      /* 24px */
--ps-accordion-trigger-padding-x: 0;
--ps-accordion-content-padding-top: var(--size-4);     /* 16px */
--ps-accordion-content-padding-bottom: var(--size-6);  /* 24px */
```

**Note** : Tous les autres tokens (colors, fonts, borders, animations) sont gérés par le composant Collapse atom.

---

## 🎯 JavaScript Coordination

**Fichier** : `source/patterns/collections/accordion/accordion.js`

**Fonctionnement** :
- Écoute events `collapse:show` des Collapse enfants (event bubbling)
- Si `single_open=true`, dispatch `collapse:external-toggle` aux autres items
- Tous items se ferment/ouvrent avec animations smooth (pattern Bootstrap)
- Aucune manipulation DOM directe (loose coupling event-driven)
- Drupal behaviors pattern avec `once()` pour idempotence

**Events** :
- **Écoute** : `collapse:show` (immediate, avant animation)
- **Dispatch** : `collapse:external-toggle` (demande fermeture aux siblings)

---

## 🧩 Composition Atomique

**Accordion = Organism/Collection** qui compose :

### Composants utilisés
- `@elements/collapse/collapse.twig` (required, multiple instances)

### Architecture
```
Accordion (Collection)
  ├── JavaScript coordination layer
  └── Collapse (Atom) × N
        ├── Trigger button
        ├── Panel with content
        └── Icon chevron
```

**Séparation des responsabilités** :
- **Collapse (Atom)** : Single-item expand/collapse behavior + visual styling
- **Accordion (Organism)** : Multi-item coordination + group orchestration

**Avantages** :
- Collapse utilisable seul (single disclosures)
- Accordion focus sur coordination uniquement
- Easier testing & maintenance (single responsibility)
- Event-driven = loose coupling

---

## ♿ Accessibilité

Toute l'accessibilité est gérée par le composant Collapse atom :
- `aria-expanded` (mis à jour dynamiquement)
- `aria-controls` (lie trigger au panel)
- `role="region"` (panel landmark)
- `hidden` attribute (masquage natif)
- Keyboard navigation (Enter/Space toggle)
- Focus-visible outline
- Screen readers announcements ("expanded/collapsed")

Accordion ajoute :
- Contexte sémantique via container `<div>`
- Coordination annoncée implicitement (fermeture automatique des autres items)

---

## 🧪 Exemples d'usage

### Basic FAQ (single-open par défaut)
```twig
{% include '@collections/accordion/accordion.twig' with {
  items: [
    { title: 'How to buy business premises?', content: '<p>Detailed explanation...</p>' },
    { title: 'What are financing options?', content: '<p>Options here...</p>' },
    { title: 'How long does the process take?', content: '<p>Timeline...</p>' }
  ]
} only %}
```

### Multiple open (no coordination)
```twig
{% include '@collections/accordion/accordion.twig' with {
  items: [
    { title: 'Feature A', content: '<p>Details A...</p>' },
    { title: 'Feature B', content: '<p>Details B...</p>' }
  ],
  single_open: false
} only %}
```

### Flush variant (reduced padding)
```twig
{% include '@collections/accordion/accordion.twig' with {
  items: faqs_array,
  variant: 'flush'
} only %}
```

### Pre-expanded item
```twig
{% include '@collections/accordion/accordion.twig' with {
  items: [
    { title: 'Section 1', content: '...', expanded: true },  {# Opened by default #}
    { title: 'Section 2', content: '...' },
    { title: 'Section 3', content: '...' }
  ]
} only %}
```

---

## 📚 Ressources

- **Implémentation** : `source/patterns/collections/accordion/`
- **Collapse atom** : `source/patterns/elements/collapse/` (composant de base)
- **Storybook** : Stories single-open vs multiple-open, variants
- **WCAG Pattern** : [Disclosure (Expand/Collapse)](https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/)
- **Bootstrap inspiration** : Event-driven coordination pattern

---

**Version** : 1.2.0  
**Dernière mise à jour** : 3 décembre 2025  
**Status** : ✅ Production-ready  
**Architecture** : Collection pattern avec composition Collapse atoms
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
