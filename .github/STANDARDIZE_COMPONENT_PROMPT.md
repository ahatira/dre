# Prompt: Standardiser un composant Storybook

## 📋 Instructions

Applique les standards de documentation et de structure Storybook au composant **[NOM_COMPOSANT]** selon les règles définies dans `.github/STORYBOOK_DOC_TEMPLATE.md`.

## 📁 ÉTAPE 0 : Lecture du contexte (OBLIGATOIRE)

**Avant toute analyse**, lire ces fichiers pour comprendre le contexte :

```bash
# 1. Lire la documentation des listes JSON
cat source/patterns/documentation/README.md

# 2. Lire les structures des fichiers JSON existants
cat source/patterns/documentation/colors-list.json
cat source/patterns/documentation/sizes-list.json
cat source/patterns/documentation/variants-list.json
cat source/patterns/documentation/icons-list.json
```

**Points clés à identifier :**
- Quels **groupes** existent dans chaque JSON (ex: `extended`, `compact`, `semantic`, etc.)
- Quelle **structure de propriétés** utiliser (ex: `colorsList.extended.values`, `sizesList.compact.values`)
- Quels **chemins d'import** utiliser (toujours relatifs : `../../documentation/`)

### Étapes à suivre

1. **📖 Analyse préalable**
   - Lire le fichier stories actuel (`[chemin]/[composant].stories.jsx`)
   - Lire les fichiers associés (`.twig`, `.yml`, `.css`)
   - Identifier les props existantes et leurs types
   - Identifier les variants, tailles, couleurs, états disponibles
   - Lister les stories actuelles (nombre, types)

2. **🔍 Analyse d'impact**
   - Déterminer les changements nécessaires :
     * Props à renommer (suivre tableau de naming dans `STORYBOOK_DOC_TEMPLATE.md`)
     * ArgTypes à catégoriser (Content, Appearance, Behavior, Link, Accessibility, Layout)
     * Listes de valeurs à centraliser (couleurs, tailles, icônes, variants)
     * Stories individuelles à supprimer
     * Stories showcase à créer
   - Vérifier la compatibilité avec les fichiers JSON existants :
     * `source/patterns/documentation/colors-list.json`
     * `source/patterns/documentation/sizes-list.json`
     * `source/patterns/documentation/variants-list.json`
     * `source/patterns/documentation/icons-list.json`
   - Identifier si de nouvelles entrées doivent être ajoutées aux JSON

3. **📝 Rapport d'analyse (à présenter AVANT modification)**
   ```markdown
   ## Analyse du composant [NOM_COMPOSANT]
   
   ### État actuel
   - Nombre de stories: X
   - Props principales: [liste]
   - Stories individuelles: [liste]
   - Stories showcase existantes: [liste]
   
   ### Modifications proposées
   
   #### A. Renommage de props
   - `variant` → `color` (suit convention sémantique)
   - `[autre]` → `[nouveau]` (raison)
   
   #### B. Centralisation des listes
   - Couleurs: utiliser `colorsList.[groupe].values` (X options)
     * Groupe disponible: [lister les groupes du JSON]
   - Tailles: utiliser `sizesList.[groupe].values` (X options)
     * Groupe disponible: [lister les groupes du JSON]
   - Icônes: utiliser `iconsList.categories.[catégorie]` (X options)
   - Variants: utiliser `variantsList.[prop].[composant]` (X options)
   
   #### C. ArgTypes - Catégorisation
   - Content: [props]
   - Appearance: [props]
   - Behavior: [props]
   - Link: [props]
   - Accessibility: [props]
   - Layout: [props]
   
   #### D. Stories showcase à créer
   - AllColors (X variants)
   - AllSizes (X variants)
   - [Autres showcases pertinents]
   - UseCases (X cas d'usage réels)
   
   #### E. Stories à supprimer
   - [Liste des stories individuelles redondantes]
   
   #### F. Documentation
   - Ajouter `parameters.docs.description.component` avec:
     * Description générale
     * Couleurs disponibles
     * Tailles disponibles
     * Fonctionnalités (icônes, liens, états)
     * Accessibilité
     * Design tokens utilisés
     * Rendu minimal (classes par défaut vs modificateurs)
   
   ### Ajouts nécessaires aux JSON
   - `colors-list.json`: [si nouvelles couleurs]
   - `sizes-list.json`: [si nouvelles tailles]
   - `variants-list.json`: [si nouveaux variants]
   
   ### Impact sur autres fichiers
   - `.twig`: [modifications nécessaires ou OK]
   - `.yml`: [modifications nécessaires ou OK]
   - `.css`: [vérifications nécessaires ou OK]
   ```

4. **✅ Validation utilisateur**
   - Attendre confirmation avant de procéder aux modifications
   - Discuter des ajustements si nécessaire

5. **🔧 Application des modifications**
   - Utiliser `multi_replace_string_in_file` pour toutes les modifications du fichier stories
   - Mettre à jour les JSON si nécessaire
   - Vérifier la syntaxe (imports, exports, structures)

6. **🧪 Vérification post-modification**
   - Confirmer que tous les imports sont corrects (chemins relatifs `../../documentation/`)
   - Vérifier que toutes les stories exportées sont valides
   - S'assurer que la documentation `parameters.docs` est complète
   - Vérifier que les argTypes utilisent bien les listes centralisées

## 🎯 Standards à respecter

### Props naming (obligatoire)
| Prop Name | Purpose | Values Example |
|-----------|---------|----------------|
| `color` | Semantic color | primary, secondary, success, warning, danger, info, default, gold |
| `variant` | Component type/form | solid, outline, ghost, text |
| `size` | Size scale | small, medium, large |
| `orientation` | Direction | horizontal, vertical |
| `shape` | Geometric shape | rounded, square, pill, circle |
| `appearance` | Visual style | filled, outlined, minimal |
| `alignment` | Text/content alignment | left, center, right, justify |
| `position` | Spatial position | top, bottom, left, right |

### ArgTypes categories (obligatoire)
- **Content**: text, icon, label, title, description
- **Appearance**: color, variant, size, shape, appearance, orientation
- **Behavior**: disabled, loading, active, expanded, dismissible
- **Link**: url, href, target, rel
- **Accessibility**: ariaLabel, ariaDescribedBy, role, tabIndex
- **Layout**: alignment, position, spacing, width, height

### Stories structure (obligatoire)
```jsx
// ✅ CORRECT
export const Default = { ... };           // Story contrôlable
export const AllColors = { ... };         // Showcase groupée
export const AllSizes = { ... };          // Showcase groupée
export const UseCases = { ... };          // Showcase groupée

// ❌ INCORRECT
export const Primary = { ... };           // Story individuelle redondante
export const Secondary = { ... };         // Story individuelle redondante
export const Small = { ... };             // Story individuelle redondante
```

### Imports (obligatoire)
```jsx
// ✅ CORRECT - Chemins relatifs
import colorsList from '../../documentation/colors-list.json';
import sizesList from '../../documentation/sizes-list.json';
import iconsList from '../../documentation/icons-list.json';
import variantsList from '../../documentation/variants-list.json';

// ❌ INCORRECT - Alias non supporté
import colorsList from '@patterns/documentation/colors-list.json';
```

### Documentation format (obligatoire)
```jsx
export default {
  title: 'Elements/ComponentName',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Description concise du composant.\n\n' +
          '- **Couleurs**: liste et explication\n' +
          '- **Tailles**: liste et explication\n' +
          '- **[Feature]**: explication\n' +
          '- **Accessibilité**: points clés\n' +
          '- **Design tokens**: tokens utilisés\n' +
          '- **Rendu minimal**: classes par défaut vs modificateurs',
      },
    },
  },
  argTypes: {
    // Content
    propName: {
      control: 'type',
      description: 'Description',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'default' },
      },
    },
    // ... autres catégories
  },
};
```

## 📚 Références

- Template complet: `.github/STORYBOOK_DOC_TEMPLATE.md`
- Listes centralisées: `source/patterns/documentation/*.json`
- Documentation listes: `source/patterns/documentation/README.md`
- Exemple de référence: `source/patterns/elements/badge/badge.stories.jsx` (version standardisée)

## 🚀 Utilisation

```
Applique les standards Storybook au composant [NOM_COMPOSANT] :
1. Analyse l'état actuel
2. Identifie les modifications nécessaires
3. Présente un rapport d'analyse détaillé
4. Attends ma validation
5. Applique les modifications
6. Vérifie le résultat
```

---

**Note**: Ce prompt garantit une analyse complète AVANT modification, permettant de valider l'approche et d'éviter les erreurs.
