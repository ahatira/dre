# 🎯 Prompt de Génération de Composant PS Theme

**Version**: 2.0.0  
**Date**: 2025-12-03  
**Statut**: 🔒 **PROMPT STANDARDISÉ - À UTILISER SYSTÉMATIQUEMENT**

---

## 📋 Template de Prompt

```
Génère, complète et vérifie le composant [ComponentName] pour le PS Theme en respectant STRICTEMENT toutes les règles du projet.

## 🎯 DIRECTIVE PRINCIPALE

**AVANT TOUTE ACTION** : Lis `.github/COMPLETE_RULES.md` (2300+ lignes, 20 sections) — c'est la RÉFÉRENCE ABSOLUE du projet.

## 📖 WORKFLOW OBLIGATOIRE

### 1. ANALYSE DE LA SPÉCIFICATION

1.1. Lis la spec complète dans `docs/design/{level}/{component}.md`
1.2. Si une maquette est fournie, analyse-la pour valider les dimensions, couleurs, espacements exactes
1.3. Identifie le level atomic (elements/components/collections/layouts/pages)
1.4. Liste TOUS les variants, modifiers, tailles et états interactifs requis
1.5. Note les tokens design attendus (couleurs hex, espacements, typographie)

### 2. VÉRIFICATION DES DÉPENDANCES (CRITICAL pour Molecules+)

**⚠️ OBLIGATOIRE pour components/collections/layouts/pages**

2.1. Identifie les atoms requis pour la composition
2.2. Vérifie leur existence : `ls source/patterns/elements/`
2.3. Si manquants : STOP et demande confirmation pour créer les atoms d'abord
2.4. Documente la stratégie de composition dans README.md

**Référence** : `.github/COMPLETE_RULES.md` Section 2 + `.github/ATOMIC_DESIGN_RULES.md`

### 3. VÉRIFICATION DES TOKENS (AVANT création)

3.1. Recherche si tokens similaires existent :
```bash
grep -r "--token-name" source/props/
```

3.2. Vérifie cohérence avec tokens existants (naming, progression)
3.3. **NE JAMAIS modifier `source/props/*.css` directement**
3.4. Si token manquant : documente besoin et propose ajout via PR dédiée

**Référence** : `.github/COMPLETE_RULES.md` Section 4

### 4. IMPLÉMENTATION (5 FICHIERS OBLIGATOIRES)

**Structure stricte** :
```
source/patterns/{level}/{component}/
├── {component}.twig          # Template Drupal-ready
├── {component}.css           # Styles BEM + nesting
├── {component}.yml           # Données par défaut
├── {component}.stories.jsx   # Stories Storybook
└── README.md                 # Documentation (EN)
```

**Référence** : `.github/COMPONENT_TEMPLATE_STANDARD.md`

#### 4.1. Template Twig

✅ **OBLIGATOIRE** :
- Commentaire d'en-tête avec TOUS les `@param` documentés
- Valeurs par défaut : `{% set prop = prop|default('value') %}`
- Classes BEM : array merge conditionnel
  ```twig
  {%- set classes = ['ps-component'] -%}
  {%- if size != 'md' -%}
    {%- set classes = classes|merge(['ps-component--' ~ size]) -%}
  {%- endif -%}
  ```
- **Drupal-ready** : TOUJOURS ternaire avec `null` (PAS de `filter(v => v)`)
  ```twig
  {# ✅ CORRECT - Compatible Drupal #}
  {% set class = condition ? 'class-name' : null %}
  
  {# ❌ INTERDIT - Arrow functions non supportées #}
  {% set class = items|filter(v => v)|join(' ') %}
  ```
- Attributs ARIA appropriés
- Placeholders contextuels (Real Estate) : "Modern office building", "Property listing", etc.

**Référence** : `.github/COMPLETE_RULES.md` Section 12

#### 4.2. Styles CSS

✅ **RÈGLES ABSOLUES** :
- **JAMAIS de valeurs en dur** : `#00915A` → `var(--primary)`
- **Tokens uniquement** : couleurs, tailles, espacements, transitions
- **BEM strict** : `.ps-component`, `.ps-component__element`, `.ps-component--modifier`
- **CSS Nesting** : syntaxe `&` obligatoire (postcss-nested supporté)
  ```css
  .ps-component {
    /* Base styles */
    
    &__element {
      /* Element styles */
    }
    
    &--modifier {
      /* Modifier styles */
    }
    
    &:hover:not(:disabled) {
      /* Hover state */
    }
    
    &:focus-visible {
      outline: var(--border-size-2) solid var(--secondary);
      outline-offset: var(--border-size-2);
    }
  }
  ```
- **Cascade correct** : Base AVANT modifiers dans le code source
- **Modifiers indépendants** : chaque modifier fonctionne seul
- **Minimal markup** : classe base contient defaults, pas de modifiers pour valeurs par défaut
- **Focus visible** : tous les interactifs ont `:focus-visible`
- **Transitions** : tokens `var(--duration-*)` + `var(--ease-*)`
- **Contraste WCAG AA** : 4.5:1 (texte normal), 3:1 (texte large/UI)

**Ordre des sections CSS** :
1. Base styles
2. Elements (nested)
3. Variants (nested)
4. Sizes (nested)
5. Colors (nested)
6. States (`:hover`, `:active`, `:focus-visible`, `:disabled`)

**Référence** : `.github/COMPLETE_RULES.md` Sections 4-8 + `.github/CSS_STANDARDS.md`

#### 4.3. YAML Configuration

✅ **STRUCTURE** :
```yaml
# Default: Description de l'état par défaut
prop1: 'value'
variant: 'primary'
size: 'md'
disabled: false

# variant options: default, primary, secondary, success, warning, danger, info
# size options: xs (24px), sm (32px), md (40px), lg (48px), xl (80px)
```

**Référence** : `.github/COMPLETE_RULES.md` Section 13

#### 4.4. Stories Storybook

✅ **RÈGLES CRITIQUES** :
- **Pas de React/JSX** : Import Twig uniquement
  ```jsx
  import componentTwig from './component.twig';
  import data from './component.yml';
  ```
- **Render Twig** : `render: (args) => componentTwig(args)`
- **tags: ['autodocs']** : OBLIGATOIRE dans export default
- **Description concise** : ≤ 2 lignes dans `parameters.docs.description.component`
  ```jsx
  parameters: {
    docs: {
      description: {
        component: 'Brief description (max two lines) summarizing role and behavior.\n\nDetails in Props, Accessibility, Tokens sections.',
      },
    },
  },
  ```
- **ArgTypes catégorisés** : Content | Appearance | Behavior | Link | Accessibility | Layout
  ```jsx
  argTypes: {
    text: {
      description: 'Component text',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
      },
    },
    color: {
      description: 'Color variant',
      control: 'select',
      options: colorsList.semantic.values, // Liste centralisée
      table: {
        category: 'Appearance',
        defaultValue: { summary: 'primary' },
      },
    },
  }
  ```
- **Stories showcase** : Default + groupements (AllColors, AllSizes, UseCases)
  ```jsx
  export const Default = {
    render: (args) => componentTwig(args),
    args: { ...data },
  };
  
  export const AllColors = {
    render: () => `
      <div style="display: flex; gap: var(--size-4);">
        ${componentTwig({ color: 'primary' })}
        ${componentTwig({ color: 'secondary' })}
        ${componentTwig({ color: 'success' })}
      </div>
    `,
  };
  ```
- **Pas de stories individuelles** (Primary, Secondary, Small, etc.)
- **Listes centralisées** : Import de `/documentation/*.json`

**Référence** : `.github/COMPLETE_RULES.md` Section 11 + `.github/STORYBOOK_DOC_TEMPLATE.md`

#### 4.5. README.md

✅ **STRUCTURE OBLIGATOIRE** (EN ANGLAIS) :
```markdown
# Component Name

Brief description (max two lines).

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| text | string | '' | Text content |
| color | string | 'primary' | Color variant |

## BEM Structure

- `.ps-component` - Block
- `.ps-component__element` - Element
- `.ps-component--modifier` - Modifier

## Design Tokens

- `--primary` - Primary color
- `--size-4` - Default padding
- `--font-size-2` - Text size

## Usage

\`\`\`twig
{% include '@{level}/{component}/{component}.twig' with {
  text: 'Example',
  color: 'primary',
  size: 'md'
} only %}
\`\`\`

## Real-World Use Cases

- **Status badge**: Display "New", "In stock", "Sale"
- **Category tag**: Content filters

## Accessibility

- Text contrast meets WCAG AA (4.5:1)
- Focus visible on interactive variants
- ARIA labels when needed

## Variants

- **Colors**: primary, secondary, success, warning, danger, info
- **Sizes**: xs, sm, md (default), lg, xl
```

**Référence** : `.github/COMPLETE_RULES.md` Section 14

### 5. RÈGLES SPÉCIALES (ZERO TOLERANCE)

#### 5.1. Icons System

**Icon contrôlable** (prop du composant) :
```twig
{%- if icon -%}
  {%- include '@elements/icon/icon.twig' with {
    name: icon,
    size: 'small'
  } only -%}
{%- endif -%}
```
- Prop : `icon` (string)
- Valeur **SANS préfixe "icon-"** : `'check'`, `'calendar'`, `'medal'`
- Storybook control : `select` avec `iconsList.categories.generic`

**Icon décoratif** (CSS uniquement) :
```twig
<span class="ps-component__icon" data-icon="check"></span>
```
```css
.ps-component__icon {
  font-family: 'bnpre-icons';
  font-style: normal;
  line-height: 1;
  /* PAS de mappings [data-icon] ici - centralisés dans icons.css */
}
```

**Référence** : `.github/COMPLETE_RULES.md` Section 9

#### 5.2. Semantic Color Naming

**OBLIGATOIRE** : Noms sémantiques uniquement (jamais "green", "purple", etc.)

| Sémantique | Token | Usage |
|------------|-------|-------|
| primary | `--primary` | Action principale |
| secondary | `--secondary` | Action secondaire |
| success | `--success` | Succès, validation |
| warning | `--warning` | Avertissement |
| danger | `--danger` | Erreur, suppression |
| info | `--info` | Information |

**Règle des 6 couleurs** : Si composant a variantes couleur → supporter TOUTES les 6

**Référence** : `.github/COMPLETE_RULES.md` Section 10

#### 5.3. JavaScript Integration (si nécessaire)

**AVANT toute implémentation JS custom** :

1. **Évaluer complexité** : Interactions complexes ? Standards UI établis ?
2. **Rechercher librairies** : Critères prioritaires
   - Popularité (> 5k stars, < 6 mois dernière release)
   - Bundle size (< 50KB minified+gzipped)
   - Accessibilité native (WCAG AA)
   - Framework-agnostic (Vanilla JS ou adaptable)
3. **Proposer 2-3 options** : Tableau comparatif avec métriques
4. **Si librairie choisie** : Wrapper Drupal behavior
5. **Si custom** : Suivre patterns Section 19

**Intégration Storybook** :
- ✅ Import global dans `.storybook/preview.js`
- ❌ JAMAIS dans `.stories.jsx` individuel (timing Drupal.attachBehaviors)

**Référence** : `.github/COMPLETE_RULES.md` Sections 19-20

### 6. VALIDATION & BUILD

6.1. **Build** : `npm run build` → Vérifier 0 erreurs
6.2. **Storybook** : `npm run watch` → Tester toutes stories
6.3. **Audit conformité** : Exécuter `.github/COMPONENT_CONFORMITY_PROMPT.md`
6.4. **Tests manuels** :
- Responsive (xs, sm, md, lg, xl)
- Keyboard nav (Tab, Enter, Space, Arrows)
- Focus visible sur tous interactifs
- Contraste couleurs (WebAIM Contrast Checker)

### 7. DOCUMENTATION & COMMIT

7.1. **Commit structuré** :
```
feat({level}): add {ComponentName} component

- Implement Twig template with all props
- Add CSS with nesting and tokens only
- Create Storybook stories (Default + showcases)
- Add README with full documentation (EN)
- Follow BEM strict, minimal markup, modifiers independence

SPECS: docs/design/{level}/{component}.md
TOKENS: [list new tokens if any]
BUILD: ✓ 0 errors ([size] CSS)
```

7.2. **Update CHANGELOG** : `docs/ps-design/CHANGELOG.md`
```markdown
## [Date] - {ComponentName}

### Added
- {ComponentName} ({level}) with X variants
- Stories: Default, AllColors, AllSizes, UseCases
- Tokens: [justify if new]

### Technical
- BEM: ps-component, ps-component__element, ps-component--modifier
- Variants: [list]
- Accessibility: [key points]
```

**Référence** : `.github/COMPLETE_RULES.md` Section 17

## 🚨 CHECKLIST FINALE (18 POINTS CRITIQUES)

**À VALIDER AVANT DE CONSIDÉRER LE COMPOSANT TERMINÉ** :

### Language
- [ ] **ALL documentation in English** (README, Storybook, comments)
- [ ] User-facing content in French (placeholders contextuels Real Estate)

### Fichiers
- [ ] 5 fichiers existent : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
- [ ] Nommage cohérent (même nom de base)

### Twig
- [ ] Commentaire d'en-tête avec `@param` complets
- [ ] Valeurs par défaut : `|default()`
- [ ] Classes BEM : array merge conditionnel
- [ ] **Drupal-ready** : ternaire avec `null` (PAS `filter(v => v)`)
- [ ] Minimal markup (pas de classes pour defaults)
- [ ] Attributs ARIA appropriés

### CSS
- [ ] **Tokens uniquement** (aucune valeur en dur)
- [ ] **Préfixe `ps-`** pour tous sélecteurs
- [ ] **CSS nesting** avec `&` syntax
- [ ] **Ordre cascade** correct (base avant modifiers)
- [ ] **Modifiers indépendants** (fonctionnent seuls)
- [ ] **Focus-visible** sur éléments interactifs
- [ ] **Transitions** avec tokens (duration + easing)
- [ ] **Contraste WCAG AA** respecté

### Storybook
- [ ] **Import Twig** unique (`componentTwig`)
- [ ] **Pas de JSX/React** (render HTML strings)
- [ ] **tags: ['autodocs']** présent
- [ ] **Description ≤ 2 lignes** (concise)
- [ ] **ArgTypes catégorisés** (Content, Appearance, Behavior, Link, Accessibility, Layout)
- [ ] **Story Default** contrôlable
- [ ] **Stories showcase** uniquement (AllColors, AllSizes, UseCases)
- [ ] **Listes centralisées** (JSON imports)

### YAML
- [ ] Valeurs par défaut sensibles
- [ ] Commentaires listant options

### README
- [ ] Description concise (≤ 2 lignes)
- [ ] Table props complète
- [ ] BEM structure documentée
- [ ] Design tokens listés
- [ ] Exemples usage (Twig)
- [ ] Cas réels utilisation
- [ ] Notes accessibilité

### Semantic Colors
- [ ] **Nomenclature sémantique** : primary, secondary, success, warning, danger, info
- [ ] **Aucun nom arbitraire** (green, purple, blue, etc.)
- [ ] **Si variantes couleur** : Support des 6 couleurs

### Icons
- [ ] **Icon contrôlable** : `@elements/icon/icon.twig`
- [ ] **Icon décoratif** : `data-icon` **SANS préfixe "icon-"**
- [ ] **Component CSS** : NE PAS ajouter mappings `[data-icon]`

### Atomic Design (si Molecule+)
- [ ] Dépendances atoms identifiées
- [ ] Stratégie composition documentée
- [ ] Includes atoms existants (pas recréation markup)

### Build & Test
- [ ] `npm run build` → 0 erreurs
- [ ] `npm run watch` → Storybook affiche correctement
- [ ] Pas erreur console navigateur
- [ ] Audit conformité passé

## 📚 RÉFÉRENCES RAPIDES

| Besoin | Document |
|--------|----------|
| **Référence absolue** | `.github/COMPLETE_RULES.md` (2300+ lignes) |
| **Structure 5 fichiers** | `.github/COMPONENT_TEMPLATE_STANDARD.md` |
| **CSS deep dive** | `.github/CSS_STANDARDS.md` (400+ lignes) |
| **CSS Variables System** | `.github/CSS_VARIABLES_SYSTEM.md` |
| **Storybook format** | `.github/STORYBOOK_DOC_TEMPLATE.md` |
| **Atomic Design** | `.github/ATOMIC_DESIGN_RULES.md` |
| **Audit conformité** | `.github/COMPONENT_CONFORMITY_PROMPT.md` |
| **Spécifications** | `docs/design/{level}/{component}.md` |

## 🎓 COMPOSANTS DE RÉFÉRENCE

**Étudier ces implémentations parfaites** :
- `source/patterns/elements/button/` - CSS nesting, tous états, stories complètes
- `source/patterns/elements/avatar/` - Minimal markup, sizing adaptatif
- `source/patterns/elements/badge/` - Couleurs sémantiques, pill variant
- `source/patterns/elements/divider/` - Simplicité, orientation variants

## ⚠️ ERREURS FRÉQUENTES À ÉVITER

1. ❌ Valeurs en dur dans CSS (`#00915A`, `16px`)
2. ❌ Oublier `tags: ['autodocs']` dans `.stories.jsx`
3. ❌ Stories individuelles (Primary, Secondary, etc.)
4. ❌ Descriptions longues (> 2 lignes) dans Storybook
5. ❌ Arrow functions dans Twig (`filter(v => v)`)
6. ❌ Classes pour valeurs par défaut dans markup
7. ❌ Modifiers nécessitant classes composées
8. ❌ Noms couleurs arbitraires (green → primary)
9. ❌ Préfixe "icon-" dans props/data-icon
10. ❌ Modifier directement `source/props/*.css`
11. ❌ Créer Molecule sans vérifier atoms disponibles
12. ❌ Manquer section README (Props, BEM, Tokens, Accessibility)

---

**Ce prompt est maintenant READY TO USE. Copier-coller et remplacer `[ComponentName]` et `{level}`.**
```
