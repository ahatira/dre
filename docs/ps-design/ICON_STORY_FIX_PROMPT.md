# Simple Prompt: Add Icon Support to Components

**Purpose**: Add native `data-icon` support to components (button, badge, alert, etc.) as a built-in feature.

**Version**: 1.0.0  
**Last Updated**: 2025-12-09

---

## 🎯 What to Do

Add `data-icon` and `data-icon-position` support directly in component markup (Twig + CSS).

**Example for Button**:
```twig
<button
  {{ attributes.addClass(classes) }}
  {% if icon %}data-icon="{{ icon }}"{% endif %}
  {% if iconPosition %}data-icon-position="{{ iconPosition }}"{% endif %}
>
  {{ label }}
</button>
```

**Example for Badge**:
```twig
<span
  class="ps-badge {{ bem(variant) }}"
  {% if icon %}data-icon="{{ icon }}"{% endif %}
  {% if iconPosition %}data-icon-position="{{ iconPosition }}"{% endif %}
>
  {{ label }}
</span>
```

---

## 📋 Implementation Steps

### 1. Add Twig Parameters
In `{component}.yml`, add:

```yaml
icon:
  description: "Icon name (e.g., 'check', 'arrow-right'). Leave empty for no icon."
  type: string
  default: null

iconPosition:
  description: "Icon position: 'start' (::before, default) or 'end' (::after)"
  type: string
  default: start
  options:
    - start
    - end
```

### 2. Update Component Template
In `{component}.twig`, add `data-icon` attributes:

```twig
{# Always in the actual element (button, span, a, etc.) #}
<button
  {{ attributes.addClass(classes) }}
  {% if icon %}data-icon="{{ icon }}"{% endif %}
  {% if iconPosition != 'start' %}data-icon-position="{{ iconPosition }}"{% endif %}
>
  {{ label }}
</button>
```

### 3. Update Stories
In `{component}.stories.jsx`, add argTypes:

```jsx
argTypes: {
  icon: {
    description: 'Icon name from sprite (no "icon-" prefix, e.g., "check", "arrow-right")',
    control: { type: 'select' },
    options: [null, ...iconsRegistry.names],
    table: {
      category: 'Content',
      type: { summary: 'string' },
    },
  },
  iconPosition: {
    description: 'Icon position: start (::before, default) or end (::after)',
    control: { type: 'inline-radio' },
    options: ['start', 'end'],
    table: {
      category: 'Appearance',
      type: { summary: 'start | end' },
      defaultValue: { summary: 'start' },
    },
  },
}
```

Add example story:

```jsx
export const WithIcon = {
  args: {
    label: 'Proceed',
    icon: 'arrow-right',
    iconPosition: 'end',
  },
};
```

### 4. CSS: No Changes Needed
The `data-icon` CSS system already works on all elements via `source/props/icons.css`.

Automatic:
- `display: inline-flex` + `gap: var(--ps-icon-gap, 0.375em)`
- Icon rendered via `::before` (start) or `::after` (end)
- Color inherited from parent via `currentColor`

### 5. Test & Validate

```bash
npm run build              # Full validation
npm run watch            # Visual check at http://localhost:6006
git add {component}/
git commit -m "feat({scope}): Add icon support to {component}

- Add icon and iconPosition parameters
- Icons rendered via data-icon CSS system
- Supports position control (start/end)
- Color inherited from component styling"
```

---

## ✅ Checklist

- [ ] Component `.twig`: Added `data-icon` and `data-icon-position` attributes
- [ ] Component `.yml`: Added `icon` and `iconPosition` parameters
- [ ] Component `.stories.jsx`: Added `icon` and `iconPosition` argTypes + example story
- [ ] Icon names verified in `icons-registry.json` (141 available)
- [ ] No `icon-` prefix in icon names
- [ ] Build passes: `npm run build`
- [ ] Storybook renders correctly: `npm run watch`
- [ ] Commit message follows format: `feat({scope}): Add icon support to {component}`

---

## 🎨 Available Icons (141 Total)

Check `source/patterns/documentation/icons-registry.json` or Storybook Elements/Icon Gallery.

Common icons:
- `check`, `close`, `arrow-right`, `arrow-left`
- `star`, `alert`, `info`, `help`
- `search`, `menu`, `user`, `settings`
- `external-link`, `download`, `upload`
- And 130+ more...

---

## 📝 Quick Template

```
Ajoute le support des icônes au composant {COMPONENT_NAME}.

Composant: source/patterns/{LEVEL}/{COMPONENT_NAME}/

Fais:
1. Ajoute `icon` et `iconPosition` dans {component}.yml
2. Ajoute `data-icon` et `data-icon-position` dans {component}.twig
3. Ajoute argTypes dans {component}.stories.jsx
4. Ajoute story exemple: WithIcon (avec un icon par défaut)
5. Vérifie: npm run build + npm run watch
6. Commite: feat({scope}): Add icon support to {component}

Référence:
- Twig: source/patterns/elements/button/button.twig (voir attributes.addClass pattern)
- CSS: source/props/icons.css (auto-gère l'affichage)
- Icons: source/patterns/documentation/icons-registry.json (noms disponibles)
```

---

**Maintainers**: Design System Team  
**Contact**: See project README for support channels  
**Last Updated**: 2025-12-09
