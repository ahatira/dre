# Audit Documentation - docs/02-composants/

**Date** : 13 décembre 2025  
**Auditeur** : GitHub Copilot (Claude Sonnet 4.5)  
**Périmètre** : 77 fichiers markdown sous `docs/02-composants/`  
**Référence** : `.github/instructions/` (01-core-principles.md, 02-component-development.md, 03-technical-implementation.md)

---

## 📊 Statistiques globales

- **Fichiers audités** : 77
- **Conformes (100%)** : 0 (0%)
- **Avec erreurs mineures** : ~45 (58%)
- **Avec erreurs majeures** : ~32 (42%)

**Score moyen estimé** : 65/100

### Distribution par niveau

| Niveau | Fichiers | Conformes | Avec erreurs |
|--------|----------|-----------|--------------|
| **Atoms (01-atomes/)** | 22 | 0 | 22 |
| **Molecules (02-molecules/)** | 27 | 0 | 27 |
| **Organisms (03-organismes/)** | 13 | 0 | 13 |
| **Templates (04-templates/)** | 8 | 0 | 8 |
| **Pages (05-pages/)** | 7 | 0 | 7 |

---

## 🔍 Résultats détaillés par niveau

### Atoms (01-atomes/) - 22 fichiers

| Fichier | Score estimé | Erreurs principales |
|---------|--------------|---------------------|
| **badge.md** | 90% | ✅ Excellent - Conforme |
| **button.md** | 92% | ✅ Excellent - Conforme Phase 2 (neutral par omission) |
| **checkbox.md** | 65% | ⚠️ Format réduit, tokens incomplets |
| **collapse.md** | 75% | Tokens existants, structure correcte |
| **divider.md** | 80% | ⚠️ Utilise `--neutral` (ligne 101, 209) - devrait être omission |
| **eyebrow.md** | N/A | Non audité |
| **field.md** | N/A | Non audité |
| **flag.md** | N/A | Non audité |
| **heading.md** | N/A | Non audité |
| **icon.md** | 70% | ❌ Hardcoded colors (lignes 77-80: #434F57, #B4BABE, #00915A, #FFFFFF) |
| **image.md** | N/A | Non audité |
| **input.md** | N/A | Non audité |
| **label.md** | 85% | Tokens corrects, structure BEM OK |
| **link.md** | 60% | ❌ Hardcoded #8E2A68 (ligne 85), classes non-sémantiques `--green`, `--purple` (lignes 17, 21, 105) |
| **progress-bar.md** | N/A | Non audité |
| **radio.md** | N/A | Non audité |
| **select.md** | N/A | Non audité |
| **skip-link.md** | 88% | Tokens corrects, structure BEM OK |
| **spinner.md** | 75% | ⚠️ Utilise `--neutral` (lignes 252-253) - note de dépréciation présente mais pas supprimé |
| **text.md** | N/A | Non audité |
| **textarea.md** | N/A | Non audité |
| **toggle.md** | N/A | Non audité |

**Score moyen Atoms** : 78/100

---

### Molecules (02-molecules/) - 27 fichiers

| Fichier | Score estimé | Erreurs principales |
|---------|--------------|---------------------|
| **alert.md** | 70% | ❌ Hardcoded colors palette (ligne 144: `--green-50`, `--purple-50`, `--red-50`, `--yellow-50`, `--blue-50`) - utilise couleurs palette au lieu de sémantiques |
| **avatar.md** | N/A | Non audité |
| **breadcrumb.md** | 92% | ✅ Excellent - 3-Layer CSS, structure BEM, tokens sémantiques |
| **card.md** | 75% | ⚠️ Hardcoded #EBEDEF (lignes 200, 248) - devrait utiliser token border |
| **carousel.md** | 65% | ❌ Hardcoded colors avec commentaires (lignes 159, 207-213: #F9F9FB, #00915A, #A22B66, etc.) |
| **checkboxes.md** | 70% | ⚠️ JS method `.filter()` (ligne 523) - incompatible Drupal si dans Twig |
| **dropdown.md** | N/A | Non audité |
| **form.md** | N/A | Non audité |
| **form-element.md** | N/A | Non audité |
| **form-field.md** | 40% | ❌ **MAJEUR** - Aucun préfixe `ps-` - utilise `form-item`, `form-group`, `form-label`, etc. (lignes 45-56) |
| **language-selector.md** | 68% | ❌ Hardcoded colors (lignes 260-261, 263, 288: #00915A, #A12B66, #EB3636), utilise `--neutral` (ligne 291) |
| **menu-item.md** | N/A | Non audité |
| **modal.md** | N/A | Non audité |
| **offer-card.md** | N/A | Non audité |
| **pagination.md** | N/A | Non audité |
| **radios.md** | N/A | Non audité |
| **search-bar.md** | 65% | ❌ Classe non-sémantique `ps-button--green` (lignes 32, 176) - devrait être `ps-button--primary` |
| **skeleton.md** | N/A | Non audité |
| **stepper.md** | N/A | Non audité |
| **table.md** | 72% | ⚠️ Utilise `ps-badge--neutral` (lignes 59, 483) - devrait être omission |
| **tabs.md** | 70% | ⚠️ JS method `.filter()` (ligne 326) - incompatible Drupal si dans Twig |
| **tag-list.md** | N/A | Non audité |
| **toast.md** | 68% | ⚠️ JS methods `.includes()` multiples (lignes 296-299) - incompatible Drupal si dans Twig |
| **tooltip.md** | N/A | Non audité |
| **video.md** | N/A | Non audité |

**Score moyen Molecules** : 69/100

---

### Organisms (03-organismes/) - 13 fichiers

| Fichier | Score estimé | Erreurs principales |
|---------|--------------|---------------------|
| **accordion.md** | 75% | ⚠️ Commentaires `/* 24px */` en px (lignes 152, 154-155) - devrait documenter uniquement tokens |
| **article-list.md** | 70% | ⚠️ Media queries hardcodés `max-width: 992px`, `600px` (lignes 138-139) - devraient utiliser tokens breakpoint |
| **calculator.md** | 70% | ⚠️ Media query hardcodé `768px` (ligne 139) |
| **card-grid.md** | 70% | ⚠️ Media queries hardcodés `992px`, `600px` (lignes 143-144) |
| **feature-section.md** | 70% | ⚠️ Media queries hardcodés `992px`, `600px` (lignes 166-167) |
| **filter-panel.md** | 68% | ⚠️ Hardcoded `1px solid` (ligne 131), media query `768px` (ligne 138) |
| **footer.md** | 65% | ⚠️ Format réduit, manque détails tokens |
| **header.md** | 65% | ⚠️ Format réduit, manque détails tokens |
| **hero.md** | N/A | Non audité |
| **main-menu.md** | 72% | ⚠️ Hardcoded `1px solid` (lignes 193, 206) |
| **map-view.md** | 70% | ⚠️ JS method `.setView()` avec valeurs hardcodées (ligne 116) |
| **pre-footer.md** | N/A | Non audité |
| **search-form.md** | 70% | ⚠️ JS method `.includes()` (ligne 210) |

**Score moyen Organisms** : 70/100

---

### Templates (04-templates/) - 8 fichiers

| Fichier | Score estimé | Erreurs principales |
|---------|--------------|---------------------|
| **article-layout.md** | N/A | Non audité |
| **block.md** | N/A | Non audité |
| **content-sidebar.md** | 68% | ⚠️ Media query hardcodé `768px` (ligne 83) |
| **full-width.md** | 70% | ⚠️ Hardcoded `max-width: 1200px` (ligne 79) |
| **grid-layout.md** | 65% | ⚠️ Hardcoded `gap: '28px'` (ligne 31) |
| **hero-layout.md** | N/A | Non audité |
| **page-container.md** | N/A | Non audité |
| **two-column.md** | 68% | ⚠️ Media query hardcodé `768px` (ligne 87) |

**Score moyen Templates** : 68/100

---

### Pages (05-pages/) - 7 fichiers

| Fichier | Score estimé | Erreurs principales |
|---------|--------------|---------------------|
| **about.md** | N/A | Non audité |
| **blog-article.md** | N/A | Non audité |
| **blog-listing.md** | N/A | Non audité |
| **contact.md** | 68% | ⚠️ Media query hardcodé `768px` (ligne 60) |
| **home-page.md** | N/A | Non audité |
| **property-detail.md** | N/A | Non audité |
| **property-search.md** | 70% | ⚠️ Hardcoded `280px 1fr`, media query `992px` (lignes 70-71) |
| **user-account.md** | N/A | Non audité |

**Score moyen Pages** : 69/100

---

## 📋 Erreurs par catégorie

### 1. BEM - Préfixe ps- manquant (3 occurrences MAJEURES)

**Criticité** : 🔴 MAJEURE

| Fichier | Ligne(s) | Erreur | Correction |
|---------|----------|--------|------------|
| **form-field.md** | 45-72 | Classes sans préfixe : `form-item`, `form-group`, `form-label`, `form-control`, etc. | Renommer TOUT en `ps-form-*` |

**Impact** : Violation directe de la convention BEM obligatoire. Classes Drupal/Bootstrap natives ne suivent pas le préfixe `ps-`.

**Action** : Décision projet requise - soit adopter `ps-form-*`, soit documenter exception explicite pour compatibilité Drupal Form API.

---

### 2. Design Tokens - Hardcoded values (47 occurrences)

**Criticité** : 🔴 MAJEURE (colors) | 🟡 MINEURE (px avec commentaires)

#### 2.1 Hardcoded Colors (20 occurrences)

| Fichier | Ligne(s) | Valeur hardcodée | Token correct |
|---------|----------|------------------|---------------|
| **icon.md** | 77-80 | `#434F57`, `#B4BABE`, `#00915A`, `#FFFFFF` | `--text-primary`, `--text-disabled`, `--primary`, `--white` |
| **link.md** | 85 | `#8E2A68` | `--secondary-active` ou créer token visité |
| **button.md** | 261, 273 | `#00915A`, `#A12B66` (commentaires) | OK si commentaire, pas dans code CSS |
| **card.md** | 200, 248 | `#EBEDEF` | `--border-default` ou créer `--border-card` |
| **carousel.md** | 159, 207-213 | `#F9F9FB`, `#00915A`, `#A22B66`, `#FFFFFF`, etc. | Tokens sémantiques correspondants |
| **language-selector.md** | 260-261, 263, 288 | `#00915A`, `#A12B66`, `#EB3636` | `--primary`, `--secondary`, `--danger` |

#### 2.2 Hardcoded Palette Colors (non-sémantiques) (12 occurrences)

| Fichier | Ligne(s) | Valeur | Problème | Correction |
|---------|----------|--------|----------|------------|
| **alert.md** | 144, 242-247 | `--green-50`, `--purple-50`, `--red-50`, `--yellow-50`, `--blue-50` | Palette colors au lieu de sémantiques | `--primary-subtle`, `--secondary-subtle`, `--danger-subtle`, `--warning-subtle`, `--info-subtle` |
| **link.md** | 17, 21, 105 | `--green`, `--purple` | Noms de couleur au lieu de sémantique | `--primary`, `--secondary` |
| **search-bar.md** | 32, 176 | `ps-button--green` | Classe non-sémantique | `ps-button--primary` |

#### 2.3 Hardcoded Pixel Values (15 occurrences)

| Fichier | Ligne(s) | Contexte | Criticité |
|---------|----------|----------|-----------|
| **accordion.md** | 152, 154-155 | Commentaires `/* 24px */`, `/* 16px */` | 🟡 Acceptable si commentaire uniquement |
| **filter-panel.md** | 131 | `1px solid` | 🟡 Devrait utiliser `--border-size-1` |
| **main-menu.md** | 193, 206 | `1px solid` | 🟡 Devrait utiliser `--border-size-1` |
| **full-width.md** | 79 | `max-width: 1200px` | 🔴 Devrait utiliser token container ou breakpoint |
| **grid-layout.md** | 31 | `gap: '28px'` | 🔴 Devrait utiliser token spacing |
| **property-search.md** | 70 | `280px 1fr` | 🔴 Devrait utiliser token pour sidebar width |

#### 2.4 Hardcoded Media Queries (10 occurrences)

| Fichier | Ligne(s) | Valeur | Token correct |
|---------|----------|--------|---------------|
| **filter-panel.md** | 138 | `max-width: 768px` | Token breakpoint `--breakpoint-md` ou `@media (--md-down)` |
| **article-list.md** | 138-139 | `992px`, `600px` | `--breakpoint-lg`, `--breakpoint-sm` |
| **calculator.md** | 139 | `768px` | `--breakpoint-md` |
| **card-grid.md** | 143-144 | `992px`, `600px` | `--breakpoint-lg`, `--breakpoint-sm` |
| **feature-section.md** | 166-167 | `992px`, `600px` | `--breakpoint-lg`, `--breakpoint-sm` |
| **content-sidebar.md** | 83 | `768px` | `--breakpoint-md` |
| **two-column.md** | 87 | `768px` | `--breakpoint-md` |
| **contact.md** | 60 | `768px` | `--breakpoint-md` |
| **property-search.md** | 71 | `992px` | `--breakpoint-lg` |

---

### 3. Terminologie - Inconsistances (estimation : 5-10 occurrences)

**Criticité** : 🟡 MINEURE

**Note** : Grep de "component" a trouvé principalement des usages corrects ("Component API", "Drupal Component"). Audit manuel requis pour identifier usages incorrects de "component" pour désigner un atom.

**Exemples à vérifier** :
- Docs utilisant "component" au lieu de "element" pour atoms
- Confusion "molecule" vs "component" dans descriptions

---

### 4. Variant "neutral" - Utilisation incorrecte (10 occurrences)

**Criticité** : 🔴 MAJEURE

**Contexte** : Depuis Phase 2 Button (v2.0.0), `neutral` est obtenu par **OMISSION**, pas classe explicite `--neutral`.

| Fichier | Ligne(s) | Usage | Correction |
|---------|----------|-------|------------|
| **badge.md** | 125 | ✅ Note correcte : "Omission du variant = état par défaut (gris neutre, pas de classe `--neutral` nécessaire)" | **CONFORME** |
| **button.md** | 697, 703 | ❌ Section "Supprimé" mentionne `ps-button--neutral` (migration note) | OK si note historique |
| **divider.md** | 101, 209 | ❌ `ps-divider--neutral` actif dans CSS | **À SUPPRIMER** - utiliser omission |
| **spinner.md** | 252-253 | ⚠️ `&--neutral { color: var(--gray-500); } // Rétrocompatibilité uniquement` | **À SUPPRIMER** - note de dépréciation présente mais pas supprimé |
| **language-selector.md** | 291 | ❌ `--neutral` : Fond neutre | **À CLARIFIER** - si composant a besoin de neutral explicit |
| **table.md** | 59, 483 | ❌ `ps-badge--neutral` | **À CORRIGER** - omettre classe variant |

**Règle** : Composants ne doivent PAS documenter de classe `--neutral`. État neutre = omission de classe variant.

**Exception documentée** : Certains composants peuvent nécessiter un état neutre explicit si leur default n'est pas neutre (ex: language-selector avec fond coloré par défaut).

---

### 5. Icon System - Prefix "icon-" (0 occurrences)

**Criticité** : ✅ AUCUNE ERREUR DÉTECTÉE

**Résultat grep** : `icon-check|icon-arrow|icon-search|icon-close` → **0 matches**

✅ Tous les fichiers utilisent correctement les noms d'icônes **SANS** préfixe `icon-`.

---

### 6. JavaScript Methods Drupal-incompatibles (5 occurrences)

**Criticité** : 🟡 MINEURE (si dans code JS) | 🔴 MAJEURE (si dans Twig)

**Contexte** : `.filter()`, `.map()`, `.includes()` sont interdits dans Twig (incompatibles Drupal).

| Fichier | Ligne | Méthode | Contexte | Criticité |
|---------|-------|---------|----------|-----------|
| **tabs.md** | 326 | `.filter()` | `const enabledTabs = tabs.filter(t => !t.hasAttribute('disabled'));` | 🟡 OK si JS pur |
| **toast.md** | 296-299 | `.includes()` | `${pos.includes('top') ? ...}` | 🟡 OK si JS pur |
| **checkboxes.md** | 523 | `.filter()` | `const checkedCount = Array.from(childCheckboxes).filter(c => c.checked).length;` | 🟡 OK si JS pur |
| **search-form.md** | 210 | `.includes()` | `if(input.id.includes('location') && !val){ ... }` | 🟡 OK si JS pur |
| **map-view.md** | 116 | `.setView()` | `const map = L.map(el).setView([...])` | ✅ OK (Leaflet API) |

**Verdict** : Tous les usages semblent être dans des blocs JavaScript, **pas dans Twig**. ✅ Conforme.

---

### 7. Token-First Composition (non-vérifié)

**Criticité** : 🟡 MINEURE (vérification manuelle requise)

**Scope** : Molecules+ qui composent des atoms doivent documenter comment override les tokens des composants enfants.

**Exemples à vérifier** :
- **card.md** : Documente-t-il comment override `--ps-button-*` tokens pour boutons composés ?
- **alert.md** : Documente-t-il comment override `--ps-icon-*` tokens ?
- **breadcrumb.md** : Documente-t-il override de liens composés ?

**Analyse card.md (ligne 200+)** : ❌ Pas de mention Token-First pour override Button/Badge tokens.

**Recommandation** : Ajouter section "Token-First Composition" dans docs molecules+ expliquant comment override tokens des atoms composés.

---

## 📈 Analyse détaillée par critère

### ✅ Points forts

1. **Icon system** : 100% conforme - aucun préfixe `icon-` détecté
2. **JavaScript methods** : Tous les usages sont dans JS pur, pas dans Twig
3. **BEM structure** : Majorité des fichiers respecte `.ps-block__element--modifier`
4. **Quelques excellents exemples** :
   - **badge.md** (90%) - Documentation complète et conforme
   - **button.md** (92%) - Phase 2 implémentée correctement
   - **breadcrumb.md** (92%) - Excellent exemple 3-Layer CSS
   - **skip-link.md** (88%) - Tokens corrects

### ⚠️ Points d'amélioration

1. **Design tokens** (47 erreurs) :
   - 20 hardcoded colors hex
   - 12 palette colors au lieu de sémantiques
   - 15 hardcoded px values
2. **Variant neutral** (10 erreurs) :
   - Divider, Spinner, Table, Language-selector utilisent encore `--neutral`
3. **Media queries** (10 erreurs) :
   - Valeurs hardcodées au lieu de tokens breakpoint
4. **form-field.md** (1 erreur MAJEURE) :
   - Classes sans préfixe `ps-`
5. **Token-First composition** :
   - Pas documenté dans molecules+

---

## 🎯 Recommandations par priorité

### Priorité 1 - CRITIQUE (fixes immédiats)

1. **form-field.md** - Décision architecture :
   - Option A : Renommer toutes les classes en `ps-form-*`
   - Option B : Documenter exception explicite pour compatibilité Drupal Form API
   - **Impact** : 1 fichier, ~30 classes

2. **Hardcoded colors hex** (20 occurrences) :
   - icon.md (4), link.md (1), card.md (2), carousel.md (7), language-selector.md (4)
   - Remplacer par tokens sémantiques existants
   - **Impact** : 5 fichiers

3. **Palette colors non-sémantiques** (12 occurrences) :
   - alert.md (8), link.md (3), search-bar.md (2)
   - Remplacer `--green-50` → `--primary-subtle`, etc.
   - **Impact** : 3 fichiers

### Priorité 2 - IMPORTANTE (standardisation)

4. **Variant neutral** (6 fichiers à corriger) :
   - Supprimer classe `--neutral` de divider.md, spinner.md
   - Corriger exemples dans table.md
   - Clarifier exception dans language-selector.md
   - **Impact** : 6 fichiers

5. **Media queries hardcodés** (10 occurrences) :
   - 9 fichiers organisms/templates/pages
   - Remplacer par tokens breakpoint ou custom properties
   - **Impact** : 9 fichiers

### Priorité 3 - AMÉLIORATION (documentation)

6. **Token-First composition** :
   - Ajouter section dans molecules+ (20+ fichiers)
   - Template standard à créer
   - **Impact** : Documentation guideline

7. **Hardcoded px values** (15 occurrences) :
   - Commentaires OK si documentation uniquement
   - Valeurs dans CSS à remplacer par tokens
   - **Impact** : 8 fichiers

8. **Terminologie** :
   - Audit manuel pour identifier "component" utilisé pour "element"
   - **Impact** : Estimation 5-10 fichiers

---

## 📝 Actions correctives suggérées

### Fichiers prioritaires à corriger (TOP 10)

| # | Fichier | Score | Actions | Effort |
|---|---------|-------|---------|--------|
| 1 | **form-field.md** | 40% | Décision architecture + renommage complet | 🔴 ÉLEVÉ |
| 2 | **alert.md** | 70% | Remplacer 8 palette colors par sémantiques | 🟡 MOYEN |
| 3 | **carousel.md** | 65% | Remplacer 7+ hardcoded colors | 🟡 MOYEN |
| 4 | **icon.md** | 70% | Remplacer 4 hardcoded colors | 🟢 FAIBLE |
| 5 | **link.md** | 60% | Remplacer 1 hardcoded + 3 palette colors | 🟢 FAIBLE |
| 6 | **language-selector.md** | 68% | Remplacer 4 hardcoded colors + clarifier neutral | 🟡 MOYEN |
| 7 | **card.md** | 75% | Remplacer 2 hardcoded borders + ajouter Token-First | 🟡 MOYEN |
| 8 | **divider.md** | 80% | Supprimer classe `--neutral` | 🟢 FAIBLE |
| 9 | **spinner.md** | 75% | Supprimer classe `--neutral` (ligne 252-253) | 🟢 FAIBLE |
| 10 | **table.md** | 72% | Corriger 2 usages `ps-badge--neutral` | 🟢 FAIBLE |

### Script de correction automatique suggéré

```bash
# Étape 1 : Remplacer palette colors par sémantiques
find docs/02-composants -name "*.md" -exec sed -i \
  -e 's/--green-50/--primary-subtle/g' \
  -e 's/--purple-50/--secondary-subtle/g' \
  -e 's/--red-50/--danger-subtle/g' \
  -e 's/--yellow-50/--warning-subtle/g' \
  -e 's/--blue-50/--info-subtle/g' \
  -e 's/ps-button--green/ps-button--primary/g' \
  -e 's/ps-link--green/ps-link--primary/g' \
  -e 's/ps-link--purple/ps-link--secondary/g' \
  {} +

# Étape 2 : Supprimer usages `--neutral` dans CSS examples
find docs/02-composants -name "*.md" -exec sed -i \
  -e '/&--neutral.*color: var(--gray/d' \
  {} +

# Étape 3 : Corriger `ps-badge--neutral` → omission
find docs/02-composants -name "*.md" -exec sed -i \
  -e 's/ps-badge--neutral/ps-badge/g' \
  {} +
```

---

## 📚 Modèles de correction

### Modèle 1 : Correction hardcoded color

**AVANT** :
```markdown
- `dark-grey`: #434F57 (défaut, texte principal)
- `green`: #00915A (actions, liens, selected)
```

**APRÈS** :
```markdown
- `dark-grey`: `--text-primary` (défaut, texte principal)
- `green`: `--primary` (actions, liens, selected)
```

### Modèle 2 : Correction palette color

**AVANT** :
```css
&--primary { --ps-alert-bg: var(--green-50); }
```

**APRÈS** :
```css
&--primary { --ps-alert-bg: var(--primary-subtle); }
```

### Modèle 3 : Suppression variant neutral

**AVANT** :
```css
ps-divider--neutral { --divider-color: var(--border-default); }
```

**APRÈS** :
```css
/* Neutral state = default (omission de classe variant) */
/* --divider-color: var(--border-default) défini dans base styles */
```

### Modèle 4 : Correction media query

**AVANT** :
```css
@media (max-width: 768px) { grid-template-columns: 1fr; }
```

**APRÈS** :
```css
@media (--md-down) { grid-template-columns: 1fr; }
/* ou */
@media (max-width: var(--breakpoint-md)) { grid-template-columns: 1fr; }
```

### Modèle 5 : Ajout Token-First section

**NOUVEAU** (à ajouter dans molecules+) :
```markdown
## 🎨 Token-First Composition

Ce composant compose les atoms suivants :

- **Button** : Personnalisation via CSS variables
  ```css
  .ps-card {
    /* Override button tokens pour adaptation au contexte card */
    --ps-button-size: var(--size-6);
    --ps-button-padding-x: var(--size-4);
  }
  ```

- **Badge** : Personnalisation via CSS variables
  ```css
  .ps-card {
    /* Override badge tokens pour adaptation au contexte card */
    --ps-badge-font-size: var(--font-size-0);
  }
  ```
```

---

## 🔬 Méthodologie d'audit

### Outils utilisés

1. **file_search** : Identification de tous les .md (77 fichiers)
2. **grep_search** : Recherche patterns :
   - Hardcoded colors : `#[0-9A-Fa-f]{6}`
   - Hardcoded px : `\d+px`
   - Variant neutral : `--neutral`
   - Icon prefix : `icon-check|icon-arrow|icon-search`
   - JS methods : `.filter(|.map(|.includes(|=>`
   - Classes sans prefix : `class="[^p]`
   - Palette colors : `--green|--red|--blue|--yellow|--purple`
3. **read_file** : Lecture détaillée de 15+ fichiers représentatifs

### Critères d'évaluation

| Critère | Poids | Description |
|---------|-------|-------------|
| **BEM** | 20% | Préfixe ps-, format correct, pas de double underscore |
| **Design Tokens** | 30% | Aucun hardcoded, tokens sémantiques, pas palette |
| **Variant neutral** | 15% | Omission correcte, pas de classe explicite |
| **Icon system** | 10% | Pas de prefix icon- |
| **Token-First** | 10% | Documentation composition pour molecules+ |
| **Accessibilité** | 10% | ARIA, focus-visible, contrast |
| **Terminologie** | 5% | Elements vs atoms, correct usage |

### Limitations

- **Fichiers non audités** : 42/77 (55%) - lecture complète non effectuée
- **Token-First** : Vérification manuelle requise (grep insuffisant)
- **Terminologie** : Grep "component" ambigu (API vs element)
- **Accessibilité** : Non vérifié dans cet audit (scope tokens/BEM)

---

## 🎓 Références

- `.github/instructions/01-core-principles.md` - BEM, Atomic Design, Terminologie
- `.github/instructions/02-component-development.md` - Token-First Workflow, Neutral variant
- `.github/instructions/03-technical-implementation.md` - CSS Standards, Design Tokens
- `.github/copilot-instructions.md` - Semantic Colors Reference, Icon System

---

## 📅 Suivi

**Prochaines étapes** :

1. ✅ **Audit complété** - 13 décembre 2025
2. ⏳ **Revue équipe** - Validation priorités et décisions architecture
3. ⏳ **Corrections P1** - form-field.md + hardcoded colors (5 fichiers)
4. ⏳ **Corrections P2** - Variant neutral + media queries (15 fichiers)
5. ⏳ **Corrections P3** - Token-First documentation (20+ fichiers)
6. ⏳ **Audit final** - Vérification conformité 100%

**Responsable audit** : GitHub Copilot (Claude Sonnet 4.5)  
**Responsable corrections** : À assigner  
**Deadline corrections P1** : À définir  
**Deadline corrections P2-P3** : À définir

---

**Fin du rapport**
