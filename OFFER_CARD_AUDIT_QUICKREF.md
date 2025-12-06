# Offer Card - Audit Quick Reference

## Conformity Checklist

| Item | Status | Notes |
|------|--------|-------|
| **5 Files Present** | ✅ | twig, css, yml, stories.jsx, README.md |
| **Storybook Autodocs** | ✅ | `tags: ['autodocs']` present |
| **Twig Drupal Safe** | ✅ | No JS methods, proper `{% if %}` logic |
| **Token Verification** | ✅ | 30+ tokens verified in source/props/ |
| **BEM Naming** | ✅ | `.ps-offer-card__*` consistent |
| **ARIA Attributes** | ✅ | aria-label, aria-pressed, aria-hidden proper |
| **CSS 3-Layer** | ✅ | Layer 1 (tokens), Layer 2 (variables), Layer 3 (selectors) |
| **Focus States** | ✅ | focus-visible present |
| **Real Estate Context** | ✅ | Property data, locations, prices |
| **Documentation** | ✅ | English-only (after fixes) |

---

## Issues Fixed: 3

| # | Issue | Location | Fix |
|---|-------|----------|-----|
| 1 | Non-standard footer gap | L105 offer-card.css | `--size-205` → `--size-3` |
| 2 | Parent modifier violation | L338-340 offer-card.css | `.ps-card` → `.ps-offer-card .ps-card` |
| 3 | Documentation language | Throughout offer-card.css | French/English → English-only |

---

## Design Tokens Used: 30+

**Colors**: primary, secondary, red-600, red-700, yellow-500, gray-100, gray-500, gray-600, gray-700, white, text-primary, text-secondary, blue-600

**Sizes**: size-1 to size-12, size-205, size-305

**Borders**: border-size-2, border-size-15, radius-1 to radius-7

**Typography**: font-sans, font-weight-400, font-weight-700, font-size-0 to font-size-2, leading-6

**Animations**: duration-fast, ease-out-1, ease-3

**Shadows**: shadow-2

---

## Build Commands

```bash
npm run build          # Validate CSS compilation
npm run watch         # Preview with Storybook
npm run generate:pattern # Scaffold new components
```

---

## Key Files

- **Audit Report**: `AUDIT_OFFER_CARD.md`
- **Corrections**: `CORRECTIONS_OFFER_CARD.md`
- **Component**: `source/patterns/components/offer-card/`
