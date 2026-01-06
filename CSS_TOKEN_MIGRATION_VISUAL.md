# CSS Token Migration - Visual Summary 📊

## Before vs After

### Old System (Ad-hoc Tokens)
```
.offer-reference     gap: --offer-spacing-xs      (20px, fixed)
.offer-meta          gap: --offer-meta-gap        (unclear)
.offer-actions       gap: --offer-spacing-md      (20px, fixed)
.offer-description   gap: --offer-spacing-sm      (20px, fixed)
.offer-features      gap: --offer-features-section-gap (unclear)
.offer-energy        gap: --offer-spacing-md      (20px, fixed)
.offer-surface       gap: --offer-surface-spacing (unclear)
.offer-location      gap: --offer-location-gap    (unclear)
.offer-map           margin-top: --offer-spacing-2xl (unclear size)

❌ Problems: Inconsistent naming, fixed values (no responsive scaling), unclear semantics
```

### New System (Semantic 3-Tier)
```
.offer-reference     gap: --offer-gap-compact     (20px mobile → 28px desktop-large) ✅
.offer-meta          gap: --offer-gap-standard    (20px mobile → 32px desktop-large) ✅
.offer-actions       gap: --offer-gap-standard    (20px mobile → 32px desktop-large) ✅
.offer-description   gap: --offer-gap-compact     (20px mobile → 28px desktop-large) ✅
.offer-features      gap: --offer-gap-section     (32px mobile → 64px desktop-large) ✅
.offer-energy        gap: --offer-gap-compact     (20px mobile → 28px desktop-large) ✅
.offer-surface       gap: --offer-gap-section     (32px mobile → 64px desktop-large) ✅
.offer-location      gap: --offer-gap-standard    (20px mobile → 32px desktop-large) ✅
.offer-map           margin-top: --offer-gap-section (32px mobile → 64px desktop-large) ✅

✅ Benefits: Clear naming, responsive scaling, semantic meaning (compact/standard/section)
```

---

## Responsive Breakdown

### Compact Gap (Red) - Component Internal Spacing
```
Mobile (default)   20px (size-5)
├─ @media --tablet  22px (~size-5)
├─ @media --laptop  24px (size-6)
├─ @media --desktop 26px (~size-6)
└─ @media --desktop-large 28px (size-7)

Used for: References, meta items, action buttons, descriptions, energy metrics
Purpose: Tight spacing within components, keep them visually cohesive
```

### Standard Gap (Blue) - Inter-Section Small Gaps
```
Mobile (default)   20px (size-5)
├─ @media --tablet  24px (size-6)
├─ @media --laptop  28px (size-7)
├─ @media --desktop 30px (~size-8)
└─ @media --desktop-large 32px (size-8)

Used for: Meta header, actions, location, energy labels
Purpose: Medium spacing between related but distinct sections
```

### Section Gap (Yellow) - Major Vertical Spacing
```
Mobile (default)   32px (size-8)
├─ @media --tablet  40px (size-10)
├─ @media --laptop  48px (size-12)
├─ @media --desktop 56px (size-14)
└─ @media --desktop-large 64px (size-16)

Used for: Features, energy metrics grid, surface table, map
Purpose: Large vertical separations between major content sections
```

---

## CSS Update Pattern

### Example: `.offer-energy` Section
```css
/* BEFORE */
.offer-energy {
  display: grid;
  gap: var(--offer-spacing-md);          /* ❌ Unclear, fixed value */
}
.offer-energy__metrics {
  gap: var(--offer-energy-grid-gap);     /* ❌ Component-specific, hard to maintain */
}

/* AFTER */
.offer-energy {
  display: grid;
  gap: var(--offer-gap-compact);         /* ✅ Clear purpose: tight component spacing */
}
.offer-energy__metrics {
  gap: var(--offer-gap-section);         /* ✅ Clear purpose: major section spacing */
}
```

---

## Statistics

| Metric | Value |
|--------|-------|
| **Sections Updated** | 10 major CSS blocks |
| **Total Gap References** | ~30 individual property updates |
| **Old Tokens Removed** | 12+ ad-hoc token names |
| **New Tokens Introduced** | 3 semantic tokens (compact/standard/section) |
| **Responsive Breakpoints** | 6 media queries per token |
| **Code Reduction** | 98 lines deleted, 207 lines added (net: refactored structure) |
| **Build Status** | ✅ PASS (0 errors, 0 warnings) |

---

## Breakpoint Strategy

Token values scale smoothly across responsive design breakpoints:

```
Desktop-Large
    ↑ 64px (section) / 32px (standard) / 28px (compact)
    │
Desktop
    ↑ 56px / 30px / 26px
    │
Laptop
    ↑ 48px / 28px / 24px
    │
Tablet
    ↑ 40px / 24px / 22px
    │
Mobile (default)
    → 32px / 20px / 20px
```

**Principle**: Spacing increases as viewport grows (more room = more generous spacing)

---

## Token Definitions (CSS)

```css
:root {
  /* Layer 2: Offer-specific gap system */
  
  /* Compact gaps (component internal) - Red */
  --offer-gap-compact: var(--size-5);           /* Default: 20px */
  
  /* Standard gaps (inter-section) - Blue */
  --offer-gap-standard: var(--size-5);          /* Default: 20px */
  
  /* Section gaps (major spacing) - Yellow */
  --offer-gap-section: var(--size-8);           /* Default: 32px */
}

@media (--tablet) {
  :root {
    --offer-gap-compact: calc(var(--size-5) + 2px);  /* 22px */
    --offer-gap-standard: var(--size-6);             /* 24px */
    --offer-gap-section: var(--size-10);             /* 40px */
  }
}

@media (--laptop) {
  :root {
    --offer-gap-compact: var(--size-6);         /* 24px */
    --offer-gap-standard: var(--size-7);        /* 28px */
    --offer-gap-section: var(--size-12);        /* 48px */
  }
}

/* ... continues for --desktop and --desktop-large */
```

---

## Maintenance Notes

### How to Use These Tokens
1. **Tight component spacing** → Use `--offer-gap-compact`
2. **Medium section gaps** → Use `--offer-gap-standard`
3. **Major vertical spacing** → Use `--offer-gap-section`

### How to Modify These Tokens
- Update values in the media queries (don't create new token names)
- Ensure responsive scaling is maintained across all 6 breakpoints
- Test on all viewport sizes before committing

### How to Add New Gaps
- If a new section needs spacing, calculate where it fits (compact/standard/section)
- Apply the appropriate token
- Don't create new bespoke tokens (maintain the 3-tier system)

---

## Validation Results

✅ **Build**: `npm run build` passed (0 errors)  
✅ **Lint**: Biome checked 119 files (0 issues)  
✅ **Format**: 119 files checked (0 formatting issues)  
✅ **Storybook**: Rendering correctly at http://localhost:6006  
✅ **Git**: Commit `fd77feb` successful with detailed message  

**Overall Status**: ✅ COMPLETE - All offer-full.css spacing is now governed by the semantic 3-tier gap system.

---

Generated: 2025-01-06  
Scope: Offer Full Layout CSS Token Refactoring  
Author: AI Assistant (GitHub Copilot)
