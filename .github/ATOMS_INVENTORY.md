# Inventaire des Atoms (Elements)

**Date** : 2025-12-02  
**Objectif** : Liste complète des Atoms disponibles et leur conformité pour composition

---

## ✅ Atoms Disponibles et Conformes

### 1. **Badge** (`elements/badge/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `text` (optional), `icon`, `color`, `pill`, `size`, `url`, `attributes`
- **Support attributes** : ✅ OUI
- **Conformité** : ✅ COMPLET (ternaire + null, attributes)
- **Action requise** : ✅ Aucune - CONFORME

### 2. **Button** (`elements/button/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `text`, `color`, `size`, `variant`, `icon_start`, `icon_end`, `disabled`, `attributes`
- **Support attributes** : ✅ OUI
- **Conformité** : ✅ COMPLET
- **Action requise** : ✅ Aucune - CONFORME

### 3. **Checkbox** (`elements/checkbox/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

### 4. **Divider** (`elements/divider/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `orientation`, `thickness`, `color`
- **Support attributes** : À vérifier
- **Action requise** : Vérifier support attributes

### 5. **Eyebrow** (`elements/eyebrow/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

### 6. **Field** (`elements/field/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

### 7. **Flag** (`elements/flag/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

### 8. **Heading** (`elements/heading/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `text`, `level`, `color`, `weight`, `align`, `visuallyHidden`, `attributes`
- **Support attributes** : ✅ OUI
- **Conformité** : ✅ COMPLET (ternaire + null, attributes)
- **Action requise** : ✅ Aucune - CONFORME

### 9. **Icon** (`elements/icon/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `icon`, `size`, `color`, `decorative`
- **Support attributes** : ✅ OUI
- **Conformité** : ✅ COMPLET
- **Action requise** : ✅ Aucune - CONFORME

### 10. **Image** (`elements/image/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `src`, `alt`, `width`, `height`, `srcset`, `sizes`, `loading`, `fit`, `rounded`, `attributes`
- **Support attributes** : ✅ OUI (via `attributes.addClass()`)
- **Conformité** : ✅ COMPLET
- **Action requise** : ✅ Aucune - CONFORME

### 11. **Label** (`elements/label/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `text`, `forId`, `required`, `disabled`, `attributes`
- **Support attributes** : ✅ OUI
- **Conformité** : ✅ COMPLET (ternaire + null, attributes)
- **Action requise** : ✅ Aucune - CONFORME

### 12. **Link** (`elements/link/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `text`, `url`, `color`, `size`, `underline`, `icon`, `iconPosition`, `target`, `rel`, `disabled`, `attributes`
- **Support attributes** : ✅ OUI
- **Conformité** : ✅ COMPLET (ternaire + null, attributes)
- **Action requise** : ✅ Aucune - CONFORME

### 13. **Progress Bar** (`elements/progress-bar/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

### 14. **Radio** (`elements/radio/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

### 15. **Skip Link** (`elements/skip-link/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

### 16. **Spinner** (`elements/spinner/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

### 17. **Text** (`elements/text/`)
- **Prêt pour composition** : ✅ OUI
- **Props** : `text`, `size`, `color`, `tag`, `align`, `muted`, `strong`, `attributes`
- **Support attributes** : ✅ OUI
- **Conformité** : ✅ COMPLET (ternaire + null, attributes, props simplifiés)
- **Action requise** : ✅ Aucune - CONFORME

### 18. **Toggle** (`elements/toggle/`)
- **Prêt pour composition** : ⚠️ À VÉRIFIER
- **Props** : À documenter
- **Support attributes** : À vérifier
- **Action requise** : Audit complet

---

## ❌ Atoms Manquants (à créer ?)

### 1. **Pagination Dots** (pour Carousel)
- **Besoin identifié** : Carousel a besoin d'indicateurs de slide
- **Options** :
  1. Utiliser `badge` avec modifications
  2. Créer nouveau atom `pagination-dot`
- **Action** : À décider

### 2. **Avatar Status Badge** (spécialisé)
- **Besoin identifié** : Avatar status indicator (online/offline/busy)
- **Options** :
  1. Utiliser `badge` avec `text: ''` et `pill: true`
  2. Créer nouveau atom `status-indicator`
- **Action** : Adapter `badge` pour accepter `text: ''`

---

## 🔧 Actions Prioritaires

### ✅ Haute Priorité - TERMINÉ

1. ✅ **Text Atom** - Refactoring COMPLET
   - ✅ Remplacé `.merge()` par ternaire + `null`
   - ✅ Ajouté support `attributes`
   - ✅ Simplifié props (supprimé `variant` legacy)
   - ✅ Documentation mise à jour avec exemple composition

2. ✅ **Badge Atom** - Adaptation COMPLÈTE
   - ✅ Permet `text: ''` (empty) pour status visual-only
   - ✅ Remplacé `.merge()` par ternaire + `null`
   - ✅ Testé pour Avatar status indicator

3. ✅ **Heading Atom** - Refactoring COMPLET
   - ✅ Remplacé `.merge()` par ternaire + `null`
   - ✅ Support `attributes` déjà présent
   - ✅ Props documentés

4. ✅ **Link Atom** - Refactoring COMPLET
   - ✅ Remplacé `.merge()` par ternaire + `null`
   - ✅ Support `attributes` déjà présent
   - ✅ Props documentés

5. ✅ **Label Atom** - Refactoring COMPLET
   - ✅ Remplacé `.merge()` par ternaire + `null`
   - ✅ Support `attributes` déjà présent
   - ✅ Props documentés

### Moyenne Priorité (nécessaire pour Components avancés)

5. **Label Atom** - Audit pour Form-Field
6. **Field Atom** - Audit pour Form-Field
7. **Eyebrow Atom** - Audit pour Card
8. **Progress Bar Atom** - Audit pour Loading states

### Basse Priorité (spécifiques)

9. **Checkbox, Radio, Toggle** - Audit pour formulaires
10. **Flag** - Audit pour i18n components
11. **Skip Link** - Audit pour accessibilité
12. **Spinner** - Audit pour loading states

---

## 📋 Template d'Audit pour Atoms

Pour chaque Atom à auditer :

```markdown
### [Nom de l'Atom] (`elements/[nom]/`)

#### 1. Lire les fichiers
- [ ] `[nom].twig` - Structure et props
- [ ] `[nom].css` - Variables Layer 2, BEM
- [ ] `[nom].yml` - Données de test
- [ ] `[nom].stories.jsx` - Props argTypes
- [ ] `README.md` - Documentation

#### 2. Vérifier conformité
- [ ] Props documentés dans commentaire Twig
- [ ] Support `attributes` paramètre
- [ ] Classes avec ternaire + `null` (pas `.merge()`)
- [ ] CSS avec Layer 2 variables
- [ ] BEM strict avec `ps-` prefix
- [ ] README liste props, tokens, BEM

#### 3. Tester composition
- [ ] Créer test include dans un Component
- [ ] Vérifier que `attributes.addClass()` fonctionne
- [ ] Vérifier que `only` ne casse pas le composant

#### 4. Actions correctives
- [ ] Lister tous les problèmes détectés
- [ ] Appliquer corrections si nécessaire
- [ ] Mettre à jour documentation
- [ ] Marquer ✅ dans inventaire
```

---

## 🎯 Roadmap de Refactoring

### Phase 1 : Préparer les Atoms (Semaine 1)
1. ✅ Badge - Adapter pour `text: ''`
2. ✅ Text - Refactoriser + `attributes`
3. ✅ Heading - Audit + `attributes`
4. ✅ Link - Audit + `attributes`
5. ✅ Label - Audit + `attributes`

### Phase 2 : Refactoriser Molecules Simples (Semaine 2)
1. Avatar - Utiliser Image + Text + Badge
2. Alert - Utiliser Icon + Heading + Text + Button
3. Breadcrumb - Utiliser Link + Icon + Text

### Phase 3 : Refactoriser Molecules Complexes (Semaine 3)
1. Card - Utiliser Image + Eyebrow + Heading + Text + Badge + Button
2. Form-Field - Utiliser Label + Field + Text + Icon
3. Dropdown - Utiliser Button + Icon + Text + Link + Divider

### Phase 4 : Refactoriser Molecules Avancées (Semaine 4)
1. Accordion - Utiliser Heading + Button + Icon + Text
2. Carousel - Utiliser Image + Button + Icon + [Pagination]

---

## 📊 Statistiques

- **Atoms disponibles** : 18
- **Atoms conformes pour composition** : 8 (44%) ✅
  - Badge ✅
  - Button ✅
  - Heading ✅
  - Icon ✅
  - Image ✅
  - Label ✅
  - Link ✅
  - Text ✅
- **Atoms à auditer** : 10 (56%)
- **Atoms à créer** : 0

**Objectif Phase 1** : ✅ **COMPLÉTÉ** - 8 Atoms prioritaires conformes et prêts pour composition

**Prochaine Phase** : Refactoriser les Molecules (Avatar, Alert, Card, etc.) avec composition atomique
