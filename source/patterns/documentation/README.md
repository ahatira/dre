# Documentation Data Files

This directory contains standardized JSON data files used across all component stories for consistency.

## 📁 Files

### `icons-list.json`
Lists of all available icons grouped by category.

**Usage:**
```jsx
import iconsList from '@patterns/documentation/icons-list.json';
options: iconsList.categories.generic
```

**Structure:**
- `regular`: All regular icons
- `poi`: Points of interest icons
- `categories`: Icons grouped by use case (generic, social, tools, etc.)
- `all`: Complete icon list

---

### `colors-list.json`
Standardized color palettes and semantic color mappings.

**Usage:**
```jsx
import colorsList from '@patterns/documentation/colors-list.json';
options: colorsList.semantic.values
```

**Structure:**
- `semantic`: Semantic colors (primary, secondary, success, warning, danger, info)
- `neutral`: Neutral colors (default, muted, subtle)
- `brand`: Brand-specific colors (primary, secondary, gold)
- `extended`: All colors combined
- `link`: Link-specific colors
- `field`: Field/input border colors

Each group includes:
- `values`: Array of color names
- `tokens`: CSS custom property mappings
- `hex`: Hex color values (where applicable)

---

### `sizes-list.json`
Standardized size scales for components.

**Usage:**
```jsx
import sizesList from '@patterns/documentation/sizes-list.json';
options: sizesList.standard.values
```

**Structure:**
- `standard`: 5-tier scale (xs, sm, md, lg, xl) - most components
- `compact`: 3-tier scale (small, medium, large) - simple components
- `extended`: 7-tier scale (2xs, xs, sm, md, lg, xl, 2xl) - complex components
- `avatar`: Avatar-specific sizes
- `font`: Font size scale

Each scale includes:
- `values`: Array of size names
- `tokens`: CSS custom property mappings
- `pixels`: Pixel value reference

---

### `variants-list.json`
Component variant definitions organized by prop name.

**Usage:**
```jsx
import variantsList from '@patterns/documentation/variants-list.json';
options: variantsList.color.common
```

**Structure:**
- `color`: Color prop values (semantic colors by component)
- `variant`: Variant prop values (component type/form)
- `appearance`: Appearance prop values (visual style)
- `size`: Size prop values (by component)
- `orientation`: Orientation values (horizontal, vertical)
- `shape`: Shape values (circle, square, rounded, pill)
- `alignment`: Alignment values (horizontal & vertical)
- `position`: Position values (cardinal & extended)

---

## 🎯 Naming Convention Standard

### Props Names

| Prop | Purpose | Values | Example |
|------|---------|--------|---------|
| `color` | Semantic color | primary, secondary, success, warning, danger, info | `color: 'primary'` |
| `variant` | Component type/form | linear, circular, solid, outlined, ghost | `variant: 'linear'` |
| `appearance` | Visual style | solid, outlined, ghost, soft | `appearance: 'outlined'` |
| `size` | Component size | xs, sm, md, lg, xl OR small, medium, large | `size: 'md'` |
| `orientation` | Spatial direction | horizontal, vertical | `orientation: 'horizontal'` |
| `shape` | Geometric form | circle, square, rounded, pill | `shape: 'circle'` |
| `alignment` | Content alignment | start, center, end, justify | `alignment: 'center'` |
| `position` | Spatial position | top, right, bottom, left | `position: 'top'` |

### Component Examples

#### ✅ Button
```jsx
color: 'primary'           // Semantic color
appearance: 'solid'        // Visual style
size: 'md'                 // Size
```

#### ✅ Progress Bar
```jsx
variant: 'linear'          // Type (linear vs circular)
color: 'primary'           // Semantic color
size: 'md'                 // Size
```

#### ✅ Divider
```jsx
orientation: 'horizontal'  // Direction
variant: 'solid'           // Style (solid, dashed, dotted)
color: 'default'           // Semantic color
```

#### ✅ Avatar
```jsx
shape: 'circle'            // Geometric form
size: 'md'                 // Size
```

---

## 🔄 Maintenance

When adding new values:

1. **Update the appropriate JSON file** in this directory
2. **Add token mapping** if CSS custom property exists
3. **Document in CHANGELOG.md** with justification
4. **Update component stories** to use the new value
5. **Verify consistency** across all components using the same prop

---

## 📚 References

- [Storybook Doc Template](/.github/STORYBOOK_DOC_TEMPLATE.md)
- [Design Tokens](/source/props/)
- [Component Specs](/docs/design/)
