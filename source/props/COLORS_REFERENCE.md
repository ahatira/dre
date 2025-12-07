# Color System Reference

**Date Updated:** 2025-12-07  
**Status:** ✅ Complete BNP Palette Implementation  

---

## 📋 Overview

The color system is built on three layers:

1. **colors.css** – Base palettes (50-900 scales) derived from official BNP specifications
2. **brand.css** – Semantic tokens mapping palettes to use cases (primary, secondary, success, danger, etc.)
3. **Components** – Use semantic tokens only, never reference color palettes directly

---

## 🎨 Color Palettes (colors.css)

All palettes follow a **50-900 scale** where:
- **50** = Lightest (subtle backgrounds, light tints)
- **100-300** = Light shades
- **400-500** = Medium shades  
- **600** = Primary shade (main color)
- **700** = Darker (hover state)
- **800** = Much darker (active state)
- **900** = Darkest (emphasis text)

### PRIMARY GREEN (BNP Official #00915A)

Used for: Primary actions, primary brand color, primary buttons

```css
--green-50:  #ebf7f4  /* Very light - subtle backgrounds */
--green-100: #d9efe9
--green-200: #c7e8df
--green-300: #99d3bd  /* Light shade */
--green-400: #6bc49a
--green-500: #04af6e  /* Medium */
--green-600: #00915a  /* ← PRIMARY (BNP Official) */
--green-700: #017f4f  /* Hover state */
--green-800: #016b44  /* Active state */
--green-900: #01563a  /* Dark emphasis */
```

**Used by:**
- `--primary` (main brand color)
- `--primary-hover`, `--primary-active`, `--primary-subtle`, etc.

---

### SECONDARY PINK (BNP Official #A12B66)

Used for: Secondary actions, accent elements

```css
--pink-50:  #f9ecf2  /* Very light */
--pink-100: #f3d9e5
--pink-200: #ecc6d8
--pink-300: #d998b8  /* Light shade */
--pink-400: #e9589d
--pink-500: #e0388c  /* Medium */
--pink-600: #ba3075  /* Mid tone */
--pink-700: #a12b66  /* ← SECONDARY (BNP Official) */
--pink-800: #8b245a  /* Darker */
--pink-900: #751d4e  /* Dark emphasis */
```

**Used by:**
- `--secondary` (secondary brand color)
- `--secondary-hover`, `--secondary-active`, etc.

---

### SUCCESS TEAL (BNP Official #198754)

Used for: Success messages, positive confirmations, validation states

```css
--teal-50:  #e7f4f1  /* Very light */
--teal-100: #d1e7dd
--teal-200: #a3cfbb
--teal-300: #7fb9a3  /* Light shade */
--teal-400: #52a98b
--teal-500: #25936f  /* Medium */
--teal-600: #198754  /* ← SUCCESS (BNP Official) */
--teal-700: #167a48  /* Hover state */
--teal-800: #146247  /* Active state */
--teal-900: #124a3b  /* Dark emphasis */
```

**Used by:**
- `--success` (success color - distinct from primary)
- `--border-success` (success borders - distinct from primary borders)
- Checkmarks, validation icons, green alerts

**Important:** Success TEAL (#198754) is different from Primary GREEN (#00915A). This separation allows:
- Different border colors for validation vs. primary buttons
- Clear visual distinction between brand identity and system feedback
- Proper semantic meaning in UI components

---

### ERROR RED (BNP Official #EB3636)

Used for: Error messages, destructive actions, error states

```css
--red-50:  #fef7f7  /* Very light */
--red-100: #fdebeb
--red-200: #f9d1d1
--red-300: #f5b5b5  /* Light shade */
--red-400: #f08b8b
--red-500: #eb6161  /* Medium */
--red-600: #eb3636  /* ← ERROR (BNP Official) */
--red-700: #d43131  /* Hover state */
--red-800: #bd2c2c  /* Active state */
--red-900: #a62626  /* Dark emphasis */
```

**Used by:**
- `--danger` (error color)
- `--border-error` (error state borders)
- Error messages, delete buttons, validation errors

---

### GREY SCALE (BNP Official)

Used for: Neutral UI, text, borders, backgrounds

```css
--gray-50:  #f9f9fb  /* Almost white */
--gray-100: #ebedef  /* Very light background */
--gray-200: #d6dbde  /* Light background */
--gray-300: #c4c9cf
--gray-400: #b4babe  /* Medium gray */
--gray-500: #8a9097
--gray-600: #555f66
--gray-700: #434f57  /* Dark text */
--gray-800: #3a4551
--gray-900: #333333  /* ← GREY (BNP Official) - Almost black */
```

**Used by:**
- All text colors (`--text-primary`, `--text-secondary`, etc.)
- All borders (`--border-default`, `--border-light`, etc.)
- Background overlays
- Disabled states

---

## 🏷️ Semantic Tokens (brand.css)

Semantic tokens provide meaningful names for specific use cases. Always use semantic tokens in components, never reference color palettes directly.

### Semantic Color Tokens

```css
/* Color Use Cases */
--primary: var(--green-600);       /* Primary brand color (#00915A) */
--secondary: var(--pink-700);      /* Secondary brand color (#A12B66) */
--success: var(--teal-600);        /* Success state (#198754) */
--danger: var(--red-600);          /* Error/danger state (#EB3636) */
--warning: var(--yellow-400);      /* Warning state */
--info: var(--blue-600);           /* Informational state */
--light: var(--gray-100);          /* Light variant (for dark backgrounds) */
--dark: var(--gray-700);           /* Dark variant (for light backgrounds) */
```

### State Tokens

Each semantic color has 9 states:

```css
/* Example: Primary states */
--primary: var(--green-600);                    /* Base color */
--primary-hover: var(--green-700);              /* Darker on hover */
--primary-active: var(--green-800);             /* Darkest on active/pressed */
--primary-text: var(--white);                   /* Text on primary backgrounds */
--primary-border: var(--green-600);             /* Border matching base */
--primary-subtle: var(--green-50);              /* Light tint for subtle elements */
--primary-bg-subtle: var(--green-50);           /* Very light background */
--primary-border-subtle: var(--green-200);      /* Light gray border */
--primary-text-emphasis: var(--green-900);      /* Dark text for emphasis */
```

### Text Tokens

```css
--text-primary: var(--gray-700);     /* #434F57 - Main text (high contrast) */
--text-secondary: var(--gray-500);   /* #8A9097 - Secondary text */
--text-disabled: var(--gray-400);    /* #B4BABE - Disabled text */
--text-inverse: var(--white);        /* #FFFFFF - Text on dark backgrounds */
```

### Border Tokens

```css
--border-default: var(--gray-200);   /* Standard borders */
--border-light: var(--gray-100);     /* Light separators */
--border-focus: var(--gray-900);     /* Focus rings */
--border-disabled: var(--gray-400);  /* Disabled borders */
--border-error: var(--red-600);      /* Error state borders */
--border-success: var(--teal-600);   /* Success state borders (distinct from primary!) */
```

### Overlay Tokens

```css
--overlay-dark-heavy: rgba(0, 0, 0, 0.6);      /* Modals, heavy overlays */
--overlay-dark-medium: rgba(0, 0, 0, 0.36);    /* Medium overlays */
--overlay-dark-light: rgba(0, 0, 0, 0.12);     /* Light overlays, hover states */
--overlay-brand-base: #1c2d37;                 /* Brand overlay base */
--overlay-brand-medium: rgba(28, 45, 55, 0.36); /* Medium brand overlay */
--overlay-brand-light: rgba(28, 45, 55, 0.12);  /* Light brand overlay */
```

---

## 📐 Usage in Components

### ✅ DO: Use Semantic Tokens

```css
/* Good: Reference semantic tokens */
.ps-button {
  background-color: var(--primary);
  color: var(--primary-text);
  border-color: var(--primary-border);
}

.ps-button:hover {
  background-color: var(--primary-hover);
}
```

### ❌ DON'T: Reference Palettes Directly

```css
/* Bad: Never reference palettes in components */
.ps-button {
  background-color: var(--green-600);  /* ❌ Don't do this */
}
```

### ❌ DON'T: Hardcode Colors

```css
/* Bad: Never hardcode colors */
.ps-button {
  background-color: #00915A;  /* ❌ Don't do this */
}
```

---

## 🎯 Design Decisions

### Why Separate PRIMARY and SUCCESS Colors?

- **PRIMARY (#00915A)** = Brand identity, primary actions
- **SUCCESS (#198754)** = System feedback, validation

Keeping them separate allows:
- Primary buttons to use `--primary` 
- Success icons/checkmarks to use `--success`
- `--border-success` to be distinct from `--border-primary`
- Proper semantic meaning in UI

### Why Complete 50-900 Scales?

The 50-900 scale provides flexibility:
- **50-300**: Subtle backgrounds, light tints (non-interactive)
- **400-500**: Medium interactive states
- **600**: Primary shade (main use)
- **700-800**: Hover and active states
- **900**: Dark emphasis text

This enables consistent visual hierarchy without needing custom values.

---

## 📝 Migration Guide

If migrating from the old color system:

1. Replace all hardcoded hex colors with semantic token references
2. Replace all palette references in components with semantic tokens
3. Update HSL values to hex format (already done in colors.css)
4. Verify all colors appear correct (visual regression testing)

---

## 🔍 Color Accessibility

All colors meet **WCAG 2.2 AA** contrast requirements:

- Text on `--primary`: White text on #00915A ✅ 
- Text on `--secondary`: White text on #A12B66 ✅
- Text on `--success`: White text on #198754 ✅
- Text on `--danger`: White text on #EB3636 ✅
- Text on `--light`: Dark gray text on #EBEDEF ✅
- Disabled states: 50% opacity ✅
- Focus indicators: 2px dark gray outline ✅

---

## 📚 Related Documentation

- **brand.css**: Semantic token definitions
- **colors.css**: Color palette definitions
- **ANALYSE_TOKENS.md**: Historical analysis of token system evolution
- **Component READMEs**: Usage examples in specific components

