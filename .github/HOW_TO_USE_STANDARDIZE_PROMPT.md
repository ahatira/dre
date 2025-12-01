> DEPRECATED — Use `.github/STANDARDIZE_COMPONENT_PROMPT.md`

Ce guide a été consolidé. Les instructions d'utilisation et le workflow sont désormais centralisés dans `STANDARDIZE_COMPONENT_PROMPT.md` (voir la section "Usage").

Pourquoi ce changement :
- Réduire la duplication entre un prompt et un guide séparé
- Maintenir une seule source de vérité

Veuillez vous référer à : `.github/STANDARDIZE_COMPONENT_PROMPT.md`

# 🚀 Guide d'utilisation : Standardiser un composant Storybook

## 📖 Comment utiliser ce prompt dans une nouvelle session

### Contexte

Le prompt `.github/STANDARDIZE_COMPONENT_PROMPT.md` permet de standardiser automatiquement un composant Storybook selon nos conventions (naming, argTypes, showcase stories, listes centralisées).

**Problème** : Sans contexte de session, l'IA ne connaît pas la structure exacte des fichiers JSON ni les conventions établies.

**Solution** : Le prompt inclut maintenant une **ÉTAPE 0** obligatoire de lecture du contexte.

---

## ✅ Utilisation étape par étape

### 1. Ouvrir une nouvelle session Copilot

Dans VS Code, ouvrir une nouvelle conversation Copilot.

### 2. Copier-coller le prompt complet

```
Applique les standards Storybook au composant [NOM_COMPOSANT] en suivant EXACTEMENT le processus décrit dans .github/STANDARDIZE_COMPONENT_PROMPT.md
```

**Exemples:**
```
Applique les standards Storybook au composant Badge
Applique les standards Storybook au composant Avatar
Applique les standards Storybook au composant ProgressBar
```

### 3. L'IA va automatiquement :

#### ÉTAPE 0 (Automatique - OBLIGATOIRE)
✅ Lire `source/patterns/documentation/README.md`
✅ Lire tous les fichiers JSON (colors, sizes, variants, icons)
✅ Identifier les groupes disponibles (ex: `extended`, `compact`, `semantic`)
✅ Noter les structures de propriétés (ex: `colorsList.extended.values`)

#### ÉTAPE 1 (Analyse)
✅ Lire les fichiers du composant (.stories.jsx, .twig, .yml, .css)
✅ Identifier les props existantes
✅ Lister les stories actuelles

#### ÉTAPE 2 (Impact)
✅ Déterminer les changements nécessaires
✅ Vérifier compatibilité avec JSON existants
✅ Identifier renommages de props

#### ÉTAPE 3 (Rapport)
✅ Présenter un rapport détaillé **AVANT toute modification**
✅ Inclure tous les groupes JSON disponibles
✅ Lister stories à créer/supprimer

### 4. Valider le rapport

L'IA va présenter un rapport comme celui-ci :

```markdown
## Analyse du composant Badge

### État actuel
- Nombre de stories: 14
- Props principales: text, icon, variant, pill, url, size
- Stories individuelles: Primary, Secondary, Gold, Info, Success, Warning, Danger
- Stories showcase existantes: Sizes, Pills, WithIcons, AsLinks, AllVariants

### Modifications proposées

#### A. Renommage de props
- `variant` → `color` (suit convention sémantique)

#### B. Centralisation des listes
- Couleurs: utiliser `colorsList.extended.values` (8 options)
  * Groupe disponible: extended, semantic, neutral, brand, link, field
- Tailles: utiliser `sizesList.compact.values` (3 options)
  * Groupe disponible: standard, compact, extended, avatar, fontSize
- Icônes: utiliser `iconsList.categories.generic`

[...]
```

**Répondre simplement :**
```
oui
```

### 5. Application automatique

L'IA va :
✅ Appliquer tous les changements via `multi_replace_string_in_file`
✅ Modifier les 4 fichiers (.stories.jsx, .twig, .yml, .css si nécessaire)
✅ Utiliser les chemins relatifs corrects (`../../documentation/`)
✅ Renommer les props selon les standards
✅ Supprimer les stories individuelles
✅ Créer les showcase stories
✅ Vérifier la syntaxe

### 6. Vérification

L'IA confirmera :
✅ Tous les imports corrects
✅ Toutes les stories valides
✅ Documentation complète
✅ ArgTypes avec listes centralisées

---

## 🎯 Exemple complet

### Prompt initial (vous)
```
Applique les standards Storybook au composant ProgressBar
```

### Réponse (IA)
```markdown
## 📊 Analyse du composant ProgressBar

[Lecture automatique des JSON...]

### État actuel
- Nombre de stories: 12
- Props principales: value, variant, color, size, indeterminate
[...]

### Modifications proposées
[Rapport détaillé avec tous les changements]

**Prêt à appliquer les modifications ?**
```

### Votre réponse
```
oui
```

### Résultat (IA)
```
✅ Standardisation ProgressBar complétée !

### Modifications appliquées:

**progress-bar.stories.jsx** (12 → 7 exports)
- ✅ Imports: ajout colorsList, sizesList
- ✅ Props: conventions respectées
[...]
```

---

## ⚠️ Points critiques

### ✅ À FAIRE
- Toujours utiliser le prompt exact : `Applique les standards Storybook au composant [NOM]`
- Attendre le rapport complet avant de valider
- Vérifier que l'ÉTAPE 0 a bien été exécutée (lecture des JSON)

### ❌ À ÉVITER
- Ne pas demander "standardise Badge" (trop vague)
- Ne pas sauter l'étape de validation du rapport
- Ne pas modifier manuellement pendant le processus

---

## 🔧 Dépannage

### Problème : "Je ne connais pas la structure des JSON"
**Solution** : Relancer avec :
```
Lis d'abord source/patterns/documentation/README.md et tous les JSON dans documentation/, puis applique les standards Storybook au composant [NOM]
```

### Problème : "Imports incorrects"
**Solution** : L'IA utilise maintenant automatiquement les chemins relatifs. Si erreur, préciser :
```
Utilise des chemins relatifs (../../documentation/) pour les imports JSON
```

### Problème : "Groupes JSON inconnus"
**Solution** : L'ÉTAPE 0 résout ce problème en lisant les JSON avant l'analyse.

---

## 📚 Références

- **Prompt complet** : `.github/STANDARDIZE_COMPONENT_PROMPT.md`
- **Template doc** : `.github/STORYBOOK_DOC_TEMPLATE.md`
- **Structure JSON** : `source/patterns/documentation/README.md`
- **Exemple réussi** : `source/patterns/elements/badge/` (composant déjà standardisé)

---

## 💡 Conseil pro

Pour standardiser plusieurs composants :

```
Applique les standards Storybook aux composants suivants dans l'ordre :
1. Badge
2. Avatar
3. ProgressBar

Pour chaque composant, présente-moi le rapport avant d'appliquer les modifications.
```

L'IA traitera chaque composant séquentiellement avec validation entre chaque.
