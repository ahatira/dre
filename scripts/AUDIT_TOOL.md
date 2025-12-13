# 🤖 Component Audit & Auto-Fix Tool

**Script automatisé pour auditer et corriger les composants selon le système 90 points.**

## 📋 Vue d'ensemble

Le script `audit-component.mjs` effectue un audit complet en 4 phases :

1. **AUDIT** - Analyse statique (scoring 90 points)
2. **FIX** - Corrections automatiques
3. **VALIDATE** - Vérification build + re-audit
4. **COMMIT** - Git commit si score ≥ 80/90

## 🚀 Utilisation

### Commandes disponibles

```bash
# Audit seul (affiche rapport)
npm run audit -- elements/badge

# Audit + mode verbose (détails des sections)
npm run audit -- elements/badge --verbose

# Audit + corrections automatiques
npm run audit -- elements/badge --fix

# Audit + corrections + commit si passing
npm run audit -- elements/badge --fix --commit

# Workflow complet (recommandé)
npm run audit -- elements/badge --fix --commit --verbose
```

### Exemples

```bash
# Vérifier Badge
npm run audit -- elements/badge --verbose

# Corriger Button et commiter
npm run audit -- elements/button --fix --commit

# Vérifier un molecule
npm run audit -- components/card --verbose
```

## 📊 Système de scoring

**Total : 90 points** (README.md supprimé en décembre 2025)

| Section | Points | Vérifications |
|---------|--------|---------------|
| **File Structure** | 8 | 4 fichiers requis (.twig, .css, .yml, .stories.jsx) |
| **Twig Template** | 15 | Header, attributes, defaults, ternary, includes |
| **CSS Styles** | 20 | Tokens, nesting, cascade, semantic, focus-visible |
| **Storybook** | 20 | Autodocs, import Twig, argTypes, stories |
| **YAML Config** | 10 | Props réalistes, complétude |
| **BEM Naming** | 5 | Préfixe ps-, format BEM valide |
| **Accessibility** | 5 | Focus-visible, contrast (WCAG AA) |
| **Architecture** | 10 | Hiérarchie atomique (vérification manuelle) |

**Seuils** :
- ✅ **80-90** : Production ready (PASSING)
- ⚠️ **65-79** : Corrections mineures requises
- ❌ **<65** : Refactoring majeur requis

## 🔧 Corrections automatisées

### Corrections fiables (automated: true)

| Type | Correction | Fichier | Sévérité |
|------|-----------|---------|----------|
| **twig-add-attributes** | Ajoute `attributes` parameter + `\|without('class')` | .twig | Haute |
| **twig-remove-arrow-functions** | Supprime arrow functions (` =>`) | .twig | Critique |
| **css-replace-colors** | Remplace hardcoded colors par tokens | .css | Haute |
| **css-add-focus-visible** | Ajoute styles focus-visible | .css | Haute |
| **story-add-autodocs** | Ajoute `tags: ['autodocs']` | .stories.jsx | Haute |
| **story-remove-react** | Supprime import React inutile | .stories.jsx | Basse |

### Corrections manuelles (automated: false)

- **css-add-nesting** : Refactorisation complète (flat → nested)
- **twig-arrow-complex** : Arrow functions complexes (map, filter)
- **architecture** : Dépendances circulaires, hiérarchie

## 📈 Rapports d'audit

### Format du rapport

```
============================================================
📊 AUDIT REPORT: elements/badge
============================================================

✅ File Structure: 8/8 (100%)

✅ Twig Template: 12/15 (80%)
   • Missing attributes parameter

⚠️ CSS Styles: 15/20 (75%)
   • 3 hardcoded colors: #00915A, #A12B66, #198754
   • No CSS nesting found (flat structure)

✅ Storybook: 18/20 (90%)
   • ArgTypes present but not categorized

============================================================
TOTAL SCORE: 73/90 (81%) - ✅ PASSING
============================================================

🔧 4 automated fixes available (use --fix flag)
```

### Interprétation

- ✅ Section ≥80% : Conforme
- ⚠️ Section 60-79% : Améliorations recommandées
- ❌ Section <60% : Corrections requises

## 🎯 Workflow recommandé

### 1. Audit nouveau composant

```bash
npm run audit -- elements/my-component --verbose
```

**Si score ≥ 80/90** : ✅ Ship it  
**Si score < 80/90** : Passer à l'étape 2

### 2. Corrections automatiques

```bash
npm run audit -- elements/my-component --fix --verbose
```

**Résultat** :
- ✅ Fixes appliqués
- 🔨 Build validé
- 📊 Nouveau score affiché

**Si nouveau score ≥ 80/90** : Passer à l'étape 3  
**Si score < 80/90** : Corrections manuelles requises

### 3. Commit si passing

```bash
npm run audit -- elements/my-component --fix --commit
```

**Message de commit automatique** :
```
fix(elements): Audit automatique my-component - Score 85/90

**Corrections automatiques** :
- Add attributes parameter
- Replace hardcoded colors with tokens (3 occurrences)
- Add autodocs tag

**Score** : 73/90 → 85/90 ✅

Audit : scripts/audit-component.mjs
```

### 4. Corrections manuelles (si nécessaire)

Si le score reste < 80/90 après `--fix`, consulter le rapport pour identifier les corrections manuelles :

```bash
npm run audit -- elements/my-component --verbose
```

**Corrections typiques** :
- Refactorisation CSS (nesting)
- Remplacement arrow functions complexes
- Catégorisation argTypes
- Ajout ARIA attributes

## 🗂️ Mappings de référence

### Couleurs hardcodées → Tokens

Le script utilise ces mappings automatiques :

```javascript
'#00915A' → 'var(--primary)'      // Green
'#A12B66' → 'var(--secondary)'    // Magenta
'#198754' → 'var(--success)'      // Teal
'#EB3636' → 'var(--danger)'       // Red
'#FBBF24' → 'var(--warning)'      // Yellow
'#2563EB' → 'var(--info)'         // Blue
'#D1AE6E' → 'var(--gold)'         // Gold
'#F8F9FA' → 'var(--light)'        // Gray 100
'#495057' → 'var(--dark)'         // Gray 700
'#FFFFFF' → 'var(--white)'
'#000000' → 'var(--black)'
```

### Tailles hardcodées → Tokens (non implémenté v1)

```javascript
'4px'  → 'var(--size-1)'
'8px'  → 'var(--size-2)'
'16px' → 'var(--size-4)'
'24px' → 'var(--size-6)'
// ... etc
```

## 🔍 Détails des vérifications

### Twig Template (15 pts)

- ✅ Header avec `@param` (2 pts)
- ✅ `attributes` parameter + `|without('class')` (2 pts)
- ✅ Default values : `|default('value')` ≥3 (3 pts)
- ✅ NO arrow functions : `=>` (3 pts critique)
- ✅ Includes avec `only` : `{% include ... only %}` (3 pts)
- ✅ Contexte Real Estate (2 pts)

### CSS Styles (20 pts)

- ✅ Tokens uniquement (no hardcoded) (5 pts)
- ✅ Nesting avec `&` (5 pts)
- ✅ Cascade : Base → Modifiers → States (3 pts)
- ✅ Modifiers indépendants (3 pts)
- ✅ Couleurs sémantiques (2 pts)
- ✅ Focus-visible pour interactifs (2 pts)

### Storybook (20 pts)

- ✅ `tags: ['autodocs']` (5 pts)
- ✅ Import Twig correct (5 pts)
- ✅ NO `import React` (4 pts)
- ✅ ArgTypes catégorisés (3 pts)
- ✅ Description ≤2 lignes (3 pts)

## 📝 Exemples de résultats

### Badge (87/90) - PASSING ✅

```bash
npm run audit -- elements/badge --verbose

TOTAL SCORE: 87/90 (97%) - ✅ PASSING
✅ Component already passing
```

### Button (87/90) - PASSING ✅

```bash
npm run audit -- elements/button --fix --commit

Applied 0 fixes (already conformant)
TOTAL SCORE: 87/90 (97%) - ✅ PASSING
✅ Component already passing
```

### Hypothetical Component (73/90) - Needs fixes ⚠️

```bash
npm run audit -- elements/test --fix --commit

Applied 3 fixes:
🔧 Added attributes parameter
🔧 Replaced 3 hardcoded colors with tokens
🔧 Added tags: ['autodocs']

🔨 Running build validation...
✅ Build passed

TOTAL SCORE: 73/90 → 85/90 ✅

💾 Changes committed
```

## 🚧 Limitations actuelles (v1)

### Non implémenté

- [ ] Remplacement sizes hardcodées (px → tokens)
- [ ] Refactorisation CSS nesting automatique
- [ ] Validation contraste couleurs (WCAG)
- [ ] Vérification cascade order complexe
- [ ] Detection ARIA manquants
- [ ] Audit de tous les composants (`--all` flag)

### Vérifications manuelles requises

- **Architecture** (10 pts) : Dépendances circulaires, hiérarchie atomique
- **Cascade order** (3 pts CSS) : Ordre Base → Modifiers correct
- **Contrast** (2 pts A11y) : Ratio 4.5:1 texte, 3:1 UI
- **ArgTypes categories** (3 pts Story) : 6 catégories complètes

## 🎓 Prochaines versions

### v2 - Corrections CSS avancées

- Remplacement sizes hardcodées
- Detection nesting manquant
- Suggestions cascade order

### v3 - Audit batch

```bash
npm run audit:all -- elements --fix --commit
# Audit tous les atoms avec auto-fix
```

### v4 - CI/CD Integration

- Pre-commit hook
- GitHub Actions workflow
- Audit PR automatique

## 📚 Références

- **Instructions** : `.github/instructions/04-quality-assurance.md`
- **Standards CSS** : `.github/instructions/03-technical-implementation.md`
- **Workflow** : `.github/instructions/02-component-development.md`

## 🤝 Contribution

Pour ajouter une nouvelle correction automatique :

1. Ajouter le type dans `audit.fixes.push({ type: 'new-fix', ... })`
2. Créer fonction `applyNewFix()` dans Phase 2
3. Ajouter switch case dans `applyFixes()`
4. Tester sur composant existant
5. Mettre à jour cette documentation

---

**Auteur** : Design System Team  
**Version** : 1.0.0  
**Date** : 2025-12-13
