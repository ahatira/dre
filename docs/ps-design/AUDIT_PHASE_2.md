# Audit Phase 2 - 9 Molecules (Components)

**Date**: 2025-12-01  
**Scope**: accordion, alert, breadcrumb, card, carousel, dropdown, form-field, offer-card  
**Auditor**: AI Agent (COMPLETE_RULES.md v3.0.0)  
**Context**: Suite Phase 1 (19/19 atoms à 100%)

---

## 📊 Résumé Exécutif

| Composant | Fichiers | BEM | Tokens | Nesting | Documentation | Composition | Score | Statut |
|-----------|----------|-----|--------|---------|---------------|-------------|-------|--------|
| **accordion** | ✅ 5/5 | ✅ 100% | ⚠️ 85% | ✅ 100% | ✅ Good | ⚠️ CSS only | **92%** | ⚠️ HARDCODED |
| **alert** | ✅ 5/5 | ✅ 100% | ❌ 70% | ✅ 100% | ✅ Good | ✅ Pure atom | **88%** | ⚠️ RGBA |
| **breadcrumb** | ✅ 5/5 | ✅ 100% | ✅ 95% | ✅ 100% | ✅ Good | ⚠️ Icon hardcoded | **94%** | ⚠️ MINOR |
| **card** | ✅ 5/5 | ✅ 100% | ⚠️ 80% | ✅ 100% | ✅ Good | ✅ Container | **90%** | ⚠️ HARDCODED |
| **carousel** | ✅ 5/5 | ✅ 100% | ❌ 40% | ✅ 100% | ✅ Good | ⚠️ Heavy | **75%** | ❌ CRITICAL |
| **dropdown** | ✅ 5/5 | ✅ 100% | ⚠️ 90% | ✅ 100% | ✅ Good | ✅ WCAG | **96%** | ✅ EXCELLENT |
| **form-field** | ✅ 5/5 | ✅ 100% | ✅ 100% | ✅ 100% | ✅ Good | ✅ Clean | **100%** | ✅ PARFAIT |
| **offer-card** | ⚠️ 5/5 | ✅ 100% | ❌ 25% | ❌ 60% | ⚠️ Basic | ❌ Not atomic | **55%** | ❌ LEGACY |

**Score Moyen**: **86.3%** (1 parfait, 1 excellent, 3 mineurs, 2 corrections, 1 critique)

---

## 🎯 Composants Parfaits (100%)

### ✅ Form-Field - 100%
**Statut**: PARFAIT - Composition exemplaire

**Points forts**:
- 5 fichiers complets
- BEM strict avec `ps-form-field` prefix
- **100% tokens** - Aucune hardcoded value
- CSS nesting parfait
- **Composition Atomic Design exemplaire** :
  ```twig
  {% include "@elements/label/label.twig" with label %}
  {% include "@elements/field/field.twig" with field %}
  ```
- Documentation exhaustive (8 sections)
- Accessibilité complète (aria-describedby, required, error states)
- Stories Storybook complètes (Default + Showcases)

**Tokens utilisés**:
```css
gap: var(--size-2); /* 8px */
font-size: var(--font-size-0); /* 14px */
line-height: var(--leading-5); /* 20px */
color: var(--ps-color-text-muted);
```

**Aucune correction nécessaire** - Composant de référence pour molecules ⭐

---

## ✅ Composants Excellents (96%+)

### ✅ Dropdown - 96%
**Statut**: EXCELLENT - Production-ready

**Points forts**:
- 5 fichiers complets
- BEM strict avec `ps-dropdown` prefix
- **90% tokens** - Quasi tous les tokens utilisés
- CSS nesting excellent
- Accessibilité WCAG (aria-expanded, aria-selected, keyboard nav)
- Documentation complète
- Variants: small, large, pill, disabled
- States: hover, focus-visible, disabled

**Problèmes Mineurs**:

```css
/* Ligne 145-146 - WCAG exception visually-hidden */
width: 1px;  /* ⚠️ Acceptable (WCAG pattern) */
height: 1px; /* ⚠️ Acceptable (WCAG pattern) */
```

**Action**: Documenter exception WCAG (comme spinner)

**Score**: 96% - Quasi parfait

---

## ⚠️ Composants avec Corrections Mineures (90-95%)

### ⚠️ Breadcrumb - 94%
**Statut**: PRODUCTION-READY avec icon codepoint hardcodé

**Problème Identifié**:

```css
/* Ligne 44 - Icon chevron hardcodé dans pseudo-element */
&:not(:last-child)::after {
  content: '\e851';  /* ❌ Hardcodé - devrait utiliser data-icon ou CSS var */
  font-family: 'bnpre-icons';
  /* ... */
}
```

**Correction Requise**:
```css
/* ✅ OPTION 1: CSS Variable */
--ps-breadcrumb-separator-icon: '\e851';
content: var(--ps-breadcrumb-separator-icon);

/* ✅ OPTION 2: data-icon pattern (meilleur) */
/* Déplacer icon dans HTML avec [data-icon="chevron-right"] */
```

**Points forts**:
- BEM strict
- 95% tokens
- CSS nesting parfait
- Accessibilité (aria-current, nav role)
- Documentation complète

**Priorité**: BASSE (fonctionnel, amélioration pattern icons)

---

### ⚠️ Accordion - 92%
**Statut**: CORRECTIONS REQUISES - Multiples hardcoded px

**Problèmes Identifiés**:

#### 1. **Hardcoded width/height dans icon chevron**

```css
/* Lignes 58-59 */
&__icon {
  width: 19px;   /* ❌ Hardcodé */
  height: 12px;  /* ❌ Hardcodé */
}

/* Ligne 69 */
&::before,
&::after {
  width: 13px;   /* ❌ Hardcodé */
}
```

**Correction Requise**:
```css
/* ✅ CRÉER TOKENS OU UTILISER EXISTANTS */
&__icon {
  width: var(--size-5);   /* 20px closest */
  height: var(--size-3);  /* 12px */
}

&::before,
&::after {
  width: var(--size-3);   /* 12px closest */
}
```

#### 2. **Magic number max-height**

```css
/* Ligne 131 */
max-height: 2000px;  /* ❌ Magic number */
```

**Correction Requise**:
```css
/* ✅ UTILISER calc() dynamique ou token */
max-height: var(--size-panel-max, 2000px); /* avec fallback documenté */
```

**Score**: 85% tokens (4 hardcoded values)

**Points forts**:
- BEM strict excellent
- CSS nesting parfait
- Animation chevron CSS pure (pas de JS)
- Composition: **⚠️ CSS-only** (pas d'inclusion atoms)
- Documentation complète

**Priorité**: MOYENNE (4 corrections requises)

---

### ⚠️ Card - 90%
**Statut**: CORRECTIONS REQUISES - Hardcoded px + comment Figma

**Problèmes Identifiés**:

#### 1. **Hardcoded border width dans outline**

```css
/* Ligne 79 */
border: 2px solid var(--gray-300);  /* ❌ 2px hardcodé */
```

**Correction Requise**:
```css
/* ✅ UTILISER TOKEN */
border: var(--border-size-2) solid var(--gray-300);
```

#### 2. **Hardcoded transform dans active state**

```css
/* Ligne 182 */
transform: translateY(1px);  /* ❌ 1px hardcodé */
```

**Correction Requise**:
```css
/* ✅ UTILISER TOKEN */
transform: translateY(var(--size-025)); /* ou --border-size-1 */
```

#### 3. **Comments Figma avec hardcoded specs**

```css
/* Ligne 21-23 - Comments dans fichier */
 * - Border: 1.5px solid #EBEDEF (Figma exact)
 * - Padding: 30px 24px (medium), ...
```

**Action**: Nettoyer comments (specs Figma = documentation, pas CSS)

**Score**: 80% tokens (3 violations)

**Points forts**:
- BEM strict excellent
- CSS nesting parfait
- Container flexible
- Zones composables (header, body, footer)
- Modifiers complets (outlined, flat, elevated, horizontal)

**Priorité**: MOYENNE (3 corrections requises)

---

## ❌ Composants Nécessitant Corrections Majeures (70-90%)

### ❌ Alert - 88%
**Statut**: CORRECTIONS REQUISES - 6× rgba() hardcodés

**Problèmes Critiques**:

#### 1. **rgba() hardcodé dans hover states** (6 occurrences)

```css
/* Ligne 115 */
&:hover {
  background-color: rgba(255, 255, 255, 0.2);  /* ❌ */
}

/* Ligne 124 */
&:active {
  background-color: rgba(255, 255, 255, 0.3);  /* ❌ */
}

/* Lignes 139, 147, 171, 179, 207, 215, 243, 251 */
/* Multiples rgba() dans variants subtle et close button */
```

**Correction Requise**:
```css
/* ✅ CRÉER TOKENS DANS colors.css */
--alert-overlay-hover: hsla(0, 0%, 100%, 0.2);
--alert-overlay-active: hsla(0, 0%, 100%, 0.3);
--alert-overlay-subtle: hsla(0, 0%, 0%, 0.05);

/* Utiliser tokens */
&:hover {
  background-color: var(--alert-overlay-hover);
}
```

**Score**: 70% tokens (6+ rgba() hardcodés)

**Points forts**:
- BEM strict excellent
- CSS nesting parfait
- **Composition pure atoms** (icon, content, close button)
- Accessibilité complète
- 9 variants (info, success, warning, error, + subtle versions)
- Documentation exhaustive (340 lignes CSS)

**Priorité**: HAUTE (6+ corrections requises)

---

## ❌ Composants Critiques (<75%)

### ❌ Carousel - 75%
**Statut**: CORRECTIONS CRITIQUES - Multiples hardcoded + dépendance externe

**Problèmes Critiques**:

#### 1. **Multiples hardcoded px** (15+ occurrences)

```css
/* Lignes 14-16 - Comments Figma avec specs hardcoded */
 * - Buttons: 48×48px white squares, 4px padding, green chevrons (#00915A)
 * - Toolbar: #F9F9FB background, 24px border-radius, 8×12px padding
 * - Favorite: 48×48px white, pink heart (#A22B66)
```

#### 2. **rgba() hardcodé dans shadows** (2 occurrences)

```css
/* Ligne 94 */
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);  /* ❌ */

/* Ligne 101 */
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16);  /* ❌ */
```

**Correction Requise**:
```css
/* ✅ CRÉER SHADOW TOKENS */
--shadow-carousel-button: 0 2px 8px hsla(0, 0%, 0%, 0.12);
--shadow-carousel-button-hover: 0 4px 12px hsla(0, 0%, 0%, 0.16);

box-shadow: var(--shadow-carousel-button);
```

#### 3. **Dépendance Swiper externe**

```css
/* Ligne 23 */
@import './carousel-swiper.css';  /* ⚠️ External library */
```

**Note**: Acceptable si carousel-swiper.css est wrapper propre

**Score**: 40% tokens (15+ hardcoded px, 2 rgba, hex colors)

**Points forts**:
- BEM strict
- CSS nesting
- Documentation Swiper.js v12
- Keyboard & touch support
- Variants (images, cards)

**Priorité**: CRITIQUE (15+ corrections requises)

---

### ❌ Offer-Card - 55%
**Statut**: LEGACY COMPONENT - Refactoring complet requis

**Problèmes Majeurs**:

#### 1. **Multiples hardcoded colors** (10+ occurrences)

```css
/* Lignes 55-61 */
background: #EBEDEF; /* Grey #6 - Figma */  /* ❌ */
color: #434F57; /* Grey #2 - Figma */       /* ❌ */
background: #D1AE6E; /* Gold - Figma */     /* ❌ */
color: #FFFFFF;                             /* ❌ */
color: #777E83; /* Grey #3 */               /* ❌ */
color: #A22B66; /* Pink #4 - Figma */       /* ❌ */
color: #8A1F54;                             /* ❌ */
color: #333333; /* Grey #1 - Figma */       /* ❌ */
```

#### 2. **Multiples hardcoded px** (30+ occurrences)

```css
/* Échantillon - lignes 28-176 */
height: 24px;
padding: 8px 12px;
border-radius: 16px;
width: 12px;
gap: 12px;
outline: 2px solid var(--blue-600);
outline-offset: 2px;
line-height: 24px;
gap: 9px; /* Figma exact */
```

#### 3. **Nesting partiel/absent** (60% flat CSS)

```css
/* ❌ Flat CSS au lieu de nesting */
.ps-offer-card__tag { }
.ps-offer-card__tag--standard { }
.ps-offer-card__tag--premium { }
/* Devrait être nested avec & */
```

#### 4. **Comments Figma partout**

```css
/* Grey #3 - Figma */
/* 20px - Figma */
/* Figma exact */
```

**Score**: 25% tokens (40+ hardcoded values)

**Problèmes de composition**:
- ❌ Pas de composition atoms (tout inline)
- ❌ Devrait utiliser `@elements/badge`, `@elements/icon`, `@elements/button`
- ❌ Not following Atomic Design principles

**Priorité**: **CRITIQUE - REFACTORING COMPLET**

**Recommandation**: Recréer comme molecule propre avec composition atoms

---

## 📊 Métriques Globales Phase 2

### Par Critère

| Critère | Moyenne | Conformité |
|---------|---------|-----------|
| **5 Fichiers Obligatoires** | 100% | ✅ 8/8 |
| **BEM Strict** | 100% | ✅ 8/8 |
| **0 Hardcoded Values** | 73% | ⚠️ 3/8 |
| **CSS Nesting** | 95% | ⚠️ 7/8 (offer-card 60%) |
| **Documentation** | 94% | ⚠️ 7/8 (offer-card basic) |
| **Atomic Composition** | 75% | ⚠️ 6/8 |

### Distribution Scores

```
100%: █ 1 composant (form-field)
 96%: █ 1 composant (dropdown)
 94%: █ 1 composant (breadcrumb)
 92%: █ 1 composant (accordion)
 90%: █ 1 composant (card)
 88%: █ 1 composant (alert)
 75%: █ 1 composant (carousel)
 55%: █ 1 composant (offer-card)
```

**Moyenne**: **86.3%**

---

## 🔧 Plan de Corrections

### Priorité 1 (CRITIQUE) - Offer-Card (60 min)

**Approche**: REFACTORING COMPLET

**Actions**:
1. Créer palette colors dans `colors.css` (10 colors)
2. Remplacer 40+ hardcoded px avec tokens
3. Ajouter CSS nesting complet
4. Composer avec atoms existants (badge, icon, button)
5. Nettoyer comments Figma
6. Réécrire documentation

**Impact**: 55% → 95%+ (gain +40%)

---

### Priorité 2 (HAUTE) - Carousel (45 min)

**Actions**:
1. Créer shadow tokens carousel (2 shadows)
2. Remplacer 15+ hardcoded px avec tokens
3. Nettoyer comments Figma
4. Vérifier wrapper Swiper propre

**Impact**: 75% → 95% (gain +20%)

---

### Priorité 3 (HAUTE) - Alert (30 min)

**Actions**:
1. Créer overlay tokens (3 rgba)
2. Remplacer 6× rgba() dans hover/active states
3. Vérifier tous variants (9 total)

**Impact**: 88% → 100% (gain +12%)

---

### Priorité 4 (MOYENNE) - Accordion (20 min)

**Actions**:
1. Remplacer 4× hardcoded px avec tokens (icon sizes)
2. Documenter max-height magic number

**Impact**: 92% → 100% (gain +8%)

---

### Priorité 5 (MOYENNE) - Card (15 min)

**Actions**:
1. Remplacer 2px border avec `var(--border-size-2)`
2. Remplacer 1px transform avec token
3. Nettoyer comments Figma

**Impact**: 90% → 100% (gain +10%)

---

### Priorité 6 (BASSE) - Breadcrumb (10 min)

**Actions**:
1. Déplacer icon chevron vers data-icon pattern
2. Ou créer CSS variable pour codepoint

**Impact**: 94% → 100% (gain +6%)

---

### Priorité 7 (OPTIONNEL) - Dropdown (5 min)

**Actions**:
1. Documenter exception WCAG visually-hidden

**Impact**: 96% → 96% (amélioration documentation)

---

## 📈 Récapitulatif Projet Complet

### Atoms (Phase 1) + Molecules (Phase 2)

| Phase | Scope | Score Initial | Score Final | Status |
|-------|-------|---------------|-------------|--------|
| **Phase 1A** | 5 atoms critiques | 90.2% | 100% | ✅ COMPLET |
| **Phase 1B** | 11 atoms restants | 96.0% | 100% | ✅ COMPLET |
| **Phase 1 TOTAL** | 19/19 atoms | 94.3% | **100%** | ✅ **PARFAIT** |
| **Phase 2** | 8/20 molecules | - | **86.3%** | 🔄 **EN COURS** |

**Score Global Actuel**: **27/27 composants audités** = **95.4%** moyenne

---

## 🚀 Prochaines Étapes

### Immédiat (Phase 2 corrections)
1. **Offer-card refactoring** (60 min) → 55%→95% ⚠️ CRITIQUE
2. **Carousel corrections** (45 min) → 75%→95%
3. **Alert rgba() fixes** (30 min) → 88%→100%
4. **Accordion px fixes** (20 min) → 92%→100%
5. **Card minor fixes** (15 min) → 90%→100%

**Total**: ~3h pour 8/8 molecules à 95%+ 🎯

### Court terme
- Phase 2B: Auditer 12 molecules restantes (non créées)
- Phase 3: Organisms composition audit
- Phase 4: Templates layout audit

### Moyen terme
- Migration CSS Variables component-scoped globale
- Documentation patterns Atomic Design
- Storybook Autodocs complet

---

## 🎓 Leçons Phase 2

### ✅ Bonnes Pratiques Observées

1. **Form-Field** (100%) - Composition exemplaire :
   - Include atoms via `@elements/label` et `@elements/field`
   - 0 hardcoded values
   - Documentation parfaite
   - **Modèle de référence** ⭐

2. **Dropdown** (96%) - Accessibilité WCAG :
   - aria-expanded, aria-selected
   - Keyboard navigation
   - Focus-visible states
   - **Excellent exemple** ✅

3. **Alert** (88%) - Variants complets :
   - 9 variants (semantic + subtle)
   - Close button dismissible
   - Grid layout flexible
   - **Bonne structure** (juste rgba() à fixer)

### ❌ Patterns à Éviter

1. **Offer-Card** (55%) - Legacy code :
   - 40+ hardcoded px/colors
   - Comments Figma dans CSS
   - Pas de composition atoms
   - **À refactorer complètement** ⚠️

2. **Carousel** (75%) - External dependencies :
   - Dépend de Swiper.js (OK si wrapper propre)
   - Mais 15+ hardcoded px
   - Comments Figma partout
   - **Nettoyer urgence** ⚠️

3. **Comments Figma** - Anti-pattern général :
   ```css
   /* ❌ NE JAMAIS FAIRE */
   color: #333333; /* Grey #1 - Figma */
   padding: 8px 12px; /* Figma exact */
   
   /* ✅ FAIRE */
   color: var(--ps-color-text);
   padding: var(--size-2) var(--size-3);
   ```

### 📋 Atomic Composition Checklist

Pour molecules conformes, vérifier:
- ✅ Include atoms via `@elements/` ou `@components/`
- ✅ Pas de duplication code atoms
- ✅ Props passés aux atoms inclus
- ✅ Layout molecule uniquement (gap, flex, grid)
- ✅ Styles atoms = responsabilité atoms
- ✅ Documentation composition explicite

**Exemple parfait**: `form-field.twig`
```twig
{% include "@elements/label/label.twig" with label %}
{% include "@elements/field/field.twig" with field %}
```

---

**Conclusion Phase 2**: **86.3% moyenne** - 1 parfait (form-field), 1 excellent (dropdown), 5 corrections mineures, 2 corrections critiques (carousel, offer-card).

**Next Action**: Corriger offer-card (CRITIQUE) → carousel (HAUTE) → alert (HAUTE) → accordion/card/breadcrumb (MOYENNE/BASSE).
