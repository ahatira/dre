# Template Standard de Composant PS Theme

**OBLIGATOIRE : Ce template DOIT être suivi À LA LETTRE pour tous les nouveaux composants.**

Basé sur l'analyse de `source/patterns/elements/button/` (référence validée).

---

## Structure des Fichiers (5 fichiers obligatoires)

Chaque composant DOIT contenir exactement ces 5 fichiers :

```
source/patterns/{category}/{component-name}/
├── {component-name}.twig       # Template Twig avec params documentés
├── {component-name}.css        # Styles BEM avec tokens uniquement
├── {component-name}.yml        # Données par défaut pour preview
├── {component-name}.stories.jsx # Stories Storybook (Twig render)
└── README.md                   # Documentation (optionnel mais recommandé)
```

---

## 1. Template Twig (`.twig`)

**Règles strictes :**
- Commentaire d'en-tête avec tous les `@param` documentés
- Variables avec valeurs par défaut via `|default()`
- Classes construites via array merge (`{% set classes = [] %}`)
- Nomenclature BEM stricte avec préfixe `ps-`
- Attributs ARIA appropriés pour accessibilité

**Template exact à suivre :**

```twig
{#
 * {Component Name} {category}
 * @param type name - Description (required/optional, default: value)
 * @param type name - Description (required/optional, default: value)
 #}

{% set paramName = paramName|default('defaultValue') %}
{% set anotherParam = anotherParam|default(false) %}

{% set classes = [] %}
{% set classes = classes|merge(['ps-component-name']) %}
{% if variant %}
  {% set classes = classes|merge(['ps-component-name--' ~ variant]) %}
{% endif %}
{% if modifier %}
  {% set classes = classes|merge(['ps-component-name--modifier']) %}
{% endif %}

<div
  class="{{ classes|join(' ')|trim }}"
  {% if attributes %}{{ attributes }}{% endif %}
  {% if ariaLabel %}aria-label="{{ ariaLabel }}"{% endif %}
>
  {# Contenu du composant #}
  {% if content %}
    <span class="ps-component-name__element">{{ content }}</span>
  {% endif %}
</div>
```

---

## 2. Styles CSS (`.css`)

**Règles ABSOLUES :**
- ❌ **JAMAIS de valeurs en dur** (`#00915A`, `16px`, etc.)
- ✅ **TOUJOURS utiliser les tokens** de `source/props/*.css`
- Nomenclature BEM stricte : `.ps-component`, `.ps-component__element`, `.ps-component--modifier`
- Commentaire d'en-tête avec BEM, variants, modifiers documentés
- États interactifs `:hover`, `:focus-visible`, `:active`, `:disabled`
- Transitions fluides avec `var(--ease-*)` ou valeurs cubic-bezier

**Template exact à suivre :**

```css
/** 
 * {Component Name} ({Category}/Atom|Molecule|Organism)
 * Description courte du composant
 * 
 * BEM: ps-component-name, ps-component-name__element, ps-component-name__element--modifier
 * Variants: variant1 | variant2 | variant3
 * Modifiers: --modifier1, --modifier2
 * Sizes: small, medium (défaut), large
 */

.ps-component-name {
  /* Reset */
  margin: 0;
  padding: 0;
  
  /* Layout */
  display: inline-flex; /* ou block, grid selon besoin */
  align-items: center;
  gap: var(--size-2); /* Toujours utiliser tokens */
  
  /* Sizing */
  height: var(--size-9);
  padding: var(--size-2) var(--size-4);
  
  /* Typography */
  font-family: var(--font-sans);
  font-weight: var(--font-weight-400);
  font-size: var(--size-4);
  line-height: var(--leading-normal);
  
  /* Visual */
  background-color: var(--brand-primary); /* Jamais #00915A */
  color: var(--white);
  border-radius: var(--radius-2);
  
  /* Transitions */
  transition: 
    background-color 150ms cubic-bezier(0.4, 0.0, 0.2, 1),
    color 150ms cubic-bezier(0.4, 0.0, 0.2, 1),
    transform 150ms cubic-bezier(0.4, 0.0, 0.2, 1);
  
  /* States */
  &:hover {
    background-color: var(--brand-primary-hover);
    transform: translateY(-1px);
  }
  
  &:active {
    background-color: var(--brand-primary-active);
    transform: translateY(0);
  }
  
  &:focus-visible {
    outline: var(--border-size-2) solid var(--blue-500);
    outline-offset: var(--border-size-2);
  }
  
  &:disabled,
  &.ps-component-name--disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
}

/* Elements BEM */
.ps-component-name__element {
  display: inline-block;
  color: inherit;
}

/* Variants */
.ps-component-name--variant1 {
  background-color: var(--btn-secondary);
  
  &:hover:not(:disabled) {
    background-color: var(--btn-secondary-hover);
  }
}

/* Modifiers */
.ps-component-name--modifier {
  border: var(--border-size-2) solid var(--brand-primary);
}

/* Sizes */
.ps-component-name--small {
  height: var(--size-8);
  padding: var(--size-1) var(--size-3);
  font-size: var(--size-305);
}

.ps-component-name--large {
  height: var(--size-10);
  padding: var(--size-3) var(--size-5);
  font-size: var(--size-5);
}
```

---

## 3. Données par Défaut (`.yml`)

**Template exact :**

```yaml
# Default: Primary state
paramName: 'value'
variant: 'primary'
size: 'medium'
disabled: false
```

---

## 4. Stories Storybook (`.stories.jsx`)

**Règles CRITIQUES :**
- ❌ **JAMAIS d'import React** (Storybook HTML/Vite)
- ✅ **Import du Twig** : `import component from './component.twig';`
- ✅ **Import des données** : `import data from './component.yml';`
- Render via fonction Twig : `render: (args) => component(args)`
- Stories multiples pour variants, sizes, états
- Un `AllVariants` showcase obligatoire

**Template exact à suivre :**

```jsx
import component from './component-name.twig';
import data from './component-name.yml';

const settings = {
  title: '{Category}/{Component Name}',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Description complète du composant avec variants, modifiers, et use cases.',
      },
    },
  },
  argTypes: {
    paramName: {
      description: 'Description du paramètre',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'default' },
      },
    },
    variant: {
      description: 'Variant du composant',
      control: { type: 'select' },
      options: ['variant1', 'variant2', 'variant3'],
      table: {
        type: { summary: 'variant1 | variant2 | variant3' },
        defaultValue: { summary: 'variant1' },
      },
    },
    size: {
      description: 'Taille du composant',
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      table: {
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    disabled: {
      description: 'État désactivé',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

export const Variant1 = {
  render: () => component({ paramName: 'value', variant: 'variant1' }),
};

export const Variant2 = {
  render: () => component({ paramName: 'value', variant: 'variant2' }),
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
      ${component({ variant: 'variant1' })}
      ${component({ variant: 'variant2' })}
      ${component({ variant: 'variant3' })}
    </div>
  `,
};

export const Sizes = {
  render: () => `
    <div style="display: flex; gap: 12px; align-items: center;">
      ${component({ size: 'small' })}
      ${component({ size: 'medium' })}
      ${component({ size: 'large' })}
    </div>
  `,
};

export const Disabled = {
  render: () => `
    <div style="display: flex; gap: 12px;">
      ${component({ disabled: false })}
      ${component({ disabled: true })}
    </div>
  `,
};

export default settings;
```

---

## 5. Checklist de Validation

Avant de considérer un composant terminé :

### Fichiers
- [ ] Les 5 fichiers existent : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md` (optionnel)
- [ ] Nomenclature cohérente : même nom pour tous les fichiers

### Twig
- [ ] Commentaires d'en-tête avec tous les `@param` documentés
- [ ] Valeurs par défaut via `|default()`
- [ ] Classes construites via array merge
- [ ] BEM avec préfixe `ps-` respecté

### CSS
- [ ] ❌ AUCUNE valeur en dur (hex, px bruts, etc.)
- [ ] ✅ TOUS les tokens utilisés (`var(--*)`)
- [ ] BEM strict : `.ps-component`, `.ps-component__element`, `.ps-component--modifier`
- [ ] États interactifs documentés
- [ ] Transitions fluides

### Stories
- [ ] ❌ PAS d'import React
- [ ] ✅ Import Twig + YML
- [ ] Story `Default` avec `args: { ...data }`
- [ ] Stories pour chaque variant
- [ ] Story `AllVariants` showcase
- [ ] Story `Sizes` si applicable
- [ ] Story `Disabled` si applicable

### Build
- [ ] `npm run vite:build` réussit
- [ ] `npm run watch` → Storybook affiche le composant
- [ ] Pas d'erreur console navigateur
- [ ] Fonts/tokens chargent correctement

---

## Exemple Complet : Icon Component

Appliquons ce template à l'icon component :

### `icon.twig`
```twig
{#
 * Icon element
 * @param string name - Icon name (required)
 * @param string size - Icon size using font-size token (optional, default: var(--font-size-3))
 * @param string color - Icon color using color token (optional, default: currentColor)
 #}

{% set name = name|default('arrow-right') %}
{% set size = size|default('var(--font-size-3)') %}
{% set color = color|default('currentColor') %}

{% set classes = [] %}
{% set classes = classes|merge(['ps-icon']) %}
{% set classes = classes|merge(['ps-icon-' ~ name]) %}

<i
  class="{{ classes|join(' ')|trim }}"
  aria-hidden="true"
  style="font-size: {{ size }}; color: {{ color }};"
></i>
```

### `icon.css`
```css
/** 
 * Icon (Element/Atom)
 * Icon font system using ps-icons font family
 * 
 * BEM: ps-icon, ps-icon-{name}
 * Sizes: Controlled via font-size token (var(--font-size-*))
 * Colors: Controlled via color token (var(--brand-*), var(--gray-*), etc.)
 */

[class^="ps-icon-"],
[class*=" ps-icon-"] {
  font-family: ps-icons !important;
  font-style: normal;
  font-weight: var(--font-weight-400) !important;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  display: inline-block;
  
  /* Better font rendering */
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Individual glyphs mapping done in source/props/icons.css */
```

### `icon.yml`
```yaml
# Default: Arrow right icon
name: 'arrow-right'
size: 'var(--font-size-3)'
color: 'currentColor'
```

### `icon.stories.jsx`
```jsx
import icon from './icon.twig';
import data from './icon.yml';

const settings = {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Icon font system using <i class="ps-icon ps-icon-{name}"> markup. Size via font-size tokens, color via color tokens.',
      },
    },
  },
  argTypes: {
    name: {
      description: 'Icon name',
      control: { type: 'select' },
      options: ['arrow-down','arrow-left','arrow-right','arrow-up','calendar','check','close','delete','edit','info','menu','minus','plus','search','test','warning'],
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'arrow-right' },
      },
    },
    size: {
      description: 'Icon size using font-size token',
      control: { type: 'text' },
      table: {
        type: { summary: 'string (CSS token)' },
        defaultValue: { summary: 'var(--font-size-3)' },
      },
    },
    color: {
      description: 'Icon color using color token',
      control: { type: 'text' },
      table: {
        type: { summary: 'string (CSS token)' },
        defaultValue: { summary: 'currentColor' },
      },
    },
  },
};

export const Default = {
  render: (args) => icon(args),
  args: { ...data },
};

export const ArrowRight = {
  render: () => icon({ name: 'arrow-right' }),
};

export const Sizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'arrow-right', size: 'var(--font-size-1)' })}
      ${icon({ name: 'arrow-right', size: 'var(--font-size-3)' })}
      ${icon({ name: 'arrow-right', size: 'var(--font-size-5)' })}
      ${icon({ name: 'arrow-right', size: 'var(--font-size-7)' })}
    </div>
  `,
};

export const Colors = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'check', color: 'var(--green-600)' })}
      ${icon({ name: 'warning', color: 'var(--yellow-600)' })}
      ${icon({ name: 'info', color: 'var(--blue-600)' })}
      ${icon({ name: 'close', color: 'var(--red-600)' })}
      ${icon({ name: 'arrow-right', color: 'var(--brand-primary)' })}
    </div>
  `,
};

export const Gallery = {
  render: () => {
    const names = [
      'arrow-down','arrow-left','arrow-right','arrow-up','calendar','check','close','delete','edit','info','menu','minus','plus','search','test','warning'
    ];
    return `
      <div style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: var(--size-6);">
        ${names.map((n) => `
          <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-3);">
            ${icon({ name: n, size: 'var(--font-size-5)' })}
            <code style="font-size: var(--font-size-0);">ps-icon-${n}</code>
          </div>
        `).join('')}
      </div>
    `;
  },
};

export default settings;
```

---

## Points Critiques à Retenir

1. **JAMAIS de JSX/React dans `.stories.jsx`** → Import Twig + render HTML
2. **JAMAIS de valeurs en dur dans CSS** → Toujours tokens `var(--*)`
3. **BEM strict avec préfixe `ps-`** → `.ps-component`, `.ps-component__element`, `.ps-component--modifier`
4. **5 fichiers obligatoires** → `.twig`, `.css`, `.yml`, `.stories.jsx`, (+ README.md optionnel)
5. **Vérifier tokens avant création** → `grep -r "--token" source/props/` pour éviter doublons
6. **Build validation** → `npm run vite:build` + `npm run watch` → vérifier Storybook
7. **Accessibilité** → ARIA labels, focus states, disabled states
8. **Documentation inline** → Commentaires d'en-tête complets

---

**Ce template est maintenant la référence OBLIGATOIRE pour tous les nouveaux composants.**
