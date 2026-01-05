# Responsive Design - Quick Reference Guide

**Version**: 1.0.0  
**Last Updated**: 2025-12-13  
**Status**: MANDATORY for all components

---

## 📱 Breakpoints Available

```css
/* From source/props/media.css */
@media (--mobile-sm)      /* 400px+ */
@media (--mobile)         /* 640px+ */
@media (--tablet)         /* 768px+ */
@media (--laptop)         /* 1024px+ */
@media (--desktop)        /* 1280px+ */
@media (--desktop-large)  /* 1440px+ */
```

---

## ✅ Standard Component Pattern

**MANDATORY**: All components MUST include all 6 breakpoints, even if empty (as comments).

```css
.ps-component {
  /* Base styles = mobile-first (no media query) */
  --ps-component-padding: var(--size-2);
  padding: var(--ps-component-padding);
  
  /* Mobile-sm (400px+) */
  @media (--mobile-sm) {
    /* Component-specific adjustments if needed */
  }
  
  /* Mobile (640px+) */
  @media (--mobile) {
    --ps-component-padding: var(--size-3);
  }
  
  /* Tablet (768px+) */
  @media (--tablet) {
    --ps-component-padding: var(--size-4);
  }
  
  /* Laptop (1024px+) */
  @media (--laptop) {
    /* Desktop-specific adjustments if needed */
  }
  
  /* Desktop (1280px+) */
  @media (--desktop) {
    --ps-component-padding: var(--size-6);
  }
  
  /* Desktop-large (1440px+) */
  @media (--desktop-large) {
    --ps-component-padding: var(--size-8);
  }
}
```

---

## 📦 Container Pattern (Reference)

Standard responsive container with max-width and padding:

```css
.container {
  margin-inline: auto;
  max-inline-size: var(--size-max-content-width); /* 1376px */
  padding-inline: var(--size-4);

  @media (--desktop) {
    padding-inline: var(--size-8);
  }

  @media (--desktop-large) { /* 1440px */
    padding-inline: 0;
  }
}
```

---

## 🎯 Key Rules

1. **Mobile-first** = Base styles without media query (smallest screens first)
2. **Override component variables** = Change CSS custom properties in media queries
3. **All breakpoints present** = Include empty blocks with comments for future work
4. **PostCSS syntax** = Use `@media (--breakpoint-name)`, NOT `@media (min-width: ...)`
5. **Logical properties** = Use `inline`/`block` axis: `padding-inline`, `margin-block`, `max-inline-size`

---

## 📋 Common Patterns

### Pattern 1: Responsive Padding

```css
.ps-card {
  --ps-card-padding: var(--size-4);
  padding: var(--ps-card-padding);
  
  @media (--tablet) {
    --ps-card-padding: var(--size-6);
  }
  
  @media (--desktop) {
    --ps-card-padding: var(--size-8);
  }
}
```

### Pattern 2: Layout Changes

```css
.ps-navigation {
  flex-direction: column;
  
  @media (--tablet) {
    flex-direction: row;
  }
}
```

### Pattern 3: Visibility Toggles

```css
/* Mobile menu (hide on tablet+) */
.ps-mobile-menu {
  display: flex;
  
  @media (--tablet) {
    display: none;
  }
}

/* Desktop menu (show on tablet+) */
.ps-desktop-menu {
  display: none;
  
  @media (--tablet) {
    display: flex;
  }
}
```

### Pattern 4: Font Sizes

```css
.ps-heading {
  --ps-heading-size: var(--font-size-7); /* 24px mobile */
  font-size: var(--ps-heading-size);
  
  @media (--tablet) {
    --ps-heading-size: var(--font-size-11); /* 32px tablet */
  }
  
  @media (--desktop) {
    --ps-heading-size: var(--font-size-15); /* 48px desktop */
  }
}
```

### Pattern 5: Grid Columns

```css
.ps-grid {
  display: grid;
  grid-template-columns: 1fr; /* 1 column mobile */
  gap: var(--size-4);
  
  @media (--mobile) {
    grid-template-columns: repeat(2, 1fr); /* 2 columns */
  }
  
  @media (--tablet) {
    grid-template-columns: repeat(3, 1fr); /* 3 columns */
  }
  
  @media (--desktop) {
    grid-template-columns: repeat(4, 1fr); /* 4 columns */
  }
}
```

---

## ❌ Common Mistakes

### ❌ WRONG - Hardcoded breakpoints
```css
@media (min-width: 768px) { /* Don't use raw values */ }
@media (width >= 768px) { /* Don't use raw values */ }
```

### ✅ CORRECT - Named breakpoints
```css
@media (--tablet) { /* Use PostCSS custom media */ }
```

---

### ❌ WRONG - Missing breakpoints
```css
.ps-component {
  padding: var(--size-2);
  
  @media (--tablet) {
    padding: var(--size-6);
  }
  /* Missing other breakpoints! */
}
```

### ✅ CORRECT - All breakpoints present
```css
.ps-component {
  padding: var(--size-2);
  
  @media (--mobile-sm) { /* Even if empty */ }
  @media (--mobile) { /* Even if empty */ }
  @media (--tablet) {
    padding: var(--size-6);
  }
  @media (--laptop) { /* Even if empty */ }
  @media (--desktop) { /* Even if empty */ }
  @media (--desktop-large) { /* Even if empty */ }
}
```

---

### ❌ WRONG - Desktop-first approach
```css
.ps-component {
  padding: var(--size-8); /* Desktop value in base */
  
  @media (max-width: 767px) {
    padding: var(--size-2); /* Override for mobile */
  }
}
```

### ✅ CORRECT - Mobile-first approach
```css
.ps-component {
  padding: var(--size-2); /* Mobile value in base */
  
  @media (--tablet) {
    padding: var(--size-8); /* Override for desktop */
  }
}
```

---

## 🔍 Quick Checklist

Before committing component CSS, verify:

- [ ] All 6 breakpoints present (mobile-sm → desktop-large)
- [ ] Empty breakpoints have comments
- [ ] Base styles = mobile (no media query)
- [ ] Using PostCSS custom media `@media (--breakpoint)`
- [ ] Overriding component CSS variables, not direct properties
- [ ] Using logical properties (`inline`/`block` axis)
- [ ] Mobile-first approach (base → tablet → desktop progression)

---

## 📚 Related Documentation

- **Full guide**: `.github/instructions/03-technical-implementation.md` (Section 1.6)
- **Breakpoint definitions**: `source/props/media.css`
- **Container widths**: `source/props/sizes.css`
- **Example components**: `source/patterns/components/breadcrumb/breadcrumb.css`

---

**Note**: This is a MANDATORY standard for all 87 components in the design system. Badge component serves as reference implementation after systematic audit (December 2025).
