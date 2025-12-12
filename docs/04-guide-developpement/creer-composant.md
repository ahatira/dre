# Créer un Composant

**Workflow complet en 11 étapes** : Spécification → Implémentation → Validation → Commit

---

## 📋 Vue d'ensemble

Ce guide décrit le **processus complet de création d'un composant** PS Theme, depuis la lecture de la spécification jusqu'au commit final.

**Durée estimée** : 2-4 heures (composant simple) | 1-2 jours (composant complexe)

---

## 🎯 Phase 1 : Préparation (15-30 min)

### Étape 1 : Lire la spécification

**Localiser le fichier** : `docs/design/{niveau}/{composant}.md`

Niveaux disponibles :
- `atoms/` → Elements (`source/patterns/elements/`)
- `molecules/` → Components (`source/patterns/components/`)
- `organisms/` → Collections (`source/patterns/collections/`)
- `templates/` → Layouts (`source/patterns/layouts/`)
- `pages/` → Pages (`source/patterns/pages/`)

**Extraire** :
- Nom du composant, description, cas d'usage
- Props (obligatoires vs optionnels)
- Variantes (tailles, couleurs, états)
- Structure BEM (classes CSS)
- Design tokens utilisés
- Exigences d'accessibilité (ARIA, keyboard)
- Dépendances (composants inclus)

---

### Étape 2 : Vérifier les dépendances

**Si molecule/organism, vérifier que les atoms existent** :

```bash
# Exemple : Alert (molecule) dépend de icon (atom)
ls source/patterns/elements/icon/
# Doit contenir : icon.twig, icon.css, icon.yml, icon.stories.jsx, README.md
```

**Si dépendance manquante** :
- ❌ **NE PAS continuer**
- ✅ Créer l'atom d'abord (composition avant création)
- ✅ Valider l'atom (audit 100/100)
- ✅ Puis créer le composant parent

---

### Étape 3 : Vérifier les tokens

**Chercher si les tokens existent** :

```bash
# Exemple : Composant nécessite --size-badge
npm run tokens:check -- --size-badge

# Ou manuellement
grep -r "--size-badge" source/props/
```

**Si token manquant** :
1. ❌ **NE PAS créer le token soi-même**
2. ✅ Documenter le besoin dans les notes du composant
3. ✅ Consulter [05-maintenance.md](../../.github/instructions/05-maintenance.md) (Création de token)
4. ⏸️ Attendre validation avant continuer

**Fichiers tokens** :
- `source/props/colors.css` (88 tokens)
- `source/props/sizes.css` (33+)
- `source/props/fonts.css` (60)
- `source/props/borders.css` (14)
- `source/props/shadows.css` (12)
- `source/props/animations.css` (6 durées + presets)

---

## 🏗️ Phase 2 : Implémentation (1-3 heures)

### Étape 4 : Créer les 5 fichiers obligatoires

**Méthode 1 : Scaffolding automatique** (recommandé)

```bash
npm run generate:pattern
# Interactif : type (element) + nom (Badge)
```

**Méthode 2 : Flags**

```bash
npm run generate:pattern -- --type=element --name="Badge"
```

**Résultat** : 5 fichiers créés dans `source/patterns/{niveau}/{composant}/`

```
badge/
├── badge.twig         # Template Twig
├── badge.css          # Styles CSS
├── badge.yml          # Données mock
├── badge.stories.jsx  # Stories Storybook
└── README.md          # Documentation
```

---

### Étape 5 : Template Twig

**Structure complète** :

```twig
{#
/**
 * @file
 * Badge component
 * 
 * Small label for statuses, categories, counts.
 * 
 * @param {string} text - Badge label (required)
 * @param {string} [variant='neutral'] - Color variant
 * @param {string} [size='md'] - Size (xs, sm, md, lg)
 * @param {boolean} [pill=false] - Pill shape
 * @param {string} [icon] - Optional icon name
 * @param {object} [attributes] - HTML attributes
 */
#}

{# Defaults #}
{% set variant = variant|default('neutral') %}
{% set size = size|default('md') %}
{% set pill = pill|default(false) %}
{% set icon = icon|default(null) %}

{# Classes array with ternary + null (Drupal-compatible) #}
{% set classes = [
  'ps-badge',
  variant != 'neutral' ? 'ps-badge--' ~ variant : null,
  size != 'md' ? 'ps-badge--' ~ size : null,
  pill ? 'ps-badge--pill' : null,
  modifier_class
] %}

<span class="{{ classes|join(' ')|trim }}"{{ attributes|default('') }}>
  {% if icon %}
    {% include '@elements/icon/icon.twig' with {
      icon: icon,
      size: 'xs',
    } only %}
  {% endif %}
  
  {% if text %}
    <span class="ps-badge__text">{{ text }}</span>
  {% endif %}
</span>
```

**Règles clés** :
- ✅ Commentaire d'entête avec params (`@param`)
- ✅ Defaults avec `|default()`
- ✅ Ternaire avec `null` : `condition ? 'class' : null`
- ❌ **JAMAIS** arrow functions : `.filter(v => v)` (Drupal incompatible)
- ✅ Composition avec `{% include %}` + `only`
- ✅ Contexte Real Estate (textes réalistes)

**Référence complète** : [03-technical-implementation.md](../../.github/instructions/03-technical-implementation.md) → Section 2 (Twig)

---

### Étape 6 : Styles CSS avec nesting

**Structure standard** :

```css
.ps-badge {
  /* ═══ Variables component-scoped (Layer 2) ═══ */
  --ps-badge-padding-x: var(--size-2);
  --ps-badge-padding-y: var(--size-1);
  --ps-badge-font-size: var(--font-size-0);
  --ps-badge-bg: var(--gray-100);
  --ps-badge-color: var(--gray-900);
  --ps-badge-border-radius: var(--radius-2);
  
  /* ═══ Base styles (ALL via tokens) ═══ */
  display: inline-flex;
  align-items: center;
  gap: var(--size-1);
  padding: var(--ps-badge-padding-y) var(--ps-badge-padding-x);
  border-radius: var(--ps-badge-border-radius);
  font-size: var(--ps-badge-font-size);
  font-weight: var(--font-weight-600);
  line-height: var(--leading-tight);
  background: var(--ps-badge-bg);
  color: var(--ps-badge-color);
  
  /* ═══ Elements (nested with &) ═══ */
  &__text {
    display: inline-block;
  }
  
  /* ═══ Modifiers : Variants (semantic colors) ═══ */
  &--primary {
    --ps-badge-bg: var(--primary-bg-subtle);
    --ps-badge-color: var(--primary);
  }
  
  &--success {
    --ps-badge-bg: var(--success-bg-subtle);
    --ps-badge-color: var(--success);
  }
  
  &--danger {
    --ps-badge-bg: var(--danger-bg-subtle);
    --ps-badge-color: var(--danger);
  }
  
  /* ═══ Modifiers : Sizes ═══ */
  &--xs {
    --ps-badge-padding-x: var(--size-1);
    --ps-badge-padding-y: var(--size-05);
    --ps-badge-font-size: var(--font-size--2);
  }
  
  &--sm {
    --ps-badge-padding-x: var(--size-105);
    --ps-badge-padding-y: var(--size-05);
    --ps-badge-font-size: var(--font-size--1);
  }
  
  /* ═══ Modifiers : States ═══ */
  &--pill {
    --ps-badge-border-radius: var(--radius-round);
  }
}
```

**Règles clés** :
- ✅ **100% tokens** (aucune valeur hardcodée)
- ✅ Variables component-scoped (Layer 2) en haut
- ✅ Nesting avec `&` (éléments + modifiers)
- ✅ Couleurs sémantiques (`--primary`, `--success`, jamais `green`)
- ✅ États interactifs (`:hover`, `:focus-visible`, `:active`)
- ❌ **JAMAIS** valeurs hardcodées (`16px`, `#00915A`, `0.3s`)

**Référence complète** : [03-technical-implementation.md](../../.github/instructions/03-technical-implementation.md) → Section 1 (CSS)

---

### Étape 7 : Données YAML (Mock)

**Contexte Real Estate obligatoire** :

```yaml
text: "Nouveau"
variant: primary
size: md
pill: false
icon: "home"
```

**Exemples contextuels** :
- "Exclusivité" (badge exclusive property)
- "Vendu" (sold)
- "Coup de cœur" (favorite)
- "Prix réduit" (reduced price)
- "4 pièces" (4 rooms)

**Utiliser Faker.js dans stories** :
```jsx
text: faker.helpers.arrayElement(['Nouveau', 'Exclusivité', 'Vendu'])
```

---

### Étape 8 : Stories Storybook

**Structure export default** :

```jsx
export default {
  title: 'Elements/Badge',
  tags: ['autodocs'], // ⚠️ OBLIGATOIRE
  parameters: {
    layout: 'centered',
    docs: {
      description: {
        component: 'Small label for statuses, categories, counts.',
      },
    },
  },
  argTypes: {
    text: {
      control: 'text',
      description: 'Badge label content',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    variant: {
      control: 'select',
      options: ['neutral', 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'gold'],
      description: 'Color variant (semantic)',
      table: { category: 'Appearance', type: { summary: 'string' }, defaultValue: { summary: 'neutral' } },
    },
    size: {
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg'],
      description: 'Size variant',
      table: { category: 'Appearance', type: { summary: 'string' }, defaultValue: { summary: 'md' } },
    },
    pill: {
      control: 'boolean',
      description: 'Pill shape (fully rounded)',
      table: { category: 'Appearance', type: { summary: 'boolean' }, defaultValue: { summary: false } },
    },
    icon: {
      control: 'text',
      description: 'Optional icon name (without icon- prefix)',
      table: { category: 'Content', type: { summary: 'string' } },
    },
  },
};
```

**Story Default** :

```jsx
export const Default = {
  args: {
    text: 'Nouveau',
    variant: 'primary',
    size: 'md',
    pill: false,
  },
};
```

**Story Showcase (variants)** :

```jsx
export const Variants = {
  render: () => `
    <div style="display: flex; gap: var(--size-2); flex-wrap: wrap;">
      {% include '@elements/badge/badge.twig' with {text: 'Neutral', variant: 'neutral'} only %}
      {% include '@elements/badge/badge.twig' with {text: 'Primary', variant: 'primary'} only %}
      {% include '@elements/badge/badge.twig' with {text: 'Success', variant: 'success'} only %}
      {% include '@elements/badge/badge.twig' with {text: 'Danger', variant: 'danger'} only %}
    </div>
  `,
};
```

**Règles clés** :
- ✅ `tags: ['autodocs']` **OBLIGATOIRE**
- ✅ argTypes catégorisés (Content, Appearance, Actions, Accessibility)
- ✅ Story Default + Showcases (variants, sizes, states)
- ✅ Utiliser `faker` pour données dynamiques

**Référence complète** : [03-technical-implementation.md](../../.github/instructions/03-technical-implementation.md) → Section 3 (Storybook)

---

### Étape 9 : README.md

**Structure standard** :

```markdown
# Badge

Small label for statuses, categories, counts, or metadata.

## Usage

\```twig
{% include '@elements/badge/badge.twig' with {
  text: 'Nouveau',
  variant: 'primary',
  size: 'md',
  pill: false,
  icon: 'home',
} only %}
\```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `text` | string | (required) | Badge label content |
| `variant` | string | `'neutral'` | Color variant: `neutral`, `primary`, `success`, `danger`, `warning`, `info`, `gold` |
| `size` | string | `'md'` | Size: `xs`, `sm`, `md`, `lg` |
| `pill` | boolean | `false` | Pill shape (fully rounded) |
| `icon` | string | `null` | Optional icon name (without `icon-` prefix) |
| `attributes` | object | `null` | Additional HTML attributes |

## BEM Structure

\```
.ps-badge                  # Base component
├── .ps-badge__text        # Text element
├── .ps-badge--primary     # Variant modifier
├── .ps-badge--md          # Size modifier
└── .ps-badge--pill        # Shape modifier
\```

## Design Tokens

\```css
/* Component-scoped (Layer 2) */
--ps-badge-padding-x: var(--size-2);
--ps-badge-padding-y: var(--size-1);
--ps-badge-font-size: var(--font-size-0);
--ps-badge-bg: var(--gray-100);
--ps-badge-color: var(--gray-900);
--ps-badge-border-radius: var(--radius-2);
\```

## Accessibility

- ✅ Semantic HTML (`<span>`)
- ✅ No interactive role (decorative only)
- ✅ Color contrast WCAG AA (all variants)
- ⚠️ If critical info, add `aria-label` or visible text

## Examples

### Status badge
\```twig
{% include '@elements/badge/badge.twig' with {
  text: 'Vendu',
  variant: 'success',
  size: 'sm',
} only %}
\```

### Category badge with icon
\```twig
{% include '@elements/badge/badge.twig' with {
  text: 'Appartement',
  variant: 'primary',
  icon: 'home',
  pill: true,
} only %}
\```

## Storybook

[View in Storybook →](../../storybook/?path=/docs/elements-badge--docs)
```

---

## ✅ Phase 3 : Validation (30 min - 1 heure)

### Étape 10 : Audit de conformité (100 points)

**Utiliser le prompt AI** :

```bash
# Copier contenu de .github/prompts/audit-component.md
# Coller dans ChatGPT/Claude avec contexte du composant
```

**Ou checklist manuelle** (extrait) :

#### Fichiers (20 points)
- [ ] 5 fichiers présents (`.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`)
- [ ] Chaque fichier > 10 lignes (non-vide)
- [ ] Nommage cohérent (`badge.*`)

#### CSS (25 points)
- [ ] 100% tokens (aucune valeur hardcodée)
- [ ] Nesting avec `&` (éléments + modifiers)
- [ ] Variables component-scoped (Layer 2) en haut
- [ ] Couleurs sémantiques (`--primary`, jamais `green`)
- [ ] Focus-visible sur interactifs

#### Twig (20 points)
- [ ] Header comment avec `@param`
- [ ] Defaults avec `|default()`
- [ ] Ternaire + `null` (jamais arrow functions)
- [ ] Composition avec `{% include %}` + `only`
- [ ] Contexte Real Estate

#### Storybook (15 points)
- [ ] `tags: ['autodocs']` présent
- [ ] argTypes catégorisés
- [ ] Story Default + Showcases (variants/sizes)

#### Accessibilité (15 points)
- [ ] ARIA appropriés
- [ ] Keyboard navigation (si interactif)
- [ ] Focus-visible sur interactifs
- [ ] Contraste WCAG AA (4.5:1 texte, 3:1 UI)

#### Documentation (5 points)
- [ ] README.md complet (Usage, Props, BEM, Tokens, A11y, Examples)

**Référence complète** : [04-quality-assurance.md](../../.github/instructions/04-quality-assurance.md) → Checklist 100 points

---

### Étape 11 : Tests finaux

**Build & Visual** :

```bash
# Build complet (doit passer sans erreur)
npm run build

# Vérification visuelle Storybook
npm run watch
# → http://localhost:6006
```

**Checklist visuelle** :
- [ ] Toutes les variantes s'affichent correctement
- [ ] Responsive (mobile 375px → desktop 1920px)
- [ ] États (hover, focus, active) fonctionnent
- [ ] Composition (avec icon) correcte
- [ ] Autodocs généré (onglet "Docs")

**Tests accessibilité** :
- [ ] Onglet Storybook "Accessibility" : 0 violation
- [ ] Test clavier (Tab, Enter, Space si interactif)
- [ ] Test lecteur d'écran (VoiceOver, NVDA)

---

## 📦 Phase 4 : Commit (10 min)

### Format de commit

```bash
git add source/patterns/{niveau}/{composant}/
git commit -m "feat({niveau}): Add {component} component

- Implement 5-file structure (twig, css, yml, stories, README)
- Support {n} variants: {list variants}
- Add {modifier} modifier and {state} state
- Full Autodocs with categorized argTypes
- WCAG 2.2 AA compliant (contrast, focus-visible)
- References spec: docs/design/{niveau}/{composant}.md
"
```

**Exemple Badge** :

```bash
git commit -m "feat(elements): Add badge component with semantic colors

- Implement 5-file structure (twig, css, yml, stories, README)
- Support 8 semantic colors (neutral, primary, success, danger, warning, info, gold, secondary)
- Support 4 sizes (xs, sm, md, lg) + pill modifier
- Add icon integration via composition
- Full Autodocs with categorized argTypes
- WCAG 2.2 AA compliant (all variants tested)
- References spec: docs/design/atoms/badge.md
"
```

### Mettre à jour CHANGELOG

**Fichier** : `docs/ps-design/CHANGELOG.md` (à migrer vers `docs/05-changelog/`)

```markdown
## [Date] - Badge (Element)

**Type** : Nouvelle implémentation  
**Score conformité** : 98/100

### Ajouts
- Composant Badge avec 8 variantes de couleur sémantique
- 4 tailles (xs, sm, md, lg)
- Modificateur pill (forme arrondie complète)
- Intégration icon via composition

### Tokens utilisés
- Couleurs : `--primary`, `--success`, `--danger`, etc. (sémantiques)
- Espacements : `--size-1`, `--size-2` (padding)
- Typographie : `--font-size-0`, `--font-weight-600`
- Bordures : `--radius-2`, `--radius-round`

### Accessibilité
- Contraste WCAG AA : ✅ (toutes variantes)
- Sémantique HTML : `<span>` (non-interactif)
- Documentation ARIA si badge critique

### Référence
- Spec : `docs/design/atoms/badge.md`
- Implémentation : `source/patterns/elements/badge/`
- Storybook : Elements > Badge
```

---

## 🎓 Références et ressources

### Documentation technique

- **Instructions** : [.github/instructions/](../../.github/instructions/)
  * 01-core-principles.md → Fondations
  * **02-component-development.md** → Ce guide (version complète anglaise)
  * 03-technical-implementation.md → Standards CSS/Twig/Storybook
  * 04-quality-assurance.md → Audit 100 points
  * 05-maintenance.md → Tokens, migration, deprecation

- **Prompts AI** : [.github/prompts/](../../.github/prompts/)
  * create-component.md → Créer composant
  * audit-component.md → Auditer composant
  * debug-build.md → Résoudre erreurs

### Composants de référence

| Composant | Score | Pourquoi étudier |
|-----------|-------|------------------|
| **Button** | 100/100 | CSS nesting parfait, tous états, stories complètes |
| **Badge** | 98/100 | Couleurs sémantiques, pill, icon integration |
| **Avatar** | 100/100 | Markup minimal, sizing adaptatif, SVG fallback |
| **Icon** | 100/100 | Système sprite, sizing, couleurs |
| **Divider** | 100/100 | Simplicité, orientation, code minimal |
| **Link** | 100/100 | États, external links, icons, a11y |

**Localisation** : `source/patterns/elements/{component}/`

---

## 🚨 Erreurs courantes

### 1. Valeurs hardcodées
```css
/* ❌ MAUVAIS */
.ps-badge { padding: 8px 16px; background: #00915A; }

/* ✅ CORRECT */
.ps-badge {
  padding: var(--ps-badge-padding-y) var(--ps-badge-padding-x);
  background: var(--ps-badge-bg);
}
```

### 2. Arrow functions en Twig
```twig
{# ❌ MAUVAIS (Drupal incompatible) #}
{% set classes = classes|filter(v => v) %}

{# ✅ CORRECT (ternaire + null) #}
{% set classes = [
  'ps-badge',
  variant != 'default' ? 'ps-badge--' ~ variant : null,
] %}
```

### 3. Couleurs non-sémantiques
```css
/* ❌ MAUVAIS */
.ps-badge--green { background: var(--green-600); }

/* ✅ CORRECT */
.ps-badge--success { background: var(--success-bg-subtle); }
```

### 4. Tags autodocs manquant
```jsx
// ❌ MAUVAIS
export default {
  title: 'Elements/Badge',
  // Missing tags!
};

// ✅ CORRECT
export default {
  title: 'Elements/Badge',
  tags: ['autodocs'], // OBLIGATOIRE
};
```

### 5. Préfixe icon-
```twig
{# ❌ MAUVAIS #}
{% set icon = 'icon-check' %}

{# ✅ CORRECT (prefix auto-ajouté par CSS) #}
{% set icon = 'check' %}
```

---

**Navigation** : [← Démarrage rapide](./demarrage-rapide.md) | [Composition →](./composition.md)
