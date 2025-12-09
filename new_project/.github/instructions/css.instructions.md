# Instructions CSS

## Règles fondamentales

### 1. Tokens obligatoires
- ❌ **INTERDIT** : valeurs hardcodées (`#00915A`, `16px`, `150ms ease`)
- ✅ **REQUIS** : tokens CSS (`var(--primary)`, `var(--size-4)`, `var(--transition-base)`)
- Tous les tokens dans `source/tokens/` (colors, sizes, fonts, shadows, animations, etc.)

### 2. Nesting PostCSS
- **Maximum 3 niveaux** de profondeur
- Utiliser `&` pour modifiers, pseudo-classes, pseudo-éléments
- Structure : base → éléments → modifiers

```css
/* ✅ CORRECT */
.ps-card {
  background: var(--surface);
  
  &__header {
    padding: var(--space-4);
  }
  
  &--primary {
    border-color: var(--primary);
  }
}

/* ❌ INTERDIT - trop profond */
.ps-card {
  &__header {
    &__title {
      &__icon { /* 4 niveaux */ }
    }
  }
}
```

### 3. Couleurs sémantiques
- **TOUJOURS** utiliser tokens sémantiques, jamais les noms de couleurs
- Disponibles : `--primary`, `--secondary`, `--success`, `--danger`, `--warning`, `--info`, `--gold`, `--light`, `--dark`
- États : `-hover`, `-active`, `-text`, `-border`, `-subtle`, `-bg-subtle`, `-border-subtle`, `-text-emphasis`

```css
/* ✅ CORRECT */
.ps-button--primary { background: var(--primary); }
.ps-alert--success { background: var(--success-subtle); color: var(--success-text-emphasis); }

/* ❌ INTERDIT */
.ps-button--primary { background: green; }
.ps-button--primary { background: var(--green-600); }
```

### 4. Ordre cascade
```css
.ps-component {
  /* 1. Base styles */
  display: block;
  background: var(--surface);
  
  /* 2. Éléments BEM */
  &__element {
    padding: var(--space-2);
  }
  
  /* 3. Modifiers */
  &--variant {
    background: var(--primary);
  }
  
  /* 4. États */
  &:hover,
  &:focus-visible {
    background: var(--primary-hover);
  }
  
  /* 5. Media queries */
  @media (--tablet) {
    padding: var(--space-4);
  }
}
```

### 5. Focus visible obligatoire
```css
/* ✅ REQUIS pour tous les interactifs */
.ps-button {
  &:focus-visible {
    outline: 2px solid var(--border-focus);
    outline-offset: 2px;
  }
}
```

### 6. Media queries
- Utiliser custom media queries : `@media (--mobile)`, `@media (--tablet)`, `@media (--desktop)`
- Définis dans `source/tokens/media.css`

### 7. BEM strict
- `.ps-block` : composant de base
- `.ps-block__element` : partie du composant
- `.ps-block--modifier` : variante du composant
- **PAS de combinaisons** : chaque modifier doit fonctionner seul

### 8. Icons
- **JAMAIS** de préfixe `icon-` dans le code
- Utiliser `data-icon="check"` (pas `data-icon="icon-check"`)
- Le préfixe est ajouté automatiquement par le build

```css
/* ✅ CORRECT */
[data-icon="check"] { background-image: url('/icons/icons-sprite.svg#icon-check'); }
```

## Interdictions absolues

- ❌ Éditer directement `source/tokens/*.css` (proposer tokens via process séparé)
- ❌ CSS flat sans nesting (nouveaux composants DOIVENT utiliser `&`)
- ❌ Combinaisons de modifiers requises (`.ps-badge--a.ps-badge--b`)
- ❌ Modifiers avant base dans cascade
- ❌ Valeurs hardcodées (couleurs, tailles, durées)
- ❌ Noms de couleurs au lieu de sémantiques (`green` → `success`)

## Fichiers de référence

**Parfaites implémentations** :
- `source/patterns/elements/button/button.css`
- `source/patterns/elements/badge/badge.css`
- `source/patterns/elements/avatar/avatar.css`
