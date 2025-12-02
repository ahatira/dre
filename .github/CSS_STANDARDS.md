# CSS Standards - PS Theme

**Version:** 1.0.0  
**Date:** 30 novembre 2025  
**Projet:** PS Theme (Drupal 10/11 + Storybook)

---

## 📋 Table des matières

1. [Stack Technique](#stack-technique)
2. [Architecture CSS](#architecture-css)
3. [Syntaxe et Formatting](#syntaxe-et-formatting)
4. [BEM Methodology](#bem-methodology)
5. [Design Tokens](#design-tokens)
6. [CSS Nesting](#css-nesting)
7. [États et Interactions](#états-et-interactions)
8. [Accessibilité](#accessibilité)
9. [Performance](#performance)
10. [Checklist Validation](#checklist-validation)

---

## 🛠 Stack Technique

### Build Tools
- **Vite** : Bundler moderne pour dev/build rapide
- **PostCSS** : Transformation CSS avec plugins
- **Autoprefixer** : Préfixes navigateurs automatiques
- **Stylelint** : Linter CSS (config standard)

### PostCSS Plugins Actifs

```javascript
// postcss.config.js
{
  autoprefixer(),           // Préfixes navigateurs
  postcssImportExtGlob(),   // Import avec glob patterns
  postcssImport(),          // @import résolution
  postcssNested(),          // Nesting CSS natif (&)
  postcssGlobalData(),      // Custom media queries
  postcssPresetEnv({        // Features CSS futures (stage 4)
    stage: 4,
    features: {
      'custom-media-queries': { preserve: false }
    }
  })
}
```

### Compatibilité Navigateurs

```json
// package.json browserslist
[
  "last 2 versions and not dead",
  ">= 1%",
  ">= 1% in US"
]
```

---

## 🏗 Architecture CSS

### Structure des Fichiers

```
source/
├── props/                    # Design tokens (CSS Custom Properties)
│   ├── index.css            # Import central de tous les tokens
│   ├── colors.css           # Couleurs (--gray-*, --primary, etc.)
│   ├── fonts.css            # Typographie (--font-*, --font-size-*)
│   ├── sizes.css            # Espacements (--size-*)
│   ├── borders.css          # Bordures et radius (--radius-*, --border-size-*)
│   ├── shadows.css          # Ombres (--shadow-*)
│   ├── animations.css       # Durées (--duration-*)
│   ├── easing.css           # Timing functions (--ease-*)
│   ├── zindex.css           # Z-index (--z-*)
│   └── media.css            # Custom media queries
│
├── patterns/
│   ├── base/
│   │   └── utilities/
│   │       └── reset.css    # Reset CSS moderne (box-sizing, normalize)
│   │
│   ├── elements/            # Atoms (button, badge, avatar, etc.)
│   ├── components/          # Molecules (card, form-field, etc.)
│   ├── collections/         # Organisms (header, footer, etc.)
│   ├── layouts/             # Templates (page layouts)
│   └── pages/               # Pages (full compositions)
```

### Ordre d'Import

```css
/* global.css - NE PAS TRIER */
@import "../../props/index";         /* 1. Design tokens */
@import "./utilities/reset";         /* 2. Reset CSS */
```

---

## ✍️ Syntaxe et Formatting

### Règles Générales

```css
/* ✅ CORRECT - Multi-line, propriétés une par ligne */
.ps-component {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--size-2);
  padding: var(--size-4);
  background: var(--gray-100);
  border-radius: var(--radius-2);
  transition: background var(--duration-fast) var(--ease-3);
}

/* ❌ INCORRECT - One-liner (sauf cas très simples) */
.ps-component { display: flex; align-items: center; }
```

### Indentation et Espacement

- **Indentation** : 2 espaces (configuré dans Biome)
- **Lignes vides** : Entre sections logiques, jamais entre sélecteur et bloc
- **Line width** : Max 100 caractères (Biome)

```css
/* ✅ CORRECT - Sections séparées */
.ps-component {
  /* Layout */
  display: flex;
  align-items: center;
  
  /* Visual */
  background: var(--gray-100);
  border-radius: var(--radius-2);
}

/* Modifier */
.ps-component--large {
  padding: var(--size-6);
}

/* ❌ INCORRECT - Pas de ligne vide entre sélecteur et bloc */
.ps-component
{
  display: flex;
}
```

### Commentaires

```css
/**
 * Component Name (Category)
 * Description brève du composant
 * 
 * BEM: ps-component, ps-component__element, ps-component--modifier
 * Variants: primary | secondary | success
 * Sizes: small | medium | large
 */

/* ======================================
 * Section Title (séparateur majeur)
 * ====================================== */

/* Subsection - commentaire descriptif */

/* Single line comment for quick note */
```

---

## 🎯 BEM Methodology

### Naming Convention

**Préfixe obligatoire** : `ps-` (PS Theme)

```
.ps-block              // Block
.ps-block__element     // Element
.ps-block--modifier    // Modifier
```

### Structure BEM Stricte

```css
/* Block - Base component */
.ps-avatar {
  display: inline-flex;
  width: 100%;
  height: 100%;
  border-radius: 50%; /* circle par défaut */
}

/* Elements - Parties du composant */
.ps-avatar__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.ps-avatar__text {
  font-family: var(--font-sans);
  font-weight: var(--font-weight-600);
  color: var(--white);
}

/* Modifiers - Variations */
.ps-avatar--square {
  border-radius: 0;
}

.ps-avatar--rounded {
  border-radius: var(--radius-4);
}

/* ❌ INCORRECT - Pas de nesting profond */
.ps-avatar__text__inner { /* NON */ }
```

### Modifiers Indépendants

**CRITIQUE** : Chaque modifier doit fonctionner seul sur la classe de base.

```css
/* ✅ CORRECT - Modifier indépendant */
.ps-badge {
  background: var(--gray-200); /* default */
}

.ps-badge--primary {
  background: var(--primary); /* fonctionne seul */
}

.ps-badge--large {
  padding: var(--size-3); /* fonctionne seul */
}

/* ❌ INCORRECT - Modifier dépendant */
.ps-badge--primary.ps-badge--large {
  /* Nécessite deux classes pour fonctionner */
}
```

### Markup Minimal

**Règle d'or** : Ne pas ajouter de classes pour valeurs par défaut.

```html
<!-- ✅ CORRECT - Classes minimales (md et circle par défaut) -->
<div class="ps-avatar">
  <img class="ps-avatar__image" src="..." alt="User" />
</div>

<!-- ✅ CORRECT - Modifier ajouté uniquement si différent du défaut -->
<div class="ps-avatar ps-avatar--square ps-avatar--lg">
  <span class="ps-avatar__text">JD</span>
</div>

<!-- ❌ INCORRECT - Classes redondantes pour défauts -->
<div class="ps-avatar ps-avatar--circle ps-avatar--md">
  <img class="ps-avatar__image" src="..." alt="User" />
</div>
```

---

## 🎨 Design Tokens

### Règle Absolue

**❌ JAMAIS de valeurs en dur** : `#00915A`, `16px`, `2px solid gray`, etc.  
**✅ TOUJOURS utiliser les tokens** : `var(--primary)`, `var(--size-4)`, etc.

### Tokens Disponibles

#### Couleurs (`colors.css`)

```css
/* Neutral scale */
--white: hsl(0, 0%, 100%);
--gray-50: hsl(210, 40%, 98%);
--gray-100: hsl(210, 40%, 96%);
--gray-200: hsl(214, 32%, 91%);
--gray-300: hsl(213, 27%, 84%);
--gray-400: hsl(215, 20%, 65%);
--gray-500: hsl(215, 16%, 47%);
--gray-600: hsl(215, 19%, 35%);
--gray-700: hsl(215, 25%, 27%);
--gray-800: hsl(217, 33%, 17%);
--gray-900: hsl(222, 47%, 11%);
--black: hsl(0, 0%, 0%);

/* Brand colors */
--primary: hsl(157, 100%, 28%);      /* #00915A - Vert BNP */
--secondary: hsl(330, 65%, 40%);     /* #A12B66 - Rose/Magenta */

--bnp-green: var(--primary);
--bnp-accent-pink: var(--secondary);
--bnp-accent-magenta: hsl(332, 59%, 41%); /* #A12B66 */

/* Semantic colors */
--green-600: hsl(162, 89%, 37%);   /* Success */
--red-600: hsl(2, 80%, 58%);       /* Error/Danger */
--yellow-500: hsl(43, 89%, 41%);   /* Warning */
--blue-600: hsl(218, 85%, 56%);    /* Info */

/* Buttons (alias sémantiques) */
--btn-primary: var(--primary);
--btn-success: var(--green-600);
--btn-warning: var(--yellow-500);
--btn-danger: var(--red-600);
--btn-info: var(--blue-600);
```

#### Typographie (`fonts.css`)

```css
/* Font families */
--font-sans: 'BNPP Sans', system-ui, sans-serif;
--font-alt: 'Open Sans', system-ui, sans-serif;
--font-mono: Menlo, Consolas, monospace;

/* Font weights */
--font-weight-400: 400;  /* Regular */
--font-weight-500: 500;  /* Medium */
--font-weight-600: 600;  /* Semi-bold */
--font-weight-700: 700;  /* Bold */

/* Font sizes (scale fluide) */
--font-size-xs: 0.625rem;   /* 10px */
--font-size-sm: 0.75rem;    /* 12px */
--font-size-0: 0.875rem;    /* 14px */
--font-size-1: 1rem;        /* 16px */
--font-size-2: 1.125rem;    /* 18px */
--font-size-3: 1.25rem;     /* 20px */
--font-size-4: 1.375rem;    /* 22px */
--font-size-5: 1.5rem;      /* 24px */
```

#### Espacements (`sizes.css`)

```css
/* Size scale (base 4px) */
--size-px: 0.063rem;   /* 1px */
--size-05: 0.125rem;   /* 2px */
--size-1: 0.25rem;     /* 4px */
--size-2: 0.5rem;      /* 8px */
--size-3: 0.75rem;     /* 12px */
--size-4: 1rem;        /* 16px */
--size-5: 1.25rem;     /* 20px */
--size-6: 1.5rem;      /* 24px */
--size-8: 2rem;        /* 32px */
--size-10: 2.5rem;     /* 40px */
--size-12: 3rem;       /* 48px */
--size-20: 5rem;       /* 80px */
```

#### Bordures (`borders.css`)

```css
/* Border widths */
--border-size-1: 1px;
--border-size-2: 2px;
--border-size-3: 3px;

/* Border radius */
--radius-1: 0.125rem;  /* 2px */
--radius-2: 0.25rem;   /* 4px */
--radius-3: 0.375rem;  /* 6px */
--radius-4: 0.5rem;    /* 8px */
--radius-5: 0.75rem;   /* 12px */
--radius-6: 1rem;      /* 16px */
--radius-round: 1e5px; /* Full round (pill) */
```

### Fallback Pattern

Utilisez des fallbacks pour compatibilité entre anciens/nouveaux tokens :

```css
/* ✅ CORRECT - Fallback pour migration progressive */
.ps-component {
  background: var(--ps-color-neutral-200, var(--gray-200));
  color: var(--ps-color-primary-600, var(--primary));
}
```

---

## 🔗 CSS Nesting

### Support PostCSS

Le projet utilise `postcss-nested` → **Nesting natif supporté**.

### Syntaxe Recommandée

```css
/* ✅ CORRECT - Nesting avec & (états et pseudo-classes) */
.ps-button {
  padding: var(--size-2) var(--size-4);
  background: var(--btn-primary);
  color: var(--white);
  transition: background var(--duration-fast) var(--ease-3);
  
  &:hover:not(:disabled) {
    background: var(--btn-primary-hover);
  }
  
  &:active:not(:disabled) {
    background: var(--btn-primary-active);
  }
  
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  &:disabled,
  &.ps-button--disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
}

/* ✅ CORRECT - Nesting pour modifiers (cas simples) */
.ps-button {
  background: var(--btn-primary);
  
  &--secondary {
    background: var(--btn-secondary);
  }
  
  &--large {
    padding: var(--size-3) var(--size-6);
  }
}
```

### Nesting Contextuels

```css
/* ✅ CORRECT - Nesting pour contextes parent-enfant */
.ps-avatar-wrapper {
  width: var(--size-10); /* md par défaut */
  height: var(--size-10);
  
  /* Modifiers sur wrapper affectent enfants */
  &--xs {
    width: var(--size-6);
    height: var(--size-6);
    
    .ps-avatar__text {
      font-size: var(--font-size-xs);
    }
    
    .ps-avatar__icon {
      width: var(--size-3);
      height: var(--size-3);
    }
  }
  
  &--lg {
    width: var(--size-12);
    height: var(--size-12);
    
    .ps-avatar__text {
      font-size: var(--font-size-4);
    }
  }
}
```

### Quand NE PAS Utiliser le Nesting

```css
/* ❌ INCORRECT - Nesting trop profond (> 3 niveaux) */
.ps-card {
  &__header {
    &__title {
      &__icon { /* 4 niveaux = illisible */ }
    }
  }
}

/* ✅ CORRECT - Flat BEM pour structures complexes */
.ps-card { }
.ps-card__header { }
.ps-card__title { }
.ps-card__icon { }
```

### Ordre de Spécificité (Cascade CSS)

**CRITIQUE** : Base d'abord, modifiers ensuite.

```css
/* ✅ CORRECT - Base avant modifiers pour cascade correcte */
.ps-avatar-wrapper {
  width: var(--size-10); /* md default */
  
  .ps-avatar__text {
    font-size: var(--font-size-2); /* Base 18px */
  }
  
  /* Modifiers APRÈS base (spécificité supérieure) */
  &--xs .ps-avatar__text {
    font-size: var(--font-size-xs); /* Override 10px */
  }
  
  &--lg .ps-avatar__text {
    font-size: var(--font-size-4); /* Override 22px */
  }
}

/* ❌ INCORRECT - Modifiers avant base (pas d'override) */
.ps-avatar-wrapper {
  &--xs .ps-avatar__text {
    font-size: var(--font-size-xs); /* Sera écrasé */
  }
  
  .ps-avatar__text {
    font-size: var(--font-size-2); /* Gagne (vient après) */
  }
}
```

---

## 🎭 États et Interactions

### États Standard

```css
.ps-component {
  /* Default state */
  background: var(--gray-200);
  color: var(--gray-900);
  transition: 
    background var(--duration-fast) var(--ease-3),
    color var(--duration-fast) var(--ease-3),
    transform var(--duration-fast) var(--ease-3);
  
  /* Hover state */
  &:hover:not(:disabled):not(.ps-component--disabled) {
    background: var(--gray-300);
    transform: translateY(-1px);
  }
  
  /* Active state */
  &:active:not(:disabled):not(.ps-component--disabled) {
    background: var(--gray-400);
    transform: translateY(0);
  }
  
  /* Focus state (keyboard navigation) */
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  /* Disabled state */
  &:disabled,
  &.ps-component--disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
}
```

### Transitions Standard

```css
/* Tokenized transition standard (duration + easing tokens) */
transition: property var(--duration-fast) var(--ease-3);

/* Multiples propriétés */
transition: 
  background var(--duration-fast) var(--ease-3),
  color var(--duration-fast) var(--ease-3),
  transform var(--duration-fast) var(--ease-3);
```

---

## ♿ Accessibilité

### Focus Visible

**Obligatoire** pour tous les éléments interactifs.

```css
.ps-interactive {
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  /* ❌ JAMAIS de outline: none sans alternative */
  &:focus {
    outline: none; /* INTERDIT sauf si :focus-visible défini */
  }
}
```

### Contraste Couleurs

Respecter WCAG 2.2 AA minimum :
- **Texte normal** : Ratio 4.5:1
- **Texte large** (18px+ ou 14px bold) : Ratio 3:1
- **Composants UI** : Ratio 3:1

```css
/* ✅ CORRECT - Contraste validé */
.ps-badge {
  background: var(--gray-200); /* #E8EBEF */
  color: var(--gray-600);      /* #54636F - Ratio 4.8:1 ✓ */
}

.ps-badge--primary {
  background: var(--primary); /* #00915A */
  color: var(--white);              /* Ratio 7.2:1 ✓ */
}
```

### États Visuels

Ne pas se reposer uniquement sur la couleur :

```css
/* ✅ CORRECT - Couleur + forme/icône/texte */
.ps-alert--error {
  background: var(--red-100);
  color: var(--red-900);
  border-left: var(--border-size-3) solid var(--red-600); /* Indicateur visuel */
}

.ps-alert--error::before {
  content: '⚠'; /* Icône en plus de la couleur */
}
```

---

## 🚀 Performance

### Sélecteurs Performants

```css
/* ✅ CORRECT - Sélecteurs simples */
.ps-component { }
.ps-component--modifier { }
.ps-component__element { }

/* ❌ ÉVITER - Sélecteurs universels et descendants profonds */
* { } /* Trop large */
.parent > * { } /* Universel ciblé */
.a .b .c .d .e { } /* Cascade profonde */
```

### Propriétés Coûteuses

```css
/* ⚠️ ATTENTION - Propriétés déclenchant reflow/repaint */
.ps-component {
  /* Éviter ces propriétés en animation : */
  width: ...;      /* Reflow */
  height: ...;     /* Reflow */
  margin: ...;     /* Reflow */
  padding: ...;    /* Reflow */
  
  /* ✅ Préférer transform/opacity pour animations */
  transform: translateY(-1px);  /* Composite layer */
  opacity: 0.5;                  /* Composite layer */
}
```

### Will-Change

Utiliser avec parcimonie pour animations fréquentes :

```css
.ps-animated {
  will-change: transform, opacity; /* Prépare GPU */
}

.ps-animated:hover {
  transform: scale(1.05);
  opacity: 0.8;
}
```

---

## ✅ Checklist Validation

### Avant Commit

- [ ] **Tokens uniquement** : Aucune valeur en dur (`#hex`, `16px`, etc.)
- [ ] **BEM strict** : Préfixe `ps-`, pas de nesting > 3 niveaux
- [ ] **Modifiers indépendants** : Chaque modifier fonctionne seul
- [ ] **Markup minimal** : Pas de classes pour valeurs par défaut
- [ ] **Cascade correcte** : Base avant modifiers (spécificité)
- [ ] **Focus visible** : `:focus-visible` sur tous les interactifs
- [ ] **Transitions** : Cubic-bezier standard `(0.4, 0.0, 0.2, 1)`
 - [ ] **Transitions** : Duration + easing via tokens (`var(--duration-*)`, `var(--ease-*)`)
- [ ] **Commentaires** : Sections et règles complexes documentées
- [ ] **Stylelint pass** : `npm run stylelint:check`

### Tests Manuels

- [ ] **Responsive** : Tester xs, sm, md, lg, xl
- [ ] **Dark mode** : Si applicable (via tokens)
- [ ] **Keyboard nav** : Tab, Enter, Space, Arrows
- [ ] **Screen reader** : NVDA/JAWS (aria-labels, rôles)
- [ ] **Contraste** : WebAIM Contrast Checker
- [ ] **Performance** : DevTools Performance tab (< 16ms)

---

## 📚 Ressources

### Documentation Interne

- **Design Tokens** : `source/props/`
- **Reset CSS** : `source/patterns/base/utilities/reset.css`
- **Composants Référence** : 
  - `source/patterns/elements/button/button.css` (nesting moderne)
  - `source/patterns/elements/badge/badge.css` (flat BEM)
  - `source/patterns/elements/divider/divider.css` (minimal)

### Liens Externes

- [BEM Methodology](https://getbem.com/)
- [CSS Nesting (MDN)](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_nesting)
- [PostCSS Nested](https://github.com/postcss/postcss-nested)
- [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [Contrast Checker](https://webaim.org/resources/contrastchecker/)

---

**Maintainers** : Design System Team  
**Dernière mise à jour** : 30 novembre 2025
