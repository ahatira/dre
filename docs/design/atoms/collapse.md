# Collapse (Atom)

**Niveau Atomic Design** : Atom / Disclosure  
**Catégorie** : Interactive  
**Statut** : ✅ Stable  
**Version** : 1.0.0

---

## 📋 Description

Élément d'affichage/masquage de contenu avec bouton déclencheur et panneau extensible. Fournit un comportement d'affichage progressif avec support ARIA complet et animations fluides.

**Implémentation** : `source/patterns/elements/collapse/`

---

## 🎨 Aperçu visuel

```
▸ Section Title                  (collapsed)
▾ Section Title                  (expanded)
  Panel content here...
  More content...
```

---

## 🏗️ Structure BEM

```html
<div class="ps-collapse">
  <button class="ps-collapse__trigger" 
          type="button" 
          aria-expanded="false" 
          aria-controls="collapse-1">
    <span class="ps-collapse__title">Section Title</span>
    <span class="ps-collapse__icon" aria-hidden="true"></span>
  </button>
  <div class="ps-collapse__panel" 
       id="collapse-1" 
       role="region" 
       aria-labelledby="trigger-1" 
       hidden>
    <div class="ps-collapse__content">
      Panel content here...
    </div>
  </div>
</div>
```

### Classes BEM

```
ps-collapse                           // Block (root container)
  ps-collapse__trigger                // Bouton déclencheur
  ps-collapse__title                  // Texte du titre
  ps-collapse__icon                   // Icône chevron (CSS pseudo-element)
  ps-collapse__panel                  // Panneau extensible (role="region")
  ps-collapse__content                // Contenu du panneau

États (Bootstrap-inspired):
  .is-collapsing                      // État de transition (ouverture/fermeture)
  .is-expanded                        // État complètement ouvert

Modifiers (variantes):
  ps-collapse--primary                // Variante couleur primary
  ps-collapse--secondary              // Variante couleur secondary
  ps-collapse--success                // Variante couleur success
  ps-collapse--warning                // Variante couleur warning
  ps-collapse--danger                 // Variante couleur danger
  ps-collapse--info                   // Variante couleur info
  ps-collapse--dark                   // Variante sombre
  ps-collapse--light                  // Variante claire
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Collapse'
status: stable
group: elements
description: 'Élément d''affichage progressif avec trigger et panel.'

props:
  type: object
  required:
    - id
    - title
  properties:
    id:
      type: string
      title: Identifiant unique
      description: 'Pour linkage ARIA trigger/panel'
    title:
      type: string
      title: Texte du titre
    content:
      type: string
      title: Contenu HTML du panneau
      default: null
    expanded:
      type: boolean
      title: État initial ouvert
      default: false
    variant:
      type: string
      title: Variante visuelle
      enum: ['primary','secondary','success','warning','danger','info','dark','light']
      default: null
    trigger_tag:
      type: string
      title: Tag HTML du trigger
      description: 'Ex: "h3" pour sémantique heading'
      default: 'button'
    classes:
      type: array
      title: Classes CSS additionnelles
      default: []
    attributes:
      type: Drupal\Core\Template\Attribute
```

---

## 🎭 Variants

### Variantes de couleur
- **Aucune** (défaut) : Style neutre avec bordure grise
- **primary** : Accent couleur primary (vert BNP)
- **secondary** : Accent couleur secondary (violet)
- **success** : Accent couleur success (vert)
- **warning** : Accent couleur warning (orange)
- **danger** : Accent couleur danger (rouge)
- **info** : Accent couleur info (bleu)
- **dark** : Variante sombre (fond gris foncé)
- **light** : Variante claire (fond gris clair)

### États
- **Collapsed** (défaut) : Panneau masqué, `aria-expanded="false"`, chevron vers la droite
- **Expanded** : Panneau visible, `aria-expanded="true"`, chevron vers le bas
- **Collapsing** : État de transition avec animation fluide (height transition)

---

## 🎨 Design Tokens

### Système à 3 couches (Bootstrap-inspired)

#### Layer 1: Primitives globales
Tokens de base dans `source/props/*.css`

#### Layer 2: Variables component-scoped
Variables CSS au niveau du composant (défauts personnalisables)

```css
/* Container */
--ps-collapse-bg: transparent
--ps-collapse-border-width: var(--border-size-15)
--ps-collapse-border-color: var(--gray-300)

/* Trigger */
--ps-collapse-trigger-padding-y: var(--size-6)    /* 24px */
--ps-collapse-trigger-padding-x: 0
--ps-collapse-trigger-bg: transparent
--ps-collapse-trigger-bg-hover: var(--gray-100)

/* Title */
--ps-collapse-title-font-family: var(--font-sans)
--ps-collapse-title-font-weight: var(--font-weight-400)
--ps-collapse-title-font-size: var(--font-size-3)    /* 20px */
--ps-collapse-title-line-height: var(--leading-6)
--ps-collapse-title-color: var(--gray-900)

/* Icon */
--ps-collapse-icon-size: var(--size-8)            /* 32px */
--ps-collapse-icon-spacing: var(--size-4)         /* 16px */
--ps-collapse-icon-color: var(--gray-600)

/* Panel */
--ps-collapse-panel-padding-top: var(--size-4)     /* 16px */
--ps-collapse-panel-padding-bottom: var(--size-6)  /* 24px */
--ps-collapse-panel-padding-x: 0

/* Animation */
--ps-collapse-transition-duration: var(--duration-normal)  /* 300ms */
--ps-collapse-transition-easing: var(--ease-3)
```

#### Layer 3: Contexte runtime
Surcharge via classes parentes ou inline styles

```css
/* Exemple: variant primary */
.ps-collapse--primary {
  --ps-collapse-title-color: var(--primary);
  --ps-collapse-icon-color: var(--primary);
  --ps-collapse-border-color: var(--primary);
}
```

### Tokens utilisés

**Typographie**
- `--font-sans` : Famille de police
- `--font-weight-400` : Regular
- `--font-weight-600` : Semibold
- `--font-size-3` : 20px (titre)
- `--leading-6` : 24px line-height

**Spacing**
- `--size-4` : 16px (gaps, padding)
- `--size-6` : 24px (padding vertical)
- `--size-8` : 32px (icône)

**Couleurs**
- `--gray-100` : Hover background
- `--gray-300` : Bordure défaut
- `--gray-600` : Icône
- `--gray-900` : Texte
- `--primary`, `--secondary`, `--success`, `--warning`, `--danger`, `--info` : Variantes

**Bordures**
- `--border-size-15` : 1.5px (bordure séparateur)
- `--border-size-2` : 2px (focus outline)

**Animations**
- `--duration-normal` : 300ms
- `--ease-3` : Easing cubic-bezier

---

## 🔧 Template Twig

Voir implémentation complète : `source/patterns/elements/collapse/collapse.twig`

```twig
{% set collapse_id = id|default('collapse-' ~ random()) %}
{% set is_expanded = expanded|default(false) %}

<div class="ps-collapse {{ variant ? 'ps-collapse--' ~ variant }} {{ is_expanded ? 'is-expanded' }}" {{ attributes }}>
  <{{ trigger_tag|default('button') }} class="ps-collapse__trigger" 
     type="button"
     aria-expanded="{{ is_expanded ? 'true' : 'false' }}"
     aria-controls="{{ collapse_id }}"
     data-collapse-trigger>
    <span class="ps-collapse__title">{{ title }}</span>
    <span class="ps-collapse__icon" aria-hidden="true"></span>
  </{{ trigger_tag|default('button') }}>
  
  <div class="ps-collapse__panel" 
       id="{{ collapse_id }}"
       role="region"
       {{ is_expanded ? '' : 'hidden' }}
       data-collapse-panel>
    <div class="ps-collapse__content">
      {{ content }}
    </div>
  </div>
</div>
```

---

## ♿ Accessibilité

### Conformité WCAG 2.2 AA

- **ARIA States** : `aria-expanded` mis à jour dynamiquement (true/false)
- **ARIA Controls** : `aria-controls` lie trigger au panel
- **ARIA Labelledby** : Panel référence le trigger pour contexte
- **Role Region** : Panel utilise `role="region"` pour landmark
- **Hidden State** : Attribut `hidden` natif pour masquage (pas CSS display:none seul)
- **Keyboard** : Navigation complète clavier (Enter/Space pour toggle)
- **Focus visible** : Outline focus personnalisé avec `--border-focus`
- **Screen readers** : Annonce état "expanded/collapsed" automatique
- **Animation safe** : Respect `prefers-reduced-motion` pour utilisateurs sensibles

### Semantic HTML
- Utilise `<button>` par défaut (sémantique correcte)
- Support `trigger_tag` pour wrapping dans headings (`<h3>`, etc.)
- Panel content wrapper pour flexibilité contenu

---

## 📱 Comportement responsive

- Layout flexible avec padding adaptatif
- Animation height calculée dynamiquement (supporte contenu variable)
- Touch-friendly (zone tactile suffisante pour trigger)
- Icône repositionnée automatiquement sur petits écrans si nécessaire

---

## 🎯 JavaScript

**Fichier** : `source/patterns/elements/collapse/collapse.js`

**Fonctionnalités** :
- Toggle animation fluide (height transition 300ms)
- États intermédiaires `is-collapsing` pendant transition
- Events custom : `collapse:show`, `collapse:hide`, `collapse:shown`, `collapse:hidden`
- Support `prefers-reduced-motion` (animation instantanée si préférence)
- Event `collapse:external-toggle` pour coordination accordion
- Drupal behaviors pattern avec `once()` pour idempotence

**Intégration** :
```javascript
// Auto-init via Drupal behaviors
Drupal.behaviors.psCollapse = {
  attach(context) {
    once('ps-collapse', '[data-collapse-trigger]', context).forEach((trigger) => {
      // Init collapse behavior
    });
  }
};
```

---

## 🧪 Exemples d'usage

### Collapse simple
```twig
{% include '@elements/collapse/collapse.twig' with {
  id: 'faq-1',
  title: 'How to buy business premises?',
  content: '<p>Detailed explanation here...</p>'
} only %}
```

### Collapse avec variante primary
```twig
{% include '@elements/collapse/collapse.twig' with {
  id: 'feature-1',
  title: 'Feature Details',
  content: '<p>Feature content...</p>',
  variant: 'primary',
  expanded: true
} only %}
```

### Collapse dans heading sémantique
```twig
{% include '@elements/collapse/collapse.twig' with {
  id: 'section-1',
  title: 'Section Title',
  content: '<p>Section content...</p>',
  trigger_tag: 'h3'
} only %}
```

### Usage dans Accordion
Le composant Collapse est la base atomique utilisée par Accordion (collection) :

```twig
{# Accordion compose plusieurs Collapse avec coordination #}
{% include '@collections/accordion/accordion.twig' with {
  items: [
    { id: 'item-1', title: 'Section 1', content: '...' },
    { id: 'item-2', title: 'Section 2', content: '...' }
  ],
  single_open: true
} only %}
```

---

## 📚 Composition Atomique

**Collapse est un Atom** qui peut être :
- Utilisé seul (disclosure simple)
- Composé dans Accordion (collection/organism)
- Intégré dans d'autres composants nécessitant affichage progressif

**Dépendances** :
- Aucune dépendance (atom autonome)
- Peut composer `@elements/text/text.twig` pour contenu riche (optionnel)

**Compose** :
- Accordion (`@collections/accordion/accordion.twig`)
- Autres composants nécessitant disclosure pattern

---

## 📋 Checklist conformité

- [x] 5 fichiers obligatoires (`.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`)
- [x] BEM strict avec préfixe `ps-`
- [x] Tokens uniquement (0 hardcoded values)
- [x] HTML minimal (pas de classes pour defaults)
- [x] Modifiers indépendants (chaque variant fonctionne seul)
- [x] CSS nesting moderne (postcss-nested)
- [x] Cascade order correct (base → elements → modifiers → states)
- [x] Description README ≤ 2 lignes
- [x] Storybook Autodocs activé (`tags: ['autodocs']`)
- [x] ArgTypes catégorisés (Content|Appearance|Behavior|Accessibility)
- [x] Stories showcases (pas individual)
- [x] Accessibilité WCAG 2.2 AA complète
- [x] JavaScript Drupal behaviors pattern
- [x] Animations avec `prefers-reduced-motion`
- [x] Système CSS variables 3 layers (Bootstrap-inspired)

---

## 📚 Ressources

- **Implémentation** : `source/patterns/elements/collapse/`
- **Storybook** : Stories avec 8 variantes + use cases
- **Tokens référence** : `source/props/*.css`
- **Atomic Design** : Atom fondamental pour patterns disclosure
- **WCAG 2.2** : [Disclosure pattern](https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/)

---

**Version** : 1.0.0  
**Dernière mise à jour** : 3 décembre 2025  
**Status** : ✅ Production-ready
