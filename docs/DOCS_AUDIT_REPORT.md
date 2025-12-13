# 📚 Audit Documentation - docs/02-composants/

**Date** : 2025-12-13  
**Auditeur** : GitHub Copilot  
**Scope** : Vérification conformité avec instructions v4.0.0

---

## 🎯 Objectif

Vérifier que toutes les documentations de composants dans `docs/02-composants/` respectent :
- Les règles BEM (préfixe `ps-`, format correct)
- Les design tokens (pas de hardcoded)
- La terminologie (elements/ vs atoms, etc.)
- Le variant neutral (omission, pas classe explicite)
- Le système d'icônes (pas de prefix `icon-`)
- Token-First Composition (molecules+)
- Accessibilité WCAG 2.2 AA

---

## 📊 Statistiques globales

**Audit en cours...**

| Métrique | Valeur |
|----------|--------|
| **Fichiers audités** | 77 fichiers .md |
| **Fichiers conformes (100%)** | À déterminer |
| **Score moyen** | À calculer |
| **Erreurs hardcoded colors** | ~20 occurrences détectées |
| **Erreurs variant neutral** | 3 occurrences (button.md, table.md×2) |
| **Erreurs icon prefix** | 0 ✅ |
| **Erreurs BEM** | À vérifier |

---

## 🔍 Erreurs détectées

### 1. Hardcoded Colors (Priorité HAUTE)

**20+ occurrences de couleurs hex dans les docs** :

#### Atoms

**button.md** :
- Ligne inconnue : `#00915A` (primary)
- Ligne inconnue : `#A12B66` (secondary)
- **Correction** : Retirer les hex, garder uniquement `--primary`, `--secondary`

**icon.md** :
- `#434F57` (dark-grey)
- `#B4BABE` (light-grey)  
- `#00915A` (green)
- `#FFFFFF` (white)
- **Correction** : Utiliser tokens sémantiques `--gray-700`, `--gray-400`, `--primary`, `--white`

**link.md** :
- `#8E2A68` (visited)
- **Correction** : `--link-visited` ou `--secondary-active`

#### Molecules

**avatar.md** (12+ occurrences) :
- `#54636F`, `#0DB089`, `#9AA6B2`, `#E53935`, `#0B5FFF`, `#F3F6F9`
- Utilise tokens palette (`--ps-color-neutral-600`) au lieu de sémantiques
- **Correction** : `--gray-600`, `--success`, `--gray-400`, `--danger`, `--focus-ring`, `--gray-100`

**card.md** :
- `#EBEDEF` (border)
- **Correction** : `--border-default` ou `--gray-200`

**carousel.md** :
- `#F9F9FB`, `#00915A`, `#A22B66`, `#FFFFFF`
- **Correction** : `--gray-50`, `--primary`, `--secondary`, `--white`

### 2. Variant Neutral explicite (Priorité MOYENNE)

**3 occurrences de classes `--neutral` à corriger** :

1. **button.md:703** :
   ```html
   <!-- ❌ WRONG -->
   <button class="ps-button ps-button--neutral">Neutral</button>
   
   <!-- ✅ CORRECT -->
   <button class="ps-button">Neutral (default)</button>
   ```

2. **table.md:59** :
   ```html
   <!-- ❌ WRONG -->
   <span class="ps-badge ps-badge--neutral">Inactif</span>
   
   <!-- ✅ CORRECT -->
   <span class="ps-badge">Inactif</span>
   ```

3. **table.md:483** :
   ```html
   <!-- ❌ WRONG (dans YAML) -->
   '<span class="ps-badge ps-badge--neutral">Inactif</span>'
   
   <!-- ✅ CORRECT -->
   '<span class="ps-badge">Inactif</span>'
   ```

**Règle** : Depuis Phase 2 Button (v2.0.0), `neutral` est obtenu par OMISSION de variant, pas par classe explicite.

### 3. Icon Prefix (Priorité BASSE)

✅ **0 erreur détectée** - Excellente conformité !

Aucune mention de `icon-check`, `icon-arrow`, etc. trouvée.  
Toutes les docs utilisent correctement `data-icon="check"` ou `icon: 'arrow'`.

### 4. Palette Colors vs Semantic (Priorité HAUTE)

**avatar.md utilise tokens palette** au lieu de sémantiques :

```css
/* ❌ WRONG - Palette colors */
var(--ps-color-neutral-600, #54636F)
var(--ps-color-success-600, #0DB089)
var(--ps-color-error-600, #E53935)

/* ✅ CORRECT - Semantic tokens */
var(--gray-600)
var(--success)
var(--danger)
```

**Fichiers affectés** : avatar.md (12 occurrences)

### 5. BEM Non-conformité (Priorité CRITIQUE)

**À vérifier** :
- Préfixe `ps-` sur toutes les classes
- Format `.ps-block__element--modifier`
- Pas de double underscore `__`

**Scan en cours des 77 fichiers...**

### 6. Token-First Composition (Priorité MOYENNE)

**Molecules+ doivent documenter** comment surcharger tokens des atoms composés.

**Exemples manquants** :
- Card composant Button → doit montrer `--ps-button-bg` override
- Form-field composant Input → doit montrer `--ps-input-border-color` override
- Breadcrumb composant Link → doit montrer `--ps-link-color` override

**À auditer** : Tous les fichiers dans `02-molecules/`, `03-organismes/`, `04-templates/`, `05-pages/`

---

## 📋 Audit par niveau (en cours)

### Atoms (01-atomes/) - 22 fichiers

| Fichier | Score | Erreurs principales | Priorité |
|---------|-------|---------------------|----------|
| badge.md | 🔍 | À auditer | - |
| button.md | 🔍 | Hardcoded colors (2), neutral explicite (1) | P1 |
| checkbox.md | 🔍 | À auditer | - |
| collapse.md | 🔍 | À auditer | - |
| divider.md | 🔍 | À auditer | - |
| eyebrow.md | 🔍 | À auditer | - |
| field.md | 🔍 | À auditer | - |
| flag.md | 🔍 | À auditer | - |
| heading.md | 🔍 | À auditer | - |
| icon.md | 🔍 | Hardcoded colors (4) | P1 |
| image.md | 🔍 | À auditer | - |
| input.md | 🔍 | À auditer | - |
| label.md | 🔍 | À auditer | - |
| link.md | 🔍 | Hardcoded color (1) | P2 |
| progress-bar.md | 🔍 | À auditer | - |
| radio.md | 🔍 | À auditer | - |
| select.md | 🔍 | À auditer | - |
| skip-link.md | 🔍 | À auditer | - |
| spinner.md | 🔍 | À auditer | - |
| text.md | 🔍 | À auditer | - |
| textarea.md | 🔍 | À auditer | - |
| toggle.md | 🔍 | À auditer | - |

### Molecules (02-molecules/) - 35 fichiers

| Fichier | Score | Erreurs principales | Priorité |
|---------|-------|---------------------|----------|
| avatar.md | 🔍 | Palette colors (12), hardcoded (6) | P1 🔴 |
| card.md | 🔍 | Hardcoded color (1) | P2 |
| carousel.md | 🔍 | Hardcoded colors (4) | P1 |
| table.md | 🔍 | Neutral explicite (2) | P2 |
| ... | 🔍 | À auditer | - |

### Organisms (03-organismes/) - 12 fichiers

**Audit en attente**

### Templates (04-templates/) - 7 fichiers

**Audit en attente**

### Pages (05-pages/) - 1 fichier

**Audit en attente**

---

## 🎯 Priorités de correction

### P1 - Critique (Corrections immédiates)

1. **Hardcoded colors** (20+ occurrences) :
   - button.md : 2 hex
   - icon.md : 4 hex
   - avatar.md : 12 palette tokens + 6 hex
   - carousel.md : 4 hex
   - card.md : 1 hex
   - link.md : 1 hex

2. **Palette colors → Semantic** (avatar.md) :
   - `--ps-color-*` → `--gray-*`, `--success`, `--danger`

### P2 - Important (Corrections planifiées)

1. **Neutral explicite** (3 occurrences) :
   - button.md : 1
   - table.md : 2

2. **Token-First documentation manquante** :
   - Toutes les molecules+ à compléter

### P3 - Améliorations (Corrections futures)

1. **BEM audit complet** (77 fichiers)
2. **Accessibilité audit** (77 fichiers)
3. **Real Estate context** (pertinence métier)

---

## 🔧 Scripts de correction suggérés

### Correction automatique hardcoded colors

```bash
# Button.md
sed -i 's/#00915A/var(--primary)/g' docs/02-composants/01-atomes/button.md
sed -i 's/#A12B66/var(--secondary)/g' docs/02-composants/01-atomes/button.md

# Icon.md
sed -i 's/#434F57/var(--gray-700)/g' docs/02-composants/01-atomes/icon.md
sed -i 's/#B4BABE/var(--gray-400)/g' docs/02-composants/01-atomes/icon.md
sed -i 's/#00915A/var(--primary)/g' docs/02-composants/01-atomes/icon.md
sed -i 's/#FFFFFF/var(--white)/g' docs/02-composants/01-atomes/icon.md

# Link.md
sed -i 's/#8E2A68/var(--secondary-active)/g' docs/02-composants/01-atomes/link.md

# Card.md
sed -i 's/#EBEDEF/var(--border-default)/g' docs/02-composants/02-molecules/card.md

# Carousel.md
sed -i 's/#F9F9FB/var(--gray-50)/g' docs/02-composants/02-molecules/carousel.md
sed -i 's/#00915A/var(--primary)/g' docs/02-composants/02-molecules/carousel.md
sed -i 's/#A22B66/var(--secondary)/g' docs/02-composants/02-molecules/carousel.md
sed -i 's/#FFFFFF/var(--white)/g' docs/02-composants/02-molecules/carousel.md
```

### Correction neutral explicite

```bash
# Button.md
sed -i 's/ps-button ps-button--neutral/ps-button/g' docs/02-composants/01-atomes/button.md

# Table.md
sed -i 's/ps-badge ps-badge--neutral/ps-badge/g' docs/02-composants/02-molecules/table.md
```

---

## 📈 Métriques de progression

### Avant corrections

- Hardcoded colors : ~20 occurrences
- Neutral explicite : 3 occurrences
- Palette colors : 12 occurrences
- Icon prefix : 0 ✅
- Token-First : Non documenté

### Cible

- Hardcoded colors : 0
- Neutral explicite : 0
- Palette colors : 0
- Icon prefix : 0 ✅
- Token-First : 100% documenté

---

## 🚀 Prochaines étapes

1. **Phase 1** : Corrections P1 (hardcoded + palette)
   - Durée : 2h
   - Impact : +30 points qualité

2. **Phase 2** : Corrections P2 (neutral + Token-First)
   - Durée : 4h
   - Impact : +20 points qualité

3. **Phase 3** : Audit complet BEM + A11y
   - Durée : 6h
   - Impact : Rapport détaillé 77 fichiers

4. **Phase 4** : Standardisation globale
   - Durée : 8h
   - Impact : 100% conformité

---

## 📚 Références

- **Instructions** : `.github/instructions/` (01-05 + README.md)
- **Core Principles** : `.github/instructions/01-core-principles.md`
- **Component Development** : `.github/instructions/02-component-development.md`
- **Technical Standards** : `.github/instructions/03-technical-implementation.md`
- **Quality Assurance** : `.github/instructions/04-quality-assurance.md`

---

**Note** : Ce rapport est un **audit préliminaire**. Un scan complet ligne par ligne des 77 fichiers est nécessaire pour un rapport final exhaustif.

**Commande pour audit complet manuel** :
```bash
for file in docs/02-composants/**/*.md; do
  echo "=== $file ==="
  # BEM check
  grep -n "class=\"[^\"]*\"" "$file" | grep -v "ps-"
  # Hardcoded check
  grep -n "#[0-9A-Fa-f]\{6\}" "$file"
  # Neutral check
  grep -n "--neutral" "$file"
done
```
