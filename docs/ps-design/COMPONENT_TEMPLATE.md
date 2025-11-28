# Template de Composant PS Design System

Ce document décrit la structure standard à suivre pour **chaque composant** du PS Design System.

---

## Structure des Fichiers

Chaque composant doit contenir **5 fichiers obligatoires** :

```
source/patterns/{level}/{component-name}/
├── {component-name}.css          # Styles BEM avec tokens CSS
├── {component-name}.twig         # Template Twig avec paramètres
├── {component-name}.yml          # Données par défaut pour preview
├── {component-name}.stories.jsx  # Stories Storybook (variants)
└── {component-name}.mdx          # Documentation Storybook
```

**Niveaux (levels)** : `elements`, `components`, `collections`, `layouts`, `pages`

---

## 1. Fichier `.twig` (Template)

### Structure Standard

```twig
{#
 * {Component Name} {level}
 * @param string prop1 - Description (required)
 * @param string prop2 - Description (default: value)
 * @param boolean prop3 - Description (default: false)
 #}

{% set prop2 = prop2|default('value') %}
{% set prop3 = prop3|default(false) %}

{% set classes = [
  'ps-component',
  'ps-component--' ~ variant,
  modifier ? 'ps-component--' ~ modifier,
  disabled ? 'ps-component--disabled',
] %}

<div class="{{ classes|join(' ')|trim }}"{% if attributes %} {{ attributes }}{% endif %}>
  {# Content here #}
</div>
```

### Règles Twig

1. **Header de documentation** : Tous les paramètres avec types et defaults
2. **Valeurs par défaut** : Utiliser `|default()`
3. **Classes BEM** : Array `classes` avec préfixe `ps-`
4. **Attributes** : Support de `{{ attributes }}` pour Drupal
5. **Sémantique HTML** : Utiliser les balises appropriées (`button`, `nav`, `article`, etc.)
6. **Accessibilité** : Attributs ARIA quand nécessaire

---

## 2. Fichier `.css` (Styles)

### Structure Standard

```css
/** {Component Name} {level} - Description */

.ps-component {
  /* Reset (si nécessaire) */
  appearance: none;
  
  /* Layout */
  display: flex;
  
  /* Sizing */
  height: var(--ps-size-default);
  padding: var(--ps-spacing-md);
  
  /* Typography */
  font-family: var(--ps-font-family-primary);
  font-size: var(--ps-font-size-base);
  
  /* Visual */
  background-color: var(--ps-bg-card);
  border: 1px solid var(--ps-border-default);
  border-radius: 0; /* Design carré par défaut */
  
  /* Transitions */
  transition: all var(--ps-duration-fast) var(--ps-easing-standard);
}

/* Elements BEM */
.ps-component__element {
  /* Styles */
}

/* Modifiers BEM */
.ps-component--variant {
  /* Override styles */
}

/* States */
.ps-component:hover {
  /* Hover state */
}

.ps-component:focus-visible {
  outline: 2px solid var(--ps-focus-outline);
  outline-offset: 2px;
}

.ps-component--disabled,
.ps-component:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}
```

### Règles CSS

1. **BEM strict** : `.ps-component`, `.ps-component__element`, `.ps-component--modifier`
2. **Tokens uniquement** : Toujours utiliser `var(--*)` depuis `source/props/*.css`
3. **Pas de valeurs en dur** : Jamais de `#00915A`, `16px`, `400`, etc.
4. **⚠️ VÉRIFIER AVANT DE CRÉER** :
   - Chercher si un token existe déjà : `grep -r "--nom-token" source/props/`
   - Vérifier la cohérence avec tokens existants (naming, valeurs)
   - Réutiliser les tokens existants quand possible
   - Si besoin de nouveau token : L'ajouter dans `source/props/{category}.css` approprié
   - Respecter strictement les conventions de nommage (préfixes, structure)
5. **États interactifs** : `:hover`, `:focus-visible`, `:active`, `:disabled`
6. **Accessibilité** : Focus visible avec `outline`
7. **Transitions** : Utiliser les tokens existants (animations.css, easing.css)
8. **Commentaires** : Section par section (Reset, Layout, Sizing, etc.)

---

## 3. Fichier `.yml` (Données par Défaut)

### Structure Standard

```yaml
# Default: Description du variant par défaut
prop1: 'Valeur par défaut'
prop2: 'value'
variant: 'default'
size: 'medium'

# Exemples commentés pour autres cas d'usage
# prop1: 'Autre valeur'
# prop2: 'autre'
# variant: 'alternate'
```

### Règles YML

1. **Variant par défaut** : Le cas d'usage le plus courant
2. **Valeurs réalistes** : Utiliser du contenu représentatif
3. **Commentaires** : Exemples alternatifs en commentaire
4. **Concis** : Minimum de props pour un preview fonctionnel

---

## 4. Fichier `.stories.jsx` (Storybook Stories)

### Structure Standard

```jsx
import component from './component.twig';
import data from './component.yml';

const settings = {
  title: '{Level}/{ComponentName}',
  argTypes: {
    variant: {
      control: { type: 'select' },
      options: ['default', 'alternate'],
    },
    size: {
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
    },
  },
};

export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

export const VariantAlternate = {
  render: () => component({ prop1: 'Valeur', variant: 'alternate' }),
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: 12px; align-items: center;">
      ${component({ prop1: 'Small', size: 'small' })}
      ${component({ prop1: 'Medium', size: 'medium' })}
      ${component({ prop1: 'Large', size: 'large' })}
    </div>
  `,
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
      ${component({ prop1: 'Default', variant: 'default' })}
      ${component({ prop1: 'Alternate', variant: 'alternate' })}
    </div>
  `,
};

export const Disabled = {
  render: () => component({ prop1: 'Disabled', disabled: true }),
};

export default settings;
```

### Règles Stories

1. **Import** : `component.twig` et `data.yml`
2. **Settings** : Titre avec niveau + nom, `argTypes` pour les controls
3. **Default story** : Utilise les données de `.yml`
4. **Stories par variant** : Une story par variant majeur
5. **Stories comparatives** : `AllSizes`, `AllVariants`, etc.
6. **Stories d'états** : `Disabled`, `Loading`, `Error`, etc.
7. **Wrapper si nécessaire** : `<div style="...">` pour background, spacing, etc.

---

## 5. Fichier `.mdx` (Documentation)

### Structure Standard

```mdx
import { Meta, Canvas } from '@storybook/addon-docs/blocks';
import * as ComponentStories from './component.stories';

<Meta of={ComponentStories} />

# {Component Name}

Le {component name} est un {level} qui permet de {description courte}.

## Spécifications techniques

* `prop1` est une propriété obligatoire qui définit {description}.
* `variant` permet de choisir entre {list variants}.
* `size` contrôle la taille du composant (small, medium, large).
* `disabled` désactive l'interaction utilisateur.

### Variant par défaut

Description du variant par défaut et de son usage principal.

<Canvas of={ComponentStories.Default} />

### Variant {Name}

Description du variant et de son cas d'usage.

<Canvas of={ComponentStories.VariantName} />

### Tailles disponibles

Le composant est disponible en trois tailles : small (32px), medium (36px), large (40px).

<Canvas of={ComponentStories.AllSizes} />

### États

#### Désactivé

État désactivé pour les situations où l'action n'est pas disponible.

<Canvas of={ComponentStories.Disabled} />

## Accessibilité

* **Contraste** : Tous les variants respectent WCAG 2.2 AA (4.5:1 minimum).
* **Navigation clavier** : Tab pour focus, Enter/Space pour activation.
* **ARIA** : Utilisation de `aria-disabled`, `aria-label` quand nécessaire.
* **Focus visible** : Indicateur clair avec outline.

## Intégration Drupal

```twig
{% include '@ps_theme/{level}/{component}/component.twig' with {
  'prop1': value,
  'variant': 'alternate',
} %}
```

## Design Tokens

Le composant utilise les tokens suivants :

* **Couleurs** : `--ps-color-primary`, `--ps-color-white`
* **Typographie** : `--ps-font-family-primary`, `--ps-font-size-base`
* **Spacing** : `--ps-spacing-md`
* **Transitions** : `--ps-duration-fast`, `--ps-easing-standard`
```

### Règles MDX

1. **Import** : Meta et Canvas de Storybook, stories du composant
2. **Titre H1** : Nom du composant
3. **Description** : Rôle et usage principal
4. **Specs techniques** : Liste des props avec descriptions
5. **Canvas par variant** : Un Canvas par story importante
6. **Section Accessibilité** : Conformité WCAG, navigation, ARIA
7. **Section Intégration Drupal** : Exemple d'inclusion Twig
8. **Design Tokens** : Liste des tokens utilisés

---

## Checklist de Validation

Avant de considérer un composant comme complet :

### 🎨 Tokens (PRIORITÉ #1 - VÉRIFIER EN PREMIER)
- [ ] **Recherche effectuée** : `grep -r "--token-name" source/props/` pour vérifier existence
- [ ] **Cohérence validée** : Nommage, valeurs, progression respectent les patterns existants
- [ ] **Réutilisation maximale** : Utilisation prioritaire des tokens déjà définis
- [ ] **Nouveaux tokens** : Si nécessaires, ajoutés dans `source/props/{category}.css` approprié
- [ ] **Conventions strictes** : Préfixes (--brand-*, --font-*, --size-*), structure, format respectés
- [ ] **Documentation** : `CHANGELOG.md` mis à jour avec justification des ajouts

### Fichiers
- [ ] `.twig` avec header de documentation et defaults
- [ ] `.css` avec BEM et tokens uniquement (source/props/*.css)
- [ ] `.yml` avec données par défaut pertinentes
- [ ] `.stories.jsx` avec au moins 4 stories (Default, Variants, Sizes, Disabled)
- [ ] `.mdx` avec documentation complète

### Code Quality
- [ ] BEM strict avec préfixe `ps-`
- [ ] Aucune valeur en dur (toujours tokens `var(--*)` depuis source/props/*.css)
- [ ] HTML sémantique approprié
- [ ] Attributs ARIA si nécessaire
- [ ] Support `{{ attributes }}` dans Twig

### Accessibilité
- [ ] Focus visible (`:focus-visible`)
- [ ] Navigation clavier fonctionnelle
- [ ] Contraste WCAG 2.2 AA validé
- [ ] États disabled/loading/error accessibles
- [ ] Textes alternatifs pour images/icônes

### Storybook
- [ ] Toutes les stories s'affichent correctement
- [ ] Controls fonctionnels dans l'addon Controls
- [ ] Documentation MDX complète et lisible
- [ ] Exemples d'intégration Drupal présents

### Tests
- [ ] Testé dans Chrome, Firefox, Safari
- [ ] Testé sur mobile (responsive)
- [ ] Testé avec lecteur d'écran (NVDA/JAWS)
- [ ] Testé navigation clavier uniquement

---

## Exemple Complet : Button

Référence : `source/patterns/elements/button/`

Le composant Button est l'**exemple de référence** à suivre pour tous les nouveaux composants. Il implémente correctement :

- ✅ BEM avec préfixe `ps-button`
- ✅ Tokens CSS pour toutes les valeurs
- ✅ Documentation Twig exhaustive
- ✅ 10+ stories couvrant tous les cas
- ✅ MDX détaillé avec specs et accessibilité
- ✅ États interactifs (hover, focus, active, disabled, loading)
- ✅ Variants multiples (primary/secondary × green/purple/white)
- ✅ Tailles (small, medium, large)
- ✅ Support icônes (left/right/only)
- ✅ Support `<a>` et `<button>` selon `url`

---

## Ressources

- **Exemple complet** : `source/patterns/elements/button/`
- **Spécifications design** : `docs/design/atoms/button.md`
- **Tokens disponibles** : `source/props/ps-tokens.css`
- **Storybook local** : `http://localhost:6006` (après `npm run watch`)

---

**Version** : 1.0.0  
**Dernière mise à jour** : 28 novembre 2025
