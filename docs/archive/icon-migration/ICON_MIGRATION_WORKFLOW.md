# Icon System Migration Workflow

**Status**: Icon system migrated to SVG sprite (December 2025)  
**Task**: Update all elements using `data-icon` to use modern SVG sprite approach  
**Approach**: Drupal-compatible, progressive enhancement

---

## 🎯 Migration Strategy

### Two Valid Approaches (Choose One Per Component)

#### **Approach A: Direct Icon Component (Recommended)**
Use `{% include '@elements/icon/icon.twig' %}` for icon rendering.

**Pros**:
- Full control over icon styling (size, color, states)
- Drupal-compatible
- Accessible (ARIA labels, roles)
- Type-safe via component API

**Cons**:
- Requires refactoring element template
- More markup

#### **Approach B: SVG Sprite via Attribute (Legacy Compatible)**
Use `<svg><use href="/icons/icons-sprite.svg#icon-name"></use></svg>` directly in template.

**Pros**:
- Minimal template changes
- Direct SVG control
- Works in pure HTML contexts

**Cons**:
- Less flexible styling
- No component encapsulation

---

## 📋 Elements Requiring Migration

### Current Status: `data-icon` Usage

| Element | File | Usage | Priority |
|---------|------|-------|----------|
| **Badge** | `badge.twig` | `<span data-icon="...">` | High |
| **Button** | `button.twig` | `<span data-icon="..." aria-hidden>` | High |
| **Divider** | `divider.twig` | `<span data-icon="..." aria-hidden>` | Medium |
| **Eyebrow** | `eyebrow.twig` | `<span data-icon="..." aria-hidden>` | Medium |
| **Field** | `field.twig` | `<span data-icon="..." aria-hidden>` (left/right) | High |
| **Link** | `link.twig` | `<span data-icon="..." aria-hidden>` | Medium |

### Already Migrated ✅
| Element | Status |
|---------|--------|
| **Icon** | ✅ Complete (ps-icon component) |

---

## 🔄 Migration Workflow (Per Component)

### Step 1: Choose Migration Approach

**For single icon per element** → Use **Approach A** (Icon Component)  
**For conditional rendering** → Use **Approach A** with `{% if icon %}`  
**For custom styling context** → Use **Approach A** with `baseClass` composition

### Step 2: Update Component Template

#### Example: Badge Component Migration

**BEFORE** (data-icon):
```twig
{%- if icon -%}
  <span class="ps-badge__icon" data-icon="{{ icon }}"></span>
{%- endif -%}
```

**AFTER** (Icon Component - Approach A):
```twig
{%- if icon -%}
  {% include '@elements/icon/icon.twig' with {
    name: icon,
    size: 'md',
    color: 'default',
    ariaLabel: null,
    baseClass: 'ps-badge__icon'
  } only %}
{%- endif -%}
```

**Key Points**:
- `name`: Icon name (without prefix, e.g., `'check'` not `'icon-check'`)
- `baseClass`: Composition parent class (ps-badge__icon becomes root, not child)
- `ariaLabel`: Only if icon is informative (not decorative)
- `size`: Match badge size context if needed
- `color`: Usually inherit from parent badge color

### Step 3: Update Component CSS

#### Badge CSS Changes

**BEFORE** (with data-icon CSS rules):
```css
.ps-badge__icon {
  /* data-icon styling via icons.css */
  display: inline-block;
  margin-right: var(--size-2);
}
```

**AFTER** (with Icon component):
```css
.ps-badge__icon {
  /* Icon component styling */
  display: inline-flex;
  align-items: center;
  margin-right: var(--size-2);
  
  &__svg {
    /* If needing SVG-specific styling */
    width: 1em;
    height: 1em;
    color: currentColor;
  }
}
```

### Step 4: Update Stories (Storybook)

Add icon prop to argTypes and examples:

```jsx
icon: {
  description: 'Icon name from sprite (e.g., "check", "calendar")',
  control: { type: 'select' },
  options: iconsList.all,
  table: {
    category: 'Content',
    type: { summary: 'string' },
    defaultValue: { summary: '' }
  }
}
```

### Step 5: Update README

Document icon usage with examples:

```markdown
## Icon Support

Icons are rendered via the centralized `ps-icon` component.

```twig
{# Badge with icon #}
{% include '@badges/badge/badge.twig' with {
  text: 'Success',
  icon: 'check',
  color: 'success'
} only %}
```

```

### Step 6: Verify & Test

1. **Linting**: `npm run lint:check` ✅
2. **Format**: `npm run format:check` ✅
3. **Build**: `npm run build` ✅
4. **Storybook**: `npm run watch` → http://localhost:6006 ✅
5. **Visual**: Check icon rendering, sizing, colors
6. **Accessibility**: Verify ARIA labels, roles, keyboard navigation

---

## 📝 Migration Checklist (Per Component)

### Badge Component ☐
- [ ] Update `badge.twig` - replace `data-icon` with icon component include
- [ ] Update `badge.css` - adjust icon spacing/styling if needed
- [ ] Update `badge.stories.jsx` - add icon prop to argTypes and examples
- [ ] Update `badge.yml` - add icon property to defaults
- [ ] Update `badge/README.md` - document icon usage
- [ ] Run: `npm run lint:check` ✅
- [ ] Run: `npm run format:check` ✅
- [ ] Visual test in Storybook ✅

### Button Component ☐
- [ ] Update `button.twig` (2 locations: icon-left, icon-right)
- [ ] Update `button.css` - icon spacing/alignment
- [ ] Update `button.stories.jsx` - icon examples
- [ ] Update `button.yml` - icon property
- [ ] Update `button/README.md` - icon documentation
- [ ] Verify both icon positions work ✅
- [ ] Run validation ✅

### Divider Component ☐
- [ ] Update `divider.twig` - icon rendering
- [ ] Update `divider.css` - icon styling
- [ ] Update `divider.stories.jsx`
- [ ] Update `divider.yml`
- [ ] Update `divider/README.md`
- [ ] Run validation ✅

### Eyebrow Component ☐
- [ ] Update `eyebrow.twig`
- [ ] Update `eyebrow.css`
- [ ] Update `eyebrow.stories.jsx`
- [ ] Update `eyebrow.yml`
- [ ] Update `eyebrow/README.md`
- [ ] Run validation ✅

### Field Component ☐
- [ ] Update `field.twig` (2 locations: left icon, right icon)
- [ ] Update `field.css`
- [ ] Update `field.stories.jsx`
- [ ] Update `field.yml`
- [ ] Update `field/README.md`
- [ ] Run validation ✅

### Link Component ☐
- [ ] Update `link.twig` - icon rendering
- [ ] Update `link.css` - icon positioning (left/right)
- [ ] Update `link.stories.jsx`
- [ ] Update `link.yml`
- [ ] Update `link/README.md`
- [ ] Run validation ✅

---

## 🚨 Important Notes

### Drupal Compatibility

✅ **Icon component is Drupal-compatible**:
- Uses Drupal Twig template inheritance
- No arrow functions (✅ ternary operators only)
- No `.filter()`, `.map()` methods
- Compatible with Drupal 10/11
- Works with Libraries API via `ps.libraries.yml`

### Icon Names

All icon names are from `source/icons-source/*.svg` (139 icons):
```
check, arrow-right, arrow-left, search, calendar, chevron-down, chevron-up,
chevron-left, chevron-right, close, heart, heart-outline, info, alert,
phone, email, share, download, ...
```

See: `source/patterns/documentation/icons-list.json` for full list.

### Composition Pattern (baseClass)

When icon is a child element of another component, use `baseClass`:

```twig
{% include '@elements/icon/icon.twig' with {
  name: icon,
  baseClass: 'ps-button__icon'
} only %}
```

This makes the icon conform to the parent's BEM structure:
- Root class: `ps-button__icon` (not `ps-icon`)
- Modifiers: `ps-button__icon--md`, `ps-button__icon--success`

---

## 🔗 References

- **Icon Component**: `source/patterns/elements/icon/`
- **Icon List**: `source/patterns/documentation/icons-list.json`
- **Build Script**: `scripts/build-icons.mjs`
- **Sprite**: `source/assets/icons/icons-sprite.svg`
- **Documentation**: `docs/ps-design/README.md`

---

## 💡 Tips

1. **Start with Badge** - Simplest case (single optional icon)
2. **Then Button** - Dual positioning (left/right icons)
3. **Then Field** - More complex (conditional left/right)
4. **Then Link, Eyebrow, Divider** - Similar patterns

---

## ✅ Completion Criteria

- All 6 elements migrated ✅
- All 6 components lint/format clean ✅
- All 6 have updated documentation ✅
- All 6 tested in Storybook ✅
- Single git commit per component ✅
- CHANGELOG.md updated ✅
