# Analyse d'Incohérences - Documentation Composants

**Date** : 13 décembre 2025  
**Scope** : Tous les fichiers `docs/02-composants/**/*.md`  
**Objectif** : Identifier et corriger toutes incohérences logiques, erreurs de conception, et violations des standards

---

## 🚨 Problèmes Critiques (Correction Immédiate)

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

### 3. **Link : utilise noms de couleurs au lieu de variants sémantiques**

**Fichier** : `docs/02-composants/01-atomes/link.md`  
**Ligne** : 35

**Problème** :
```yaml
color: { type: string, enum: ['green','purple','white','default'], default: 'green' }
```

**Impact** : Violation du principe Token-First (noms de couleurs au lieu de variants sémantiques)

**Correction attendue** :
```yaml
variant: { type: string, enum: ['primary','secondary','neutral','inverse'], default: 'primary' }
# primary = green brand, secondary = purple brand, inverse = white
```

**Justification** :
- Tous les autres composants utilisent `variant` avec valeurs sémantiques
- `green`/`purple` = couplage à l'implémentation actuelle
- Changement de couleur brand = breaking change avec système actuel

---

### 4. **Token `neutral` non documenté mais utilisé partout**

**Fichiers affectés** : Badge, Button, Divider, Language Selector, etc.

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

**Problème Button ligne 140-147** :
```yaml
variant:
  enum: ['primary', 'secondary']
color:
  enum: ['green', 'purple', 'white']
```

**Impact** : Deux props pour la même fonction ? `variant` devrait suffire.

**Recommandation standard** :
- **`variant`** : Pour couleurs sémantiques (primary/secondary/success/danger/...)
- **`size`** : Pour tailles (small/medium/large)
- **`shape`** : Pour formes (rounded/square/pill)
- **`type`** : Pour types fonctionnels HTML (`button`/`submit`/`reset`)

---

## 📊 Problèmes Modérés (Amélioration Recommandée)

### 9. **Valeurs enum inconsistantes entre composants**

**Tailles** :
- Badge : `['small','medium','large']` ✅
- Spinner : `['xs','sm','md','lg','xl']` ⚠️ (5 tailles)
- Toggle : `['small','medium','large']` ✅
- Search Bar : `['small','medium','large']` ✅
- Language Selector : `['xs','sm','md','lg','xl','xxl']` ❌ (6 tailles !)

**Recommandation** : **Standardiser sur 3 tailles** (`small`, `medium`, `large`)

**Formes** :
- Badge : `['rounded','square','pill']` ✅
- Flag : `['square','rounded','circle']` ⚠️ (`circle` = `pill` ?)

**Recommandation** : Unifier terminologie (`pill` = complètement arrondi)

---

### 10. **Tokens non documentés mais utilisés**

**Tokens utilisés dans docs mais absents de `docs/03-tokens/` :**

| Token utilisé | Fichiers | Existe dans brand.css ? | Action |
|---------------|----------|-------------------------|--------|
| `--neutral` | Badge, Button, Divider | ❌ Non | Remplacer par --gray ou créer |
| `--accent` | Eyebrow, Tag List | ❌ Non | Remplacer par --info |
| `--surface-*` | N/A (devrait être utilisé) | ❌ Non | Créer ou documenter alternatives |
| `--text-primary` | Label, nombreux | ✅ Oui (couleurs.md) | ✅ OK |
| `--border-default` | Badge, nombreux | ✅ Oui (couleurs.md) | ✅ OK |

---

### 11. **Descriptions composants manquent contexte Real Estate**

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

### Corrections Critiques (Bloquantes)

1. ✅ **Badge : Ajouter `gold` dans enum YAML** ← FAIT commit 0291aaa
2. ✅ **Badge : Remplacer `error` par `danger` partout** ← Reste YAML
3. ⚠️ **Résoudre token `--neutral` manquant** (utilisé 10+ fois)
4. ⚠️ **Link : Remplacer color names par variants sémantiques**
5. ⚠️ **Supprimer tokens palette directe** (`--gray-200` → tokens sémantiques)

### Corrections Prioritaires

6. ⚠️ **Supprimer préfixe `--ps-` + fallbacks hardcodés** (Tabs, Table, Modal, etc.)
7. ⚠️ **Clarifier Button : variant vs color**
8. ⚠️ **Remplacer `accent` par `info`** (Eyebrow, Tag List)

### Améliorations Recommandées

9. 📊 **Standardiser tailles enum** (3 tailles partout)
10. 📊 **Documenter tokens manquants** (`--surface-*`, `--neutral` si créé)
11. 📊 **Ajouter contexte Real Estate** dans composants génériques

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
