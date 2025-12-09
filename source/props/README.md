# Design Tokens (source/props/)

This directory contains all **CSS Custom Properties** (design tokens) for the PS Theme design system, organized by category.

---

## 📁 File Structure

### Color Tokens

- **`colors.css`** – Official BNP Paribas Real Estate color palettes (50-900 scales)
  - PRIMARY GREEN: #00915A
  - SECONDARY PINK: #A12B66
  - SUCCESS TEAL: #198754
  - ERROR RED: #EB3636
  - GREY SCALE: #333333 → #FFFFFF

- **`brand.css`** – Semantic color tokens (primary, secondary, success, danger, etc.)
  - Maps color palettes to meaningful use cases
  - Defines state variations (hover, active, subtle, emphasis)
  - Includes text, border, and overlay tokens

### Typography & Spacing

- **`fonts.css`** – Font families, sizes, weights, line-heights
- **`sizes.css`** – Spacing scales (4px, 8px, 16px, etc.)

### UI Elements

- **`borders.css`** – Border radii and widths
- **`shadows.css`** – Box shadow presets
- **`animations.css`** – Animation durations and keyframes
- **`easing.css`** – Timing functions / easing curves

### Layout & Metadata

- **`zindex.css`** – Z-index layers for stacking context
- **`aspects.css`** – Aspect ratio utilities
- **`media.css`** – Responsive breakpoints
- **`theme.css`** – Theme variables (rarely used)
### Utility

- **`index.css`** – Main entry point (@import orchestration)

---

## 📖 Documentation

### Quick Reference
- **COLORS_REFERENCE.md** – Complete color system guide with usage examples
- **ANALYSE_TOKENS.md** – Historical analysis of token system evolution

### Project Documentation
- **docs/ps-design/README.md** – Design tokens overview
- **docs/ps-design/CHANGELOG.md** – Implementation history
- **.github/instructions/core.instructions.md** – Coding standards and token usage rules

---

## 🎨 Color Palette Organization

### Official BNP Palettes (colors.css)

Each palette has a **50-900 scale**:

| Scale | Usage |
|-------|-------|
| 50 | Very light (subtle backgrounds, light tints) |
| 100-300 | Light shades |
| 400-500 | Medium shades |
| **600** | **Primary shade (main color)** |
| 700 | Darker (hover state) |
| 800 | Much darker (active state) |
| 900 | Darkest (emphasis text) |

### Example: GREEN Palette (PRIMARY)

```css
--green-50:  #ebf7f4  /* Lightest */
--green-600: #00915a  /* ← PRIMARY (BNP #00915A) */
--green-700: #017f4f  /* Hover */
--green-800: #016b44  /* Active */
--green-900: #01563a  /* Darkest */
```

### Example: TEAL Palette (SUCCESS)

```css
--teal-50:  #e7f4f1  /* Lightest */
--teal-600: #198754  /* ← SUCCESS (BNP #198754) */
--teal-700: #167a48  /* Hover */
--teal-800: #146247  /* Active */
--teal-900: #124a3b  /* Darkest */
```

**Important**: SUCCESS TEAL (#198754) is different from PRIMARY GREEN (#00915A). This separation ensures:
- Distinct visual meaning
- Separate `--border-success` from `--border-primary`
- Proper semantic feedback in UI components

---

## 🏷️ Semantic Tokens (brand.css)

Semantic tokens provide meaningful names for use cases. **Always use semantic tokens in components**, never reference palettes directly.

```css
/* Semantic Color Tokens */
--primary: var(--green-600);        /* Primary actions */
--secondary: var(--pink-700);       /* Secondary actions */
--success: var(--teal-600);         /* Success feedback */
--danger: var(--red-600);           /* Error/danger feedback */
--warning: var(--yellow-400);       /* Warning feedback */
--info: var(--blue-600);            /* Informational feedback */
--light: var(--gray-100);           /* Light variant */
--dark: var(--gray-700);            /* Dark variant */

/* State Variations (example with --primary) */
--primary-hover: var(--green-700);
--primary-active: var(--green-800);
--primary-subtle: var(--green-50);
--primary-text: var(--white);
--primary-border: var(--green-600);

/* Text Tokens */
--text-primary: var(--gray-700);     /* Main text */
--text-secondary: var(--gray-500);   /* Secondary text */
--text-disabled: var(--gray-400);    /* Disabled text */
--text-inverse: var(--white);        /* Text on dark */

/* Border Tokens */
--border-default: var(--gray-200);
--border-light: var(--gray-100);
--border-focus: var(--gray-900);
--border-success: var(--teal-600);   /* Note: uses TEAL, not GREEN */
--border-error: var(--red-600);
```

---

## ✅ Usage in Components

### DO: Use Semantic Tokens

```css
/* Good: Reference semantic tokens from brand.css */
.ps-button {
  background-color: var(--primary);
  color: var(--primary-text);
  border: 1px solid var(--primary-border);
}

.ps-button:hover {
  background-color: var(--primary-hover);
}
```

### ❌ DON'T: Reference Palettes

```css
/* Bad: Never reference color palettes in components */
.ps-button {
  background-color: var(--green-600);  /* ❌ Don't */
}
```

### ❌ DON'T: Hardcode Values

```css
/* Bad: Never hardcode colors */
.ps-button {
  background-color: #00915A;  /* ❌ Don't */
}
```

---

## 🔄 Build Integration

### Import Chain

```css
/* index.css imports all tokens in order: */
@import './colors.css';      /* 1. Base palettes */
@import './brand.css';       /* 2. Semantic tokens */
@import './fonts.css';       /* 3. Typography */
@import './sizes.css';       /* 4. Spacing */
@import './borders.css';
@import './shadows.css';
@import './animations.css';
@import './easing.css';
@import './zindex.css';
@import './aspects.css';
@import './media.css';
@import './theme.css';
```

### Build Process

```bash
npm run build          # Full build with token validation
npm run watch         # Watch mode (dev with Storybook)
npm run vite:build    # CSS/JS compilation
```

All tokens are compiled into `dist/css/styles.css` and available as CSS Custom Properties in the browser.

---

## 📚 Related Files

- **src/patterns/styles.css** – Imports index.css and component styles
- **src/patterns/storybook.css** – Storybook-specific token documentation
- **docs/design/tokens/** – Token specifications (YAML format)

---

## 🚨 Important Rules

1. **Never edit `source/props/*.css` directly** during component development
2. **Always use semantic tokens** in components (never palette references)
3. **Never hardcode hex values** in component CSS
4. **Maintain 3-layer architecture**: Palettes → Semantic → Components
5. **Request new tokens** via separate process, don't add ad-hoc

---

## 🎓 Further Reading

- **COLORS_REFERENCE.md** – Complete color system guide
- **ANALYSE_TOKENS.md** – Token system analysis
- **.github/instructions/core.instructions.md** – Coding standards
- **docs/ps-design/README.md** – Project-level documentation

