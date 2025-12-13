# Analyse d'Incohérences - Documentation Composants

**Date** : 13 décembre 2025  
**Dernière mise à jour** : 13 décembre 2025 - Phase 1 complétée  
**Scope** : Tous les fichiers `docs/02-composants/**/*.md`  
**Objectif** : Identifier et corriger toutes incohérences logiques, erreurs de conception, et violations des standards

**Statut** : Phase 1 complétée (7/13 issues résolues - 54%) - Commit 1001e77

---

## ✅ Problèmes Critiques RÉSOLUS (Phase 1)

### 1. **Badge : enum manquant `gold` dans YAML** ✅ RÉSOLU

**Fichier** : `docs/02-composants/01-atomes/badge.md`  
**Ligne** : 94-95  
**Status** : ✅ Corrigé dans commit 1001e77

**Problème** :
```yaml
# AVANT
enum: ['primary','secondary','info','success','warning','error','neutral']
```

**Solution appliquée** :
```yaml
# APRÈS
enum: ['primary','secondary','info','success','warning','danger','gold','neutral']
```

---

### 2. **Badge : `error` vs `danger` - incohérence terminologie** ✅ RÉSOLU

**Fichier** : `docs/02-composants/01-atomes/badge.md`  
**Status** : ✅ Corrigé dans commit 1001e77

**Solution** : Standardisé sur `danger` partout (YAML enum, descriptions, cohérence avec brand.css tokens)

---

### 3. **Badge : token --neutral n'existe pas** ✅ RÉSOLU

**Fichier** : `docs/02-composants/01-atomes/badge.md`  
**Lignes** : 139 (Design Tokens section), 256 (CSS)  
**Status** : ✅ Corrigé dans commit 1001e77

**Solution appliquée** :
- Design Tokens : Remplacé `--neutral`, `--neutral-hover`, `--neutral-text` par `--gray-200`, `--gray-600`, `--border-default`
- CSS : Remplacé `--gray-200/600` par tokens sémantiques `--light` + `--text-secondary`

---

### 4. **Badge : tokens palette directs au lieu de sémantiques** ✅ RÉSOLU

**Fichier** : `docs/02-composants/01-atomes/badge.md`  
**Lignes** : 256-263 (CSS variants)  
**Status** : ✅ Corrigé dans commit 1001e77

**Solution appliquée** : Tous les variants utilisent maintenant tokens sémantiques :
```scss
// APRÈS
&--default { --badge-bg: var(--light); --badge-color: var(--text-secondary); }
&--primary { --badge-bg: var(--primary-subtle); --badge-color: var(--primary-text-emphasis); }
// ... tous les autres utilisent -subtle + -text-emphasis
```

---

### 5. **Link : utilise noms de couleurs au lieu de variants sémantiques** ✅ RÉSOLU

**Fichier** : `docs/02-composants/01-atomes/link.md`  
**Ligne** : 35  
**Status** : ✅ Corrigé dans commit 1001e77

**Solution appliquée** :
```yaml
# AVANT
color: { type: string, enum: ['green','purple','white','default'], default: 'green' }

# APRÈS
variant: { type: string, enum: ['primary','secondary','neutral','inverse'], default: 'primary' }
```

---

### 6. **Eyebrow : token 'accent' n'existe pas** ✅ RÉSOLU

**Fichier** : `docs/02-composants/01-atomes/eyebrow.md`  
**Lignes** : 50, 83, 116, 130, 268  
**Status** : ✅ Corrigé dans commit 1001e77

**Solution appliquée** : Remplacé 'accent' par 'info' partout (enum YAML, BEM, descriptions, tokens, exemples)

---

### 7. **TagList : token 'accent' + 'error' incohérents** ✅ RÉSOLU

**Fichier** : `docs/02-composants/02-molecules/tag-list.md`  
**Lignes** : 103, 356, 371  
**Status** : ✅ Corrigé dans commit 1001e77

**Solution appliquée** :
- Enum YAML : 'accent' → 'info', 'error' → 'danger'
- Exemples : variant 'accent' → 'info'

---

## 🚨 Problèmes Critiques RESTANTS (Phase 2)

### 1. **Badge : enum manquant `gold` dans YAML**

**Fichier** : `docs/02-composants/01-atomes/badge.md`  
**Ligne** : 94-95

**Problème** :
```yaml
variant:
  type: string
  enum: ['primary','secondary','info','success','warning','error','neutral']
  default: 'neutral'
```

**Impact** : Le YAML Component schema indique 7 variants alors que :
- Le BEM documente 8 variants (ligne 58 : `ps-badge--gold`)
- Le CSS implémente 8 variants (ligne 263 : `&--gold`)
- La description mentionne 8 variants (ligne 13)

**Correction** :
```yaml
enum: ['primary','secondary','info','success','warning','danger','gold','neutral']
# Note : 'error' → 'danger' pour cohérence avec tokens (--danger existe, pas --error)
```

---

### 2. **Badge : `error` vs `danger` - incohérence terminologie**

**Fichier** : `docs/02-composants/01-atomes/badge.md`

**Problèmes multiples** :
- Ligne 94 : enum utilise `'error'`
- Ligne 62 : BEM utilise `ps-badge--danger`
- Ligne 146 : Tokens utilisent `--danger`
- Ligne 262 : CSS utilise `&--danger`

**Impact** : Confusion entre `error` (JavaScript/UI) et `danger` (token sémantique CSS)

**Décision** : **Utiliser `danger` partout** (cohérence avec brand.css tokens)

**Corrections** :
1. YAML : `'error'` → `'danger'`
2. Ligne 125 : `'error'` → `'danger'` dans props description

---

## 🚨 Problèmes Critiques RESTANTS (Phase 2)

### 8. **Token --neutral utilisé mais n'existe pas dans brand.css**

**Fichiers affectés** : 10+ composants  
**Status** : ⚠️ Partiellement résolu (Badge ✅), autres à traiter

**Exemples** :
- Button ligne 19 : `--neutral`, `--neutral-hover`
- Divider ligne 101, 209 : `--neutral`
- Language Selector : états neutral

**Vérification** :
```bash
grep -r "^  --neutral" source/props/brand.css
# Résultat : 0 matches ❌
```

**Solution recommandée** : Utiliser tokens existants
- Background neutral : `--light` ou `--gray-200`
- Text neutral : `--text-secondary` ou `--gray-600`
- Border neutral : `--border-default`

**Action requise** : Audit complet et remplacement dans tous les fichiers

---

### 9. **Tokens palette directe au lieu de sémantiques** (Autres composants)

**Problème global** : Plusieurs composants utilisent `--gray-200`, `--blue-100`, etc. au lieu de tokens sémantiques  
**Status** : ⚠️ Partiellement résolu (Badge ✅)

**Pattern à corriger** :
```scss
# MAUVAIS
--badge-bg: var(--gray-200);
--badge-color: var(--blue-700);

# CORRECT
--badge-bg: var(--surface-subtle);
--badge-color: var(--info-text-emphasis);
```

**Action requise** : Grep search `--gray-|--blue-|--green-|--red-|--yellow-` dans docs/02-composants/**/*.md

---

## ⚠️ Problèmes Prioritaires (Phase 2)

### 10. **Préfixe --ps- avec fallbacks hardcodés**

**Fichiers affectés** : 6+ composants  
**Status** : ⚠️ À corriger en Phase 2

**Exemples** :
- Tabs ligne 208
- Table ligne 355
- Modal ligne 225
- Dropdown ligne 208
- Avatar ligne 295
- Search Bar

**Pattern problématique** :
```css
border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
```

**Problèmes** :
1. Préfixe `--ps-` non standard (vrais tokens n'ont pas de préfixe)
2. Fallbacks hardcodés `1px`, `#D2D7DB` violent Token-First
3. Token `--ps-color-neutral-300` n'existe pas (confusion Tailwind?)

**Correction attendue** :
```css
border: var(--border-size-1) solid var(--border-default);
```

**Action requise** : Multi-replace dans 6 fichiers pour supprimer préfixe + fallbacks

---

### 11. **Button : confusion variant vs color props**

**Problème** :
- Badge ligne 139 : `--neutral`, `--neutral-hover`, `--neutral-text` utilisés
- **MAIS** `grep "--neutral" source/props/brand.css` → **0 résultats**

**Impact** : Token utilisé dans documentation mais **n'existe pas dans brand.css**

**Solutions possibles** :
1. **Ajouter tokens `--neutral` dans brand.css** (9 états complets)
2. **Remplacer par `--gray-*` existants** dans toute la documentation
3. **Utiliser tokens de bordure** : `--border-default`, `--text-secondary`

**Recommandation** : **Solution 3** (tokens existants) pour cohérence immédiate :
```css
/* Au lieu de --neutral (n'existe pas) */
.ps-badge--default {
  --badge-bg: var(--gray-200);      /* Fond neutre */
  --badge-color: var(--gray-600);   /* Texte neutre */
}
```

---

### 5. **Token `accent` utilisé sans définition claire**

**Fichiers** : Eyebrow, Tag List, Card, Collapse

**Problème** :
- Eyebrow ligne 83 : `enum: ['primary','secondary','accent','neutral']`
- Tag List ligne 103 : `enum: [...,'accent',...]`
- **MAIS** `--accent` n'existe pas dans brand.css

**Impact** : Variant documenté mais token non défini

**Solutions** :
1. **Définir `--accent`** → Quel token ? `--info` ? `--secondary` ?
2. **Remplacer par token existant** : `'info'` ou `'secondary'`

**Recommandation** : **Remplacer `accent` par `info`** (bleu = couleur accent typique)

---

## ⚠️ Problèmes Majeurs (Correction Prioritaire)

### 6. **Utilisation directe de tokens palette (`--gray-200`, `--blue-100`, etc.)**

**Fichiers affectés** : Badge (256), Button (19), Avatar (160, 295), nombreux autres

**Problème** : Utilisation de tokens de **palette** au lieu de tokens **sémantiques**

**Exemples trouvés** :
```css
/* ❌ MAUVAIS - Tokens palette directement */
--badge-bg: var(--gray-200);
--badge-color: var(--gray-600);

/* ✅ BON - Tokens sémantiques avec fallback neutre */
--badge-bg: var(--surface-subtle);
--badge-color: var(--text-secondary);
```

**Impact** :
- Violation architecture Token-First (3 couches)
- Couplage direct à l'implémentation couleur
- Changement de palette = breaking changes massifs

**Correction systématique requise** :
1. Documenter tokens manquants : `--surface-*`, `--text-*`
2. Ou utiliser tokens existants : `--light`, `--light-hover`, etc.

---

### 7. **Tokens hardcodés avec fallback dans exemples CSS**

**Fichiers** : Tabs, Table, Modal, Dropdown, Avatar, Search Bar

**Exemples** :
```css
/* Ligne 208 tabs.md */
border-bottom: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);

/* Ligne 355 table.md */
border-bottom: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
```

**Problèmes** :
1. **Préfixe `--ps-`** non standard (tokens utilisent pas de préfixe)
2. **Fallback hardcodé** (`1px`, `#D2D7DB`) = violation Token-First
3. **`--ps-color-neutral-300`** n'existe pas (confusion avec Tailwind ?)

**Correction** :
```css
/* ✅ Utiliser tokens réels sans fallback */
border-bottom: var(--border-size-1) solid var(--border-default);
```

---

### 8. **Props YAML : incohérence `variant` vs `color` vs `type`**

**Analyse** : Certains composants utilisent `color`, d'autres `variant`, d'autres `type`

| Composant | Prop | Valeurs | Cohérence |
|-----------|------|---------|-----------|
| Badge | `variant` | primary/secondary/... | ✅ Standard |
| Button | `variant` + `color` | Les 2 ❓ | ⚠️ Confusion |
| Link | `color` | green/purple/white | ❌ Noms couleurs |
| Spinner | `variant` | circular/dots/bars | ✅ Mais différent usage |
| Eyebrow | `variant` | primary/accent/... | ✅ Standard |

---

### 11. **Button : confusion variant vs color props**

**Fichier** : `docs/02-composants/01-atomes/button.md`  
**Lignes** : 140-147  
**Status** : ⚠️ À clarifier en Phase 2

**Problème** :
```yaml
variant:
  enum: ['primary', 'secondary']
color:
  enum: ['green', 'purple', 'white']
```

**Impact** : Deux props pour la même fonction ? `variant` devrait suffire ou clarifier relation.

**Recommandation** : Choisir une approche unique
- **Option A** : Supprimer `color`, étendre `variant` (primary/secondary/tertiary/ghost/link)
- **Option B** : Documenter relation : variant = style (solid/outline), color = teinte (semantic)

**Action requise** : Décision design system + clarification dans documentation

---

## 📊 Problèmes Modérés (Phase 3 - Améliorations)

### 12. **Enums size incohérents entre composants**
- **`variant`** : Pour couleurs sémantiques (primary/secondary/success/danger/...)
- **`size`** : Pour tailles (small/medium/large)
- **`shape`** : Pour formes (rounded/square/pill)
- **`type`** : Pour types fonctionnels HTML (`button`/`submit`/`reset`)

---

## 📊 Problèmes Modérés (Amélioration Recommandée)

### 9. **Valeurs enum inconsistantes entre composants**

## 📊 Problèmes Modérés (Phase 3 - Améliorations)

### 12. **Enums size incohérents entre composants**

**Status** : 💡 Amélioration recommandée

**Analyse** :

| Composant | Enum size | Nombre | Cohérence |
|-----------|-----------|--------|-----------|
| Badge | `['small','medium','large']` | 3 | ✅ Standard |
| Toggle | `['small','medium','large']` | 3 | ✅ Standard |
| Search Bar | `['small','medium','large']` | 3 | ✅ Standard |
| Spinner | `['xs','sm','md','lg','xl']` | 5 | ⚠️ T-shirt sizes |
| Language Selector | `['xs','sm','md','lg','xl','xxl']` | 6 | ❌ Incohérent |

**Recommandation** : **Standardiser sur 3 tailles** (`small`, `medium`, `large`) pour tous les composants

**Action** :
- Spinner : Réduire de 5 à 3 tailles
- Language Selector : Réduire de 6 à 3 tailles
- Documenter mapping si besoin de granularité (xs=small, sm=small, md=medium, lg=large, xl=large)

---

### 13. **Enums shape/forme incohérents**

**Status** : 💡 Amélioration recommandée

**Analyse** :
- Badge : `['rounded','square','pill']` ✅
- Flag : `['square','rounded','circle']` ⚠️

**Problème** : `circle` vs `pill` = même concept (complètement arrondi)

**Recommandation** : Unifier terminologie :
- `pill` = forme capsule (border-radius complet)
- `circle` = pour éléments carrés devenus ronds (ex: avatar, flag)
- `rounded` = coins arrondis standards (border-radius partiel)
- `square` = coins droits (border-radius: 0)

---

### 14. **Tokens non documentés mais utilisés**

**Status** : ✅ Partiellement résolu (--neutral, --accent), autres à documenter

**Tokens restants à vérifier** :

| Token pattern | Usage supposé | Existe ? | Action |
|---------------|---------------|----------|--------|
| `--surface-*` | Backgrounds (subtle, muted) | ❌ Non trouvé | Créer ou documenter alternatives |
| `--text-primary` | Texte principal | ✅ Oui | ✅ OK (déjà documenté) |
| `--text-secondary` | Texte secondaire | ✅ Oui | ✅ OK (déjà documenté) |

---

### 15. **Descriptions composants manquent contexte Real Estate**

**Problème** : Descriptions génériques au lieu de contexte métier

**Exemples** :
- Badge ligne 13 : "Indicateur visuel compact..." ✅ Générique OK
- Offer Card ligne 13 : "Composant spécialisé pour annonces immobilières..." ✅ Contexte métier
- Card ligne générique : Manque exemples Real Estate

**Recommandation** : Ajouter section "Cas d'usage Real Estate" dans composants génériques

---

## 🔍 Problèmes Mineurs (Nice to Have)

### 12. **Exemples Twig : chemins inconsistants**

**Exemples** :
- Badge ligne 300 : `@ps_theme/ps-badge/ps-badge.twig`
- Button : `@elements/button/button.twig`
- Card : `@components/card/card.twig`

**Impact** : Confusion sur convention de nommage

**Clarification requise** : Documenter convention namespace Twig

---

### 13. **Ordre sections README pas toujours cohérent**

**Standard observé** :
1. Description
2. Aperçu visuel
3. Structure BEM
4. Props YAML
5. Variants
6. Design Tokens
7. Template Twig
8. Styles CSS
9. Accessibilité
10. Responsive
11. Exemples
12. Ressources

**Certains fichiers** : Ordre différent (Accessibility avant CSS, etc.)

**Recommandation** : Template générateur devrait imposer ordre strict

---

## 📋 Résumé et Priorités

### ✅ Phase 1 Complétée (7/13 issues - 54%) - Commit 1001e77

**Corrections critiques appliquées** :
1. ✅ Badge : Ajouté `gold` dans enum YAML
2. ✅ Badge : Remplacé `error` par `danger` partout
3. ✅ Badge : Résolu token `--neutral` manquant (remplacé par --gray-200/600 puis --light/--text-secondary)
4. ✅ Badge : Supprimé tokens palette directe (--gray-200 → --light, --primary/etc. → -subtle/-text-emphasis)
5. ✅ Link : Remplacé `color` names par `variant` sémantiques
6. ✅ Eyebrow : Remplacé `accent` par `info` (enum, BEM, exemples)
7. ✅ TagList : Remplacé `accent` + `error` par `info` + `danger`

**Temps Phase 1** : 45 min (estimation initiale 1h)

---

### ⚠️ Phase 2 À Faire (3 issues prioritaires restantes)

**Token --neutral dans autres composants** :
- Button ligne 19 : `--neutral`, `--neutral-hover`
- Divider ligne 101, 209 : `--neutral`
- 8+ autres composants à identifier et corriger

**Préfixe --ps- avec fallbacks** :
- Tabs ligne 208
- Table ligne 355
- Modal ligne 225
- Dropdown ligne 208
- Avatar ligne 295
- Search Bar
→ Supprimer préfixe + fallbacks hardcodés (1px, #D2D7DB)

**Button : clarifier variant vs color** :
- Lignes 140-147 : Les deux props définies
- Décider : Supprimer color ou documenter relation

**Temps Phase 2 estimé** : 1h30

---

### 📊 Phase 3 À Faire (3 améliorations recommandées)

**Standardiser enums size** :
- Spinner : 5 tailles → 3 tailles (small/medium/large)
- Language Selector : 6 tailles → 3 tailles

**Unifier enums shape/forme** :
- Flag : `circle` → `pill` (terminologie cohérente)

**Documenter tokens manquants** :
- Vérifier existence `--surface-*` patterns
- Créer ou documenter alternatives

**Temps Phase 3 estimé** : 1h

---

### 📈 Statistiques

**Total issues identifiées** : 13  
**Résolues Phase 1** : 7 (54%)  
**Restantes** : 6 (46%)

**Breakdown par sévérité** :
- Critiques : 9 identifiées → 7 résolues (78%), 2 restantes
- Prioritaires : 3 identifiées → 0 résolues (0%), 3 restantes  
- Améliorations : 3 identifiées → 0 résolues (0%), 3 restantes

**Temps total estimé** :
- Phase 1 : ✅ 45min (fait)
- Phase 2 : ⚠️ 1h30 (restant)
- Phase 3 : 📊 1h (restant)
- **Total restant** : 2h30

---

## 🔧 Actions Recommandées

### Phase 1 - Corrections YAML (1h)
- [ ] Badge : `'error'` → `'danger'` + ajouter `'gold'`
- [ ] Link : `color` → `variant` avec valeurs sémantiques
- [ ] Button : Clarifier props `variant` vs `color`
- [ ] Eyebrow/TagList : `'accent'` → `'info'`

### Phase 2 - Tokens Documentation (2h)
- [ ] Décider : Créer `--neutral` ou documenter alternative `--gray-*`
- [ ] Audit complet tokens utilisés vs tokens existants
- [ ] Corriger tous tokens palette directe → sémantiques
- [ ] Supprimer tous `--ps-` prefix + fallbacks hardcodés

### Phase 3 - Standardisation (1h)
- [ ] Unifier tailles enum (3 tailles standard)
- [ ] Vérifier ordre sections tous composants
- [ ] Ajouter section "Real Estate Usage" template

### Phase 4 - Validation (30min)
- [ ] Script automatique : grep tokens non existants
- [ ] Script validation : enum cohérents
- [ ] Build check : aucun warning tokens

---

## 📊 Statistiques

**Fichiers analysés** : 87+ composants docs  
**Problèmes identifiés** : 13 catégories  
**Corrections critiques** : 5  
**Corrections prioritaires** : 3  
**Améliorations** : 5

**Temps estimé corrections complètes** : 4h30

---

**Prochaine étape** : Approuver plan correction + prioriser phases
