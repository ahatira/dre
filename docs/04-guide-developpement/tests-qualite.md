# Tests et Qualité

**Validation complète avant commit** : Audit 100 points + Build + Tests

---

## 🎯 Vue d'ensemble

Ce guide décrit le **processus de validation complet** pour garantir la qualité des composants PS Theme avant commit.

**Minimum requis** : 90/100 points pour production

---

## 📊 Audit de Conformité (100 points)

### Checklist complète

#### 1. Architecture & Dépendances (10 points)

- [ ] **5 pts** - Respecte hiérarchie Atomic (pas de dépendances circulaires)
- [ ] **5 pts** - Toutes les dépendances existent et sont conformes

**Validation** :
```bash
# Vérifier dépendances dans Twig
grep -n "{% include" source/patterns/{niveau}/{composant}/*.twig

# Vérifier atoms existent
ls source/patterns/elements/{atom}/
```

---

#### 2. Structure Fichiers (10 points)

- [ ] **2 pts** - `.twig` existe avec header comment
- [ ] **2 pts** - `.css` existe avec nesting
- [ ] **2 pts** - `.yml` existe avec données Real Estate
- [ ] **2 pts** - `.stories.jsx` existe avec Autodocs
- [ ] **2 pts** - `README.md` existe avec toutes sections

**Validation** :
```bash
ls -la source/patterns/{niveau}/{composant}/
# DOIT avoir : .twig, .css, .yml, .stories.jsx, README.md (5 fichiers)
```

---

#### 3. Template Twig (15 points)

- [ ] **3 pts** - Header comment avec `@param`
- [ ] **3 pts** - Defaults : `prop|default('value')`
- [ ] **3 pts** - Classes avec ternaire + `null` (PAS arrow functions)
- [ ] **3 pts** - Composition via `{% include %}` avec `only`
- [ ] **3 pts** - Contexte Real Estate (placeholders réalistes)

**Validation** :
```bash
# Chercher arrow functions (ne doit rien retourner)
grep -n "=>" source/patterns/{niveau}/{composant}/*.twig

# Vérifier 'only'
grep -n "only" source/patterns/{niveau}/{composant}/*.twig
```

---

#### 4. Styles CSS (20 points)

- [ ] **5 pts** - 100% tokens (aucune valeur hardcodée : `#00915A`, `16px`)
- [ ] **5 pts** - Nesting avec `&`
- [ ] **3 pts** - Ordre cascade : Base → Éléments → Modifiers → États
- [ ] **3 pts** - Modifiers indépendants (chacun fonctionne seul)
- [ ] **2 pts** - Couleurs sémantiques (`primary`, pas `green`)
- [ ] **2 pts** - Focus-visible pour interactifs

**Validation** :
```bash
# Chercher couleurs hardcodées
grep -rE "#[0-9A-Fa-f]{6}" source/patterns/{niveau}/{composant}/

# Chercher tailles hardcodées
grep -rE "[0-9]+px|[0-9]+rem" source/patterns/{niveau}/{composant}/*.css | grep -v "var(--"

# Vérifier focus-visible
grep -n "focus-visible" source/patterns/{niveau}/{composant}/*.css
```

---

#### 5. Storybook (20 points)

- [ ] **5 pts** - `tags: ['autodocs']` dans export default
- [ ] **5 pts** - Import Twig : `import twig from './component.twig'`
- [ ] **4 pts** - argTypes catégorisés (6 catégories)
- [ ] **3 pts** - Description ≤ 2 lignes
- [ ] **3 pts** - Stories : Default + Showcases (pas variants individuels)

**Validation** :
```bash
# Vérifier tags autodocs
grep -n "tags.*autodocs" source/patterns/{niveau}/{composant}/*.stories.jsx

# Vérifier imports React (ne doit rien retourner)
grep -n "import React" source/patterns/{niveau}/{composant}/*.stories.jsx
```

---

#### 6. Configuration YAML (10 points)

- [ ] **5 pts** - Données Real Estate réalistes
- [ ] **3 pts** - Toutes props obligatoires définies
- [ ] **2 pts** - Props optionnelles avec defaults significatifs

---

#### 7. Documentation README (10 points)

- [ ] **2 pts** - Section : Usage (exemple Twig)
- [ ] **2 pts** - Section : Props (tableau)
- [ ] **2 pts** - Section : BEM Structure (arbre)
- [ ] **2 pts** - Section : Design Tokens (variables CSS)
- [ ] **2 pts** - Section : Accessibility (checklist)

**Validation** :
```bash
grep -n "## " source/patterns/{niveau}/{composant}/README.md
```

---

#### 8. Nomenclature BEM (5 points)

- [ ] **2 pts** - Préfixe `ps-` obligatoire
- [ ] **2 pts** - Format : `.ps-block__element--modifier`
- [ ] **1 pt** - Pas de double underscore (`__`)

---

#### 9. Accessibilité (5 points)

- [ ] **2 pts** - Contraste : Texte 4.5:1, UI 3:1 (WCAG AA)
- [ ] **2 pts** - Focus-visible pour interactifs
- [ ] **1 pt** - Attributs ARIA appropriés

---

#### 10. Token-First (5 points)

- [ ] **3 pts** - Variables component-scoped (Layer 2) en haut
- [ ] **2 pts** - Composition via override tokens (Layer 3)

---

### Interprétation du score

| Score | Statut | Action |
|-------|--------|--------|
| **90-100** | ✅ Prêt production | Commit OK |
| **75-89** | ⚠️ Corrections mineures | Corriger violations, re-auditer |
| **< 75** | ❌ Refactoring majeur | Workflow refactoring complet |

---

## 🧪 Validation Build

### Pre-commit (OBLIGATOIRE)

```bash
npm run build
```

**Valide** :
- ✅ Biome lint (JavaScript, JSON)
- ✅ Biome format (tous fichiers)
- ✅ Compilation CSS (Vite + PostCSS)
- ✅ Pas d'erreurs syntaxe (Twig via Storybook)

**Si échec** : Consulter section Troubleshooting

---

### Validation visuelle

```bash
npm run watch
# → http://localhost:6006
```

**Checklist Storybook** :
- [ ] Composant s'affiche correctement
- [ ] Toutes variantes/modifiers fonctionnent
- [ ] Fonctionnalités interactives OK
- [ ] Onglet Docs affiche Autodocs
- [ ] Aucune erreur console

---

## 🚨 Troubleshooting (15 erreurs courantes)

### 1. Token non trouvé

**Erreur** :
```
ERROR: CSS variable '--primary-dark' is not defined
```

**Solution** :
```bash
# Chercher token
npm run tokens:check -- --primary

# Ou manuellement
grep -r "--primary" source/props/
```

**Correction** :
```css
/* ❌ AVANT */
background: var(--primary-dark); /* N'existe pas */

/* ✅ APRÈS */
background: var(--primary-hover); /* Existe dans brand.css */
```

---

### 2. Valeur hardcodée

**Erreur** :
```
Hardcoded value detected: 16px
```

**Solution** :
```css
/* ❌ AVANT */
padding: 16px;

/* ✅ APRÈS */
padding: var(--size-4); /* 16px via token */
```

---

### 3. Syntaxe nesting CSS incorrecte

**Erreur** :
```
Nested selector must start with &
```

**Solution** :
```css
/* ❌ AVANT */
.ps-button {
  .ps-button__icon { /* Manque & */
    width: var(--size-4);
  }
}

/* ✅ APRÈS */
.ps-button {
  &__icon {
    width: var(--size-4);
  }
}
```

---

### 4. Biome lint error

**Erreur** :
```
Expected semicolon (lint/style/noUnusedVariables)
```

**Solution** :
```bash
# Auto-fix
npm run lint:fix

# Ou manuellement corriger dans .jsx
```

---

### 5. Arrow function en Twig

**Erreur** :
```
Twig syntax error: Unexpected token "=>"
```

**Solution** :
```twig
{# ❌ AVANT (Drupal incompatible) #}
{% set classes = classes|filter(v => v) %}

{# ✅ APRÈS (ternaire + null) #}
{% set classes = [
  'ps-badge',
  variant != 'default' ? 'ps-badge--' ~ variant : null,
] %}
<span class="{{ classes|join(' ')|trim }}">
```

---

### 6. Couleur non-sémantique

**Erreur** :
```
Use semantic color instead of palette
```

**Solution** :
```css
/* ❌ AVANT */
.ps-badge--green { background: var(--green-600); }

/* ✅ APRÈS */
.ps-badge--success { background: var(--success-bg-subtle); }
```

---

### 7. Missing 'only' keyword

**Erreur** :
```
Include without 'only' (security risk)
```

**Solution** :
```twig
{# ❌ AVANT #}
{% include '@elements/icon/icon.twig' with {icon: 'check'} %}

{# ✅ APRÈS #}
{% include '@elements/icon/icon.twig' with {icon: 'check'} only %}
```

---

### 8. Tags autodocs manquant

**Erreur** :
```
Autodocs not generated for story
```

**Solution** :
```jsx
// ❌ AVANT
export default {
  title: 'Elements/Badge',
};

// ✅ APRÈS
export default {
  title: 'Elements/Badge',
  tags: ['autodocs'], // OBLIGATOIRE
};
```

---

### 9. Syntaxe React/JSX

**Erreur** :
```
Cannot use JSX in Twig renderer
```

**Solution** :
```jsx
// ❌ AVANT (JSX)
export const Default = () => <Button text="Click" />;

// ✅ APRÈS (Twig string)
export const Default = {
  args: { text: 'Click' },
};
```

---

### 10. Import React inutile

**Erreur** :
```
'React' is defined but never used
```

**Solution** :
```jsx
// ❌ AVANT
import React from 'react';
import componentTwig from './component.twig';

// ✅ APRÈS (supprimer import React)
import componentTwig from './component.twig';
```

---

### 11. Focus-visible manquant

**Solution** :
```css
/* ✅ Ajouter pour tous interactifs */
.ps-button:focus-visible {
  outline: var(--border-size-2) solid var(--border-focus);
  outline-offset: var(--size-05);
}
```

---

### 12. Prefix icon-

**Solution** :
```twig
{# ❌ AVANT #}
{% set icon = 'icon-check' %}

{# ✅ APRÈS (prefix auto-ajouté par CSS) #}
{% set icon = 'check' %}
```

---

### 13. Modifier non-indépendant

**Erreur** :
```
Modifier requires combination: .ps-badge--primary.ps-badge--sm
```

**Solution** :
```css
/* ✅ Chaque modifier doit fonctionner seul */
.ps-badge--primary { background: var(--primary-bg-subtle); }
.ps-badge--sm { font-size: var(--font-size--1); }
```

---

### 14. Ordre cascade incorrect

**Solution** :
```css
/* ✅ Ordre correct */
.ps-button {
  /* 1. Variables component-scoped */
  --ps-button-bg: var(--primary);
  
  /* 2. Base styles */
  display: inline-flex;
  background: var(--ps-button-bg);
  
  /* 3. Elements */
  &__icon { width: var(--size-4); }
  
  /* 4. Modifiers */
  &--secondary { --ps-button-bg: var(--secondary); }
  
  /* 5. States */
  &:hover { --ps-button-bg: var(--primary-hover); }
}
```

---

### 15. Flat CSS (sans nesting)

**Solution** :
```css
/* ❌ AVANT (flat) */
.ps-button { display: flex; }
.ps-button__icon { width: var(--size-4); }
.ps-button--primary { background: var(--primary); }

/* ✅ APRÈS (nested) */
.ps-button {
  display: flex;
  
  &__icon {
    width: var(--size-4);
  }
  
  &--primary {
    background: var(--primary);
  }
}
```

---

## ✅ Checklist finale

### Avant commit

- [ ] Audit conformité : Score ≥ 90/100
- [ ] `npm run build` passe sans erreur
- [ ] `npm run watch` : Composant s'affiche correctement
- [ ] Toutes variantes testées visuellement
- [ ] Responsive testé (375px → 1920px)
- [ ] Accessibilité validée (Storybook Accessibility tab)
- [ ] README.md complet

### Commit

- [ ] Message structuré (type/scope + body FR)
- [ ] Changelog mis à jour (`docs/ps-design/CHANGELOG.md`)
- [ ] Référence spec dans commit body

---

## 🎓 Ressources

### Prompts AI

- **Auditer composant** : `.github/prompts/audit-component.md`
- **Debug build** : `.github/prompts/debug-build.md`
- **Refactor legacy** : `.github/prompts/refactor-legacy.md`

### Documentation

- **Instructions complètes** : [.github/instructions/04-quality-assurance.md](../../.github/instructions/04-quality-assurance.md) (829 lignes)
- **Standards techniques** : [.github/instructions/03-technical-implementation.md](../../.github/instructions/03-technical-implementation.md)

### Composants référence (100/100)

- **Button** : `source/patterns/elements/button/` (CSS nesting parfait, tous états)
- **Avatar** : `source/patterns/elements/avatar/` (Markup minimal, sizing adaptatif)
- **Icon** : `source/patterns/elements/icon/` (Système sprite SVG)
- **Divider** : `source/patterns/elements/divider/` (Simplicité, code minimal)

---

**Navigation** : [← Composition](./composition.md) | [Guide développement ↑](./README.md)
