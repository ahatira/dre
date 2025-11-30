# Storybook Documentation Standard

**Version**: 1.0.0  
**Date**: 2025-11-29  
**Statut**: 📋 Standard obligatoire

---

## 🎯 Objectif

Définir un format standardisé pour la documentation des composants dans Storybook (via Autodocs), garantissant cohérence, lisibilité et complétude.

---

## 🌍 Language Rule (MANDATORY)

**ALL Storybook documentation MUST be written in English.**

This applies to:
- `parameters.docs.description.component` text
- `argTypes` descriptions
- Story comments and names
- Section titles (Variants, Accessibility, Design Tokens, etc.)

**Exception**: User-facing content in demo stories (e.g., "Rechercher" button label) can remain in French for realistic examples.

---

## 📐 Structure obligatoire

### 1. Bloc JSDoc (optionnel, en tête de fichier)

```jsx
/**
 * PS [ComponentName] — [Level]
 * [Brève description du composant]
 *
 * ## Props
 * | Prop       | Type    | Default  | Description                    |
 * |------------|---------|----------|--------------------------------|
 * | propName   | type    | default  | Description                    |
 *
 * ## Design Tokens
 * - Liste des tokens utilisés
 *
 * ## Accessibilité
 * - Points clés d'accessibilité
 *
 * ## Exemples d'usage
 * [Code HTML minimal]
 */
```

**Utilité** : Documentation pour les développeurs lisant le code source. Optionnel mais recommandé.

---

### 2. Export default avec parameters.docs.description.component (OBLIGATOIRE, BRÈVE)

```jsx
export default {
  title: '[Category]/[ComponentName]',
  tags: ['autodocs'],
  render: (args) => componentTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          '[Brève description en deux lignes max: rôle et comportement principal].\n\n' +
          'Détails complets dans les sections Props, Accessibilité, Tokens et Showcases.',
      },
    },
  },
  argTypes: {
    propName: {
      control: 'type',
      description: 'Description de la prop',
      table: {
        category: 'content|appearance|behavior|layout',
        type: { summary: 'string|number|boolean', required: true },
        defaultValue: { summary: 'default' },
      },
    },
  },
};
```

**Catégories argTypes** (approche Pattern UI) :
- **Content** : Props de contenu (text, label, initials, src, alt, children, etc.)
- **Appearance** : Props visuelles (variant, color, size, shape, pill, bordered, striped, etc.)
- **Behavior** : Props comportementales (clickable, disabled, indeterminate, collapsible, etc.)
- **Link** : Props de lien (url, href, target, rel, etc.)
- **Accessibility** : Props d'accessibilité (aria-label, role, etc.)
- **Layout** : Props de disposition (alignment, position, orientation, etc.)

**Attributs table recommandés** :
- `category` : Groupe logique de la prop (voir catégories ci-dessus)
- `type.summary` : Type de la prop (`string`, `number`, `boolean`, `enum`, etc.)
- `type.required` : Si la prop est obligatoire (`true` si requis)
- `defaultValue.summary` : Valeur par défaut affichée (ex: `'medium'`, `false`, `'circle'`)

**Règles de catégorisation** :
- **Content** : Tout ce qui définit le contenu affiché (texte, images, icônes, données)
- **Appearance** : Tout ce qui affecte l'apparence visuelle (couleurs, tailles, formes, styles)
- **Behavior** : Tout ce qui change le comportement interactif (états, actions, modes)
- **Link** : Spécifiquement pour les props de navigation/liens (séparé de Behavior pour clarté)
- **Accessibility** : Props ARIA et accessibilité explicites (même si d'autres props ont un impact a11y)
- **Layout** : Props affectant la disposition spatiale (alignement, direction, espacement)

---

### 3. ArgTypes détaillés (OBLIGATOIRE)

Chaque prop doit être documentée avec :

```jsx
argTypes: {
  // Content
  text: {
    control: 'text',
    description: 'Badge text',
    table: {
      category: 'Content',
      type: { summary: 'string', required: true },
    },
  },
  icon: {
    control: 'text',
    description: 'Icon name (semantic, used by CSS via data-icon)',
    table: {
      category: 'Content',
      type: { summary: 'string' },
    },
  },
  
  // Appearance
  variant: {
    control: { type: 'select' },
    options: ['default', 'primary', 'secondary', 'gold', 'info', 'success', 'warning', 'danger'],
    description: 'Color variant',
    table: {
      category: 'Appearance',
      defaultValue: { summary: 'default' },
    },
  },
  size: {
    control: { type: 'inline-radio' },
    options: ['small', 'medium', 'large'],
    description: 'Badge size',
    table: {
      category: 'Appearance',
      defaultValue: { summary: 'medium' },
    },
  },
  pill: {
    control: 'boolean',
    description: 'Rounded pill shape',
    table: {
      category: 'Appearance',
      defaultValue: { summary: false },
    },
  },
  
  // Link
  url: {
    control: 'text',
    description: 'Link URL (renders <a>)',
    table: {
      category: 'Link',
      type: { summary: 'string' },
    },
  },
};
```

**Utilité** : Documentation affichée dans Storybook Autodocs. **OBLIGATOIRE**.

---

## 📋 Sections standardisées

### Description principale (1ère ligne)
Phrase courte décrivant le rôle du composant.

**Exemple** :
```
'Indicateur de progression pour tâches déterminées ou indéterminées (upload, téléchargement, formulaire multi-étapes).\n\n'
```

---

### Variantes/Types (si applicable)
Liste les variantes principales du composant.

**Exemple** :
```
'- **Variantes**: linear (barre horizontale), circular (anneau).\n'
```

---

### Couleurs (si applicable)
Liste les couleurs sémantiques supportées avec référence aux tokens.

**Exemple** :
```
'- **Couleurs**: primary, secondary, success, warning, danger, info — tokens sémantiques via `--ps-color-*-600`.\n'
```

---

### Tailles (si applicable)
Liste les tailles disponibles avec valeurs concrètes.

**Exemple** :
```
'- **Tailles**: xs, sm, md (défaut), lg, xl — hauteurs linéaires: 4px, 8px, 12px; tailles circulaires: 40px, 64px, 96px.\n'
```

---

### États/Options (si applicable)
Liste les états spéciaux ou options comportementales.

**Exemple** :
```
'- **États**: indeterminate (animation infinie), striped (rayures animées pour linear).\n'
'- **Bordure**: `bordered` ajoute un liseré blanc pour fond sombre.\n'
'- **Icônes**: via nom d\'icône (font `bnpre-icons`) sans balise supplémentaire.\n'
```

---

### Props principaux (si complexe)
Résumé des props clés si le composant en a beaucoup.

**Exemple** :
```
'- **Label**: `showLabel` affiche le pourcentage; `label` fournit un texte pour les lecteurs d\'écran.\n'
```

---

### Accessibilité (OBLIGATOIRE)
Points clés d'accessibilité : rôles ARIA, attributs, focus, contraste.

**Exemple** :
```
'- **Accessibilité**: role="progressbar", aria-valuenow, aria-valuemin, aria-valuemax, aria-label; non focusable (élément non-interactif).\n'
'- **Accessibilité**: `alt` requis si image; les initiales servent de contenu textuel; focus visible quand `clickable` est vrai.\n'
```

---

### Design tokens (OBLIGATOIRE)
Liste les tokens CSS utilisés avec leurs catégories.

**Exemple** :
```
'- **Design tokens**: --ps-color-neutral-200 (track), --ps-border-radius-full, --ps-transition-duration-normal.\n'
'- **Design tokens**: couleurs via tokens de marque; espacements/typos pilotés par tokens.\n'
```

---

### Rendu minimal (OBLIGATOIRE)
Note explicative sur le markup minimal et l'approche BEM.

**Exemple** :
```
'- **Rendu minimal**: la classe de base applique les styles par défaut; les modificateurs n\'apparaissent que si l\'option change du défaut.'
```

---

## 🎨 Format Markdown

- Utiliser `\n\n` pour les sauts de ligne entre sections
- Utiliser `\n` pour les retours à la ligne dans une liste
- Utiliser `**Titre**:` pour les titres de section en gras
- Utiliser backticks (`` ` ``) pour les noms de props, tokens, classes CSS
- Utiliser tirets (`-`) pour les listes à puces
- Utiliser `|` pour énumérer des options (ex: `online | offline | busy`)

---

## 📚 Exemples de référence

### Exemple 1 : Avatar (composant simple)

```jsx
parameters: {
  docs: {
    description: {
      component:
        'Avatar affichant une photo, des initiales ou une icône par défaut.\n\n' +
        '- **Modes**: image (`src`), initiales (`initials`), fallback icône (si `src` et `initials` sont vides).\n' +
        '- **Tailles**: xs, sm, md (défaut), lg, xl — valeurs exactes via tokens.\n' +
        '- **Formes**: circle (défaut), square, rounded.\n' +
        '- **Statut**: `status` = online | offline | busy (badge en bas à droite).\n' +
        '- **Bordure**: `bordered` ajoute un liseré blanc pour fond sombre.\n' +
        '- **Accessibilité**: `alt` requis si image; les initiales servent de contenu textuel; focus visible quand `clickable` est vrai.\n' +
        "- **Rendu minimal**: la classe de base applique les styles par défaut; les modificateurs n'apparaissent que si l'option change du défaut.",
    },
  },
},
```

---

### Exemple 2 : Badge (composant avec variantes)

```jsx
parameters: {
  docs: {
    description: {
      component:
        'Badge compact indiquant un état ou une étiquette.\n\n' +
        '- **Variants**: default (gris), primary (vert), secondary (violet), gold, info, success, warning, danger — couleurs via tokens de marque.\n' +
        '- **Tailles**: small, medium (défaut), large — espacements/typos pilotés par tokens.\n' +
        '- **Forme**: base arrondie (rayon par défaut), option `pill` pour pilule.\n' +
        "- **Icônes**: via nom d'icône (font `bnpre-icons`) sans balise supplémentaire.\n" +
        '- **Liens**: `url` rend une balise <a> accessible.\n' +
        "- **Accessibilité**: texte toujours lisible (contraste défini par tokens); focus visible sur les liens; rôle implicite d'étiquette.\n" +
        "- **Marquage minimal**: `.ps-badge` fournit les styles par défaut; les modificateurs n'apparaissent que si une option diffère du défaut.",
    },
  },
},
```

---

### Exemple 3 : Progress Bar (composant complexe)

```jsx
parameters: {
  docs: {
    description: {
      component:
        'Indicateur de progression pour tâches déterminées ou indéterminées (upload, téléchargement, formulaire multi-étapes).\n\n' +
        '- **Variantes**: linear (barre horizontale), circular (anneau).\n' +
        '- **Couleurs**: primary, secondary, success, warning, danger, info — tokens sémantiques via `--ps-color-*-600`.\n' +
        '- **Tailles**: xs, sm, md (défaut), lg, xl — hauteurs linéaires: 4px, 8px, 12px; tailles circulaires: 40px, 64px, 96px.\n' +
        '- **États**: indeterminate (animation infinie), striped (rayures animées pour linear).\n' +
        '- **Label**: `showLabel` affiche le pourcentage; `label` fournit un texte pour les lecteurs d\'écran.\n' +
        '- **Accessibilité**: role="progressbar", aria-valuenow, aria-valuemin, aria-valuemax, aria-label; non focusable (élément non-interactif).\n' +
        '- **Design tokens**: --ps-color-neutral-200 (track), --ps-border-radius-full, --ps-transition-duration-normal.\n' +
        '- **Rendu minimal**: la classe de base applique les styles par défaut; les modificateurs n\'apparaissent que si l\'option change du défaut.',
    },
  },
},
```

---

## ✅ Checklist de validation

Avant de considérer la documentation d'un composant comme complète, vérifier :

- [ ] Description principale claire et concise
- [ ] Variantes/types listés (si applicable)
- [ ] Couleurs sémantiques documentées avec tokens (si applicable)
- [ ] Tailles documentées avec valeurs concrètes (si applicable)
- [ ] États/options comportementales listés (si applicable)
- [ ] Section **Accessibilité** présente et complète
- [ ] Section **Design tokens** présente avec tokens spécifiques
- [ ] Note **Rendu minimal** présente
- [ ] Format Markdown correct (sauts de ligne, gras, backticks)
- [ ] Cohérence avec les exemples de référence
- [ ] **ArgTypes organisés** avec `table.category` (Content, Appearance, Behavior, Link, Accessibility, Layout)
- [ ] **ArgTypes complets** avec `type.summary`, `type.required` (si applicable), `defaultValue.summary` (si applicable)
- [ ] **ArgTypes groupés logiquement** : commentaires séparant les catégories dans le code
- [ ] **Nommage des props standardisé** : `color` (couleur), `variant` (type/forme), `size` (taille), `orientation` (direction), etc.
- [ ] **Stories showcase uniquement** : pas de stories individuelles, uniquement Default + groupements logiques (AllColors, AllSizes, UseCases)
- [ ] **Listes de données centralisées** : utilisation de `/documentation/*.json` pour icons, sizes, colors, variants

---

## 🔧 Workflow d'implémentation

1. **Lire la spec** dans `docs/design/{level}/{component}.md`
2. **Identifier les sections** applicables (variantes, couleurs, tailles, états, etc.)
3. **Rédiger la description** en suivant le format standardisé
4. **Vérifier les tokens** utilisés dans le CSS du composant
5. **Documenter l'accessibilité** (rôles ARIA, focus, contraste)
6. **Ajouter la note** sur le rendu minimal
7. **Organiser les argTypes** selon les catégories Pattern UI
8. **Créer les stories showcase** (pas de stories individuelles)
9. **Valider** avec la checklist ci-dessus
10. **Tester** le rendu dans Storybook Autodocs

---

## 📖 Stories : Structure standardisée

### ❌ À ÉVITER : Stories individuelles

Ne pas créer de stories pour chaque variant individuel :

```jsx
// ❌ MAUVAIS
export const Primary = { args: { variant: 'primary' } };
export const Secondary = { args: { variant: 'secondary' } };
export const Success = { args: { variant: 'success' } };
// ... etc.
```

### ✅ À FAIRE : Stories groupées logiques

Créer uniquement :
1. **Default** : Story par défaut avec valeurs communes
2. **Stories showcase** : Groupements logiques affichant tous les variants

```jsx
// ✅ BON
export const Default = {
  render: (args) => componentTwig(args),
  args: { ...data },
};

// Showcase logique : Toutes les couleurs
export const AllColors = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${component({ variant: 'default', text: 'Default' })}
      ${component({ variant: 'primary', text: 'Primary' })}
      ${component({ variant: 'secondary', text: 'Secondary' })}
      ${component({ variant: 'success', text: 'Success' })}
      ${component({ variant: 'warning', text: 'Warning' })}
      ${component({ variant: 'danger', text: 'Danger' })}
      ${component({ variant: 'info', text: 'Info' })}
    </div>
  `,
};

// Showcase logique : Toutes les tailles
export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${component({ size: 'xs', text: 'XS' })}
      ${component({ size: 'sm', text: 'SM' })}
      ${component({ size: 'md', text: 'MD' })}
      ${component({ size: 'lg', text: 'LG' })}
      ${component({ size: 'xl', text: 'XL' })}
    </div>
  `,
};

// Showcase logique : Cas d'usage réels
export const UseCases = {
  render: () => `
    <div style="display: grid; gap: var(--size-6);">
      <div>
        <h4>Notification de succès</h4>
        ${component({ variant: 'success', text: 'Enregistré', icon: 'check' })}
      </div>
      <div>
        <h4>Badge exclusif</h4>
        ${component({ variant: 'gold', text: 'Exclusivité', icon: 'medal', pill: true })}
      </div>
    </div>
  `,
};
```

### 📋 Nommage des stories showcase

**Format** : `All[Dimension]` ou `[UseCase]`

- **AllColors** : Toutes les variantes de couleur
- **AllSizes** : Toutes les tailles
- **AllShapes** : Toutes les formes
- **AllStates** : Tous les états (hover, disabled, loading, etc.)
- **WithIcons** : Exemples avec icônes
- **WithLinks** : Exemples de liens
- **UseCases** : Cas d'usage réels dans un contexte

---

## 🏷️ Nommage des Props : Standard

### Principe : Un nom = Un concept

**❌ PROBLÈME** : `variant` utilisé pour différents concepts
```jsx
// ❌ Ambigu
variant: 'primary'     // couleur ?
variant: 'horizontal'  // orientation ?
variant: 'linear'      // type ?
```

**✅ SOLUTION** : Noms spécifiques par concept

```jsx
// ✅ Clair et sans ambiguïté
color: 'primary'           // Couleur sémantique
orientation: 'horizontal'  // Direction spatiale
variant: 'linear'          // Type/forme de composant
```

### Standard de nommage des props

| Concept | Nom de prop | Valeurs typiques | Exemple |
|---------|-------------|------------------|---------|
| **Couleur sémantique** | `color` | primary, secondary, success, warning, danger, info | `color: 'primary'` |
| **Type/Forme** | `variant` | linear, circular, solid, outlined, ghost | `variant: 'linear'` |
| **Taille** | `size` | xs, sm, md, lg, xl | `size: 'md'` |
| **Orientation** | `orientation` | horizontal, vertical | `orientation: 'horizontal'` |
| **Forme géométrique** | `shape` | circle, square, rounded | `shape: 'circle'` |
| **État visuel** | `appearance` | solid, outlined, ghost, soft | `appearance: 'outlined'` |
| **Alignement** | `alignment` | start, center, end, justify | `alignment: 'center'` |
| **Position** | `position` | top, right, bottom, left | `position: 'top'` |
| `icon` | Icon name (string) | check, calendar, close, medal | `icon: 'check'` |

### Règles spécifiques pour les icônes

**Icônes contrôlables par l'utilisateur** (prop du composant):
- ✅ Nom de prop: `icon` (string)
- ✅ Valeur **SANS préfixe "icon-"**: 'check', 'calendar', 'medal' (PAS 'icon-check')
- ✅ Utiliser le composant icon: `{%- include '@elements/icon/icon.twig' with { name: icon } only -%}`
- ✅ Storybook control: `control: 'select', options: ['', ...iconsList.categories.generic]`

**Icônes décoratives** (CSS uniquement, non contrôlables):
- ✅ Utiliser attribut `data-icon` **SANS préfixe**: `<span class="ps-component__icon" data-icon="check"></span>`
- ✅ Component CSS: **NE PAS ajouter de mappings data-icon**
  - Tous les mappings `[data-icon]` sont centralisés dans `source/props/icons.css`
  - CSS du composant: seulement font-family et styles de base
  ```css
  .ps-component__icon {
    font-family: 'bnpre-icons';
    /* PAS de [data-icon="..."]::before ici */
  }
  ```
- ❌ Éviter markup supplémentaire: `<i class="icon-name"></i>` ou classes `ps-icon ps-icon-*`

**Important**: Toujours utiliser les noms d'icônes SANS le préfixe "icon-" dans les props, data-icon, YAML, et documentation.

### Exemples d'application

#### Progress Bar
```jsx
// ✅ CORRECT
variant: 'linear' | 'circular'      // Type de barre
color: 'primary' | 'success' | ...  // Couleur
size: 'sm' | 'md' | 'lg'            // Taille
```

#### Divider
```jsx
// ✅ CORRECT
orientation: 'horizontal' | 'vertical'  // Direction
variant: 'solid' | 'dashed' | 'dotted'  // Style de trait
color: 'default' | 'primary' | ...      // Couleur (si applicable)
```

#### Button
```jsx
// ✅ CORRECT
color: 'primary' | 'secondary' | ...    // Couleur
appearance: 'solid' | 'outlined' | ...  // Style de remplissage
size: 'sm' | 'md' | 'lg'                // Taille
```

---

## 📚 Listes de données : Standardisation

### Principe : Données centralisées dans `/documentation/`

Toutes les listes de valeurs (icônes, tailles, couleurs, etc.) doivent être externalisées dans des fichiers JSON dans `source/patterns/documentation/`.

### Structure des fichiers de données

#### `icons-list.json` (existant)
```json
{
  "regular": ["icon-search", "icon-check", ...],
  "poi": ["icon-poi-sport", ...],
  "categories": {
    "generic": ["icon-search", "icon-check", ...],
    "social": ["icon-facebook", "icon-linkedin", ...]
  },
  "all": [...]
}
```

#### `sizes-list.json` (à créer)
```json
{
  "standard": ["xs", "sm", "md", "lg", "xl"],
  "compact": ["sm", "md", "lg"],
  "extended": ["2xs", "xs", "sm", "md", "lg", "xl", "2xl"],
  "tokens": {
    "xs": "var(--size-2)",
    "sm": "var(--size-3)",
    "md": "var(--size-4)",
    "lg": "var(--size-6)",
    "xl": "var(--size-8)"
  }
}
```

#### `colors-list.json` (à créer)
```json
{
  "semantic": ["primary", "secondary", "success", "warning", "danger", "info"],
  "neutral": ["default", "muted", "subtle"],
  "brand": ["primary", "secondary", "gold"],
  "tokens": {
    "primary": "var(--brand-primary)",
    "secondary": "var(--brand-secondary)",
    "success": "var(--brand-primary)",
    "warning": "var(--brand-warning)",
    "danger": "var(--brand-danger)",
    "info": "var(--brand-info)",
    "gold": "var(--gold-500)"
  }
}
```

**⚠️ IMPORTANT** : Cette structure est un **EXEMPLE**. Avant de créer ce fichier :
1. Auditer les tokens existants dans `source/props/colors.css` et `source/props/brand.css`
2. Identifier les tokens réellement utilisés pour les couleurs sémantiques
3. Standardiser les noms et valeurs en cohérence avec l'existant
4. Documenter les choix dans `docs/ps-design/CHANGELOG.md`

#### `variants-list.json` (à créer)
```json
{
  "button": {
    "appearance": ["solid", "outlined", "ghost", "soft"],
    "colors": ["primary", "secondary", "success", "warning", "danger", "info"]
  },
  "progressBar": {
    "variant": ["linear", "circular"],
    "colors": ["primary", "secondary", "success", "warning", "danger", "info"]
  },
  "divider": {
    "orientation": ["horizontal", "vertical"],
    "variant": ["solid", "dashed", "dotted"]
  }
}
```

### Utilisation dans les stories

```jsx
// Import des listes
import iconsList from '@patterns/documentation/icons-list.json';
import sizesList from '@patterns/documentation/sizes-list.json';
import colorsList from '@patterns/documentation/colors-list.json';

export default {
  argTypes: {
    icon: {
      control: 'select',
      options: iconsList.categories.generic, // ✅ Liste centralisée
      description: 'Icon name',
    },
    size: {
      control: 'select',
      options: sizesList.standard, // ✅ Liste centralisée
      description: 'Size',
    },
    color: {
      control: 'select',
      options: colorsList.semantic, // ✅ Liste centralisée
      description: 'Color',
    },
  },
};
```

### Avantages

- ✅ **Single Source of Truth** : Une seule définition par liste
- ✅ **Cohérence** : Tous les composants utilisent les mêmes valeurs
- ✅ **Maintenabilité** : Modification en un seul endroit
- ✅ **Documentation** : Listes disponibles pour la doc automatique
- ✅ **Validation** : Possibilité de valider les valeurs via JSON Schema

---

## 🔧 Workflow d'implémentation (mis à jour)

---

## 📚 Références

- [Storybook Autodocs](https://storybook.js.org/docs/react/writing-docs/autodocs)
- [Component Template Standard](.github/COMPONENT_TEMPLATE_STANDARD.md)
- [Component Audit Prompt](.github/COMPONENT_AUDIT_PROMPT.md)

---

## 🔄 Maintenance

Ce template sera mis à jour si de nouvelles sections ou conventions émergent. Consulter `docs/ps-design/CHANGELOG.md` pour l'historique des modifications.
