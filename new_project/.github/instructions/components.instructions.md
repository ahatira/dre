# Instructions Composants

## Structure 5 fichiers obligatoires

Chaque composant DOIT avoir :

```
component-name/
├── component-name.twig          # Template Drupal
├── component-name.css           # Styles (tokens uniquement)
├── component-name.component.yml # Props schema SDC
├── component-name.stories.jsx   # Storybook stories
└── README.md                    # Documentation complète
```

**Exception** : Composants `base/*` (colors, fonts, shadows, etc.) utilisent 4 fichiers sans README.

## 1. Twig (.twig)

### Header obligatoire
```twig
{#
/**
 * @file
 * Component: Component Name
 * Category: Atoms|Molecules|Organisms|Templates|Pages
 * 
 * Description of component.
 */
#}
```

### Defaults
```twig
{% set defaults = {
  variant: 'default',
  size: 'md',
  icon: null
} %}
{% set props = defaults|merge(props|default({})) %}
```

### Classes
```twig
{% set classes = [
  'ps-component',
  props.variant ? 'ps-component--' ~ props.variant,
  props.size ? 'ps-component--' ~ props.size,
  props.extraClass
]|filter(v => v)|join(' ') %}
```

### Includes avec `only`
```twig
{% include '@elements/icon/icon.twig' with {
  icon: props.icon,
  size: 'sm'
} only %}
```

### INTERDICTIONS Twig
- ❌ Arrow functions : `filter(v => v)` → ✅ Ternary : `condition ? 'class' : null`
- ❌ Méthodes JS : `.map()`, `.filter()`, `.includes()` (Drupal incompatible)
- ❌ `baseClass` parameter pour composition → ✅ `attributes.addClass()` uniquement

## 2. CSS (.css)

Voir `css.instructions.md` pour règles complètes.

**Résumé** :
- Tokens uniquement (`var(--token)`)
- Nesting max 3 niveaux
- BEM strict (`.ps-block__element--modifier`)
- Couleurs sémantiques (`--primary`, `--success`, etc.)
- Focus-visible obligatoire

## 3. YAML (.component.yml)

```yaml
'$schema': 'https://git.drupalcode.org/project/drupal/-/raw/11.x/core/modules/sdc/src/metadata.schema.json'
name: Component Name
status: stable
props:
  type: object
  properties:
    variant:
      type: string
      title: Variant
      default: default
      enum:
        - default
        - primary
        - secondary
    size:
      type: string
      title: Size
      default: md
      enum:
        - sm
        - md
        - lg
    icon:
      type: string
      title: Icon name
      default: ''
  required: []
slots:
  default:
    title: Content
    description: Main content area
libraryOverrides:
  dependencies:
    - ps/global
```

## 4. Storybook (.stories.jsx)

### Export default AVEC tags
```jsx
export default {
  title: 'Atoms/Component Name',
  tags: ['autodocs'], // ❌ OBLIGATOIRE (sauf base/*)
  parameters: {
    docs: {
      description: {
        component: 'Component description.'
      }
    }
  },
  argTypes: {
    variant: {
      control: 'select',
      options: ['default', 'primary', 'secondary'],
      description: 'Visual variant',
      table: { category: 'Appearance' }
    },
    size: {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Size preset',
      table: { category: 'Appearance' }
    }
  }
};
```

### Catégories argTypes
- **Content** : text, children, icon
- **Appearance** : variant, size, color
- **State** : disabled, loading, active
- **Behavior** : onClick, onSubmit, href

### Stories
```jsx
// Story 1 : Default
export const Default = {
  args: {
    variant: 'default',
    size: 'md',
    text: 'Example'
  }
};

// Story 2+ : Showcases (variantes, sizes, states)
export const Variants = {
  render: () => `
    <div class="story-grid">
      ${['default', 'primary', 'secondary'].map(variant => 
        render({ variant, text: variant })
      ).join('')}
    </div>
  `
};
```

### Données Faker.js
```jsx
import { faker } from '@faker-js/faker';

export const WithFakeData = {
  args: {
    title: faker.lorem.words(3),
    description: faker.lorem.sentence(),
    image: faker.image.urlLoremFlickr({ category: 'building', width: 400, height: 300 })
  }
};
```

## 5. README (.md)

### Structure obligatoire
```markdown
# Component Name

Description courte (1-2 phrases).

## Usage

\```twig
{% include '@atoms/component/component.twig' with {
  variant: 'primary',
  size: 'md'
} only %}
\```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| variant | string | 'default' | Visual variant (default, primary, secondary) |
| size | string | 'md' | Size preset (sm, md, lg) |

## BEM Structure

- `.ps-component` : Base
- `.ps-component__element` : Sub-part
- `.ps-component--modifier` : Variant

## Design Tokens

- `--primary` : Main color
- `--space-4` : Padding
- `--radius-md` : Border radius

## Accessibility

- WCAG 2.2 AA compliant
- Keyboard navigation support
- Focus-visible indicator
- ARIA attributes: `role`, `aria-label`

## Examples

### Primary Button
\```twig
{% include '@atoms/button/button.twig' with {
  variant: 'primary',
  text: 'Submit'
} only %}
\```
```

## BEM strict

### Blocs
- Nom du composant : `.ps-component`
- Kebab-case : `.ps-form-field`

### Éléments
- Sous-partie : `.ps-component__element`
- Double underscore : `.ps-card__header`

### Modifiers
- Variante : `.ps-component--modifier`
- Double tiret : `.ps-button--primary`

### Règles
- ❌ Pas de combinaisons requises : `.ps-badge--a.ps-badge--b`
- ✅ Chaque modifier fonctionne seul
- ❌ Pas de nesting BEM : `.ps-card__header__title`
- ✅ Flat : `.ps-card__header-title`

## Composition

### Atoms incluent rendering systems
Les atoms peuvent inclure leur propre système de rendu (icons, avatars, badges) car ils sont des éléments de base autonomes.

```twig
{# ✅ CORRECT - Icon atom with data-icon #}
<span class="ps-icon" data-icon="{{ props.icon }}"></span>
```

### Molécules/Organismes utilisent attributes.addClass()
```twig
{# ✅ CORRECT - Composition sans baseClass #}
{% include '@atoms/button/button.twig' with {
  text: 'Submit',
  attributes: attributes.addClass('ps-form__submit')
} only %}
```

## Validation

Avant commit :
- [ ] 5 fichiers présents (sauf base/*)
- [ ] Twig : header, defaults, ternary (pas arrow functions)
- [ ] CSS : tokens uniquement, nesting, focus-visible
- [ ] YAML : schema complet, required[], libraryOverrides
- [ ] Stories : tags: ['autodocs'], argTypes catégorisés
- [ ] README : Usage, Props, BEM, Tokens, A11y, Examples
